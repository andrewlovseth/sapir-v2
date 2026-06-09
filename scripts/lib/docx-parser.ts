/**
 * Parse .docx files using mammoth.js.
 *
 * Document structure (general pattern — designer varies notation each quarter):
 *   Line 1: AUTHOR NAME(S) in ALL CAPS
 *   HED: Article Headline
 *   SUBHED: Subtitle / dek text
 *   [X DROP CAP] rest of first word...
 *   Body paragraphs...
 *   [[section separator]] or [[section break]] etc. between sections
 *   [[block q]] or [[block quote]] etc. around block quotes
 *
 * IMPORTANT: The markers are NOT standardized. Each quarter, run inspect-docx.ts
 * on a sample article FIRST to verify the current notation. If the designer
 * changed the markers, update the regexes below before running the full import.
 */

import mammoth from "mammoth";
import { readFile } from "fs/promises";
import type { ParsedArticle } from "./types";

export async function parseDocx(filePath: string): Promise<ParsedArticle> {
  const buffer = await readFile(filePath);
  const { value: html } = await mammoth.convertToHtml({ buffer });

  // mammoth produces flat <p>...</p> tags — split them
  const rawParagraphs = html
    .split(/<\/p>\s*<p>/)
    .map((p) => p.replace(/^<p>/, "").replace(/<\/p>$/, ""));

  let hed = "";
  let dek = "";
  let dropcapLetter = "";
  const paragraphs: string[] = [];
  const separatorIndices: number[] = [];
  const blockQuoteRanges: { start: number; end: number }[] = [];
  const pullQuotes: string[] = [];
  let blockQuoteOpen = -1;
  let isConversation = false;

  // Track how many non-metadata paragraphs have speaker patterns
  let speakerCount = 0;
  let bodyParaCount = 0;

  for (const raw of rawParagraphs) {
    const text = stripHtml(raw).trim();

    // Skip empty paragraphs
    if (!text) continue;

    // Capture the designer's trailing pull-quote annotation, then stop. Each
    // docx ends with a "BLOCK QUOTES:" (or "PULL QUOTES:") heading followed by
    // an <ol> of the quotes pulled for the print layout. mammoth renders it as
    //   <p>BLOCK QUOTES:</p><ol><li>quote one…</li><li>quote two…</li></ol>
    // Because we split on </p><p>, this final chunk's `raw` contains both the
    // heading and the whole <ol>. We harvest each <li>'s inner HTML (keeping
    // inline <em> etc.) into pullQuotes, then break: the annotation never lands
    // in `paragraphs`, so the real final paragraph keeps its .last-p dingbat.
    if (/^(block|pull)\s*quotes?\b/i.test(text)) {
      const liRegex = /<li>([\s\S]*?)<\/li>/gi;
      let liMatch: RegExpExecArray | null;
      while ((liMatch = liRegex.exec(raw)) !== null) {
        const item = liMatch[1].trim();
        if (item) pullQuotes.push(item);
      }
      break;
    }

    // Skip author name line (all caps, first real paragraph)
    if (!hed && !dek && paragraphs.length === 0 && isAllCaps(text)) continue;

    // Extract HED
    if (text.startsWith("HED:")) {
      hed = text.replace(/^HED:\s*/, "").trim();
      continue;
    }

    // Extract SUBHED → dek
    if (text.startsWith("SUBHED:") || text.startsWith("SUBHED :")) {
      dek = text.replace(/^SUBHED\s*:\s*/, "").trim();
      continue;
    }

    // Section separator — designer varies notation each quarter:
    // [[section separator]], [[section break]], [[separator]], [[ Section Break ]], etc.
    if (/^\[*\s*section\s*(separator|break|divider)\s*\]*$/i.test(text) ||
        /^\[*\s*(separator|break|divider)\s*\]*$/i.test(text)) {
      // Insert separator before the next paragraph
      separatorIndices.push(paragraphs.length);
      continue;
    }

    // Block quote marker — designer varies notation each quarter:
    // [[block q]], [[block quote]], [[blockquote]], [[block]], [[ Block Q ]], etc.
    if (/^\[*\s*block\s*(q(uote)?)?\s*\]*$/i.test(text)) {
      if (blockQuoteOpen === -1) {
        // Opening marker — record where the next paragraph will start
        blockQuoteOpen = paragraphs.length;
      } else {
        // Closing marker — save the range
        blockQuoteRanges.push({ start: blockQuoteOpen, end: paragraphs.length });
        blockQuoteOpen = -1;
      }
      continue;
    }

    // Handle drop cap: [X DROP CAP] rest...
    const dropCapMatch = raw.match(
      /\[([A-Z])\s+DROP\s+CAP\]\s*(.*)/i
    );
    if (dropCapMatch) {
      dropcapLetter = dropCapMatch[1];
      // Prepend the dropcap letter to the rest of the paragraph.
      // CSS hides the first letter visually (replaced by the dropcap image),
      // but the letter must be in the HTML for copy/paste and printing.
      let restOfPara = cleanSoftBreaks(dropCapMatch[2].trim());
      paragraphs.push(dropcapLetter.toUpperCase() + restOfPara);
      bodyParaCount++;
      continue;
    }

    // Regular body paragraph — keep the HTML formatting
    paragraphs.push(cleanSoftBreaks(raw.trim()));
    bodyParaCount++;

    // Check for conversation format (speaker names in lowercase followed by colon)
    if (/^[a-z][a-z\s]+:/.test(text)) {
      speakerCount++;
    }
  }

  // If more than 30% of paragraphs start with a speaker pattern, it's a conversation
  isConversation = bodyParaCount > 0 && speakerCount / bodyParaCount > 0.3;

  // Detect sign-off paragraphs at the end (dates, author names, locations).
  // These are typically short, italicized, and appear after the body concludes.
  const signoffIndices: number[] = [];
  let lastBodyIndex = paragraphs.length - 1;

  for (let i = paragraphs.length - 1; i >= 0; i--) {
    const text = stripHtml(paragraphs[i]).trim();
    const isFullyItalic =
      paragraphs[i].trim().startsWith("<em>") &&
      paragraphs[i].trim().endsWith("</em>");
    const isShort = text.length < 80;
    const looksLikeDate = /^\w+\s+\d{1,2},?\s+\d{4}$/.test(text);
    const looksLikeLocation = /^[A-Z][a-z]+,?\s+[A-Z]/.test(text) && isShort;

    if (isShort && (isFullyItalic || looksLikeDate || looksLikeLocation)) {
      signoffIndices.unshift(i);
      lastBodyIndex = i - 1;
    } else {
      break; // Stop scanning once we hit a normal body paragraph
    }
  }

  return {
    hed,
    dek,
    dropcapLetter,
    paragraphs,
    separatorIndices,
    isConversation,
    lastBodyIndex,
    signoffIndices,
    blockQuoteRanges,
    pullQuotes,
  };
}

/**
 * Strip HTML tags AND decode common entities to get plain text.
 * Entity decoding matters for detection logic: mammoth encodes a literal "&"
 * in a byline (e.g. "SARA PAASCHE-ORLOW & AMY SCHECTMAN") as "&amp;". Without
 * decoding, the lowercase "amp" defeats the all-caps author-line check and the
 * byline leaks into the body. Only detection uses this — stored paragraph
 * content keeps the original HTML entities.
 */
function stripHtml(html: string): string {
  return html
    .replace(/<[^>]+>/g, "")
    .replace(/&amp;/g, "&")
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">")
    .replace(/&quot;/g, '"')
    .replace(/&#39;/g, "'")
    .replace(/&nbsp;/g, " ");
}

/**
 * Convert soft line breaks within a body paragraph to spaces.
 * Designers use shift+enter in the docx to control line wrapping; mammoth turns
 * those into <br/> mid-sentence (e.g. "five years of <br/>government service"),
 * which renders as an ugly break in flowing prose. Replace each <br> with a
 * space and collapse the doubled space. (Verse/poetry in these articles comes
 * through as separate paragraphs or [[block]] quotes, not <br>.)
 */
function cleanSoftBreaks(html: string): string {
  return html.replace(/<br\s*\/?>/gi, " ").replace(/ {2,}/g, " ").trim();
}

/** Check if a string is all uppercase (ignoring punctuation/spaces) */
function isAllCaps(text: string): boolean {
  const letters = text.replace(/[^a-zA-Z]/g, "");
  return letters.length > 0 && letters === letters.toUpperCase();
}

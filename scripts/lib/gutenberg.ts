/**
 * Convert parsed article data into Gutenberg block markup.
 *
 * Matches the block patterns found in existing published Sapir articles:
 * - <!-- wp:paragraph --> for body text
 * - <!-- wp:separator --> with has-alpha-channel-opacity for section breaks
 * - <!-- wp:pullquote --> for pull quotes
 */

import type { ParsedArticle } from "./types";

export function toGutenbergBlocks(
  parsed: ParsedArticle,
  pullquotes: string[] = []
): string {
  const blocks: string[] = [];
  const separatorSet = new Set(parsed.separatorIndices);

  // Plan pullquote placement — source-aware: each pull quote lands a couple
  // paragraphs after the body sentence it was excerpted from.
  const pullquotePlacements = planPullquotePlacement(
    parsed.paragraphs,
    pullquotes,
    parsed.separatorIndices,
    parsed.blockQuoteRanges
  );

  const signoffSet = new Set(parsed.signoffIndices);

  // Build a set of paragraph indices inside block quote ranges,
  // and a map from the range start index to the range itself
  const blockQuoteStart = new Map<number, { start: number; end: number }>();
  const blockQuoteInner = new Set<number>();
  for (const range of parsed.blockQuoteRanges) {
    blockQuoteStart.set(range.start, range);
    for (let j = range.start; j < range.end; j++) {
      blockQuoteInner.add(j);
    }
  }

  for (let i = 0; i < parsed.paragraphs.length; i++) {
    // Insert separator before this paragraph if marked
    if (separatorSet.has(i)) {
      blocks.push(separator());
    }

    // Insert pullquote if one is placed here
    const pqIndex = pullquotePlacements.get(i);
    if (pqIndex !== undefined) {
      blocks.push(pullquote(pullquotes[pqIndex]));
    }

    // Block quote range: collect all paragraphs into one pullquote.basic block
    if (blockQuoteStart.has(i)) {
      const range = blockQuoteStart.get(i)!;
      const innerParagraphs = parsed.paragraphs
        .slice(range.start, range.end)
        .map((p) => `<p>${p}</p>`)
        .join("");
      blocks.push(pullquoteBasic(innerParagraphs));
      i = range.end - 1; // skip to end of range (loop will i++)
      continue;
    }

    // Skip paragraphs inside a block quote range (handled above)
    if (blockQuoteInner.has(i)) continue;

    if (i === parsed.lastBodyIndex) {
      // Last body paragraph gets the dingbat ornament class
      blocks.push(paragraphWithClass(parsed.paragraphs[i], "last-p"));
    } else if (signoffSet.has(i)) {
      // Sign-off paragraphs (dates, author names) get right alignment
      blocks.push(paragraphAlignRight(parsed.paragraphs[i]));
    } else {
      blocks.push(paragraph(parsed.paragraphs[i]));
    }
  }

  return blocks.join("\n\n");
}

function paragraph(content: string): string {
  return `<!-- wp:paragraph -->\n<p>${content}</p>\n<!-- /wp:paragraph -->`;
}

function paragraphWithClass(content: string, className: string): string {
  return `<!-- wp:paragraph {"className":"${className}"} -->\n<p class="${className}">${content}</p>\n<!-- /wp:paragraph -->`;
}

function paragraphAlignRight(content: string): string {
  return `<!-- wp:paragraph {"align":"right"} -->\n<p class="has-text-align-right">${content}</p>\n<!-- /wp:paragraph -->`;
}

function separator(): string {
  return `<!-- wp:separator -->\n<hr class="wp-block-separator has-alpha-channel-opacity"/>\n<!-- /wp:separator -->`;
}

function pullquote(text: string): string {
  return `<!-- wp:pullquote -->\n<figure class="wp-block-pullquote"><blockquote><p>${text}</p></blockquote></figure>\n<!-- /wp:pullquote -->`;
}

function pullquoteBasic(innerHtml: string): string {
  return `<!-- wp:pullquote {"className":"basic"} -->\n<figure class="wp-block-pullquote basic"><blockquote>${innerHtml}</blockquote></figure>\n<!-- /wp:pullquote -->`;
}

/**
 * Place each pull quote NEAR the body sentence it was excerpted from.
 *
 * A pull quote is inserted in the "gap" BEFORE a given paragraph index — i.e.
 * gap g means the pull quote renders between paragraph g-1 and paragraph g.
 *
 * A pull quote must stay in the SAME SECTION as its source — sections are the
 * paragraph ranges between separators. Crossing a separator would strand the
 * quote far from the sentence it was pulled from.
 *
 * Algorithm (per pull quote, in document order):
 *   1. Find source paragraph: match the first 6 normalized words of the quote
 *      against each paragraph (then retry with 4 words). src = that index, or -1.
 *   2. Determine the source's section [secStart, secEnd) from separatorIndices.
 *      Only gaps strictly inside that section are eligible.
 *   3. Target gap = src + 3 — two full body paragraphs sit between the source
 *      and the pull quote, the house convention.
 *   4. Reject invalid gaps (out of section, the src+1 "repeat" slot, adjacent to
 *      a separator, the newsletter slot ~10, inside a block-quote range, or too
 *      close to an already-placed pull quote).
 *   5. Search AFTER the source within-section, preferring LATER gaps (src+3,
 *      src+4, src+2, src+5…), never src+1; if none, search BEFORE the source
 *      within-section (src-2, src-3…; skip src-1 to avoid hugging the source).
 *   6. If the source can't be found OR no valid gap exists in the whole section,
 *      fall back to the old even-distribution placement for that one quote.
 *
 * Returns Map<gapIndex, pullquoteArrayIndex>, consumed by toGutenbergBlocks.
 */
function planPullquotePlacement(
  paragraphs: string[],
  pullquotes: string[],
  separatorIndices: number[],
  blockQuoteRanges: { start: number; end: number }[]
): Map<number, number> {
  const placements = new Map<number, number>();
  if (pullquotes.length === 0) return placements;

  const separatorSet = new Set(separatorIndices);
  const normalizedParas = paragraphs.map(normalizeForMatch);
  // Gaps (the index a pull quote sits before) that already hold a pull quote.
  const placedGaps: number[] = [];

  // Section boundaries: paragraph ranges between separators. separatorIndices
  // are "separator before this paragraph index", so they double as the section
  // split points. e.g. separators [4, 14] over 20 paras → sections [0,4), [4,14),
  // [14,20). Return the [start, end) section containing a given paragraph index.
  const sortedSeparators = [...separatorIndices].sort((a, b) => a - b);
  const sectionOf = (paraIdx: number): { start: number; end: number } => {
    let start = 0;
    let end = paragraphs.length;
    for (const sep of sortedSeparators) {
      if (sep <= paraIdx) start = sep;
      else {
        end = sep;
        break;
      }
    }
    return { start, end };
  };

  const isValidGap = (g: number, src: number): boolean => {
    // In-range: a gap of 0 (before the first paragraph) or after the last are
    // both unusable — a pull quote must sit between two real paragraphs.
    if (g < 1 || g > paragraphs.length - 1) return false;
    // Never directly after the source (the "repeat" right under the sentence).
    if (g === src + 1) return false;
    // Keep ≥1 paragraph away from any separator. separatorIndices are "separator
    // before this index", so a separator at g, g-1, or g+1 is too close.
    if (separatorSet.has(g) || separatorSet.has(g - 1) || separatorSet.has(g + 1)) {
      return false;
    }
    // Avoid the newsletter form slot (injected ≈ body paragraph index 10).
    if (g === 9 || g === 10 || g === 11) return false;
    // Never inside a block-quote range [start, end).
    for (const r of blockQuoteRanges) {
      if (g >= r.start && g < r.end) return false;
    }
    // Keep ≥1 paragraph from an already-placed pull quote.
    for (const placed of placedGaps) {
      if (Math.abs(g - placed) <= 1) return false;
    }
    return true;
  };

  for (let k = 0; k < pullquotes.length; k++) {
    const src = findSourceParagraph(pullquotes[k], normalizedParas);

    let chosen = -1;

    if (src !== -1) {
      // Keep all searching inside the source's own section. Gaps strictly inside
      // [secStart, secEnd) are secStart+1 .. secEnd-1 (both adjacent paragraphs
      // must belong to the section).
      const { start: secStart, end: secEnd } = sectionOf(src);
      const minGap = secStart + 1;
      const maxGap = secEnd - 1;

      // Search AFTER the source, within-section. Preference order is the target
      // (src+3) first, then bias later: src+4, src+2, src+5, src+6, … never src+1.
      // Build that ordered list of gaps, then keep going strictly later (src+7+)
      // if the section is long enough.
      const afterGaps = [src + 3, src + 4, src + 2, src + 5, src + 6];
      for (let g = src + 7; g <= maxGap; g++) afterGaps.push(g);
      for (const g of afterGaps) {
        if (g >= src + 2 && g <= maxGap && isValidGap(g, src)) {
          chosen = g;
          break;
        }
      }

      // Nothing valid after the source in this section — try before it,
      // within-section (src-2, src-3, …; skip src-1 to avoid hugging the source).
      if (chosen === -1) {
        for (let g = src - 2; g >= minGap; g--) {
          if (isValidGap(g, src)) {
            chosen = g;
            break;
          }
        }
      }
    }

    // Fallback: source not found, or no valid gap near it.
    if (chosen === -1) {
      chosen = fallbackPlacement(
        paragraphs.length,
        separatorIndices,
        k,
        pullquotes.length,
        (g) => isValidGap(g, src)
      );
    }

    if (chosen !== -1) {
      placements.set(chosen, k);
      placedGaps.push(chosen);
    }
  }

  return placements;
}

/**
 * Find the body paragraph a pull quote was excerpted from. Matches the first 6
 * normalized words of the quote against each paragraph; if no hit, retries with
 * the first 4. Returns the paragraph index, or -1.
 */
function findSourceParagraph(
  pullquote: string,
  normalizedParas: string[]
): number {
  const normQuote = normalizeForMatch(pullquote);
  const words = normQuote.split(" ").filter(Boolean);

  for (const wordCount of [6, 4]) {
    if (words.length < wordCount) continue;
    const key = words.slice(0, wordCount).join(" ");
    if (!key) continue;
    for (let i = 0; i < normalizedParas.length; i++) {
      if (normalizedParas[i].includes(key)) return i;
    }
  }
  return -1;
}

/**
 * Old even-distribution behavior, kept as a per-quote fallback. Distributes the
 * k-th of `count` pull quotes across the sections between separators and returns
 * the first gap that passes the supplied validity check, or -1.
 */
function fallbackPlacement(
  totalParagraphs: number,
  separatorIndices: number[],
  k: number,
  count: number,
  isValid: (g: number) => boolean
): number {
  // Build sections: ranges of paragraph indices between separators.
  const sectionBoundaries = [0, ...separatorIndices, totalParagraphs];
  const sections: { start: number; end: number }[] = [];
  for (let i = 0; i < sectionBoundaries.length - 1; i++) {
    const start = sectionBoundaries[i];
    const end = sectionBoundaries[i + 1];
    if (end - start >= 3) sections.push({ start, end });
  }
  if (sections.length === 0) return -1;

  const sectionIdx = Math.min(
    sections.length - 1,
    Math.floor((k * sections.length) / Math.max(count, 1))
  );
  const section = sections[sectionIdx];
  const mid = Math.floor((section.start + 1 + section.end - 1) / 2);

  // Try the section midpoint, then walk outward within the section for a valid gap.
  if (isValid(mid)) return mid;
  for (let d = 1; d < section.end - section.start; d++) {
    if (mid + d < section.end && isValid(mid + d)) return mid + d;
    if (mid - d > section.start && isValid(mid - d)) return mid - d;
  }
  return -1;
}

/** Strip HTML tags from a string (for text matching only). */
function stripHtml(html: string): string {
  return html.replace(/<[^>]+>/g, "");
}

/**
 * Normalize text for fuzzy matching: strip tags, lowercase, replace smart
 * quotes/punctuation with spaces, and collapse whitespace. Result contains only
 * [a-z0-9] and single spaces, so quote text matches body text regardless of the
 * <em> wrapping or curly-vs-straight punctuation that differs between the
 * print-layout annotation and the mammoth-rendered body.
 */
function normalizeForMatch(text: string): string {
  return stripHtml(text)
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, " ")
    .replace(/\s+/g, " ")
    .trim();
}

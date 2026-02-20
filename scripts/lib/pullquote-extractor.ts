/**
 * Extract pullquotes by cross-referencing PDF text against article body.
 *
 * Strategy: Pull quotes in the typeset PDF are repeated excerpts from the body.
 * We extract all text from the PDF, find fragments that appear in the body text
 * but are positioned separately in the PDF layout (typically in larger/different
 * font, isolated between paragraphs).
 *
 * This is a heuristic approach — the --skip-pullquotes flag exists as fallback.
 */

import { readFile } from "fs/promises";
// pdf-parse v2 has broken ESM exports — use require() for Bun compat
import { createRequire } from "module";
const require = createRequire(import.meta.url);
const pdfParse = require("pdf-parse");

/**
 * Extract candidate pullquotes from a PDF by finding text fragments
 * that are duplicated in the body.
 */
export async function extractPullquotes(
  pdfPath: string,
  bodyParagraphs: string[]
): Promise<string[]> {
  const buffer = await readFile(pdfPath);
  const { text: pdfText } = await pdfParse(buffer);

  // Normalize the body text for comparison
  const bodyText = bodyParagraphs
    .map((p) => stripHtml(p).trim())
    .join(" ")
    .replace(/\s+/g, " ")
    .toLowerCase();

  // Split PDF into lines/chunks and look for duplicated fragments
  const pdfLines = pdfText
    .split("\n")
    .map((line) => line.trim())
    .filter((line) => line.length > 30); // Pullquotes are typically 30+ chars

  const candidates: { text: string; score: number }[] = [];

  // Group consecutive PDF lines that might form a single pullquote
  let currentChunk: string[] = [];
  const chunks: string[] = [];

  for (const line of pdfLines) {
    const normalizedLine = line.toLowerCase().replace(/\s+/g, " ");

    // Check if this line appears verbatim in the body
    if (bodyText.includes(normalizedLine)) {
      currentChunk.push(line);
    } else {
      if (currentChunk.length > 0) {
        chunks.push(currentChunk.join(" "));
        currentChunk = [];
      }
    }
  }
  if (currentChunk.length > 0) {
    chunks.push(currentChunk.join(" "));
  }

  // Score chunks: prefer longer fragments that are clearly pull-quote length
  for (const chunk of chunks) {
    const words = chunk.split(/\s+/).length;
    // Pullquotes are typically 15-60 words
    if (words >= 15 && words <= 80) {
      // Higher score for chunks in the sweet spot
      const score = words >= 20 && words <= 50 ? 2 : 1;
      candidates.push({ text: chunk, score });
    }
  }

  // Sort by score (descending), take top 2-3
  candidates.sort((a, b) => b.score - a.score);

  return candidates.slice(0, 3).map((c) => c.text);
}

function stripHtml(html: string): string {
  return html.replace(/<[^>]+>/g, "");
}

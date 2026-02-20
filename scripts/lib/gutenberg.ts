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

  // Plan pullquote placement
  const pullquotePlacements = planPullquotePlacement(
    parsed.paragraphs.length,
    parsed.separatorIndices,
    pullquotes.length
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
 * Distribute pullquotes evenly across sections, avoiding placement:
 * - Right after a separator (section start)
 * - Right before a separator or at the very end (section end)
 */
function planPullquotePlacement(
  totalParagraphs: number,
  separatorIndices: number[],
  pullquoteCount: number
): Map<number, number> {
  if (pullquoteCount === 0) return new Map();

  // Build sections: ranges of paragraph indices between separators
  const sectionBoundaries = [0, ...separatorIndices, totalParagraphs];
  const sections: { start: number; end: number }[] = [];
  for (let i = 0; i < sectionBoundaries.length - 1; i++) {
    const start = sectionBoundaries[i];
    const end = sectionBoundaries[i + 1];
    // A section needs at least 3 paragraphs to place a pullquote in the middle
    if (end - start >= 3) {
      sections.push({ start, end });
    }
  }

  if (sections.length === 0) return new Map();

  const placements = new Map<number, number>();
  const separatorSet = new Set(separatorIndices);

  // Distribute pullquotes across sections as evenly as possible
  for (let pq = 0; pq < pullquoteCount && pq < sections.length; pq++) {
    const sectionIdx = Math.floor((pq * sections.length) / pullquoteCount);
    const section = sections[sectionIdx];

    // Place in the middle of the section, avoiding first and last positions
    const safeStart = section.start + 1; // not right after separator
    const safeEnd = section.end - 1; // not right before next separator or end
    const placement = Math.floor((safeStart + safeEnd) / 2);

    // Verify it's not adjacent to a separator
    if (!separatorSet.has(placement) && !separatorSet.has(placement + 1)) {
      placements.set(placement, pq);
    }
  }

  return placements;
}

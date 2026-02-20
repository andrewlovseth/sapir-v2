/**
 * Match article folders to WordPress draft posts.
 *
 * Folder naming convention: `02_foer_foster_1`
 *   - Sequence number (02)
 *   - Author last names (foer, foster) — underscored, lowercased
 *   - Suffix (1) — ignored
 *
 * Matching strategy:
 *   1. Parse folder name → extract author last names
 *   2. For each WP draft post assigned to the issue, resolve its author CPT IDs
 *   3. Look up each author CPT's `last_name` field
 *   4. Match folder last names to post author last names
 */

import { readdir, stat } from "fs/promises";
import { join, basename } from "path";
import type { ArticleFolder, MatchedArticle, ParsedArticle } from "./types";
import {
  getIssueDrafts,
  getAuthorLastName,
  getPostContent,
  getPostMeta,
} from "./wp-bridge";
import { parseDocx } from "./docx-parser";

/** Parse a folder name into its components */
function parseFolderName(dirName: string): {
  sequence: number;
  lastNames: string[];
} {
  // e.g., "02_foer_foster_1" → ["02", "foer", "foster", "1"]
  const parts = dirName.split("_");
  const sequence = parseInt(parts[0], 10);

  // Last element is the suffix (always "1"), everything in between is author names
  const lastNames = parts.slice(1, -1).map((n) => n.toLowerCase());

  return { sequence, lastNames };
}

/** Scan a directory for article folders and identify their contents */
export async function scanFolders(
  rootPath: string
): Promise<ArticleFolder[]> {
  const entries = await readdir(rootPath);
  const folders: ArticleFolder[] = [];

  for (const entry of entries) {
    const fullPath = join(rootPath, entry);
    const s = await stat(fullPath);
    if (!s.isDirectory() || entry.startsWith(".")) continue;

    const { sequence, lastNames } = parseFolderName(entry);
    if (isNaN(sequence) || lastNames.length === 0) {
      console.warn(`  Skipping unrecognized folder: ${entry}`);
      continue;
    }

    // Find .docx, .pdf, .jpg in the folder
    const files = await readdir(fullPath);
    const docx = files.find((f) => f.endsWith(".docx"));
    const pdf = files.find((f) => f.endsWith(".pdf"));
    const jpg = files.find((f) => f.endsWith(".jpg") || f.endsWith(".jpeg"));

    if (!docx || !pdf || !jpg) {
      console.warn(
        `  Folder ${entry} missing files (docx=${!!docx}, pdf=${!!pdf}, jpg=${!!jpg})`
      );
      continue;
    }

    folders.push({
      path: fullPath,
      dirName: entry,
      sequence,
      authorLastNames: lastNames,
      docxPath: join(fullPath, docx),
      pdfPath: join(fullPath, pdf),
      dropcapPath: join(fullPath, jpg),
    });
  }

  return folders.sort((a, b) => a.sequence - b.sequence);
}

/** Match folders to WP draft posts */
export async function matchFoldersToWpPosts(
  folders: ArticleFolder[],
  issueId: number
): Promise<MatchedArticle[]> {
  console.log(`\nFetching draft posts for issue ${issueId}...`);
  const drafts = await getIssueDrafts(issueId);
  console.log(`  Found ${drafts.length} draft posts`);

  // Resolve author last names for each draft
  const draftAuthors = new Map<
    number,
    { title: string; lastNames: string[]; authorMeta: string }
  >();

  for (const draft of drafts) {
    const lastNames: string[] = [];

    // Author meta can be a serialized PHP array or a single ID
    // For WP-CLI, `post meta get` returns the raw value
    // ACF stores multiple post_objects as serialized arrays
    const authorIds = parseAcfIds(draft.authorMeta);

    for (const authorId of authorIds) {
      const lastName = await getAuthorLastName(authorId);
      if (lastName) {
        lastNames.push(lastName.toLowerCase());
      }
    }

    draftAuthors.set(draft.id, {
      title: draft.title,
      lastNames,
      authorMeta: draft.authorMeta,
    });
  }

  // Pre-parse all docx files to get HEDs for title-based disambiguation.
  // This handles cases like Bret Stephens having both the Editor's Note
  // and "Israel Studies Can Redeem Academia".
  const folderParsed = new Map<string, ParsedArticle>();
  for (const folder of folders) {
    const parsed = await parseDocx(folder.docxPath);
    folderParsed.set(folder.dirName, parsed);
  }

  // Match each folder to a draft. Track claimed posts to prevent double-matching.
  const matched: MatchedArticle[] = [];
  const claimedPostIds = new Set<number>();
  const normalize = (s: string) => s.replace(/[-\s]/g, "").toLowerCase();

  for (const folder of folders) {
    const parsed = folderParsed.get(folder.dirName)!;
    let bestMatch: { id: number; title: string } | null = null;
    let bestScore = 0;

    for (const [postId, info] of draftAuthors) {
      if (claimedPostIds.has(postId)) continue;

      // Score 1: Author name matching
      const authorScore = folder.authorLastNames.filter((name) =>
        info.lastNames.some(
          (wpName) =>
            normalize(wpName) === normalize(name) ||
            normalize(wpName).includes(normalize(name)) ||
            normalize(name).includes(normalize(wpName))
        )
      ).length;

      if (authorScore === 0) continue;

      // Score 2: HED/title matching — strong tiebreaker when same author
      // has multiple articles (e.g., Bret Stephens)
      const hedLower = parsed.hed.toLowerCase();
      const titleLower = info.title.toLowerCase();
      const titleMatch =
        titleLower.includes(hedLower) || hedLower.includes(titleLower)
          ? 10
          : 0;

      const totalScore = authorScore + titleMatch;

      if (totalScore > bestScore) {
        bestScore = totalScore;
        bestMatch = { id: postId, title: info.title };
      }
    }

    if (!bestMatch || bestScore === 0) {
      // Special case: editors_note folder → match by title
      if (folder.dirName.includes("editors_note")) {
        const editorDraft = drafts.find(
          (d) =>
            d.title.toLowerCase().includes("editor") &&
            !claimedPostIds.has(d.id)
        );
        if (editorDraft) {
          bestMatch = { id: editorDraft.id, title: editorDraft.title };
        }
      }
    }

    if (!bestMatch) {
      console.warn(
        `  No WP match for folder: ${folder.dirName} (authors: ${folder.authorLastNames.join(", ")})`
      );
      continue;
    }

    claimedPostIds.add(bestMatch.id);

    // Reuse pre-parsed docx result (parsed was declared earlier in the loop)
    const parsedArticle = folderParsed.get(folder.dirName)!;

    // Check existing state
    const content = await getPostContent(bestMatch.id);
    const dropcapMeta = await getPostMeta(bestMatch.id, "dropcap");
    const pdfMeta = await getPostMeta(bestMatch.id, "pdf");

    matched.push({
      folder,
      parsed: parsedArticle,
      postId: bestMatch.id,
      postTitle: bestMatch.title,
      hasContent: content.length > 0,
      hasDrop: dropcapMeta !== "" && dropcapMeta !== "0",
      hasPdf: pdfMeta !== "" && pdfMeta !== "0",
    });
  }

  return matched;
}

/**
 * Parse ACF serialized post object IDs.
 * WP stores multiple post_objects as PHP serialized arrays like:
 *   a:2:{i:0;s:4:"1234";i:1;s:4:"5678";}
 * or sometimes as simple comma-separated or single values.
 */
function parseAcfIds(raw: string): number[] {
  if (!raw) return [];

  // Try JSON first (sometimes wp-cli returns JSON)
  try {
    const parsed = JSON.parse(raw);
    if (Array.isArray(parsed)) return parsed.map(Number).filter((n) => !isNaN(n));
    if (typeof parsed === "number") return [parsed];
  } catch {}

  // Try PHP serialized array: extract all number values
  const phpMatches = raw.match(/[si]:\d+[:;]"?(\d+)"?/g);
  if (phpMatches) {
    return phpMatches
      .map((m) => {
        const numMatch = m.match(/"?(\d+)"?$/);
        return numMatch ? parseInt(numMatch[1], 10) : NaN;
      })
      .filter((n) => !isNaN(n) && n > 100); // Filter out array indices (small numbers)
  }

  // Try single number
  const num = parseInt(raw, 10);
  if (!isNaN(num)) return [num];

  return [];
}

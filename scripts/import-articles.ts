/**
 * Import article content from .docx files into WordPress drafts.
 *
 * Usage:
 *   bun run scripts/import-articles.ts '/path/to/SAPIR Vol 20' --issue-id=4405 --dry-run
 *   bun run scripts/import-articles.ts '/path/to/SAPIR Vol 20' --issue-id=4405
 *   bun run scripts/import-articles.ts '/path/to/SAPIR Vol 20' --issue-id=4405 --skip-pullquotes
 */

import { parseArgs } from "util";
import { writeFile, mkdir } from "fs/promises";
import { join } from "path";
import { scanFolders, matchFoldersToWpPosts } from "./lib/folder-matcher";
import { toGutenbergBlocks } from "./lib/gutenberg";
import { extractPullquotes } from "./lib/pullquote-extractor";
import { optimizeDrop, cleanupTemp } from "./lib/image-optimizer";
import {
  updatePostContent,
  setDek,
  importMedia,
  setDropcap,
  setPdf,
  cleanupStaging,
  getPostSlug,
} from "./lib/wp-bridge";
import type { MatchedArticle, ImportResult } from "./lib/types";

const { values, positionals } = parseArgs({
  args: Bun.argv.slice(2),
  options: {
    "issue-id": { type: "string" },
    "dry-run": { type: "boolean", default: false },
    "skip-pullquotes": { type: "boolean", default: false },
  },
  allowPositionals: true,
});

const folderPath = positionals[0];
const issueId = parseInt(values["issue-id"] ?? "", 10);
const dryRun = values["dry-run"] ?? false;
const skipPullquotes = values["skip-pullquotes"] ?? false;

if (!folderPath || isNaN(issueId)) {
  console.error(
    "Usage: bun run scripts/import-articles.ts <folder-path> --issue-id=<id> [--dry-run] [--skip-pullquotes]"
  );
  process.exit(1);
}

console.log(`\n=== Sapir Article Import ===`);
console.log(`  Source: ${folderPath}`);
console.log(`  Issue ID: ${issueId}`);
console.log(`  Dry run: ${dryRun}`);
console.log(`  Skip pullquotes: ${skipPullquotes}`);

// --- Step 1: Scan folders ---
console.log("\n--- Scanning folders ---");
const folders = await scanFolders(folderPath);
console.log(`  Found ${folders.length} article folders`);

for (const f of folders) {
  console.log(
    `    ${f.dirName} → authors: [${f.authorLastNames.join(", ")}]`
  );
}

// --- Step 2: Match to WP posts ---
console.log("\n--- Matching to WordPress posts ---");
const matched = await matchFoldersToWpPosts(folders, issueId);

console.log(`\n  Matched ${matched.length}/${folders.length} folders:`);
for (const m of matched) {
  const status = [];
  if (m.hasContent) status.push("has content");
  if (m.hasDrop) status.push("has dropcap");
  if (m.hasPdf) status.push("has pdf");
  const statusStr = status.length > 0 ? ` [${status.join(", ")}]` : "";
  console.log(
    `    ${m.folder.dirName} → #${m.postId} "${m.postTitle}"${statusStr}`
  );
  console.log(`      HED: ${m.parsed.hed}`);
  console.log(`      DEK: ${m.parsed.dek}`);
  console.log(
    `      Paragraphs: ${m.parsed.paragraphs.length}, Separators: ${m.parsed.separatorIndices.length}`
  );
}

if (dryRun) {
  console.log("\n--- Dry run complete. No changes made. ---\n");

  // In dry run, show a preview of the first article's Gutenberg output
  if (matched.length > 0) {
    const preview = matched[0];
    const blocks = toGutenbergBlocks(preview.parsed);
    console.log(
      `\n--- Preview: "${preview.postTitle}" (first 2000 chars) ---`
    );
    console.log(blocks.slice(0, 2000));
    if (blocks.length > 2000) console.log("\n  ... (truncated)");
  }

  process.exit(0);
}

// --- Step 3: Import each article ---
console.log("\n--- Importing articles ---");
const results: ImportResult[] = [];
const DDEV_PROJECT_ROOT = "/Users/andrewlovseth/Dev/sapir-journal";
const tempContentDir = join(DDEV_PROJECT_ROOT, "wp", "wp-content", "tmp-import-content");
await mkdir(tempContentDir, { recursive: true });

for (const article of matched) {
  const result: ImportResult = {
    postId: article.postId,
    postTitle: article.postTitle,
    success: false,
    skipped: false,
    actions: [],
  };

  try {
    console.log(`\n  Processing: "${article.postTitle}" (#${article.postId})`);

    // --- Update post content (skip if already populated) ---
    if (article.hasContent) {
      console.log(`    Skipping content: already set`);
    } else {
      // --- Extract pullquotes ---
      let pullquotes: string[] = [];
      if (!skipPullquotes) {
        try {
          pullquotes = await extractPullquotes(
            article.folder.pdfPath,
            article.parsed.paragraphs
          );
          if (pullquotes.length > 0) {
            console.log(`    Found ${pullquotes.length} pullquote(s)`);
            result.actions.push(`extracted ${pullquotes.length} pullquotes`);
          }
        } catch (err) {
          console.warn(
            `    Pullquote extraction failed: ${err instanceof Error ? err.message : err}`
          );
        }
      }

      // --- Generate Gutenberg content ---
      const gutenbergContent = toGutenbergBlocks(article.parsed, pullquotes);

      // Write to temp file for wp post update
      const contentFile = join(tempContentDir, `post-${article.postId}.html`);
      await writeFile(contentFile, gutenbergContent);

      // --- Update post content ---
      await updatePostContent(article.postId, contentFile);
      result.actions.push("updated post content");
      console.log(`    Updated post content (${article.parsed.paragraphs.length} paragraphs)`);
    }

    // --- Set dek ---
    if (article.parsed.dek) {
      await setDek(article.postId, article.parsed.dek);
      result.actions.push(`set dek: "${article.parsed.dek}"`);
      console.log(`    Set dek: "${article.parsed.dek}"`);
    }

    // --- Upload and set dropcap ---
    if (!article.hasDrop) {
      const optimizedPath = await optimizeDrop(
        article.folder.dropcapPath,
        `dropcap-${article.postId}.jpg`
      );
      const attachId = await importMedia(optimizedPath, article.postId);
      await setDropcap(article.postId, attachId);
      result.actions.push(`uploaded dropcap (attachment ${attachId})`);
      console.log(`    Uploaded dropcap → attachment #${attachId}`);
    } else {
      console.log(`    Skipping dropcap: already set`);
    }

    // --- Upload and set PDF (renamed to post slug) ---
    if (!article.hasPdf) {
      const slug = await getPostSlug(article.postId);
      const slugPdfPath = join(article.folder.path, `${slug}.pdf`);
      const { copyFile } = await import("fs/promises");
      await copyFile(article.folder.pdfPath, slugPdfPath);

      const pdfAttachId = await importMedia(slugPdfPath, article.postId);
      await setPdf(article.postId, pdfAttachId);

      // Clean up the renamed copy
      const { rm } = await import("fs/promises");
      await rm(slugPdfPath);

      result.actions.push(`uploaded PDF as ${slug}.pdf (attachment ${pdfAttachId})`);
      console.log(`    Uploaded PDF as ${slug}.pdf → attachment #${pdfAttachId}`);
    } else {
      console.log(`    Skipping PDF: already set`);
    }

    result.success = true;
    result.skipped = result.actions.length === 0;
  } catch (err) {
    const message = err instanceof Error ? err.message : String(err);
    result.error = message;
    console.error(`    ERROR: ${message}`);
  }

  results.push(result);
}

// --- Cleanup ---
await cleanupTemp();
await cleanupStaging();
const { rm } = await import("fs/promises");
await rm(tempContentDir, { recursive: true, force: true });

// --- Summary ---
console.log("\n=== Import Summary ===");
const succeeded = results.filter((r) => r.success && !r.skipped);
const skipped = results.filter((r) => r.skipped);
const failed = results.filter((r) => !r.success);

console.log(`  Imported: ${succeeded.length}`);
console.log(`  Skipped:  ${skipped.length}`);
console.log(`  Failed:   ${failed.length}`);

if (failed.length > 0) {
  console.log("\n  Failed articles:");
  for (const f of failed) {
    console.log(`    #${f.postId} "${f.postTitle}": ${f.error}`);
  }
}

console.log("");
process.exit(failed.length > 0 ? 1 : 0);

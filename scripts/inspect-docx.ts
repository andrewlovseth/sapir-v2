/**
 * Diagnostic tool: inspect mammoth.js output from a .docx file.
 * Run BEFORE building the import pipeline to understand document structure.
 *
 * Usage: bun run scripts/inspect-docx.ts '/path/to/article.docx'
 */

import mammoth from "mammoth";
import { readFile } from "fs/promises";

const filePath = process.argv[2];

if (!filePath) {
  console.error("Usage: bun run scripts/inspect-docx.ts <path-to-docx>");
  process.exit(1);
}

console.log(`\n=== Inspecting: ${filePath} ===\n`);

const buffer = await readFile(filePath);

// --- Raw text extraction ---
const rawResult = await mammoth.extractRawText({ buffer });
console.log("─── RAW TEXT ───────────────────────────────────────");
console.log(rawResult.value);
console.log("\n─── END RAW TEXT ───────────────────────────────────\n");

// --- HTML conversion (default style map) ---
const htmlResult = await mammoth.convertToHtml({ buffer });
console.log("─── HTML OUTPUT ────────────────────────────────────");
console.log(htmlResult.value);
console.log("\n─── END HTML OUTPUT ────────────────────────────────\n");

// --- Conversion messages (warnings about unmapped styles, etc.) ---
if (htmlResult.messages.length > 0) {
  console.log("─── MAMMOTH MESSAGES ───────────────────────────────");
  for (const msg of htmlResult.messages) {
    console.log(`  [${msg.type}] ${msg.message}`);
  }
  console.log("─── END MESSAGES ───────────────────────────────────\n");
}

// --- Style analysis: re-run with a custom style map that tags styles ---
const styledResult = await mammoth.convertToHtml({
  buffer,
  styleMap: [
    // Tag any custom paragraph styles so we can see them
    "p[style-name='Pull Quote'] => blockquote.pullquote:fresh",
    "p[style-name='Pullquote'] => blockquote.pullquote:fresh",
    "p[style-name='Block Quote'] => blockquote.blockquote:fresh",
    "p[style-name='Subhead'] => h3.subhead:fresh",
    "p[style-name='SUBHEAD'] => h3.subhead:fresh",
    "p[style-name='Section Break'] => hr.section-break",
  ],
});

if (styledResult.value !== htmlResult.value) {
  console.log("─── HTML WITH STYLE MAP ────────────────────────────");
  console.log(styledResult.value);
  console.log("\n─── END STYLED HTML ────────────────────────────────\n");
}

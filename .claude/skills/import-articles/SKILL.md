---
name: import-articles
description: Import article content (docx, dropcap, PDF) into scaffolded WordPress drafts for a Sapir Journal issue
user_invocable: true
arguments:
  - name: context
    description: "Optional: issue ID, folder path, or describe what you have"
---

# Import Articles — Populate Drafts with Content

This skill populates empty WordPress draft articles (created by `/create-issue`) with formatted content, dropcap images, and PDFs from the print team's article folders.

**Prerequisite:** Draft posts must already exist via `/create-issue`. This script matches folders to those drafts.

## Expected Folder Structure

The print team delivers a folder per article:

```
SAPIR Vol XX/
  01_editors_note_1/
    editors_note.docx
    editors_note.pdf
    editors_note.jpg
  02_foer_foster_1/
    foer_foster.docx
    foer_foster.pdf
    foer_foster.jpg
  ...
```

**Naming convention:** `{sequence}_{author_lastnames}_{suffix}`
- Sequence number for ordering (01, 02, ...)
- Author last names joined by underscores, lowercased
- Suffix (always `1`) — ignored by parser
- Each folder must contain exactly one `.docx`, one `.pdf`, one `.jpg`

**Document structure** (inside .docx):
```
AUTHOR NAME(S) IN ALL CAPS     ← skipped
HED: Article Headline           ← for matching + verification
SUBHED: Subtitle text           ← becomes ACF dek field
[X DROP CAP] rest of word...   ← dropcap letter extracted
Body paragraphs...
[[section separator]]           ← becomes wp:separator block
[[block q]]                     ← opens/closes a wp:pullquote.basic block
More paragraphs...
```

**WARNING: Marker notation is NOT standardized.** The designer varies the exact wording each quarter (e.g., `[[section separator]]` vs `[[section break]]` vs `[[separator]]`, or `[[block q]]` vs `[[block quote]]`). The parser uses loose regexes to handle known variations, but new ones may appear. **Always run the diagnostic first.**

## Workflow

### Step 1: Inspect a .docx (REQUIRED each quarter)

The designer changes marker notation between issues. Always inspect a sample article before running the import to verify the parser will handle the current format:

```bash
bun run scripts/inspect-docx.ts '/path/to/article.docx'
```

Review the HTML and raw text output. Confirm the HED/SUBHED/DROP CAP/separator patterns match what `docx-parser.ts` expects.

### Step 2: Dry Run

```bash
bun run scripts/import-articles.ts '/path/to/SAPIR Vol XX' \
  --issue-id=<ISSUE_ID> \
  --dry-run \
  --skip-pullquotes
```

Verify in the output:
- [ ] All folders matched to the correct WP posts (check author names + titles)
- [ ] HED lines match the WP post titles
- [ ] DEK lines look correct
- [ ] Paragraph counts are reasonable (15-50 per article)
- [ ] No "No WP match for folder" warnings

**Common issues:**
- Hyphenated author names: `louisklein` folder → `Louis-Klein` in WP (handled by normalizer)
- Same author with multiple articles: HED/title cross-referencing disambiguates (e.g., Bret Stephens)
- `editors_note` folder: Special-case matching by title (no author match possible)

### Step 3: Import

```bash
bun run scripts/import-articles.ts '/path/to/SAPIR Vol XX' \
  --issue-id=<ISSUE_ID> \
  --skip-pullquotes
```

Per article, the script:
1. Parses .docx → extracts dek, body paragraphs, section separators, sign-offs
2. Converts to Gutenberg blocks (wp:paragraph, wp:separator, last-p class, right-aligned sign-offs)
3. Optimizes dropcap image (400x400 JPEG, quality 80 via sharp)
4. Uploads dropcap + PDF to WP media library via `ddev wp media import`
5. Sets ACF fields: dek, dropcap attachment, PDF attachment
6. Updates post content with Gutenberg markup

### Step 4: Verify

Check a few articles on the local site (https://sapirjournal.dev):
- [ ] Post content renders with correct Gutenberg blocks
- [ ] Section separators show the themed SVG ornament
- [ ] Last body paragraph has the dingbat ornament (`.last-p` class)
- [ ] Sign-off paragraphs (dates, author names) are right-aligned
- [ ] Dropcap image displays as first-letter decoration
- [ ] PDF download link works in article header
- [ ] Dek shows below the headline

### Step 5: Re-run if Needed

The import is **idempotent** — articles with existing content, dropcap, or PDF are skipped. To re-import a specific article, clear its content first:

```bash
# Clear post content
ddev wp post update <POST_ID> --post_content=""

# Clear dropcap (set to 0 to mark as empty)
ddev wp post meta update <POST_ID> dropcap 0
ddev wp post meta update <POST_ID> _dropcap field_605cd84833e59

# Clear PDF
ddev wp post meta update <POST_ID> pdf 0
ddev wp post meta update <POST_ID> _pdf field_60661569538a8

# Then re-run the import
```

## CLI Flags

| Flag | Required | Description |
|------|----------|-------------|
| `--issue-id` | Yes | WordPress issue CPT post ID |
| `--dry-run` | No | Preview matching and parsing without changes |
| `--skip-pullquotes` | No | Skip PDF-based pullquote extraction |

## Pipeline Files

```
scripts/
  import-articles.ts          # CLI entry point
  inspect-docx.ts             # Diagnostic: dump mammoth output for one .docx
  lib/
    types.ts                  # TypeScript interfaces + ACF field keys
    docx-parser.ts            # mammoth.js → HED, dek, paragraphs, separators
    gutenberg.ts              # Paragraphs → Gutenberg block markup
    pullquote-extractor.ts    # PDF cross-reference (experimental)
    image-optimizer.ts        # sharp: resize dropcap to 400x400 JPEG
    wp-bridge.ts              # Shell wrappers for ddev wp commands
    folder-matcher.ts         # Match folder author names → WP draft posts
```

## ACF Field Keys (for debugging)

| Field | Key | Notes |
|-------|-----|-------|
| `dek` | `field_63bf69d90e5ad` | Article subtitle |
| `dropcap` | `field_605cd84833e59` | Dropcap image attachment ID |
| `pdf` | `field_60661569538a8` | PDF attachment ID |
| `pullquote` | `field_637509f1624f4` | Pull quote text |
| `display_title` | `field_606e256e8e135` | Clean title for front-end |

## Troubleshooting

**"No WP match for folder"** — The folder's author last names don't match any draft post's author CPT `last_name` field. Check:
- Is the author spelled differently in WP vs the folder name?
- Does the draft post have the correct issue assigned?
- Is the draft actually a draft (not published/trashed)?

**Content looks wrong** — Run `inspect-docx.ts` on the problem .docx to see what mammoth produces. Check for unexpected formatting (nested lists, tables, footnotes).

**Dropcap not showing** — Verify the attachment ID is set in both `dropcap` and `_dropcap` meta. The front-end CSS expects a specific image structure.

**"files outside DDEV project root"** — The script auto-stages external files (e.g., from ~/Downloads/) into a temp directory inside the project root before importing. If this fails, manually copy the folder into the project root.

---
name: create-issue
description: Guide the quarterly workflow for bulk-creating articles for a new Sapir Journal issue
user_invocable: true
arguments:
  - name: context
    description: "Optional: paste article list, CSV data, or describe what you have"
---

# Create Issue — Quarterly Article Setup

This skill guides you through creating all draft articles for a new Sapir Journal issue so the print team can get URLs for QR codes.

## Workflow

### Step 1: Prepare the CSV

The WP-CLI command expects a CSV with these columns:

```
Title,Authors,Category,Issue,Interviewers
```

**Rules:**
- Multiple authors/interviewers are comma-separated within quotes: `"Joshua Foer, William Foster"`
- Author names must be full names (first + last), split on last space
- Watch for shorthand like "Adiri and Lotan" — expand to full names: `"Yonatan Adiri, Shachar Lotan"`
- The Issue and Category columns are typically the same (the issue name, e.g., "Aspiration II")
- Interviewers column can be empty
- For interview pieces, interviewees go in Authors and the interviewer in Interviewers
- Never include a "Letters" row — Letters is a static URL backed by its own CPT index, not an issue-specific article (Andy, 2026-08-17). Front matter varies per issue (some have a Publisher's Note, some don't); only scaffold what the TOC lists
- Season values follow the site convention Winter/Spring/Summer/**Autumn** — never "Fall". Volume names are spelled out ("Volume Twenty-Two")
- Verify author spellings against real-world usage before the run — the CSV becomes the permanent record (e.g., 2026: TOC had "Jenna Weissman-Joselit"; she publishes unhyphenated, and the hyphen controls whether she files under "Joselit" or "Weissman-Joselit")
- Recurring titles (Editor's Note, Publisher's Note) are handled automatically — the CLI appends the issue name and sets `display_title` for the clean front-end version

If the user pastes a list, email, or spreadsheet data, help them transform it into this CSV format. Save it to the project root so DDEV can access it:

```bash
# Save/copy the CSV into the project root (DDEV can't see ~/Downloads/)
cp ~/Downloads/articles.csv /Users/andrewlovseth/Dev/sapir-journal/articles.csv
```

### Step 2: Validate

Review the CSV for:
- [ ] All rows have a Title
- [ ] Author names are full names (not last-name-only or abbreviated)
- [ ] No "and" in author fields (should be comma-separated)
- [ ] Category and Issue columns are consistent
- [ ] No obvious duplicates

### Step 3: Dry Run

The CSV must be inside the project root so DDEV can access it. Use the container path `/var/www/html/` prefix:

```bash
ddev wp sapir create-issue /var/www/html/articles.csv \
  --season="<Season Year>" \
  --volume="<Volume Name>" \
  --dry-run
```

Review the output table. Confirm:
- Correct number of articles
- URLs look right (pattern: `/{category-slug}/{year}/{post-slug}/`)
- No unexpected skips — recurring titles (Editor's Note, etc.) should show "would create (renamed)"
- Author names parsed correctly

Dry-run caveat: `find_or_create_issue` returns `0` in dry run, so a slug-colliding post whose `issue` meta is empty misreports as "skipped" instead of "renamed". If a skip looks wrong, check that post's `issue` meta directly.

### Step 4: Execute

```bash
ddev wp sapir create-issue /var/www/html/articles.csv \
  --season="<Season Year>" \
  --volume="<Volume Name>"
```

**Optional flag:** `--new-author-status=draft` keeps newly created author CPT stubs as drafts (useful when the issue won't release for weeks). Default is `publish`.

### Step 5: Verify

Run the command again — all articles should say "skipped" (idempotency check).

Then verify in WP admin:
- Category exists
- Issue CPT has correct season and volume
- All articles are drafts with correct ACF fields (issue, authors, interviewers)

### Step 6: Deliver URLs to Print Team

```bash
ddev wp sapir create-issue /path/to/articles.csv \
  --season="<Season Year>" \
  --volume="<Volume Name>" \
  --format=csv
```

This outputs a clean `Title,URL,Status` CSV. The URLs follow the site's permalink structure: `/{category-slug}/{year}/{post-slug}/`

Production URLs use the base: `https://sapirjournal.org`

#### Fill the shared Google Sheet

The print team tracks URLs in the "SAPIR Journal URLs" sheet (one tab per volume, columns Author / Headline / URL):
`https://docs.google.com/spreadsheets/d/1_6g_1LTm-6t0AJObv0rvPCacZsf2r6yyMasZdlO3ths/`

Conventions (match prior tabs):
- Multiple authors joined with "and"; interviews credit the interviewees only (interviewer stays in WP)
- Headlines plain, no surrounding quotes added; URLs are full production links
- Fill via clipboard, not keystrokes: build a TSV, `pbcopy` it, click the first cell, ⌘V. The Chrome `type` action inserts tabs as literal characters inside one cell instead of advancing cells
- A headline that begins with an apostrophe needs it doubled (`''People…`) — Sheets consumes a leading `'` as a force-text marker on paste

### Step 7: Deploy to Production

The theme is deployed to WP Engine via WP Pusher (auto-deploys from GitHub). The CLI command runs on production to create articles there too.

#### 7a. Push theme to GitHub (only if the CLI changed)

Skip this step when there are no theme/CLI changes — the command already lives on production from the prior issue.

```bash
cd /Users/andrewlovseth/Dev/sapir-journal/wp/wp-content/themes/sapir-v2
git push origin master
```

WP Pusher will automatically deploy the updated theme (including any CLI fixes) to production on WP Engine.

#### 7b. Upload the CSV to WP Engine

WP Engine's SFTP subsystem is disabled, so OpenSSH 9+ scp fails with `subsystem request failed`. Use the `-O` flag to force legacy SCP protocol:

```bash
scp -O articles.csv sapirjournal@sapirjournal.ssh.wpengine.net:sites/sapirjournal/articles.csv
```

#### 7c. SSH into WP Engine and dry-run

WP Engine's SSH wrapper strips inner double quotes from one-line commands, so `--season="Spring 2026"` is parsed as `--season=Spring` plus a stray `2026`. Pipe the command through `bash -s` via heredoc to preserve quoting:

```bash
ssh sapirjournal@sapirjournal.ssh.wpengine.net 'bash -s' <<'EOF'
wp sapir create-issue /sites/sapirjournal/articles.csv \
  --season="<Season Year>" \
  --volume="<Volume Name>" \
  --new-author-status=draft \
  --dry-run
EOF
```

Review the output, same checks as Step 3.

#### 7d. Execute on production

```bash
ssh sapirjournal@sapirjournal.ssh.wpengine.net 'bash -s' <<'EOF'
wp sapir create-issue /sites/sapirjournal/articles.csv \
  --season="<Season Year>" \
  --volume="<Volume Name>" \
  --new-author-status=draft
EOF
```

#### 7e. Verify and clean up

```bash
ssh sapirjournal@sapirjournal.ssh.wpengine.net 'bash -s' <<'EOF'
wp sapir create-issue /sites/sapirjournal/articles.csv \
  --season="<Season Year>" \
  --volume="<Volume Name>" \
  --new-author-status=draft \
  --dry-run 2>&1 | tail -5
rm /sites/sapirjournal/articles.csv && echo "CSV removed from server"
EOF
```

Spot-check a few articles in the production WP admin at `https://sapirjournal.org/wp-admin/`.

## Reference

### CSV Format

| Column | Required | Notes |
|--------|----------|-------|
| Title | Yes | Article title |
| Authors | Yes | Full names, comma-separated if multiple |
| Category | Yes | Usually matches the issue name |
| Issue | Yes | Issue name (e.g., "Aspiration II") |
| Interviewers | No | For interview/conversation pieces |

### CLI Flags

| Flag | Required | Description |
|------|----------|-------------|
| `--season` | Yes | e.g., "Winter 2026" |
| `--volume` | Yes | e.g., "Volume Twenty" |
| `--dry-run` | No | Preview without creating anything |
| `--format` | No | table (default), csv, or json |

### ACF Field Keys (for debugging)

- Article `issue`: `field_605cd86033e5b`
- Article `author`: `field_605cd85233e5a`
- Article `interviewers`: `field_64078dff145fe`
- Article `display_title`: `field_606e256e8e135` (clean title when post title has issue name appended)
- Author `first_name`: `field_63c5b45f618a5`
- Author `last_name`: `field_63c5b4340ff52`
- Issue `season`: `field_6066107c07bda`
- Issue `volume`: `field_6066108207bdb`

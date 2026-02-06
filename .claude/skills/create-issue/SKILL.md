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

### Step 4: Execute

```bash
ddev wp sapir create-issue /var/www/html/articles.csv \
  --season="<Season Year>" \
  --volume="<Volume Name>"
```

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

### Step 7: Deploy to Production

The theme is deployed to WP Engine via WP Pusher (auto-deploys from GitHub). The CLI command runs on production to create articles there too.

#### 7a. Push theme to GitHub

```bash
cd /Users/andrewlovseth/Dev/sapir-journal/wp/wp-content/themes/sapir-v2
git push origin master
```

WP Pusher will automatically deploy the updated theme (including any CLI fixes) to production on WP Engine.

#### 7b. Upload the CSV to WP Engine

```bash
scp articles.csv sapirjournal@sapirjournal.ssh.wpengine.net:sites/sapirjournal/
```

#### 7c. SSH into WP Engine and dry-run

```bash
ssh sapirjournal@sapirjournal.ssh.wpengine.net

# On the server — no ddev prefix, wp is available directly
wp sapir create-issue /sites/sapirjournal/articles.csv \
  --season="<Season Year>" \
  --volume="<Volume Name>" \
  --dry-run
```

Review the output, same checks as Step 3.

#### 7d. Execute on production

```bash
wp sapir create-issue /sites/sapirjournal/articles.csv \
  --season="<Season Year>" \
  --volume="<Volume Name>"
```

#### 7e. Verify and clean up

```bash
# Idempotency check — all articles should say "skipped"
wp sapir create-issue /sites/sapirjournal/articles.csv \
  --season="<Season Year>" \
  --volume="<Volume Name>" \
  --dry-run

# Remove the CSV from the server
rm /sites/sapirjournal/articles.csv
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

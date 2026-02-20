/**
 * Push article content from local DDEV to production via WP REST API.
 *
 * Transfers post_content, dek, dropcap images, and PDFs for all articles
 * linked to a given issue. Matches local → prod posts by slug.
 *
 * Prerequisites:
 *   1. Create Application Password: WP Admin → Users → Profile → Application Passwords
 *   2. Add to .env: PROD_WP_USER=<username>  PROD_WP_APP_PASSWORD=<password>
 *
 * Usage:
 *   bun run scripts/push-to-prod.ts --issue-id=4405 --dry-run
 *   bun run scripts/push-to-prod.ts --issue-id=4405
 *   bun run scripts/push-to-prod.ts --issue-id=4405 --create-missing
 */

import { parseArgs } from "util";
import { join } from "path";
import { ACF_FIELDS } from "./lib/types";

// --- Constants ---

const DDEV_PROJECT_ROOT = "/Users/andrewlovseth/Dev/sapir-journal";
const LOCAL_UPLOADS = join(DDEV_PROJECT_ROOT, "wp/wp-content/uploads");
const PROD_BASE_URL = "https://sapirjournal.org/wp-json/wp/v2";

// Local slugs that differ from prod (local → prod)
const SLUG_OVERRIDES = new Map<string, string>([]);

// --- Types ---

interface LocalArticle {
  localId: number;
  title: string;
  slug: string;
  dek: string;
  dropcapAttId: number;
  dropcapFilePath: string;
  pdfAttId: number;
  pdfFilePath: string;
  localAuthorIds: number[];
  categorySlug: string;
}

interface ProdArticle {
  prodId: number;
  slug: string;
  title: string;
}

interface ProdState {
  hasContent: boolean;
  hasDek: boolean;
  hasDropcap: boolean;
  hasPdf: boolean;
}

interface PushResult {
  slug: string;
  title: string;
  localId: number;
  prodId: number;
  success: boolean;
  skipped: boolean;
  created: boolean;
  actions: string[];
  error?: string;
}

// WP REST API response shapes
interface WpPost {
  id: number;
  title: { rendered: string };
  slug: string;
  content: { rendered: string };
  acf?: Record<string, unknown>;
}

interface WpTerm {
  id: number;
  slug: string;
}

// --- REST API Client ---

class ProdApi {
  private base = PROD_BASE_URL;
  private authHeader: string;

  constructor() {
    const user = process.env.PROD_WP_USER;
    const pass = process.env.PROD_WP_APP_PASSWORD;
    if (!user || !pass) {
      throw new Error(
        "Missing PROD_WP_USER or PROD_WP_APP_PASSWORD in .env\n" +
          "Create an Application Password at WP Admin → Users → Profile → Application Passwords"
      );
    }
    this.authHeader = "Basic " + btoa(`${user}:${pass}`);
  }

  async get<T>(endpoint: string, params?: Record<string, string>): Promise<T> {
    const url = new URL(`${this.base}${endpoint}`);
    if (params) {
      for (const [k, v] of Object.entries(params)) {
        url.searchParams.set(k, v);
      }
    }
    const res = await fetch(url.toString(), {
      headers: { Authorization: this.authHeader },
    });
    if (!res.ok) {
      const body = await res.text();
      throw new Error(
        `GET ${endpoint} failed (${res.status}): ${body.slice(0, 300)}`
      );
    }
    return res.json() as Promise<T>;
  }

  async post<T>(endpoint: string, body: unknown): Promise<T> {
    const res = await fetch(`${this.base}${endpoint}`, {
      method: "POST",
      headers: {
        Authorization: this.authHeader,
        "Content-Type": "application/json",
      },
      body: JSON.stringify(body),
    });
    if (!res.ok) {
      const text = await res.text();
      throw new Error(
        `POST ${endpoint} failed (${res.status}): ${text.slice(0, 300)}`
      );
    }
    return res.json() as Promise<T>;
  }

  async uploadMedia(
    filePath: string,
    filename: string,
    postId?: number
  ): Promise<number> {
    const file = Bun.file(filePath);
    const data = await file.arrayBuffer();
    const url = new URL(`${this.base}/media`);
    if (postId) url.searchParams.set("post", String(postId));

    const res = await fetch(url.toString(), {
      method: "POST",
      headers: {
        Authorization: this.authHeader,
        "Content-Disposition": `attachment; filename="${filename}"`,
        "Content-Type": file.type || "application/octet-stream",
      },
      body: data,
    });
    if (!res.ok) {
      const text = await res.text();
      throw new Error(
        `Upload ${filename} failed (${res.status}): ${text.slice(0, 300)}`
      );
    }
    const media = (await res.json()) as { id: number };
    return media.id;
  }

  /** Verify API connectivity, auth, and ACF REST support */
  async checkConnection(): Promise<{ acfRestEnabled: boolean }> {
    const me = await this.get<{ id: number; name: string }>("/users/me");
    console.log(`  Authenticated as: ${me.name} (ID ${me.id})`);

    // Check if ACF REST is enabled by inspecting a draft post's acf field
    const testPosts = await this.get<WpPost[]>("/posts", {
      status: "draft",
      per_page: "1",
    });
    const acfRestEnabled =
      testPosts.length > 0 &&
      typeof testPosts[0].acf === "object" &&
      !Array.isArray(testPosts[0].acf);

    if (!acfRestEnabled) {
      console.log(
        `  WARNING: ACF REST API is not enabled. ACF fields (dek, dropcap, pdf) cannot be pushed.`
      );
      console.log(
        `  To fix: WP Admin → ACF → Field Groups → each group → Settings → Show in REST API → Yes`
      );
    } else {
      console.log(`  ACF REST API: enabled`);
    }

    return { acfRestEnabled };
  }
}

// --- Local helpers ---

async function ddevWp(args: string): Promise<string> {
  const proc = Bun.spawn(
    ["bash", "-c", `cd ${DDEV_PROJECT_ROOT} && ddev wp ${args}`],
    { stdout: "pipe", stderr: "pipe" }
  );
  const stdout = await new Response(proc.stdout).text();
  const stderr = await new Response(proc.stderr).text();
  const exitCode = await proc.exited;
  if (exitCode !== 0) {
    throw new Error(`ddev wp ${args} failed (exit ${exitCode}): ${stderr}`);
  }
  return stdout.trim();
}

async function ddevEvalPhp(
  php: string,
  args: string[] = []
): Promise<string> {
  const containerPath = `/tmp/_eval_${Date.now()}.php`;
  const b64 = Buffer.from(php).toString("base64");
  const argsStr = args.length > 0 ? " " + args.join(" ") : "";
  const proc = Bun.spawn(
    [
      "bash",
      "-c",
      `cd ${DDEV_PROJECT_ROOT} && ddev exec "echo ${b64} | base64 -d > ${containerPath}" && ddev wp eval-file ${containerPath}${argsStr}`,
    ],
    { stdout: "pipe", stderr: "pipe" }
  );
  const stdout = await new Response(proc.stdout).text();
  const stderr = await new Response(proc.stderr).text();
  const exitCode = await proc.exited;
  if (exitCode !== 0) {
    throw new Error(
      `Local eval-file failed (exit ${exitCode}): ${stderr.trim()}`
    );
  }
  return stdout.trim();
}

// --- Phase 1: Local data collection ---

async function getLocalArticles(issueId: number): Promise<LocalArticle[]> {
  const php = `<?php
$issue_id = intval($args[0]);
$posts = get_posts([
  'post_type'   => 'post',
  'post_status' => 'draft',
  'numberposts' => -1,
  'meta_key'    => 'issue',
  'meta_value'  => $issue_id,
]);
$result = [];
foreach ($posts as $p) {
  $dek = (string) get_post_meta($p->ID, 'dek', true);
  $dropcap_id = intval(get_post_meta($p->ID, 'dropcap', true));
  $dropcap_path = '';
  if ($dropcap_id > 0) {
    $dropcap_path = (string) get_post_meta($dropcap_id, '_wp_attached_file', true);
  }
  $pdf_id = intval(get_post_meta($p->ID, 'pdf', true));
  $pdf_path = '';
  if ($pdf_id > 0) {
    $pdf_path = (string) get_post_meta($pdf_id, '_wp_attached_file', true);
  }
  $author_raw = get_post_meta($p->ID, 'author', true);
  $author_ids = is_array($author_raw) ? array_map('intval', $author_raw) : [];
  $cats = wp_get_post_categories($p->ID, ['fields' => 'slugs']);
  $result[] = [
    'ID'           => $p->ID,
    'title'        => $p->post_title,
    'slug'         => $p->post_name,
    'dek'          => $dek,
    'dropcap_id'   => $dropcap_id,
    'dropcap_path' => $dropcap_path,
    'pdf_id'       => $pdf_id,
    'pdf_path'     => $pdf_path,
    'author_ids'   => $author_ids,
    'category'     => !empty($cats) ? $cats[0] : '',
  ];
}
echo json_encode($result);
`;

  const json = await ddevEvalPhp(php, [String(issueId)]);

  const rows: {
    ID: number;
    title: string;
    slug: string;
    dek: string;
    dropcap_id: number;
    dropcap_path: string;
    pdf_id: number;
    pdf_path: string;
    author_ids: number[];
    category: string;
  }[] = JSON.parse(json);

  return rows.map((r) => ({
    localId: r.ID,
    title: r.title,
    slug: r.slug,
    dek: r.dek,
    dropcapAttId: r.dropcap_id,
    dropcapFilePath: r.dropcap_path ? join(LOCAL_UPLOADS, r.dropcap_path) : "",
    pdfAttId: r.pdf_id,
    pdfFilePath: r.pdf_path ? join(LOCAL_UPLOADS, r.pdf_path) : "",
    localAuthorIds: r.author_ids,
    categorySlug: r.category,
  }));
}

async function getLocalPostContent(localId: number): Promise<string> {
  return ddevEvalPhp(
    `<?php echo get_post_field('post_content', intval($args[0]));`,
    [String(localId)]
  );
}

// --- Phase 2: Prod discovery via REST API ---

async function getProdIssueId(
  localIssueId: number,
  api: ProdApi
): Promise<number> {
  const issueSlug = await ddevWp(
    `post get ${localIssueId} --field=post_name`
  );
  console.log(`  Local issue slug: ${issueSlug}`);

  const issues = await api.get<WpPost[]>("/issue", {
    slug: issueSlug,
    status: "any",
    per_page: "1",
  });

  if (issues.length === 0) {
    throw new Error(
      `Could not find issue with slug "${issueSlug}" on production.\n` +
        `  Check that the 'issue' CPT has show_in_rest enabled and rest_base is "issue".`
    );
  }
  return issues[0].id;
}

async function getProdDrafts(
  prodIssueId: number,
  api: ProdApi
): Promise<ProdArticle[]> {
  // Fetch all draft posts — slug matching in Step 3 pairs them with local articles.
  // ACF REST may not be enabled, so we don't filter by acf.issue here.
  const posts = await api.get<WpPost[]>("/posts", {
    status: "draft",
    per_page: "100",
  });

  // If ACF REST is active, narrow to this issue; otherwise return all drafts
  const acfAvailable = posts.length > 0 && typeof posts[0].acf === "object" && !Array.isArray(posts[0].acf);

  if (acfAvailable) {
    const filtered = posts.filter((p) => {
      const issueVal = p.acf?.issue;
      if (typeof issueVal === "number") return issueVal === prodIssueId;
      if (issueVal && typeof issueVal === "object" && "ID" in issueVal)
        return (issueVal as { ID: number }).ID === prodIssueId;
      return false;
    });
    if (filtered.length > 0) {
      return filtered.map((p) => ({
        prodId: p.id,
        slug: p.slug,
        title: p.title.rendered,
      }));
    }
  }

  // Fallback: return all drafts, let slug matching sort it out
  console.log(
    `  Note: ACF REST not available or no issue match — returning all ${posts.length} drafts for slug matching`
  );
  return posts.map((p) => ({
    prodId: p.id,
    slug: p.slug,
    title: p.title.rendered,
  }));
}

async function batchGetProdState(
  prodIds: number[],
  api: ProdApi
): Promise<Map<number, ProdState>> {
  // Parallel fetch — fine for ~15 posts
  const results = await Promise.all(
    prodIds.map((id) => api.get<WpPost>(`/posts/${id}`))
  );

  const map = new Map<number, ProdState>();
  for (const post of results) {
    map.set(post.id, {
      hasContent: (post.content?.rendered?.length ?? 0) > 50,
      hasDek: String(post.acf?.dek ?? "") !== "",
      hasDropcap: Number(post.acf?.dropcap ?? 0) > 0,
      hasPdf: Number(post.acf?.pdf ?? 0) > 0,
    });
  }
  return map;
}

// --- Phase 3: Create missing posts ---

async function createProdPost(
  article: LocalArticle,
  prodIssueId: number,
  api: ProdApi
): Promise<number> {
  console.log(`    Creating draft post on prod...`);

  // Look up category ID by slug
  let categoryIds: number[] = [];
  if (article.categorySlug) {
    const cats = await api.get<WpTerm[]>("/categories", {
      slug: article.categorySlug,
    });
    if (cats.length > 0) categoryIds = [cats[0].id];
  }

  // Get display_title from local
  let displayTitle = "";
  try {
    displayTitle = await ddevWp(
      `post meta get ${article.localId} display_title`
    );
  } catch {}

  // Match authors: local IDs → slugs → prod IDs
  let prodAuthorIds: number[] = [];
  if (article.localAuthorIds.length > 0) {
    const authorSlugsJson = await ddevEvalPhp(
      `<?php
$result = [];
foreach ($args as $id) {
    $post = get_post(intval($id));
    if ($post) $result[] = $post->post_name;
}
echo json_encode($result);`,
      article.localAuthorIds.map(String)
    );

    let localSlugs: string[] = [];
    try {
      localSlugs = JSON.parse(authorSlugsJson);
    } catch {}

    for (const slug of localSlugs) {
      const authors = await api.get<WpPost[]>("/authors", {
        slug,
        per_page: "1",
      });
      if (authors.length > 0) {
        prodAuthorIds.push(authors[0].id);
      } else {
        console.warn(`    Warning: No prod author with slug "${slug}"`);
      }
    }
  }

  const acfFields: Record<string, unknown> = {
    issue: prodIssueId,
  };
  if (displayTitle) acfFields.display_title = displayTitle;
  if (prodAuthorIds.length > 0) acfFields.author = prodAuthorIds;

  const post = await api.post<WpPost>("/posts", {
    title: article.title,
    slug: article.slug,
    status: "draft",
    categories: categoryIds,
    acf: acfFields,
  });

  return post.id;
}

// --- Phase 4: Push content and media via REST API ---

async function pushContent(
  localId: number,
  prodId: number,
  api: ProdApi
): Promise<void> {
  const content = await getLocalPostContent(localId);
  await api.post(`/posts/${prodId}`, { content });
}

async function pushDek(
  prodId: number,
  dek: string,
  api: ProdApi
): Promise<void> {
  await api.post(`/posts/${prodId}`, { acf: { dek } });
}

async function pushDropcap(
  prodId: number,
  localFilePath: string,
  api: ProdApi
): Promise<void> {
  const filename = localFilePath.split("/").pop()!;
  const attId = await api.uploadMedia(localFilePath, filename, prodId);
  await api.post(`/posts/${prodId}`, { acf: { dropcap: attId } });
  console.log(`      Dropcap -> attachment #${attId}`);
}

async function pushPdf(
  prodId: number,
  localFilePath: string,
  slug: string,
  api: ProdApi
): Promise<void> {
  const filename = `${slug}.pdf`;
  const attId = await api.uploadMedia(localFilePath, filename, prodId);
  await api.post(`/posts/${prodId}`, { acf: { pdf: attId } });
  console.log(`      PDF -> attachment #${attId}`);
}

// --- Main ---

const { values } = parseArgs({
  args: Bun.argv.slice(2),
  options: {
    "issue-id": { type: "string" },
    "dry-run": { type: "boolean", default: false },
    "create-missing": { type: "boolean", default: false },
  },
  allowPositionals: false,
});

const issueId = parseInt(values["issue-id"] ?? "", 10);
const dryRun = values["dry-run"] ?? false;
const createMissing = values["create-missing"] ?? false;

if (isNaN(issueId)) {
  console.error(
    "Usage: bun run scripts/push-to-prod.ts --issue-id=<local_issue_id> [--dry-run] [--create-missing]"
  );
  process.exit(1);
}

console.log(`\n=== Sapir Push to Production (REST API) ===`);
console.log(`  Issue ID (local): ${issueId}`);
console.log(`  Dry run: ${dryRun}`);
console.log(`  Create missing: ${createMissing}`);

// Initialize REST API client
const api = new ProdApi();

// Verify connectivity
console.log("\n--- Checking API connection ---");
try {
  await api.checkConnection();
} catch (err) {
  console.error(
    `  Failed to connect to production REST API: ${err instanceof Error ? err.message : err}`
  );
  console.error(
    "  Verify PROD_WP_USER and PROD_WP_APP_PASSWORD in .env are correct."
  );
  process.exit(1);
}

// --- Step 1: Collect local articles ---
console.log("\n--- Collecting local articles ---");
const localArticles = await getLocalArticles(issueId);
console.log(`  Found ${localArticles.length} local drafts`);

for (const a of localArticles) {
  const flags = [];
  if (a.dek) flags.push("dek");
  if (a.dropcapAttId) flags.push("dropcap");
  if (a.pdfAttId) flags.push("pdf");
  console.log(`    #${a.localId} ${a.slug} [${flags.join(", ")}]`);
}

// --- Step 2: Discover prod articles ---
console.log("\n--- Discovering production articles ---");
const prodIssueId = await getProdIssueId(issueId, api);
console.log(`  Prod issue ID: ${prodIssueId}`);

const prodDrafts = await getProdDrafts(prodIssueId, api);
console.log(`  Found ${prodDrafts.length} prod drafts`);

const prodBySlug = new Map<string, ProdArticle>();
for (const p of prodDrafts) {
  prodBySlug.set(p.slug, p);
}

// --- Step 3: Match local → prod by slug ---
console.log("\n--- Matching local -> prod ---");
const matched: { local: LocalArticle; prodId: number; created: boolean }[] = [];
const unmatched: LocalArticle[] = [];

for (const local of localArticles) {
  const prodSlug = SLUG_OVERRIDES.get(local.slug) ?? local.slug;
  const prod = prodBySlug.get(prodSlug);
  if (prod) {
    const override =
      prodSlug !== local.slug ? ` (via override: ${prodSlug})` : "";
    console.log(
      `  + ${local.slug}: local #${local.localId} -> prod #${prod.prodId}${override}`
    );
    matched.push({ local, prodId: prod.prodId, created: false });
  } else {
    console.log(`  - ${local.slug}: no prod match`);
    unmatched.push(local);
  }
}

// Handle unmatched articles
if (unmatched.length > 0) {
  if (createMissing) {
    console.log(`\n--- Creating ${unmatched.length} missing article(s) ---`);
    if (!dryRun) {
      for (const local of unmatched) {
        try {
          const newProdId = await createProdPost(local, prodIssueId, api);
          console.log(`  Created: ${local.slug} -> prod #${newProdId}`);
          matched.push({ local, prodId: newProdId, created: true });
        } catch (err) {
          console.error(
            `  Failed to create ${local.slug}: ${err instanceof Error ? err.message : err}`
          );
        }
      }
    } else {
      for (const local of unmatched) {
        console.log(`  Would create: ${local.slug}`);
      }
    }
  } else {
    console.log(
      `\n  ${unmatched.length} article(s) have no prod match. Use --create-missing to auto-create.`
    );
    for (const u of unmatched) {
      console.log(`    - ${u.slug} (local #${u.localId})`);
    }
  }
}

// --- Batch check prod state ---
const matchedProdIds = matched.map((m) => m.prodId);
const stateMap =
  matchedProdIds.length > 0
    ? await batchGetProdState(matchedProdIds, api)
    : new Map<number, ProdState>();

if (dryRun) {
  console.log("\n--- Dry run: what would be pushed ---");
  for (const { local, prodId } of matched) {
    const state = stateMap.get(prodId);
    if (!state) {
      console.log(`  ${local.slug} (#${prodId}): could not check state`);
      continue;
    }
    const actions = [];
    if (!state.hasContent && (local.dek || local.dropcapAttId || local.pdfAttId))
      actions.push("push content");
    if (!state.hasDek && local.dek) actions.push("push dek");
    if (!state.hasDropcap && local.dropcapFilePath) actions.push("push dropcap");
    if (!state.hasPdf && local.pdfFilePath) actions.push("push pdf");
    if (actions.length === 0) actions.push("nothing to do");
    console.log(`  ${local.slug} (#${prodId}): ${actions.join(", ")}`);
  }

  console.log("\n--- Dry run complete. No changes made. ---\n");
  process.exit(0);
}

// --- Step 4: Push content ---
console.log("\n--- Pushing content to production ---");

const results: PushResult[] = [];

for (const { local, prodId, created } of matched) {
  const result: PushResult = {
    slug: local.slug,
    title: local.title,
    localId: local.localId,
    prodId,
    success: false,
    skipped: false,
    created,
    actions: [],
  };

  try {
    console.log(
      `\n  ${local.slug} (local #${local.localId} -> prod #${prodId})`
    );

    const state = stateMap.get(prodId) ?? {
      hasContent: false,
      hasDek: false,
      hasDropcap: false,
      hasPdf: false,
    };

    // Skip articles with no local content (empty scaffolds)
    const hasLocalContent =
      local.dek || local.dropcapAttId || local.pdfAttId;

    // --- Content ---
    if (state.hasContent) {
      console.log(`    Content: already set, skipping`);
    } else if (!hasLocalContent) {
      console.log(`    Content: empty locally, skipping`);
    } else {
      await pushContent(local.localId, prodId, api);
      result.actions.push("pushed content");
      console.log(`    Content: pushed`);
    }

    // --- Dek ---
    if (state.hasDek) {
      console.log(`    Dek: already set, skipping`);
    } else if (local.dek) {
      await pushDek(prodId, local.dek, api);
      result.actions.push("pushed dek");
      console.log(`    Dek: pushed`);
    } else {
      console.log(`    Dek: none locally, skipping`);
    }

    // --- Dropcap ---
    if (state.hasDropcap) {
      console.log(`    Dropcap: already set, skipping`);
    } else if (local.dropcapFilePath) {
      const dropcapFile = Bun.file(local.dropcapFilePath);
      if (await dropcapFile.exists()) {
        await pushDropcap(prodId, local.dropcapFilePath, api);
        result.actions.push("pushed dropcap");
      } else {
        console.log(
          `    Dropcap: file not found at ${local.dropcapFilePath}`
        );
      }
    } else {
      console.log(`    Dropcap: none locally, skipping`);
    }

    // --- PDF ---
    if (state.hasPdf) {
      console.log(`    PDF: already set, skipping`);
    } else if (local.pdfFilePath) {
      const pdfFile = Bun.file(local.pdfFilePath);
      if (await pdfFile.exists()) {
        await pushPdf(prodId, local.pdfFilePath, local.slug, api);
        result.actions.push("pushed pdf");
      } else {
        console.log(`    PDF: file not found at ${local.pdfFilePath}`);
      }
    } else {
      console.log(`    PDF: none locally, skipping`);
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

// --- Summary ---
console.log("\n=== Push Summary ===");
const succeeded = results.filter((r) => r.success && !r.skipped);
const skipped = results.filter((r) => r.skipped);
const failed = results.filter((r) => !r.success);
const createdCount = results.filter((r) => r.created).length;

console.log(`  Pushed:   ${succeeded.length}`);
console.log(`  Skipped:  ${skipped.length} (already up to date)`);
console.log(`  Created:  ${createdCount}`);
console.log(`  Failed:   ${failed.length}`);

if (succeeded.length > 0) {
  console.log("\n  Pushed articles:");
  for (const r of succeeded) {
    console.log(
      `    #${r.prodId} "${r.title}": ${r.actions.join(", ")}`
    );
  }
}

if (failed.length > 0) {
  console.log("\n  Failed articles:");
  for (const f of failed) {
    console.log(`    #${f.prodId} "${f.title}": ${f.error}`);
  }
}

console.log("");
process.exit(failed.length > 0 ? 1 : 0);

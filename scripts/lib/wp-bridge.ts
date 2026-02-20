/**
 * WordPress bridge — shells out to `ddev wp` for all WP operations.
 *
 * All file paths passed to ddev wp must be relative to the DDEV project root,
 * which maps to /var/www/html/ inside the container.
 */

import { ACF_FIELDS } from "./types";
import { copyFile, mkdir } from "fs/promises";
import { join, basename } from "path";

const DDEV_PROJECT_ROOT = "/Users/andrewlovseth/Dev/sapir-journal";
const CONTAINER_ROOT = "/var/www/html";
const STAGING_DIR = join(DDEV_PROJECT_ROOT, "wp/wp-content/tmp-media-staging");

/** Convert a local absolute path to its container-relative equivalent */
function toContainerPath(localPath: string): string {
  if (localPath.startsWith(DDEV_PROJECT_ROOT)) {
    return localPath.replace(DDEV_PROJECT_ROOT, CONTAINER_ROOT);
  }
  return localPath;
}

/** Run a ddev wp command and return stdout */
async function ddevWp(args: string): Promise<string> {
  const proc = Bun.spawn(["bash", "-c", `cd ${DDEV_PROJECT_ROOT} && ddev wp ${args}`], {
    stdout: "pipe",
    stderr: "pipe",
  });

  const stdout = await new Response(proc.stdout).text();
  const stderr = await new Response(proc.stderr).text();
  const exitCode = await proc.exited;

  if (exitCode !== 0) {
    throw new Error(`ddev wp ${args} failed (exit ${exitCode}): ${stderr}`);
  }

  return stdout.trim();
}

/** Get post content to check if already populated */
export async function getPostContent(postId: number): Promise<string> {
  return ddevWp(`post get ${postId} --field=post_content`);
}

/** Get an ACF meta value (scalar) */
export async function getPostMeta(
  postId: number,
  key: string
): Promise<string> {
  try {
    return await ddevWp(`post meta get ${postId} ${key}`);
  } catch {
    return "";
  }
}

/** Get an ACF meta value as JSON (for arrays like post_object multiples) */
export async function getPostMetaJson(
  postId: number,
  key: string
): Promise<string> {
  try {
    return await ddevWp(`post meta get ${postId} ${key} --format=json`);
  } catch {
    return "";
  }
}

/**
 * Update post content from a file without creating a revision.
 *
 * Uses wpdb->update() directly instead of wp_update_post() to avoid
 * triggering revision creation. This prevents the issue where WP-CLI
 * updates create revisions without ACF meta, causing ACF fields
 * (dropcap, dek, authors, etc.) to disappear from draft previews.
 */
export async function updatePostContent(
  postId: number,
  contentFilePath: string
): Promise<void> {
  const containerPath = toContainerPath(contentFilePath);
  const php = `
    \\$content = file_get_contents('${containerPath}');
    global \\$wpdb;
    \\$wpdb->update(\\$wpdb->posts, ['post_content' => \\$content], ['ID' => ${postId}]);
    clean_post_cache(${postId});
  `;
  await ddevWp(`eval "${php}"`);
}

/** Set ACF dek field */
export async function setDek(postId: number, dek: string): Promise<void> {
  // Escape single quotes for shell
  const escaped = dek.replace(/'/g, "'\\''");
  await ddevWp(`post meta update ${postId} dek '${escaped}'`);
  await ddevWp(
    `post meta update ${postId} _dek '${ACF_FIELDS.dek}'`
  );
}

/**
 * Import a media file and attach to a post. Returns attachment ID.
 * Files outside the DDEV project root are copied into a staging dir first,
 * since the container can only access files under the project root.
 */
export async function importMedia(
  filePath: string,
  postId: number
): Promise<number> {
  let importPath = filePath;

  // If the file is outside the project root, stage it
  if (!filePath.startsWith(DDEV_PROJECT_ROOT)) {
    await mkdir(STAGING_DIR, { recursive: true });
    const stagedPath = join(STAGING_DIR, basename(filePath));
    await copyFile(filePath, stagedPath);
    importPath = stagedPath;
  }

  const containerPath = toContainerPath(importPath);
  const result = await ddevWp(
    `media import '${containerPath}' --post_id=${postId} --porcelain`
  );
  const attachId = parseInt(result, 10);
  if (isNaN(attachId)) {
    throw new Error(`Failed to parse attachment ID from: ${result}`);
  }
  return attachId;
}

/** Clean up the media staging directory */
export async function cleanupStaging(): Promise<void> {
  const { rm } = await import("fs/promises");
  await rm(STAGING_DIR, { recursive: true, force: true });
}

/** Set ACF dropcap image field */
export async function setDropcap(
  postId: number,
  attachmentId: number
): Promise<void> {
  await ddevWp(
    `post meta update ${postId} dropcap '${attachmentId}'`
  );
  await ddevWp(
    `post meta update ${postId} _dropcap '${ACF_FIELDS.dropcap}'`
  );
}

/** Set ACF PDF file field */
export async function setPdf(
  postId: number,
  attachmentId: number
): Promise<void> {
  await ddevWp(
    `post meta update ${postId} pdf '${attachmentId}'`
  );
  await ddevWp(
    `post meta update ${postId} _pdf '${ACF_FIELDS.pdf}'`
  );
}

/** Query posts by issue meta value, return list of {id, title, authors} */
export async function getIssueDrafts(
  issueId: number
): Promise<
  { id: number; title: string; authorMeta: string }[]
> {
  const json = await ddevWp(
    `post list --post_type=post --post_status=draft --meta_key=issue --meta_value=${issueId} --fields=ID,post_title --format=json`
  );
  const posts: { ID: number; post_title: string }[] = JSON.parse(json);

  const results = [];
  for (const post of posts) {
    const authorMeta = await getPostMetaJson(post.ID, "author");
    results.push({
      id: post.ID,
      title: post.post_title,
      authorMeta,
    });
  }

  return results;
}

/** Get author CPT last_name by post ID */
export async function getAuthorLastName(
  authorPostId: number
): Promise<string> {
  return getPostMeta(authorPostId, "last_name");
}

/** Get post slug */
export async function getPostSlug(postId: number): Promise<string> {
  return ddevWp(`post get ${postId} --field=post_name`);
}

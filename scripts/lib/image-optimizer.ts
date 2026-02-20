/**
 * Optimize dropcap images using sharp.
 * Source images are ~1.5-2MB JPEGs; we resize to 400x400 and compress
 * to ~20-30KB using mozjpeg.
 */

import sharp from "sharp";
import { join } from "path";
import { mkdtemp } from "fs/promises";
import { tmpdir } from "os";

/** Directory for optimized images (inside DDEV project root for container access) */
const DDEV_PROJECT_ROOT =
  "/Users/andrewlovseth/Dev/sapir-journal/wp/wp-content";

let tempDir: string | null = null;

async function getTempDir(): Promise<string> {
  if (!tempDir) {
    tempDir = await mkdtemp(join(DDEV_PROJECT_ROOT, "tmp-import-"));
  }
  return tempDir;
}

export async function optimizeDrop(
  sourcePath: string,
  outputName: string
): Promise<string> {
  const dir = await getTempDir();
  const outputPath = join(dir, outputName);

  await sharp(sourcePath)
    .resize(400, 400, { fit: "cover" })
    .jpeg({ quality: 80, mozjpeg: true })
    .toFile(outputPath);

  return outputPath;
}

/** Clean up temp directory after import */
export async function cleanupTemp(): Promise<void> {
  if (tempDir) {
    const { rm } = await import("fs/promises");
    await rm(tempDir, { recursive: true, force: true });
    tempDir = null;
  }
}

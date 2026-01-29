import * as sass from "sass";
import { transform, browserslistToTargets } from "lightningcss";

const SCSS_ENTRIES = ["scss/style.scss"];

interface CompileResult {
  file: string;
  success: boolean;
  error?: string;
}

// Extract @charset, comments, and @import rules that must stay at top of CSS
// LightningCSS doesn't handle external @import rules, so we extract and re-prepend them
function extractTopRules(css: string): { topRules: string; rest: string } {
  const lines = css.split("\n");
  const topRules: string[] = [];
  const rest: string[] = [];
  let inTopSection = true;
  let inComment = false;

  for (const line of lines) {
    const trimmed = line.trim();

    if (inTopSection) {
      // Track multi-line comments
      if (trimmed.startsWith("/*") && !trimmed.includes("*/")) {
        inComment = true;
        topRules.push(line);
        continue;
      }
      if (inComment) {
        topRules.push(line);
        if (trimmed.includes("*/")) {
          inComment = false;
        }
        continue;
      }

      // Keep @charset, @import, single-line comments, and empty lines at top
      if (
        trimmed.startsWith("@charset") ||
        trimmed.startsWith("@import") ||
        (trimmed.startsWith("/*") && trimmed.endsWith("*/")) ||
        trimmed === ""
      ) {
        topRules.push(line);
      } else {
        inTopSection = false;
        rest.push(line);
      }
    } else {
      rest.push(line);
    }
  }

  return {
    topRules: topRules.join("\n"),
    rest: rest.join("\n"),
  };
}

async function compileCSS(entry: string, isDev = false): Promise<CompileResult> {
  const outputFile = entry.replace("scss/", "").replace(".scss", ".css");

  try {
    // Compile SCSS with Dart Sass
    // Always use expanded style to preserve WordPress theme header comment
    const result = sass.compile(entry, {
      sourceMap: true,
      sourceMapIncludeSources: true,
      style: "expanded",
      silenceDeprecations: ["import"],
      // Suppress "obsolete deprecation" warnings (deprecations that have completed)
      logger: {
        warn(message, options) {
          // Filter out obsolete deprecation notices - these are informational only
          if (message.includes("deprecation is obsolete")) return;
          // Log other warnings normally
          console.warn(`Sass warning: ${message}`);
        },
      },
    });

    // Extract @charset and @import rules before LightningCSS processing
    const { topRules, rest } = extractTopRules(result.css);

    // Process with LightningCSS for autoprefixing only (no minification)
    // Original gulpfile didn't minify, and WordPress needs the theme header comment
    // errorRecovery allows legacy CSS with vendor prefixes to pass through
    const transformed = transform({
      filename: outputFile,
      code: Buffer.from(rest),
      minify: false,
      sourceMap: isDev,
      errorRecovery: true,
      targets: browserslistToTargets([
        "> 0.5%",
        "last 2 versions",
        "not dead",
      ]),
    });

    // Reassemble: top rules + processed CSS
    let css = topRules
      ? topRules + "\n" + transformed.code.toString()
      : transformed.code.toString();

    // Append inline sourcemap for dev
    if (isDev && transformed.map) {
      const base64Map = Buffer.from(transformed.map).toString("base64");
      css += `\n/*# sourceMappingURL=data:application/json;base64,${base64Map} */`;
    }

    await Bun.write(outputFile, css);

    console.log(`✓ ${entry} → ${outputFile}`);
    return { file: outputFile, success: true };
  } catch (err) {
    const message = err instanceof Error ? err.message : String(err);
    console.error(`✗ ${entry}: ${message}`);
    return { file: outputFile, success: false, error: message };
  }
}

async function build(isDev = false) {
  console.log(isDev ? "Building CSS (dev)..." : "Building CSS (production)...");

  const results = await Promise.all(SCSS_ENTRIES.map((entry) => compileCSS(entry, isDev)));
  const failed = results.filter((r) => !r.success);

  if (failed.length > 0) {
    console.error(`\n${failed.length} file(s) failed to compile`);
    process.exit(1);
  }

  console.log("\nBuild complete!");
}

// Export for use in dev.ts
export { compileCSS, SCSS_ENTRIES };

// Run if executed directly
if (import.meta.main) {
  build(false);
}

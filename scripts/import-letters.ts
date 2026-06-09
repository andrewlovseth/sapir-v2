/**
 * import-letters.ts — Convert a quarterly print "Letters" docx into Gutenberg blocks.
 *
 * Usage:
 *   bun scripts/import-letters.ts <Letters.docx> [--out /tmp/letters-content.html]
 *
 * Emits Gutenberg block HTML to stdout (or --out), modeled on the golden
 * specimen post #4317 "Letters (Money)":
 *   - intro paragraph(s)
 *   - <p><em>To the Editor:</em></p> / <p><em>Name responds:</em></p>
 *   - body paragraph with inline floated dropcap <img> (placeholder URL)
 *   - right-aligned small-caps signature paragraph (name + location/affiliation)
 *   - <!-- wp:separator --> between topic groups
 *
 * Dropcap images use {{DROPCAP:<filename>}} placeholders. The expected files
 * follow the designer's convention: first occurrence of letter X → Letter_X.jpg,
 * second → Letter_X2.jpg, etc. A manifest of placeholders is printed to stderr —
 * upload the files, then substitute the real attachment URLs.
 *
 * Marker notation (varies by quarter — run inspect-docx.ts first to verify):
 *   [[X drop cap]]       dropcap; for letters other than "I" a leading space in
 *                        the remaining text is stripped (e.g. "[[Y drop cap]] arom"
 *                        → Y + "arom"); for "I" the space is kept ("I typically…")
 *   [[section separator]] horizontal rule between letter topics
 *   HED: / SUBHED:        title/dek metadata (SUBHED reported to stderr as dek)
 *   BLOCK QUOTES:         end-of-content annotation; everything after is dropped
 */

import mammoth from "mammoth";

const args = process.argv.slice(2);
const docxPath = args.find((a) => !a.startsWith("--"));
const outIdx = args.indexOf("--out");
const outPath = outIdx !== -1 ? args[outIdx + 1] : null;

if (!docxPath) {
    console.error("Usage: bun scripts/import-letters.ts <Letters.docx> [--out file]");
    process.exit(1);
}

const { value: html } = await mammoth.convertToHtml({ path: docxPath });

// Split mammoth output into paragraph inner-HTML strings
const paras = [...html.matchAll(/<p>(.*?)<\/p>/gs)].map((m) => m[1].trim());

const stripTags = (s: string) => s.replace(/<[^>]+>/g, "").replace(/&amp;/g, "&").trim();
// re-encode bare ampersands when emitting plain text back into HTML
const escAmp = (s: string) => s.replace(/&(?!#?\w+;)/g, "&amp;");

const dropcapCounts: Record<string, number> = {};
const manifest: string[] = [];
const blocks: string[] = [];

const para = (inner: string) => blocks.push(`<!-- wp:paragraph -->\n<p>${inner}</p>\n<!-- /wp:paragraph -->`);

const rightPara = (lines: string[]) =>
    blocks.push(
        `<!-- wp:paragraph {"align":"right"} -->\n<p class="has-text-align-right">${lines.join("<br>")}</p>\n<!-- /wp:paragraph -->`
    );

const separator = () =>
    blocks.push(
        `<!-- wp:separator -->\n<hr class="wp-block-separator has-alpha-channel-opacity"/>\n<!-- /wp:separator -->`
    );

// A signature name line: entirely lowercase plain text, short, not a marker/em line
const isNameLine = (inner: string) => {
    const plain = stripTags(inner);
    return (
        plain.length > 0 &&
        plain.length < 80 &&
        plain === plain.toLowerCase() &&
        /[a-z]/.test(plain) &&
        !inner.startsWith("[[") &&
        !/^<em>.*<\/em>$/.test(inner)
    );
};

let dek: string | null = null;
let i = 0;

while (i < paras.length) {
    let p = paras[i];
    const plain = stripTags(p);

    // Metadata / terminators
    if (plain === "[[none]]" || plain.startsWith("HED:")) {
        i++;
        continue;
    }
    if (plain.startsWith("SUBHED:")) {
        dek = plain.replace(/^SUBHED:\s*/, "");
        i++;
        continue;
    }
    if (plain.startsWith("BLOCK QUOTES:")) break;

    if (plain === "[[section separator]]") {
        separator();
        i++;
        continue;
    }

    // Dropcap paragraph
    const dc = p.match(/^\[\[([A-Z]) drop cap\]\](.*)$/s);
    if (dc) {
        const letter = dc[1];
        let rest = dc[2];
        // "I" reads as a standalone word ("I typically…") — keep its space.
        // Other letters continue the word ("Y" + "arom") — strip it.
        if (letter !== "I") rest = rest.replace(/^\s+/, "");
        dropcapCounts[letter] = (dropcapCounts[letter] || 0) + 1;
        const n = dropcapCounts[letter];
        const filename = n === 1 ? `Letter_${letter}.jpg` : `Letter_${letter}${n}.jpg`;
        manifest.push(filename);
        const img = `<img style="float: left; margin: 0 0.5rem 0.5rem 0; width: 80px; height: 80px;" src="{{DROPCAP:${filename}}}" alt="${letter}">`;
        para(`${img}${rest}`);
        i++;
        continue;
    }

    // Signature block: lowercase name line + following affiliation lines
    if (isNameLine(p)) {
        // normalize designer's <em>&</em> inside names
        const name = escAmp(stripTags(p));
        const lines = [`<span class="small-caps">${name}</span>`];
        i++;
        while (i < paras.length) {
            const next = paras[i];
            const nextPlain = stripTags(next);
            if (
                next.startsWith("[[") ||
                /^<em>.*<\/em>$/.test(next) ||
                nextPlain.startsWith("BLOCK QUOTES:") ||
                isNameLine(next) ||
                nextPlain.length === 0
            )
                break;
            // affiliation/location lines are short; a long paragraph means body text
            if (nextPlain.length > 90) break;
            lines.push(escAmp(nextPlain));
            i++;
        }
        rightPara(lines);
        continue;
    }

    // Everything else: plain paragraph (intro, body, em-lines like "To the Editor:")
    para(p);
    i++;
}

const content = blocks.join("\n\n");
if (outPath) {
    await Bun.write(outPath, content);
    console.error(`Wrote ${blocks.length} blocks to ${outPath}`);
} else {
    console.log(content);
}
console.error(`dek: ${dek}`);
console.error(`dropcap images needed (in order): ${manifest.join(", ")}`);

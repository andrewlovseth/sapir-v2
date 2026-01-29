import { watch } from "fs";
import { compileCSS, SCSS_ENTRIES } from "./build.ts";

const WORDPRESS_URL = "https://sapirjournal.dev";
const WORDPRESS_ALIASES = ["https://sapirjournal.dev", "https://sapir-journal.ddev.site"];
const DEV_PORT = parseInt(process.env.PORT || "3030", 10);

// Track connected WebSocket clients
const clients = new Set<ServerWebSocket<unknown>>();

// Live reload script injected into HTML
const LIVE_RELOAD_SCRIPT = `
<script>
(function() {
  const ws = new WebSocket('ws://' + location.host + '/__ws');
  ws.onmessage = function(e) {
    if (e.data === 'reload') {
      location.reload();
    } else if (e.data === 'css') {
      document.querySelectorAll('link[rel="stylesheet"]').forEach(function(link) {
        const href = link.href.split('?')[0];
        link.href = href + '?v=' + Date.now();
      });
    }
  };
  ws.onclose = function() {
    setTimeout(function() { location.reload(); }, 1000);
  };
})();
</script>
</body>`;

// Broadcast message to all connected clients
function broadcast(message: string) {
  for (const client of clients) {
    client.send(message);
  }
}

// Compile all SCSS files
async function compileAll(): Promise<boolean> {
  const results = await Promise.all(
    SCSS_ENTRIES.map((entry) => compileCSS(entry, true))
  );
  return results.every((r) => r.success);
}

// Initial build
console.log("Initial CSS build...");
await compileAll();

// Debounce helper for async functions
function debounceAsync(fn: () => Promise<void>, ms: number): () => void {
  let timeout: Timer | null = null;
  return () => {
    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(() => fn(), ms);
  };
}

// Watch SCSS files
const debouncedSCSSCompile = debounceAsync(async () => {
  console.log("[scss] Recompiling...");
  if (await compileAll()) {
    broadcast("css");
  }
}, 300);

watch("./scss", { recursive: true }, (event, filename) => {
  if (filename?.endsWith(".scss")) {
    debouncedSCSSCompile();
  }
});

// Watch PHP and JS files
const debouncedReload = debounceAsync(async () => {
  console.log("[reload] Browser refresh");
  broadcast("reload");
}, 100);

// Watch patterns matching original gulpfile.js
const watchDirs = [".", "template-parts", "templates", "blocks", "js"];

for (const dir of watchDirs) {
  try {
    watch(`./${dir}`, { recursive: true }, (event, filename) => {
      if (!filename) return;
      if (filename.endsWith(".php") || filename.endsWith(".js")) {
        debouncedReload();
      }
    });
  } catch {
    // Directory might not exist, ignore
  }
}

type ServerWebSocket<T> = {
  send(message: string): void;
  close(): void;
  data: T;
};

// Start dev server
const server = Bun.serve({
  port: DEV_PORT,

  fetch(req, server) {
    const url = new URL(req.url);

    // Handle WebSocket upgrade
    if (url.pathname === "/__ws") {
      const upgraded = server.upgrade(req);
      if (upgraded) return undefined;
      return new Response("WebSocket upgrade failed", { status: 400 });
    }

    // Serve theme files locally (CSS, JS, images) for live reload to work
    const themePath = "/wp-content/themes/sapir-v2/";
    if (url.pathname.startsWith(themePath)) {
      const localPath = "." + url.pathname.replace(themePath, "/");
      const file = Bun.file(localPath);
      if (file.size > 0) {
        return new Response(file);
      }
    }

    // Proxy to WordPress
    return proxyRequest(req);
  },

  websocket: {
    open(ws) {
      clients.add(ws);
    },
    close(ws) {
      clients.delete(ws);
    },
    message() {},
  },
});

async function proxyRequest(req: Request): Promise<Response> {
  const url = new URL(req.url);
  const targetUrl = `${WORDPRESS_URL}${url.pathname}${url.search}`;

  try {
    const response = await fetch(targetUrl, {
      method: req.method,
      headers: {
        ...Object.fromEntries(req.headers),
        host: new URL(WORDPRESS_URL).host,
      },
      body: req.method !== "GET" && req.method !== "HEAD" ? req.body : undefined,
      redirect: "manual",
      // @ts-ignore - Bun-specific option for self-signed certs
      tls: { rejectUnauthorized: false },
    });

    const contentType = response.headers.get("content-type") || "";

    // Handle redirects - rewrite Location header
    if (response.status >= 300 && response.status < 400) {
      const location = response.headers.get("location");
      if (location) {
        let newLocation = location;
        for (const alias of WORDPRESS_ALIASES) {
          const host = new URL(alias).host;
          newLocation = newLocation
            .replace(alias, `http://localhost:${DEV_PORT}`)
            .replace(`//${host}`, `//localhost:${DEV_PORT}`);
        }

        const headers = new Headers(response.headers);
        headers.set("location", newLocation);

        return new Response(null, {
          status: response.status,
          statusText: response.statusText,
          headers,
        });
      }
    }

    // For HTML responses, inject the live reload script and rewrite URLs
    if (contentType.includes("text/html")) {
      let html = await response.text();

      // Rewrite WordPress URLs to localhost for navigation
      for (const alias of WORDPRESS_ALIASES) {
        const host = new URL(alias).host;
        html = html.replaceAll(alias, `http://localhost:${DEV_PORT}`);
        html = html.replaceAll(`//${host}`, `//localhost:${DEV_PORT}`);
      }

      // Inject live reload script before </body>
      if (html.includes("</body>")) {
        html = html.replace("</body>", LIVE_RELOAD_SCRIPT);
      }

      // Build new headers, excluding problematic ones
      const headers = new Headers();
      response.headers.forEach((value, key) => {
        if (!["content-encoding", "content-length", "transfer-encoding"].includes(key.toLowerCase())) {
          headers.set(key, value);
        }
      });
      headers.set("content-length", String(new TextEncoder().encode(html).length));

      return new Response(html, {
        status: response.status,
        statusText: response.statusText,
        headers,
      });
    }

    // For non-HTML, pass through but fix headers
    const body = await response.arrayBuffer();
    const headers = new Headers();
    response.headers.forEach((value, key) => {
      if (!["content-encoding", "transfer-encoding"].includes(key.toLowerCase())) {
        headers.set(key, value);
      }
    });
    headers.set("content-length", String(body.byteLength));

    return new Response(body, {
      status: response.status,
      statusText: response.statusText,
      headers,
    });
  } catch (err) {
    console.error(`[proxy] Error: ${err}`);
    return new Response(`Proxy error: ${err}`, { status: 502 });
  }
}

console.log(`
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  Dev server running at http://localhost:${DEV_PORT}
  Proxying to ${WORDPRESS_URL}

  Watching:
    • scss/**/*.scss → CSS injection (no reload)
    • **/*.php → full reload
    • js/**/*.js → full reload

  Press Ctrl+C to stop
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
`);

/**
 * Tiny zero-dependency static server for local previewing.
 *   npm run dev -- 8080
 */
import { createServer } from 'node:http';
import { createReadStream, promises as fs } from 'node:fs';
import { extname, join, normalize } from 'node:path';
import { dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const port = Number(process.argv[2] ?? process.env.PORT ?? 8080);

const TYPES = {
  '.html': 'text/html; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.js': 'text/javascript; charset=utf-8',
  '.mjs': 'text/javascript; charset=utf-8',
  '.json': 'application/json; charset=utf-8',
  '.woff2': 'font/woff2',
  '.svg': 'image/svg+xml',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.zip': 'application/zip',
  '.ico': 'image/x-icon',
};

createServer(async (req, res) => {
  try {
    const url = new URL(req.url, `http://${req.headers.host}`);
    let path = decodeURIComponent(url.pathname);
    if (path.endsWith('/')) path += 'index.html';

    const file = join(root, normalize(path).replace(/^(\.\.[/\\])+/, ''));
    if (!file.startsWith(root)) {
      res.writeHead(403).end('Forbidden');
      return;
    }

    const stat = await fs.stat(file);
    if (stat.isDirectory()) {
      res.writeHead(302, { Location: path.replace(/\/?$/, '/') }).end();
      return;
    }

    res.writeHead(200, {
      'Content-Type': TYPES[extname(file)] ?? 'application/octet-stream',
      'Content-Length': stat.size,
      'Cache-Control': 'no-store',
    });
    createReadStream(file).pipe(res);
  } catch {
    res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' }).end('Not found');
  }
}).listen(port, '0.0.0.0', () => console.log(`serving ${root} → http://0.0.0.0:${port}`));

/**
 * Static sanity check — no browser required.
 *   - every local href/src resolves to a file that exists
 *   - every CSS @import / url() resolves
 *   - braces balance in every stylesheet
 *   - every sample ships index.html + sample.css + sample.js
 *   - every component in the catalog has a folder and a download
 *
 *   npm run check
 */
import { promises as fs } from 'node:fs';
import { dirname, join, normalize, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const problems = [];
const notes = [];

const exists = async (p) => !!(await fs.stat(p).catch(() => null));

async function checkHtml(file) {
  const html = await fs.readFile(file, 'utf8');
  const dir = dirname(file);

  for (const [, attr, url] of html.matchAll(/(?:href|src)="([^"]+)"/g)) {
    if (!url || url.startsWith('#') || url.startsWith('data:') || url.startsWith('mailto:') || url.startsWith('tel:')) continue;
    if (/^https?:\/\//.test(url)) continue;
    // root-absolute, as used by bundlers (Vite serves /src/main.tsx)
    if (url.startsWith('/')) continue;
    const target = resolve(dir, url);
    if (!(await exists(target))) problems.push(`${rel(file)} → missing ${url}`);
  }

  for (const [, url] of html.matchAll(/@import\s+url\(['"]?([^'")]+)['"]?\)/g)) {
    const target = resolve(dir, url);
    if (!(await exists(target))) problems.push(`${rel(file)} → missing @import ${url}`);
  }

  // module graph
  for (const [, url] of html.matchAll(/(?:from|import)\s+['"](\.[^'"]+)['"]/g)) {
    const target = resolve(dir, url);
    if (!(await exists(target))) problems.push(`${rel(file)} → missing module ${url}`);
  }
}

async function checkCss(file) {
  // data: URIs contain their own url(#filter) references — not real files
  const css = (await fs.readFile(file, 'utf8')).replace(/url\((['"]?)data:[^)]*\)/g, 'url(data)');
  const dir = dirname(file);

  const open = (css.match(/\{/g) ?? []).length;
  const close = (css.match(/\}/g) ?? []).length;
  if (open !== close) problems.push(`${rel(file)} → unbalanced braces (${open} { vs ${close} })`);

  for (const [, url] of css.matchAll(/url\(['"]?([^'")]+)['"]?\)/g)) {
    if (url === 'data' || url.startsWith('data:') || /^https?:\/\//.test(url)) continue;
    const target = resolve(dir, url);
    if (!(await exists(target))) problems.push(`${rel(file)} → missing url() ${url}`);
  }

  for (const [, url] of css.matchAll(/@import\s+url\(['"]?([^'")]+)['"]?\)/g)) {
    const target = resolve(dir, url);
    if (!(await exists(target))) problems.push(`${rel(file)} → missing @import ${url}`);
  }
}

const rel = (p) => p.slice(root.length + 1);

const walk = async (dir, out = []) => {
  for (const entry of await fs.readdir(dir, { withFileTypes: true })) {
    if (entry.name === 'node_modules' || entry.name === '.git' || entry.name === 'downloads') continue;
    const full = join(dir, entry.name);
    if (entry.isDirectory()) await walk(full, out);
    else out.push(full);
  }
  return out;
};

const files = await walk(root);
const htmls = files.filter((f) => f.endsWith('.html'));
const csses = files.filter((f) => f.endsWith('.css'));

for (const f of htmls) await checkHtml(f);
for (const f of csses) await checkCss(f);

/* ---- sample + download completeness ------------------------------------- */
const { COMPONENTS } = await import('../assets/js/catalog.js');
for (const c of COMPONENTS) {
  const dir = join(root, 'samples', c.slug);
  for (const name of ['index.html', 'sample.css', 'sample.js']) {
    if (!(await exists(join(dir, name)))) problems.push(`samples/${c.slug} → missing ${name}`);
  }
  if (!(await exists(join(root, 'downloads', `${c.slug}.zip`)))) {
    problems.push(`downloads/${c.slug}.zip → missing (run: npm run downloads)`);
  }
}

const sampleDirs = (await fs.readdir(join(root, 'samples'), { withFileTypes: true }))
  .filter((e) => e.isDirectory())
  .map((e) => e.name);
for (const slug of sampleDirs) {
  if (!COMPONENTS.some((c) => c.slug === slug)) notes.push(`samples/${slug} has no entry in catalog.js`);
}

/* ---- report -------------------------------------------------------------- */
console.log(`checked ${htmls.length} html · ${csses.length} css · ${sampleDirs.length} samples`);
for (const n of notes) console.log(`  !  ${n}`);
if (problems.length) {
  console.log(`\n${problems.length} problem(s):`);
  for (const p of problems) console.log(`  ✗  ${p}`);
  process.exit(1);
}
console.log('\nAll references resolve.');

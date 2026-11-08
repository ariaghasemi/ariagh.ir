# آریا قاسمی · کارگاه طراحی

<div dir="rtl">

زیرساخت سیستم طراحی و کتابخانهٔ قطعاتِ سایت شخصی [ariagh.ir](https://ariagh.ir) — یک سایت
چندزبانه (فارسی / انگلیسی / عربی) که روی وردپرس و المنتور ساخته می‌شود.

هر قطعه‌ای که اینجا ساخته می‌شود، بعداً به **یک افزونهٔ مستقل وردپرس** تبدیل می‌شود که
ویجت‌های اختصاصی خودش را به المنتور اضافه می‌کند. این مخزن جایی است که آن قطعات قبل از
تبدیل‌شدن به افزونه، ساخته و در مرورگر آزمایش می‌شوند.

</div>

---

## Aria Ghasemi · Design Atelier

The design system and component library behind a multilingual (fa / en / ar) personal
site built on WordPress and Elementor. Every component here becomes a standalone
WordPress plugin that registers its own Elementor widgets.

### Overview

| | |
| --- | --- |
| **Stack** | Plain HTML, CSS and JavaScript — no build step, no framework |
| **Motion** | GSAP + ScrollTrigger, vendored locally |
| **Type** | Vazirmatn (Persian / Arabic / UI) + Instrument Serif (Latin display), self-hosted |
| **Themes** | Lapis (dark) and Parchment (light), driven by one token file |
| **Directions** | RTL and LTR from the same markup — nothing is mirrored by hand |

### Layout

```
index.html                  the atelier dashboard
assets/
  css/tokens.css            ← the single source of truth (palette, type, space, motion)
  css/base.css              reset + shared primitives
  css/dashboard.css         the dashboard shell
  css/sample.css            scaffolding shared by every sample
  js/app.js                 dashboard runtime (state, router, tabs)
  js/i18n.js                fa / en / ar dictionaries
  js/catalog.js             the registry: components, plugins, changelog
  js/system-data.js         content for the Tokens / Typography / Motion tabs
  js/sample-shell.js        runtime shared by every sample
  vendor/                   fonts + GSAP, copied out of node_modules
samples/
  hero-3d/                  each sample is a standalone page with its own folder
  hero-motion/
  about-3d/
  about-motion/
  products-carousel/
  portfolio-carousel/
  header/
  footer/
downloads/                  one standalone .zip per sample
scripts/                    vendor.mjs · build-downloads.mjs · serve.mjs · check.mjs
```

### Commands

```bash
npm install          # dev dependencies only (fonts, GSAP, archiver)
npm run vendor       # copy fonts + GSAP out of node_modules into assets/vendor
npm run downloads    # build one .zip per sample into downloads/
npm run check        # verify every reference resolves
npm run dev          # serve locally at http://localhost:8080
npm run build        # vendor + downloads + check
```

The site itself needs no build — open `index.html` or serve the folder.

### Adding a component

1. Create `samples/<slug>/` with `index.html`, `sample.css`, `sample.js`.
2. Add one entry to `COMPONENTS` in `assets/js/catalog.js`.
3. Run `npm run build`.

It appears on the Overview, in the component grid, in the live preview and in `downloads/`.

### Design direction

Grounded in the material world of Persian lacquer work rather than in software defaults:

- **Lapis lazuli** for depth — the base is an inked indigo, never pure black
- **Neyshabur turquoise** as the single interactive colour
- **Brass** for structural lines and premium details only
- **Madder** reserved for blocked states, so it never competes with turquoise

Restraint is the rule: one bright accent, one metal, and quiet surfaces. Motion answers
what someone did rather than decorating the page, and `prefers-reduced-motion` collapses
every duration to zero.

### Roadmap → WordPress plugins

| Plugin | Status | Registers |
| --- | --- | --- |
| `ari-core` | building | tokens, language layer, motion runtime |
| `ari-hero` | planned | Hero · Depth, Hero · Flow |
| `ari-about` | planned | About · Depth, About · Story + page template |
| `ari-products` | planned | Products carousel, product card |
| `ari-portfolio` | planned | Portfolio carousel, project grid |
| `ari-header` | planned | Header, mega menu |
| `ari-footer` | planned | Footer, newsletter |

Each one ships `tokens.css` and `base.css`, so a token change propagates to every
installed plugin at once.

### Locales

The infrastructure is trilingual: `fa` and `en` ship full interface coverage, `ar` is
wired in and awaiting content. Samples carry `fa` and `en` copy in the same file and
switch on `<html lang>`, which the dashboard drives through `?lang=`.

---

Built in Tehran · 1404

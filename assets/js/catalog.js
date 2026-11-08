/* =============================================================================
   catalog.js — the registry. Every sample on disk has exactly one entry here,
   and the dashboard is generated from it. Add a folder in /samples, add an
   entry here, and it shows up everywhere (cards, counts, downloads, plugins).
   ========================================================================== */

export const GROUPS = [
  { id: 'hero', name: { fa: 'هیرو', en: 'Hero' } },
  { id: 'about', name: { fa: 'درباره', en: 'About' } },
  { id: 'carousel', name: { fa: 'کاروسل', en: 'Carousel' } },
  { id: 'chrome', name: { fa: 'کادر صفحه', en: 'Page chrome' } },
];

export const COMPONENTS = [
  {
    slug: 'hero-3d',
    group: 'hero',
    dimension: '3d',
    status: 'ready',
    version: '0.1.0',
    updated: '2026-11-08',
    name: { fa: 'هیرو · عمق', en: 'Hero · Depth' },
    summary: {
      fa: 'صحنه‌ای با لایه‌های واقعیِ سه‌بعدی: حلقه‌های هم‌مرکز، اورب‌های شناور و تیتری که خودش عمق دارد. با اشاره‌گر می‌چرخد و با لمس هم همین‌طور کار می‌کند.',
      en: 'A stage built from real 3D layers: concentric rings, drifting orbs, and a headline that carries its own depth. Tilts with the pointer and responds the same way to touch.',
    },
    tags: ['perspective', 'preserve-3d', 'pointer', 'gsap'],
    deps: ['gsap'],
    files: ['index.html', 'sample.css', 'sample.js'],
  },
  {
    slug: 'hero-motion',
    group: 'hero',
    dimension: '2d',
    status: 'ready',
    version: '0.1.0',
    updated: '2026-11-08',
    name: { fa: 'هیرو · جریان', en: 'Hero · Flow' },
    summary: {
      fa: 'بدون یک پیکسل عمقِ سه‌بعدی. پس‌زمینه‌ای از توریِ رنگی که آرام می‌چرخد، تیتری که واژه‌به‌واژه می‌آید و مارکویی که با اسکرول تغییر سرعت می‌دهد.',
      en: 'Not a pixel of 3D. A colour-mesh backdrop drifting slowly, a headline arriving word by word, and a marquee that changes speed as you scroll.',
    },
    tags: ['scroll', 'stagger', 'marquee', 'gsap'],
    deps: ['gsap', 'scrolltrigger'],
    files: ['index.html', 'sample.css', 'sample.js'],
  },
  {
    slug: 'about-3d',
    group: 'about',
    dimension: '3d',
    status: 'ready',
    version: '0.1.0',
    updated: '2026-11-08',
    name: { fa: 'درباره · عمق', en: 'About · Depth' },
    summary: {
      fa: 'کارتِ چهره روی صفحه‌هایی که مثل یک قلمدانِ لاکی باز می‌شوند. آمارها در عمق‌های متفاوت شناورند و با اسکرول، صحنه به‌آرامی می‌چرخد.',
      en: 'A portrait card resting on panels that open like a lacquer pen-box. Facts float at different depths and the whole scene rotates gently as the page scrolls.',
    },
    tags: ['preserve-3d', 'scroll-scrub', 'tilt', 'gsap'],
    deps: ['gsap', 'scrolltrigger'],
    files: ['index.html', 'sample.css', 'sample.js'],
  },
  {
    slug: 'about-motion',
    group: 'about',
    dimension: '2d',
    status: 'ready',
    version: '0.1.0',
    updated: '2026-11-08',
    name: { fa: 'درباره · روایت', en: 'About · Story' },
    summary: {
      fa: 'دو ستونِ تحریریه‌ای. خط‌ها با رسیدن به دید باز می‌شوند، شمارنده‌ها یک‌باره بالا می‌روند و نوارِ مهارت‌ها فقط وقتی پر می‌شود که واقعاً دیده شود.',
      en: 'Two editorial columns. Lines open as they enter view, counters run once, and the skill bars only fill when they are actually on screen.',
    },
    tags: ['reveal', 'counter', 'scroll-scrub', 'gsap'],
    deps: ['gsap', 'scrolltrigger'],
    files: ['index.html', 'sample.css', 'sample.js'],
  },
  {
    slug: 'products-carousel',
    group: 'carousel',
    dimension: '2d',
    status: 'ready',
    version: '0.1.0',
    updated: '2026-11-08',
    name: { fa: 'کاروسل محصولات', en: 'Products carousel' },
    summary: {
      fa: 'ردیفی که با کشیدن، چرخ‌دندهٔ ماوس و کلیدهای جهت‌دار حرکت می‌کند. کارت‌های کناری با perspective عقب می‌روند و میزانِ پیشرفت زیرِ ردیف معلوم است.',
      en: 'A rail that moves by drag, wheel and arrow keys. Side cards fall back in perspective and a progress rail tracks where you are.',
    },
    tags: ['drag', 'snap', 'perspective', 'a11y'],
    deps: ['gsap'],
    files: ['index.html', 'sample.css', 'sample.js'],
  },
  {
    slug: 'portfolio-carousel',
    group: 'carousel',
    dimension: '2d',
    status: 'ready',
    version: '0.1.0',
    updated: '2026-11-08',
    name: { fa: 'کاروسل نمونه‌کارها', en: 'Portfolio carousel' },
    summary: {
      fa: 'یک اسلایدِ بزرگ با یک ردیف انگشت‌شمار در پایین. تصویر با اختلاف‌منظر حرکت می‌کند و شمارندهٔ پروژه با وزنِ متغیرِ فونت بزرگ می‌نشیند.',
      en: 'One large slide with a thumbnail rail beneath it. The artwork parallaxes as it moves and the project number sets large in the variable weight axis.',
    },
    tags: ['parallax', 'thumbnails', 'keyboard', 'gsap'],
    deps: ['gsap'],
    files: ['index.html', 'sample.css', 'sample.js'],
  },
  {
    slug: 'header',
    group: 'chrome',
    dimension: '2d',
    status: 'ready',
    version: '0.1.0',
    updated: '2026-11-08',
    name: { fa: 'هدر', en: 'Header' },
    summary: {
      fa: 'هدرِ چسبان که با اسکرول فشرده می‌شود، زیرخطی که دنبالِ نشانگر می‌رود و یک پنلِ مگامنو. جهتِ نوشتار را از زبانِ فعال می‌گیرد، نه از قالب.',
      en: 'A sticky header that condenses on scroll, an underline that follows the pointer, and a mega-menu panel. It takes its writing direction from the active language, not from the theme.',
    },
    tags: ['sticky', 'condense', 'mega-menu', 'rtl'],
    deps: ['gsap'],
    files: ['index.html', 'sample.css', 'sample.js'],
  },
  {
    slug: 'footer',
    group: 'chrome',
    dimension: '2d',
    status: 'ready',
    version: '0.1.0',
    updated: '2026-11-08',
    name: { fa: 'فوتر', en: 'Footer' },
    summary: {
      fa: 'فوتری با نقشهٔ سایت، فیلدِ خبرنامه و نشانِ واژگانی که تا لبهٔ صفحه کشیده می‌شود. خطِ برنجی بالای آن، آخرین چیزی است که بازدیدکننده می‌بیند.',
      en: 'A footer with a sitemap, a newsletter field, and a wordmark stretched to the page edge. The brass rule above it is the last thing a visitor sees.',
    },
    tags: ['sitemap', 'newsletter', 'wordmark', 'rtl'],
    deps: ['gsap'],
    files: ['index.html', 'sample.css', 'sample.js'],
  },
];

/** The plugin each component becomes. One row per WordPress plugin. */
export const PLUGINS = [
  {
    slug: 'ari-core',
    name: { fa: 'هستهٔ آریا', en: 'Ari Core' },
    status: 'wip',
    version: '0.1.0',
    purpose: {
      fa: 'نشانه‌های طراحی، لایهٔ زبان و زمان‌ اجرای حرکت را برای همهٔ افزونه‌های دیگر فراهم می‌کند. بدون آن هیچ افزونه‌ای کار نمی‌کند.',
      en: 'Ships the design tokens, the language layer and the motion runtime to every other plugin. Nothing works without it.',
    },
    widgets: [
      { fa: 'تنظیمات سراسری', en: 'Global settings' },
      { fa: 'تغییردهندهٔ زبان', en: 'Language switcher' },
    ],
  },
  {
    slug: 'ari-hero',
    name: { fa: 'آریا · هیرو', en: 'Ari Hero' },
    status: 'planned',
    version: '0.0.0',
    purpose: {
      fa: 'بنر هیروِ موشن‌دار و سه‌بعدی را به عنوان ویجت به المنتور اضافه می‌کند؛ همان‌طور که اسلایدرِ روولوشن کار می‌کند، ولی با دستگاه طراحیِ خودمان.',
      en: 'Adds the motion and 3D hero banner to Elementor as a widget — the way Revolution Slider does, but on our own design system.',
    },
    widgets: [
      { fa: 'هیرو · عمق', en: 'Hero · Depth' },
      { fa: 'هیرو · جریان', en: 'Hero · Flow' },
    ],
  },
  {
    slug: 'ari-about',
    name: { fa: 'آریا · درباره', en: 'Ari About' },
    status: 'planned',
    version: '0.0.0',
    purpose: {
      fa: 'یک قالب کاملِ صفحه به کتابخانهٔ قالب‌های المنتور اضافه می‌کند تا کل صفحهٔ درباره با المنتور ویرایش شود.',
      en: 'Registers a full page template into the Elementor template library so the whole About page is editable in Elementor.',
    },
    widgets: [
      { fa: 'درباره · عمق', en: 'About · Depth' },
      { fa: 'درباره · روایت', en: 'About · Story' },
    ],
  },
  {
    slug: 'ari-products',
    name: { fa: 'آریا · محصولات', en: 'Ari Products' },
    status: 'planned',
    version: '0.0.0',
    purpose: {
      fa: 'نوعِ نوشتارِ محصول و کاروسل آن را ثبت می‌کند، به‌همراه ویجت‌هایی که المنتور برای نمایشِ پیش‌فرض ندارد.',
      en: 'Registers the product post type and its carousel, plus the widgets Elementor does not ship for a showcase layout.',
    },
    widgets: [
      { fa: 'کاروسل محصولات', en: 'Products carousel' },
      { fa: 'کارت محصول', en: 'Product card' },
    ],
  },
  {
    slug: 'ari-portfolio',
    name: { fa: 'آریا · نمونه‌کار', en: 'Ari Portfolio' },
    status: 'planned',
    version: '0.0.0',
    purpose: {
      fa: 'نمونه‌کارها را با ردیفِ انگشت‌شمار و اختلاف‌منظر نمایش می‌دهد و نوعِ نوشتارِ پروژه را ثبت می‌کند.',
      en: 'Shows work with a thumbnail rail and parallax, and registers the project post type.',
    },
    widgets: [
      { fa: 'کاروسل نمونه‌کارها', en: 'Portfolio carousel' },
      { fa: 'شبکهٔ پروژه', en: 'Project grid' },
    ],
  },
  {
    slug: 'ari-header',
    name: { fa: 'آریا · هدر', en: 'Ari Header' },
    status: 'planned',
    version: '0.0.0',
    purpose: {
      fa: 'هدرِ چسبان و مگامنو را با رفتارِ فشرده‌شدن و آگاهی از جهتِ نوشتار به المنتور می‌آورد.',
      en: 'Brings the sticky header and mega menu to Elementor, with condense behaviour and writing-direction awareness.',
    },
    widgets: [
      { fa: 'هدر', en: 'Header' },
      { fa: 'مگامنو', en: 'Mega menu' },
    ],
  },
  {
    slug: 'ari-footer',
    name: { fa: 'آریا · فوتر', en: 'Ari Footer' },
    status: 'planned',
    version: '0.0.0',
    purpose: {
      fa: 'فوتر را با نقشهٔ سایت، خبرنامه و نشانِ واژگانی به عنوان قالبِ قابل‌ویرایش ثبت می‌کند.',
      en: 'Registers the footer — sitemap, newsletter, wordmark — as an editable Elementor template.',
    },
    widgets: [
      { fa: 'فوتر', en: 'Footer' },
      { fa: 'خبرنامه', en: 'Newsletter' },
    ],
  },
];

export const CHANGELOG = [
  {
    version: '0.1.0',
    date: '2026-11-08',
    entries: [
      { kind: 'added', text: { fa: 'داشبورد کارگاه با هشت تب و پیش‌نمایش زنده.', en: 'Atelier dashboard with eight tabs and a live preview.' } },
      { kind: 'added', text: { fa: 'هشت قطعه: دو هیرو، دو درباره، دو کاروسل، هدر و فوتر.', en: 'Eight components: two heroes, two abouts, two carousels, header and footer.' } },
      { kind: 'added', text: { fa: 'سیستم نشانه‌ها با پوستهٔ لاجورد و پوستی.', en: 'Token system with lapis and parchment themes.' } },
      { kind: 'added', text: { fa: 'فونت‌های Vazirmatn و Instrument Serif به‌صورت میزبانِ خودکار.', en: 'Vazirmatn and Instrument Serif self-hosted.' } },
      { kind: 'changed', text: { fa: 'همهٔ کتابخانه‌ها از CDN به فایل‌های محلی منتقل شدند.', en: 'All libraries moved from CDN to local vendored files.' } },
    ],
  },
];

export const componentBySlug = (slug) => COMPONENTS.find((c) => c.slug === slug);
export const componentsByGroup = (group) => COMPONENTS.filter((c) => c.group === group);

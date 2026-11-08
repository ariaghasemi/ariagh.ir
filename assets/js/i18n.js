/* =============================================================================
   i18n.js — the trilingual layer for the dashboard.
   Adding a locale = adding one entry to DICT + one to LOCALES. Nothing else in
   the app hardcodes a string.
   ========================================================================== */

export const LOCALES = {
  fa: { code: 'fa', dir: 'rtl', native: 'فارسی', english: 'Persian', coverage: 100 },
  en: { code: 'en', dir: 'ltr', native: 'English', english: 'English', coverage: 100 },
  ar: { code: 'ar', dir: 'rtl', native: 'العربية', english: 'Arabic', coverage: 0 },
};

export const DEFAULT_LOCALE = 'fa';

const DICT = {
  fa: {
    'brand.name': 'آریا قاسمی',
    'brand.atelier': 'کارگاه طراحی',
    'brand.tagline': 'سیستم طراحی و کتابخانهٔ قطعات سایت شخصی — ساخته‌شده برای وردپرس و المنتور.',

    'nav.section.product': 'محصول',
    'nav.section.system': 'سیستم',
    'nav.section.roadmap': 'نقشه راه',
    'nav.overview': 'نمای کلی',
    'nav.components': 'قطعات',
    'nav.tokens': 'نشانه‌ها',
    'nav.typography': 'تایپوگرافی',
    'nav.motion': 'حرکت',
    'nav.plugins': 'افزونه‌ها',
    'nav.locales': 'زبان‌ها',
    'nav.changelog': 'تغییرات',

    'top.locale': 'زبان',
    'top.theme': 'پوسته',
    'top.theme.dark': 'تیره',
    'top.theme.light': 'روشن',
    'top.repo': 'مخزن',
    'top.build': 'نسخه',

    'overview.eyebrow': 'وضعیت کارگاه',
    'overview.title': 'هشت قطعه، یک دستگاه طراحی',
    'overview.lede':
      'هر قطعه یک افزونهٔ مستقل وردپرس می‌شود که ویجت‌های خودش را به المنتور اضافه می‌کند. اینجا قبل از تبدیل‌شدن به افزونه، ساخته و آزمایش می‌شوند.',
    'overview.stat.components': 'قطعهٔ آماده',
    'overview.stat.plugins': 'افزونهٔ برنامه‌ریزی‌شده',
    'overview.stat.locales': 'زبان',
    'overview.stat.tokens': 'نشانهٔ طراحی',
    'overview.recent': 'آخرین تغییرات',
    'overview.recent.note': 'جدیدترین قطعات دست‌خورده',
    'overview.next': 'قدم‌های بعد',
    'overview.next.note': 'آنچه از اینجا به افزونه تبدیل می‌شود',
    'overview.empty': 'هنوز چیزی اینجا نیست.',

    'components.title': 'قطعات',
    'components.note': 'روی هر قطعه بزن تا در پیش‌نمایش بالا باز شود.',
    'components.preview': 'پیش‌نمایش زنده',
    'components.reload': 'بارگذاری دوباره',
    'components.newTab': 'تب جدید',
    'components.download': 'دانلود',
    'components.downloadPlugin': 'دانلود افزونه',
    'components.copy': 'کپی مسیر',
    'components.copied': 'کپی شد',
    'components.show': 'نمایش',
    'components.width': 'عرض',
    'components.direction': 'جهت',
    'components.fit': 'جای‌گیری در قاب',
    'components.actual': 'اندازهٔ واقعی',
    'components.count': 'قطعه',
    'components.filterAll': 'همه',
    'components.deps': 'وابستگی‌ها',
    'components.files': 'فایل‌ها',

    'tokens.title': 'نشانه‌های طراحی',
    'tokens.note': 'برای کپی‌کردن مقدار، روی هر ردیف بزن.',
    'tokens.color': 'رنگ',
    'tokens.color.note': 'یک رنگ تعاملی (فیروزه)، یک فلز ساختاری (برنج)، و سطوحِ لاجوردی.',
    'tokens.radius': 'گردی گوشه',
    'tokens.radius.note': 'گردی با سلسله‌مراتب تغییر می‌کند؛ یک مقدار برای همه چیز نیست.',
    'tokens.space': 'فاصله‌گذاری',
    'tokens.space.note': 'پایهٔ ۴ پیکسل. فاصله‌ها در هر دو جهتِ نوشتار یکسان‌اند.',
    'tokens.elevation': 'ارتفاع',
    'tokens.elevation.note': 'سایه‌ها عمیق و سردند، نه مهِ خاکستریِ ملایم.',
    'tokens.role': 'نقش',
    'tokens.copied': 'کپی شد',

    'typography.title': 'تایپوگرافی',
    'typography.note': 'دو خانواده با دو وظیفهٔ کاملاً متفاوت — نه یک فونت برای همه چیز.',
    'typography.scale': 'مقیاس اندازه',
    'typography.scale.note': 'فارسی و عربی به ارتفاع خط بیشتری نیاز دارند؛ لاتین فشرده‌تر می‌نشیند.',
    'typography.specimen': 'نمونهٔ سه‌زبانه',
    'typography.specimen.note': 'یک جمله، سه زبان، یک مقیاس.',
    'typography.weights': 'محور وزن',
    'typography.weights.note': 'وزنِ متغیر از ۱۰۰ تا ۹۰۰ — بدون فایل جداگانه.',
    'typography.used': 'کاربرد',
    'typography.do': 'چه‌طور استفاده می‌شود',
    'typography.dont': 'چه‌طور استفاده نمی‌شود',

    'motion.title': 'حرکت',
    'motion.note': 'حرکت پاسخ است، نه تزیین. برای اجرا روی هر منحنی بزن.',
    'motion.easing': 'منحنی‌های زمان',
    'motion.easing.note': 'هر منحنی یک کار دارد؛ یکی برای خروج، یکی برای جابه‌جایی.',
    'motion.duration': 'بازه‌های زمانی',
    'motion.duration.note': 'کوتاه برای کنترل، بلند برای ورودِ صحنه.',
    'motion.principles': 'قواعد',
    'motion.play': 'اجرا',
    'motion.principle.1.title': 'یک لحظهٔ هماهنگ',
    'motion.principle.1.body':
      'یک توالی ورود در ابتدای صفحه بهتر از ده افکت پراکنده است. اگر همه چیز تکان بخورد، هیچ چیز توجه را جلب نمی‌کند.',
    'motion.principle.2.title': 'حرکت جواب می‌دهد',
    'motion.principle.2.body':
      'انیمیشنی که به عملِ کاربر پاسخ می‌دهد — باز شدن، جابه‌جایی، تأیید — همیشه خوش‌آمد است، چون نشان می‌دهد چه چیزی تغییر کرده.',
    'motion.principle.3.title': 'کاهش حرکت محترم است',
    'motion.principle.3.body':
      'با prefers-reduced-motion همهٔ زمان‌ها به صفر می‌رسند. محتوا کامل می‌ماند، فقط حرکت حذف می‌شود.',
    'motion.principle.4.title': 'عمق واقعی، نه قلابی',
    'motion.principle.4.body':
      'نسخه‌های سه‌بعدی از تبدیل‌های واقعیِ perspective استفاده می‌کنند و به لمس و اشاره‌گر واکنش نشان می‌دهند — در وردپرس هم همین‌طور کار می‌کنند.',

    'plugins.title': 'افزونه‌ها',
    'plugins.note':
      'هر قطعه به یک افزونهٔ مستقل تبدیل می‌شود که ویجت‌های اختصاصی‌اش را به المنتور اضافه می‌کند.',
    'plugins.plugin': 'افزونه',
    'plugins.purpose': 'وظیفه',
    'plugins.widgets': 'ویجت‌های المنتور',
    'plugins.status': 'وضعیت',
    'plugins.version': 'نسخه',
    'plugins.status.ready': 'آماده',
    'plugins.status.wip': 'در ساخت',
    'plugins.status.planned': 'برنامه‌ریزی‌شده',

    'locales.title': 'زبان‌ها',
    'locales.note': 'سه زبان، دو جهت نوشتار. زیرساخت آماده است؛ پوششِ محتوا مرحله‌به‌مرحله کامل می‌شود.',
    'locales.language': 'زبان',
    'locales.direction': 'جهت',
    'locales.coverage': 'پوشش رابط',
    'locales.font': 'خانوادهٔ فونت',
    'locales.status': 'وضعیت',
    'locales.status.live': 'زنده',
    'locales.status.pending': 'در انتظار محتوا',
    'locales.rtl': 'راست‌به‌چپ',
    'locales.ltr': 'چپ‌به‌راست',

    'changelog.title': 'تغییرات',
    'changelog.note': 'تاریخچهٔ نسخه‌های کارگاه و سیستم طراحی.',
    'changelog.version': 'نسخه',
    'changelog.kind.added': 'اضافه شد',
    'changelog.kind.changed': 'تغییر کرد',
    'changelog.kind.fixed': 'رفع شد',
  },

  en: {
    'brand.name': 'Aria Ghasemi',
    'brand.atelier': 'Design Atelier',
    'brand.tagline':
      'Design system and component library for a personal site — built for WordPress and Elementor.',

    'nav.section.product': 'Product',
    'nav.section.system': 'System',
    'nav.section.roadmap': 'Roadmap',
    'nav.overview': 'Overview',
    'nav.components': 'Components',
    'nav.tokens': 'Tokens',
    'nav.typography': 'Typography',
    'nav.motion': 'Motion',
    'nav.plugins': 'Plugins',
    'nav.locales': 'Locales',
    'nav.changelog': 'Changelog',

    'top.locale': 'Language',
    'top.theme': 'Theme',
    'top.theme.dark': 'Dark',
    'top.theme.light': 'Light',
    'top.repo': 'Repo',
    'top.build': 'Build',

    'overview.eyebrow': 'Atelier status',
    'overview.title': 'Eight components, one design machine',
    'overview.lede':
      'Each component becomes a standalone WordPress plugin that registers its own Elementor widgets. This is where they are built and tested before that happens.',
    'overview.stat.components': 'components ready',
    'overview.stat.plugins': 'plugins planned',
    'overview.stat.locales': 'locales',
    'overview.stat.tokens': 'design tokens',
    'overview.recent': 'Recently changed',
    'overview.recent.note': 'Components touched most recently',
    'overview.next': 'What comes next',
    'overview.next.note': 'What turns into a plugin from here',
    'overview.empty': 'Nothing here yet.',

    'components.title': 'Components',
    'components.note': 'Select a component to load it into the preview above.',
    'components.preview': 'Live preview',
    'components.reload': 'Reload',
    'components.newTab': 'New tab',
    'components.download': 'Download',
    'components.downloadPlugin': 'Download plugin',
    'components.copy': 'Copy path',
    'components.copied': 'Copied',
    'components.show': 'Show',
    'components.width': 'Width',
    'components.direction': 'Direction',
    'components.fit': 'Fit to frame',
    'components.actual': 'Actual size',
    'components.count': 'components',
    'components.filterAll': 'All',
    'components.deps': 'Depends on',
    'components.files': 'Files',

    'tokens.title': 'Design tokens',
    'tokens.note': 'Select any row to copy its value.',
    'tokens.color': 'Colour',
    'tokens.color.note': 'One interactive colour (turquoise), one structural metal (brass), lapis surfaces.',
    'tokens.radius': 'Corner radius',
    'tokens.radius.note': 'Radius shifts with hierarchy — never one value for everything.',
    'tokens.space': 'Spacing',
    'tokens.space.note': 'A 4px base. Spacing is identical in both writing directions.',
    'tokens.elevation': 'Elevation',
    'tokens.elevation.note': 'Shadows are deep and cool, never a soft grey haze.',
    'tokens.role': 'Role',
    'tokens.copied': 'Copied',

    'typography.title': 'Typography',
    'typography.note': 'Two families with two clearly different jobs — not one face for everything.',
    'typography.scale': 'Type scale',
    'typography.scale.note': 'Persian and Arabic need more leading; Latin sits tighter.',
    'typography.specimen': 'Trilingual specimen',
    'typography.specimen.note': 'One sentence, three languages, one scale.',
    'typography.weights': 'Weight axis',
    'typography.weights.note': 'A variable axis from 100 to 900 — no separate files.',
    'typography.used': 'Used for',
    'typography.do': 'How it is used',
    'typography.dont': 'How it is not used',

    'motion.title': 'Motion',
    'motion.note': 'Motion answers, it does not decorate. Select a curve to run it.',
    'motion.easing': 'Easing curves',
    'motion.easing.note': 'Each curve has one job — one for exits, one for travel.',
    'motion.duration': 'Durations',
    'motion.duration.note': 'Short for controls, long for a scene entering.',
    'motion.principles': 'Principles',
    'motion.play': 'Run',
    'motion.principle.1.title': 'One orchestrated moment',
    'motion.principle.1.body':
      'A single entrance sequence beats ten scattered effects. When everything moves, nothing draws the eye.',
    'motion.principle.2.title': 'Motion answers',
    'motion.principle.2.body':
      'Animation that responds to what someone did — opening, moving, confirming — is always welcome, because it shows what changed.',
    'motion.principle.3.title': 'Reduced motion is respected',
    'motion.principle.3.body':
      'With prefers-reduced-motion every duration collapses to zero. The content stays complete; only the movement goes.',
    'motion.principle.4.title': 'Real depth, not fake depth',
    'motion.principle.4.body':
      'The 3D variants use genuine perspective transforms and react to pointer and touch — and they behave the same inside WordPress.',

    'plugins.title': 'Plugins',
    'plugins.note':
      'Each component becomes a standalone plugin that registers its own Elementor widgets.',
    'plugins.plugin': 'Plugin',
    'plugins.purpose': 'Purpose',
    'plugins.widgets': 'Elementor widgets',
    'plugins.status': 'Status',
    'plugins.version': 'Version',
    'plugins.status.ready': 'Ready',
    'plugins.status.wip': 'Building',
    'plugins.status.planned': 'Planned',

    'locales.title': 'Locales',
    'locales.note':
      'Three languages, two writing directions. The infrastructure is in place; content coverage fills in stage by stage.',
    'locales.language': 'Language',
    'locales.direction': 'Direction',
    'locales.coverage': 'Interface coverage',
    'locales.font': 'Font family',
    'locales.status': 'Status',
    'locales.status.live': 'Live',
    'locales.status.pending': 'Awaiting content',
    'locales.rtl': 'Right to left',
    'locales.ltr': 'Left to right',

    'changelog.title': 'Changelog',
    'changelog.note': 'Version history of the atelier and its design system.',
    'changelog.version': 'Version',
    'changelog.kind.added': 'Added',
    'changelog.kind.changed': 'Changed',
    'changelog.kind.fixed': 'Fixed',
  },
};

/** Every key that exists in the reference locale (fa) — used for coverage stats. */
export const ALL_KEYS = Object.keys(DICT[DEFAULT_LOCALE]);

export function translate(locale, key) {
  return DICT[locale]?.[key] ?? DICT[DEFAULT_LOCALE][key] ?? key;
}

export function localeCoverage(code) {
  const dict = DICT[code];
  if (!dict) return 0;
  return Math.round((ALL_KEYS.filter((k) => k in dict).length / ALL_KEYS.length) * 100);
}

/** Picks the right side of a `{ fa, en }` value, falling back gracefully. */
export function pick(value, locale) {
  if (value == null) return '';
  if (typeof value === 'string') return value;
  return value[locale] ?? value[DEFAULT_LOCALE] ?? value.fa ?? value.en ?? '';
}

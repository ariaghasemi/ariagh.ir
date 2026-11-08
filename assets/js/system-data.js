/* =============================================================================
   system-data.js — static content for the Tokens / Typography / Motion tabs.
   Colour values are written as var() references so the tables always show
   what is live in tokens.css rather than a stale copy.
   ========================================================================== */

export const COLOR_GROUPS = [
  {
    name: { fa: 'سطح‌ها', en: 'Surfaces' },
    note: {
      fa: 'پایه یک سرمه‌ایِ جوهری است، نه مشکیِ خالص — گرمای خودش را به پوستهٔ روشن منتقل می‌کند.',
      en: 'The base is an inked indigo rather than pure black, which carries warmth across to the light theme.',
    },
    swatches: [
      { token: '--surface-0', role: { fa: 'پس‌زمینهٔ صفحه', en: 'Page background' } },
      { token: '--surface-1', role: { fa: 'پنل', en: 'Panel' } },
      { token: '--surface-2', role: { fa: 'برجسته', en: 'Raised' } },
      { token: '--surface-3', role: { fa: 'ورودی و حالتِ اشاره', en: 'Input and hover' } },
      { token: '--surface-inset', role: { fa: 'فرورفته', en: 'Inset' } },
      { token: '--line', role: { fa: 'خطِ مویی', en: 'Hairline' } },
      { token: '--line-strong', role: { fa: 'خطِ مرزی', en: 'Border' } },
    ],
  },
  {
    name: { fa: 'متن', en: 'Text' },
    note: {
      fa: 'متن عاجی است، نه سفیدِ خالص. سلسله‌مراتب با سه سطحِ روشنایی ساخته می‌شود، نه با اندازهٔ فونت.',
      en: 'Text is ivory rather than pure white. Hierarchy comes from three levels of luminance, not from font size alone.',
    },
    swatches: [
      { token: '--text', role: { fa: 'متنِ اصلی', en: 'Primary' } },
      { token: '--text-2', role: { fa: 'متنِ دوم', en: 'Secondary' } },
      { token: '--text-3', role: { fa: 'برچسب و متنِ سوم', en: 'Labels and tertiary' } },
    ],
  },
  {
    name: { fa: 'رنگِ تعاملی', en: 'Interactive colour' },
    note: {
      fa: 'فقط یک رنگِ روشن در کل سیستم: فیروزهٔ نیشابور. هر چیزی که کلیک‌خور یا فعال است همین رنگ را دارد.',
      en: 'Exactly one bright colour in the entire system: Neyshabur turquoise. Anything clickable or active uses it.',
    },
    swatches: [
      { token: '--accent', role: { fa: 'تعامل و حالتِ فعال', en: 'Interaction and active state' } },
      { token: '--accent-ink', role: { fa: 'متن روی رنگِ تأکید', en: 'Text on accent' } },
      { token: '--accent-soft', role: { fa: 'پس‌زمینهٔ ملایم', en: 'Soft background' } },
      { token: '--accent-line', role: { fa: 'خطِ تأکید', en: 'Accent border' } },
    ],
  },
  {
    name: { fa: 'فلزِ ساختاری', en: 'Structural metal' },
    note: {
      fa: 'برنج فقط برای خطوط، قاب‌ها و جزئیاتِ لوکس است — هرگز برای نشان‌دادنِ وضعیت یا تعامل.',
      en: 'Brass is only for rules, frames and premium details — never to signal state or interaction.',
    },
    swatches: [
      { token: '--brass', role: { fa: 'خطوط و قاب', en: 'Rules and frames' } },
      { token: '--brass-bright', role: { fa: 'برجستگیِ فلزی', en: 'Metallic highlight' } },
      { token: '--brass-line', role: { fa: 'خطِ موییِ طلایی', en: 'Gold hairline' } },
    ],
  },
  {
    name: { fa: 'لاجورد و وضعیت', en: 'Lapis and status' },
    note: {
      fa: 'لاجورد برای گرادیانِ عمق است، نه تأکید. کهربا در خانوادهٔ برنج می‌ماند و روناس فقط برای حالتِ مسدود است.',
      en: 'Lapis builds depth gradients, not emphasis. Amber stays inside the brass family and madder is reserved for blocked states.',
    },
    swatches: [
      { token: '--lapis-900', role: { fa: 'عمقِ دور', en: 'Far depth' } },
      { token: '--lapis-700', role: { fa: 'عمقِ میانی', en: 'Mid depth' } },
      { token: '--lapis-500', role: { fa: 'عمقِ نزدیک', en: 'Near depth' } },
      { token: '--status-ready', role: { fa: 'آماده', en: 'Ready' } },
      { token: '--status-wip', role: { fa: 'در ساخت', en: 'Building' } },
      { token: '--status-planned', role: { fa: 'برنامه‌ریزی‌شده', en: 'Planned' } },
      { token: '--status-blocked', role: { fa: 'مسدود', en: 'Blocked' } },
    ],
  },
];

export const RADIUS_TOKENS = [
  { token: '--r-xs', value: '4px', use: { fa: 'ورودی‌های کوچک و نشان‌های وضعیت', en: 'Small inputs and status dots' } },
  { token: '--r-sm', value: '8px', use: { fa: 'دکمه‌ها', en: 'Buttons' } },
  { token: '--r-md', value: '12px', use: { fa: 'فیلدها و منوها', en: 'Fields and menus' } },
  { token: '--r-lg', value: '18px', use: { fa: 'پنل‌ها', en: 'Panels' } },
  { token: '--r-xl', value: '26px', use: { fa: 'صحنهٔ پیش‌نمایش و قاب‌های بزرگ', en: 'Preview stage and large frames' } },
  { token: '--r-pill', value: '999px', use: { fa: 'نشان‌ها و کلیدهای گرد', en: 'Badges and pill controls' } },
];

export const SPACE_TOKENS = [
  { token: '--sp-1', value: '4px' },
  { token: '--sp-2', value: '8px' },
  { token: '--sp-3', value: '12px' },
  { token: '--sp-4', value: '16px' },
  { token: '--sp-5', value: '24px' },
  { token: '--sp-6', value: '32px' },
  { token: '--sp-7', value: '48px' },
  { token: '--sp-8', value: '64px' },
];

export const ELEVATION_TOKENS = [
  { token: '--elev-1', use: { fa: 'پنلِ هم‌سطح', en: 'Resting panel' } },
  { token: '--elev-2', use: { fa: 'منو و کارتِ شناور', en: 'Menu and floating card' } },
  { token: '--elev-3', use: { fa: 'پوشش و صحنهٔ مودال', en: 'Overlay and modal stage' } },
];

export const FONT_FAMILIES = [
  {
    name: 'Vazirmatn',
    variable: true,
    css: "'Vazirmatn', 'IRANSans', Tahoma, system-ui, sans-serif",
    role: { fa: 'همه چیز: رابط، فارسی، عربی و بدنهٔ لاتین', en: 'Everything: interface, Persian, Arabic and Latin body' },
    weights: '100 – 900',
    specimen: { fa: 'طراحی یعنی تصمیم گرفتن؛ بقیه‌اش تزیین است.', en: 'Design is the decision; the rest is decoration.' },
    do: {
      fa: 'برای جفت‌شدنِ اندازهٔ بصری با لاتین، وزن‌های ۴۰۰ تا ۷۰۰ را برای تیترهای فارسی به‌کار ببر.',
      en: 'Use weights 400–700 for Persian headings so they match the optical size of Latin.',
    },
    dont: {
      fa: 'فاصلهٔ خط را برای فارسی کمتر از ۱/۷ نگیر؛ حروفِ چسبیده خواندن را کند می‌کند.',
      en: 'Never drop Persian line-height below 1.7 — connected letters slow reading down.',
    },
  },
  {
    name: 'Instrument Serif',
    variable: false,
    css: "'Instrument Serif', 'Times New Roman', Georgia, serif",
    role: { fa: 'فقط نمایشِ لاتین: نشانِ واژگانی، اعدادِ بزرگ، تیترهای انگلیسی', en: 'Latin display only: wordmark, large numerals, English headlines' },
    weights: '400',
    specimen: { en: 'Aria Ghasemi' },
    do: {
      fa: 'برای نام و عددهای بزرگ؛ جایی که تضادِ بالای حروف کار می‌کند.',
      en: 'For names and large numerals — places where the high stroke contrast earns its keep.',
    },
    dont: {
      fa: 'هرگز برای متنِ فارسی یا عربی، چون این زبان‌ها را پوشش نمی‌دهد.',
      en: 'Never for Persian or Arabic text — it does not cover those scripts.',
    },
  },
];

export const TYPE_SCALE = [
  { token: '--fs-display', use: { fa: 'تیترِ صفحه', en: 'Page headline' }, sample: { fa: 'هشت قطعه', en: 'Eight components' } },
  { token: '--fs-h1', use: { fa: 'تیترِ بخش', en: 'Section heading' }, sample: { fa: 'یک دستگاه طراحی', en: 'One design machine' } },
  { token: '--fs-h2', use: { fa: 'تیترِ فرعی', en: 'Subheading' }, sample: { fa: 'نشانه‌های طراحی', en: 'Design tokens' } },
  { token: '--fs-h3', use: { fa: 'تیترِ پنل', en: 'Panel heading' }, sample: { fa: 'منحنی‌های زمان', en: 'Easing curves' } },
  { token: '--fs-body', use: { fa: 'بدنهٔ متن', en: 'Body' }, sample: { fa: 'هر قطعه یک افزونه می‌شود.', en: 'Each component becomes a plugin.' } },
  { token: '--fs-sm', use: { fa: 'متنِ کمکی', en: 'Supporting text' }, sample: { fa: 'وابسته به هسته', en: 'Depends on core' } },
  { token: '--fs-xs', use: { fa: 'برچسب', en: 'Label' }, sample: { fa: 'نسخهٔ ۰/۱', en: 'Version 0.1' } },
];

export const SPECIMEN = [
  { lang: 'fa', dir: 'rtl', label: 'فارسی', text: 'طراحی یعنی تصمیم گرفتن؛ بقیه‌اش تزیین است.' },
  { lang: 'en', dir: 'ltr', label: 'English', text: 'Design is the decision; everything after it is decoration.' },
  { lang: 'ar', dir: 'rtl', label: 'العربية', text: 'التصميم هو القرار، وما بعده زينة.' },
];

export const EASINGS = [
  {
    token: '--ease-out-expo',
    value: 'cubic-bezier(0.16, 1, 0.3, 1)',
    use: { fa: 'ورودِ صحنه و پنل', en: 'Scene and panel entrances' },
    note: { fa: 'سریع شروع می‌کند و نرم می‌نشیند. صحنه حاضر به‌نظر می‌رسد، نه در حالِ حرکت.', en: 'Starts fast and settles softly. The scene feels present rather than travelling.' },
  },
  {
    token: '--ease-out-quart',
    value: 'cubic-bezier(0.25, 1, 0.5, 1)',
    use: { fa: 'دکمه و کنترل', en: 'Buttons and controls' },
    note: { fa: 'برای چیزهایی که کاربر لمس کرده؛ پاسخ فوری حس می‌شود.', en: 'For things someone just touched — the response needs to feel immediate.' },
  },
  {
    token: '--ease-in-out-quint',
    value: 'cubic-bezier(0.83, 0, 0.17, 1)',
    use: { fa: 'جابه‌جاییِ کامل', en: 'Full travel' },
    note: { fa: 'وقتی چیزی از یک نقطه به نقطهٔ دیگر می‌رود و می‌ایستد.', en: 'When something travels from one position to another and stops there.' },
  },
  {
    token: '--ease-spring',
    value: 'cubic-bezier(0.34, 1.56, 0.64, 1)',
    use: { fa: 'تأیید و باز شدن', en: 'Confirm and expand' },
    note: { fa: 'کمی از هدف جلو می‌زند. فقط برای لحظه‌های تأیید، نه برای جابه‌جایی.', en: 'Overshoots slightly. Reserved for confirmations, never for plain travel.' },
  },
];

export const DURATIONS = [
  { token: '--dur-xs', value: '120ms', use: { fa: 'تغییرِ رنگ و حاشیه', en: 'Colour and border change' } },
  { token: '--dur-sm', value: '200ms', use: { fa: 'دکمه و اشاره', en: 'Button and hover' } },
  { token: '--dur-md', value: '380ms', use: { fa: 'باز و بسته شدن', en: 'Opening and closing' } },
  { token: '--dur-lg', value: '640ms', use: { fa: 'ورودِ پنل', en: 'Panel entrance' } },
  { token: '--dur-xl', value: '1000ms', use: { fa: 'توالیِ صحنه', en: 'Scene sequence' } },
];

export const TOKEN_COUNT =
  COLOR_GROUPS.reduce((n, g) => n + g.swatches.length, 0) +
  RADIUS_TOKENS.length +
  SPACE_TOKENS.length +
  ELEVATION_TOKENS.length +
  EASINGS.length +
  DURATIONS.length;

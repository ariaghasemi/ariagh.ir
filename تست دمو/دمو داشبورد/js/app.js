/* ═══════════════════════════════════════════
   داشبورد نمونه‌ها — ariagh
   ═══════════════════════════════════════════ */
(function () {
  'use strict';

  const gsap = window.gsap;
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (gsap && window.ScrollTrigger) gsap.registerPlugin(window.ScrollTrigger);

  /* ── آیکون‌ها ── */
  const ICONS = {
    overview: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7.5" height="7.5" rx="2"/><rect x="13.5" y="3" width="7.5" height="7.5" rx="2"/><rect x="3" y="13.5" width="7.5" height="7.5" rx="2"/><rect x="13.5" y="13.5" width="7.5" height="7.5" rx="2"/></svg>',
    hero: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l2.4 5.5L20 9.7l-4 4.2.9 6.1L12 17l-4.9 3 .9-6.1-4-4.2 5.6-1.2z"/></svg>',
    about: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4.5 20c1.4-3.6 4.2-5.4 7.5-5.4s6.1 1.8 7.5 5.4"/></svg>',
    products: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8.4v7.2a2 2 0 0 1-1 1.7l-7 3.9a2 2 0 0 1-2 0l-7-3.9a2 2 0 0 1-1-1.7V8.4a2 2 0 0 1 1-1.7l7-3.9a2 2 0 0 1 2 0l7 3.9a2 2 0 0 1 1 1.7z"/><path d="M3.3 7.3 12 12l8.7-4.7M12 12v9.4"/></svg>',
    portfolio: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18"/></svg>',
    header: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="7" rx="2"/><path d="M3 14h18M3 18h10"/></svg>',
    footer: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 15h18M7 18h4"/></svg>',
    sections: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 3 8l9 5 9-5z"/><path d="m3 13 9 5 9-5"/></svg>',
    capabilities: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4.5 13.5H11L10 22l8.5-11.5H12z"/></svg>',
  };

  /* ── تب‌ها ── */
  const TABS = [
    { id: 'overview', name: 'پیشخوان', icon: 'overview', desc: 'نمای کلی پروژه، نقشهٔ راه افزونه‌ها و وضعیت نمونه‌ها' },
    { id: 'hero', name: 'هیرو', icon: 'hero', desc: 'بنر اصلی صفحهٔ نخست — دو نسخهٔ سه‌بعدی و موشن کلاسیک' },
    { id: 'about', name: 'درباره من', icon: 'about', desc: 'معرفی کامل شما با انیمیشن — سه‌بعدی و غیرسه‌بعدی' },
    { id: 'products', name: 'محصولات', icon: 'products', desc: 'کاروسل محصولات با افکت‌های لوکس و روان' },
    { id: 'portfolio', name: 'نمونه‌کارها', icon: 'portfolio', desc: 'کاروسل پروژه‌ها و دستاوردها' },
    { id: 'header', name: 'هدر', icon: 'header', desc: 'ناوبری بالای سایت — شناور مینیمال و تمام‌صفحه' },
    { id: 'footer', name: 'فوتر', icon: 'footer', desc: 'پایین صفحه — چندستونه و جمع‌وجور' },
    { id: 'sections', name: 'بخش‌های تکمیلی', icon: 'sections', desc: 'شمارنده، CTA، نظرات، فرم تماس و لودر' },
    { id: 'capabilities', name: 'قابلیت‌های ویژه', icon: 'capabilities', badge: 'جدید', desc: 'مهارت‌هایی که این افزونه‌ها به المنتور اضافه می‌کنند' },
  ];

  /* ── نمونه‌ها ── */
  const SAMPLES = [
    // هیرو
    {
      tab: 'hero', title: 'هیرو مین‌فریم (Mainframe)', ready: true,
      desc: 'بنر تمام‌صفحه با ویدیویی که با ماوس جلو و عقب می‌رود، تایپ‌نویسر و دکمه‌های قرصی — همراه با افزونهٔ اختصاصی المنتور (ویجت + قالب آماده).',
      tags: ['المنتور', 'افزونه', 'ویدیویی'],
      mock: 'text',
      image: 'images/mainframe-preview.svg',
      demoUrl: 'https://ariaghasemi.github.io/ariagh.ir/%D8%AA%D8%B3%D8%AA%20%D8%AF%D9%85%D9%88/%D8%AF%D9%85%D9%88%20%D9%87%DB%8C%D8%B1%D9%88%20%D9%85%DB%8C%D9%86%D9%81%D8%B1%DB%8C%D9%85/dist/',
      downloadUrl: 'downloads/mainframe-elementor.zip',
    },
    { tab: 'hero', title: 'هیرو سه‌بعدی', desc: 'بنر تمام‌صفحه با عمق، نور و حرکت سه‌بعدی — سنگین‌ترین امضای بصری سایت.', tags: ['سه‌بعدی', 'WebGL', 'موشن'], mock: 'layers' },
    { tab: 'hero', title: 'هیرو موشن کلاسیک', desc: 'تیتر متحرک، لایه‌های شناور و ورود اسلایسی — سریع، سبک و چشم‌گیر.', tags: ['GSAP', '۲بعدی', 'متن‌حرکتی'], mock: 'text' },
    // درباره من
    { tab: 'about', title: 'درباره من — سه‌بعدی', desc: 'پرترهٔ تعاملی با مدارهای چرخان و واکنش به حرکت ماوس.', tags: ['سه‌بعدی', 'شخصی'], mock: 'orbit' },
    { tab: 'about', title: 'درباره من — موشن', desc: 'کارت معرفی با پالس‌های نرم، خطوط ورودی و تایپوگرافی زنده.', tags: ['۲بعدی', 'استاندارد'], mock: 'blob' },
    // محصولات
    { tab: 'products', title: 'کاروسل محصولات', desc: 'ردیف کارت‌های اسلایدشونده با نقطه‌های هوشمند و هاور جزئیات.', tags: ['کاروسل', 'فروشگاهی'], mock: 'carousel' },
    // نمونه‌کارها
    { tab: 'portfolio', title: 'کاروسل نمونه‌کارها', desc: 'گالری لغزندهٔ پروژه‌ها با درخشش طلایی روی هر قاب.', tags: ['کاروسل', 'گالری'], mock: 'gallery' },
    // هدر
    { tab: 'header', title: 'هدر شناور مینیمال', desc: 'نوار بالایی که با اسکرول جمع می‌شود؛ لوگو، منو و دکمهٔ طلایی.', tags: ['هدر', 'شناور'], mock: 'header' },
    { tab: 'header', title: 'هدر با منوی تمام‌صفحه', desc: 'کلیک روی منو، پنل تمام‌صفحه را با انیمیشن باز می‌کند.', tags: ['هدر', 'منو'], mock: 'menu' },
    // فوتر
    { tab: 'footer', title: 'فوتر چندستونه', desc: 'ستون‌های لینک، شبکه‌های اجتماعی و نوار پایین با تایپ متحرک.', tags: ['فوتر', 'چندستونه'], mock: 'footer' },
    // بخش‌های تکمیلی
    { tab: 'sections', title: 'آمار و شمارنده', desc: 'اعدادی که از صفر تا هدف می‌شمارند؛ همراه با برچسب‌های زنده.', tags: ['شمارنده', 'آمار'], mock: 'counter' },
    { tab: 'sections', title: 'بنر دعوت به اقدام', desc: 'دکمهٔ CTA با پالس و حلقه‌های نورانی دور آن.', tags: ['CTA', 'تبدیل'], mock: 'cta' },
    { tab: 'sections', title: 'نظرات مشتریان', desc: 'کارت نقل‌قول با گیومهٔ طلایی و ستاره‌های پلکانی.', tags: ['نظرات', 'اعتماد'], mock: 'quote' },
    { tab: 'sections', title: 'فرم تماس', desc: 'فیلدهای متحرک با افکت تایپ و دکمهٔ ارسال ضربانی.', tags: ['فرم', 'تماس'], mock: 'form' },
    { tab: 'sections', title: 'لودر صفحه', desc: 'حلقهٔ چرخان و نوار پیشرفت — اولین برند سایت هنگام باز شدن.', tags: ['لودر', 'برندینگ'], mock: 'loader' },
    // قابلیت‌های ویژه
    { tab: 'capabilities', title: 'موتور موشن GSAP', desc: 'افزودن تایم‌لاین، اسکرول‌تریگر و انیمیشن‌های حرفه‌ای به المنتور.', tags: ['GSAP', 'قابلیت'], mock: 'nodes' },
    { tab: 'capabilities', title: 'افکت‌های متنی', desc: 'تیتر واواً، ماسک، blur و رولینگ — برای تیترهای صفحه.', tags: ['متن', 'FX'], mock: 'text' },
    { tab: 'capabilities', title: 'تعامل سه‌بعدی WebGL', desc: 'افزودن صحنه‌های سه‌بعدی به هر بخش، آن‌هم بدون کد نوشتن.', tags: ['سه‌بعدی', 'WebGL'], mock: 'layers' },
    { tab: 'capabilities', title: 'پارالاکس و اسکرول حرفه‌ای', desc: 'لایه‌های موازی، صحنه‌های pin‌شده و روایت اسکرولی.', tags: ['اسکرول', 'پارالاکس'], mock: 'nodes' },
  ];

  /* ── نقشهٔ راه افزونه‌ها ── */
  const PLUGINS = [
    { name: 'افزونهٔ هیرو (بنر موشن صفحهٔ اصلی)', pct: 100, state: 'آماده — دانلود از کارت هیرو مین‌فریم' },
    { name: 'افزونهٔ درباره من (تمپلیت کامل المنتور)', pct: 0 },
    { name: 'افزونهٔ صفحهٔ محصولات', pct: 0 },
    { name: 'افزونهٔ نمونه‌کارها', pct: 0 },
    { name: 'افزونهٔ هدر و ناوبری', pct: 0 },
    { name: 'افزونهٔ فوتر', pct: 0 },
  ];

  /* ── وضعیت‌ها ── */
  const STATUS = {
    demo: { chip: 'در صف ساخت', icon: '▶', title: 'دموی زنده', text: 'این نمونه هنوز ساخته نشده است. پس از ساخت، دکمهٔ «دمو» پیش‌نمایش زندهٔ همین بخش را در داشبورد باز می‌کند.' },
    download: { chip: 'در صف ساخت', icon: '⬇', title: 'دانلود فایل', text: 'فایل نمونه (HTML / CSS / JS) پس از ساخت، از همین کارت قابل دانلود خواهد بود — هر نمونه پوشهٔ اختصاصی خودش را در گیت‌هاب دارد.' },
    import: { chip: 'نیازمند اتصال', icon: '＋', title: 'افزودن به سایت', text: 'پس از ساخت، این نمونه به‌صورت ویجت یا تمپلیتِ المنتور به سایت وردپرسی شما اضافه می‌شود. اتصال وردپرس در نسخهٔ بعدی فعال می‌شود.' },
  };

  /* ═══ رندر ═══ */
  const tabNav = document.getElementById('tab-nav');
  const grid = document.getElementById('card-grid');
  const panelGrid = document.getElementById('panel-grid');
  const panelOverview = document.getElementById('panel-overview');
  const emptyEl = document.getElementById('empty');
  const titleEl = document.getElementById('tab-title');
  const descEl = document.getElementById('tab-desc');
  const searchEl = document.getElementById('search');
  let currentTab = 'overview';

  const faNum = (n) => Number(n).toLocaleString('fa-IR');

  function countOf(tabId) {
    return SAMPLES.filter((s) => s.tab === tabId).length;
  }

  function renderNav() {
    tabNav.innerHTML = TABS.map((t) => {
      const count = t.id === 'overview' ? '' :
        `<span class="t-count">${faNum(countOf(t.id))}</span>`;
      const badge = t.badge ? `<span class="tab-badge">${t.badge}</span>` : '';
      return `<button class="tab-item${t.id === currentTab ? ' active' : ''}" data-tab="${t.id}">
        ${ICONS[t.icon] || ''}
        <span>${t.name}</span>
        ${badge || count}
      </button>`;
    }).join('');
  }

  function thumbHTML(mock) {
    switch (mock) {
      case 'layers': return '<div class="m-layers"><span class="ly"></span><span class="ly"></span><span class="ly"></span><span class="ly-dot"></span></div>';
      case 'text': return '<div class="m-text"><span class="ln"></span><span class="ln"></span><span class="ln"></span></div>';
      case 'orbit': return '<div class="m-orbit"><span class="ring r1"></span><span class="ring r2"></span><span class="sat"></span><span class="core">A</span></div>';
      case 'blob': return '<div class="m-blob"><span class="pulse"></span><span class="pulse p2"></span><span class="av"></span><span class="ln"></span><span class="ln l2"></span></div>';
      case 'carousel': return '<div class="m-carousel"><div class="track"><span class="mini"></span><span class="mini"></span><span class="mini"></span><span class="mini"></span><span class="mini"></span><span class="mini"></span></div><div class="dots"><i></i><i></i><i></i></div></div>';
      case 'gallery': return '<div class="m-gallery"><span class="cell"><i></i></span><span class="cell"><i></i></span><span class="cell"><i></i></span><span class="cell"><i></i></span></div>';
      case 'header': return '<div class="m-header"><div class="bar"><span class="logo-d"></span><span class="nv n1"></span><span class="nv n2"></span><span class="nv n3"></span><span class="cta-d"></span></div><div class="hero-ln"></div><div class="hero-ln l2"></div></div>';
      case 'menu': return '<div class="m-menu"><div class="top"><span class="logo-d"></span><span class="burger"><i></i><i></i><i></i></span></div><div class="panel"><i></i><i></i><i></i></div></div>';
      case 'footer': return '<div class="m-footer"><div class="cols"><span class="col"><i></i><i></i><i></i><i></i></span><span class="col"><i></i><i></i><i></i><i></i></span><span class="col"><i></i><i></i><i></i><i></i></span></div><div class="bar-b"><i></i><i></i><i></i></div></div>';
      case 'counter': return '<div class="m-counter"><div class="nums">' +
        '<span class="n"><span class="reel"><span class="col"><span>۱۲</span><span>۴۸</span><span>۹۶</span><span>۱۲</span></span></span><span class="lbl"></span></span>' +
        '<span class="n"><span class="reel"><span class="col"><span>۳</span><span>۱۵</span><span>۳۰</span><span>۳</span></span></span><span class="lbl"></span></span>' +
        '<span class="n"><span class="reel"><span class="col"><span>۷</span><span>۲۴</span><span>۵۲</span><span>۷</span></span></span><span class="lbl"></span></span>' +
        '</div></div>';
      case 'cta': return '<div class="m-cta"><span class="ring2"></span><span class="ring2 r2"></span><span class="pill">شروع کنیم</span></div>';
      case 'quote': return '<div class="m-quote"><span class="qm">”</span><span class="ln q1"></span><span class="ln q2"></span><span class="stars"><i>★</i><i>★</i><i>★</i><i>★</i><i>★</i></span></div>';
      case 'form': return '<div class="m-form"><div class="fld"><span class="fill"></span><span class="caret"></span></div><div class="fld f2"><span class="fill"></span></div><span class="send"></span></div>';
      case 'loader': return '<div class="m-loader"><span class="ring3"></span><span class="pbar"><i></i></span></div>';
      case 'nodes': return '<div class="m-nodes"><span class="nd n1"></span><span class="nd n2"></span><span class="nd n3"></span><span class="nd n4"></span><span class="edge e1"></span><span class="edge e2"></span><span class="edge e3"></span><span class="edge e4"></span><span class="hub">JS</span></div>';
      default: return '';
    }
  }

  function cardHTML(s, idx, showTab) {
    const tab = TABS.find((t) => t.id === s.tab);
    const num = String(idx + 1).padStart(2, '0');
    const thumb = s.image
      ? `<img class="thumb-img" src="${s.image}" alt="پیش‌نمایش ${s.title}" loading="lazy">`
      : thumbHTML(s.mock);
    const chip = showTab
      ? `<span class="chip chip-gold card-tab-chip">${tab ? tab.name : ''}</span>`
      : s.ready
        ? '<span class="chip chip-ok">آماده</span>'
        : '<span class="chip chip-wait">در صف ساخت</span>';
    return `<article class="card" data-idx="${idx}">
      <div class="card-top">
        <span class="card-idx">#${num}</span>
        ${chip}
      </div>
      <div class="thumb">${thumb}</div>
      <div class="card-body">
        <h3>${s.title}</h3>
        <p>${s.desc}</p>
        <div class="tags">${s.tags.map((t) => `<span class="tag">${t}</span>`).join('')}</div>
      </div>
      <div class="card-actions">
        <button class="btn btn-gold" data-action="demo" data-idx="${idx}">▶ دمو</button>
        <button class="btn btn-ghost" data-action="download" data-idx="${idx}">⬇ دانلود</button>
        <button class="btn btn-quiet" data-action="import" data-idx="${idx}">＋ افزودن به سایت</button>
      </div>
    </article>`;
  }

  function renderGrid(animate) {
    const q = searchEl.value.trim().toLowerCase();
    let list;
    if (q) {
      list = SAMPLES.map((s, i) => ({ s, i })).filter(({ s }) =>
        (s.title + ' ' + s.desc + ' ' + s.tags.join(' ')).toLowerCase().includes(q)
      );
    } else {
      if (currentTab === 'overview') { panelGrid.classList.add('hidden'); panelOverview.classList.remove('hidden'); return; }
      list = SAMPLES.map((s, i) => ({ s, i })).filter(({ s }) => s.tab === currentTab);
    }

    panelOverview.classList.add('hidden');
    panelGrid.classList.remove('hidden');
    grid.innerHTML = list.map(({ s, i }) => cardHTML(s, i, !!q)).join('');
    emptyEl.classList.toggle('hidden', list.length > 0);

    if (animate && !reduceMotion && gsap) {
      gsap.fromTo(grid.querySelectorAll('.card'),
        { opacity: 0, y: 26 },
        { opacity: 1, y: 0, duration: 0.5, stagger: 0.06, ease: 'power3.out', clearProps: 'transform' });
    }
  }

  function setTab(id, skipHash) {
    if (!TABS.some((t) => t.id === id)) id = 'overview';
    currentTab = id;
    searchEl.value = '';

    const tab = TABS.find((t) => t.id === id);
    titleEl.textContent = tab.name;
    descEl.textContent = tab.desc;

    tabNav.querySelectorAll('.tab-item').forEach((el) =>
      el.classList.toggle('active', el.dataset.tab === id)
    );

    if (id === 'overview') {
      panelGrid.classList.add('hidden');
      panelOverview.classList.remove('hidden');
      if (!reduceMotion && gsap) {
        gsap.fromTo(panelOverview.children,
          { opacity: 0, y: 18 },
          { opacity: 1, y: 0, duration: 0.5, stagger: 0.08, ease: 'power3.out', clearProps: 'transform' });
      }
    } else {
      renderGrid(true);
    }

    if (!skipHash) history.replaceState(null, '', '#' + id);
    closeSidebar();
  }

  /* ═══ نقشهٔ راه ═══ */
  function renderRoadmap() {
    document.getElementById('roadmap').innerHTML = PLUGINS.map((p) => `
      <li>
        <span class="rm-name">${p.name}</span>
        <span class="rm-state">${p.state || 'در دست توسعه'}</span>
        <span class="rm-bar"><i style="width:${p.pct}%"></i></span>
      </li>`).join('');
  }

  /* ═══ شمارنده‌های KPI ═══ */
  function countUp() {
    document.querySelectorAll('.kpi-num').forEach((el) => {
      const target = parseInt(el.dataset.count, 10);
      if (reduceMotion || !gsap) { el.textContent = faNum(target); return; }
      const obj = { v: 0 };
      gsap.to(obj, {
        v: target, duration: 1.6, ease: 'power2.out',
        onUpdate: () => { el.textContent = faNum(Math.round(obj.v)); },
      });
    });
  }

  /* ═══ مودال ═══ */
  const modal = document.getElementById('modal');
  const modalTitle = document.getElementById('modal-title');
  const modalText = document.getElementById('modal-text');
  const modalChip = document.getElementById('modal-chip');
  const modalIcon = document.getElementById('modal-icon');

  function openModal(idx, action) {
    const s = SAMPLES[idx];
    const st = STATUS[action];
    modalTitle.textContent = `${st.title} — ${s.title}`;
    modalText.textContent = st.text;
    modalChip.textContent = st.chip;
    modalIcon.textContent = st.icon;
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
    if (!reduceMotion && gsap) {
      gsap.fromTo('.modal-card', { opacity: 0, scale: 0.9, y: 18 }, { opacity: 1, scale: 1, y: 0, duration: 0.4, ease: 'back.out(1.6)' });
      gsap.fromTo('#modal-backdrop', { opacity: 0 }, { opacity: 1, duration: 0.3 });
    }
  }
  function closeModal() {
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
  }
  document.getElementById('modal-x').addEventListener('click', closeModal);
  document.getElementById('modal-ok').addEventListener('click', closeModal);
  document.getElementById('modal-backdrop').addEventListener('click', closeModal);
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

  /* ═══ رویدادها ═══ */
  tabNav.addEventListener('click', (e) => {
    const btn = e.target.closest('.tab-item');
    if (btn) setTab(btn.dataset.tab);
  });

  grid.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;
    const s = SAMPLES[parseInt(btn.dataset.idx, 10)];
    const action = btn.dataset.action;
    // نمونه‌های دارای لینک واقعی: دمو → پیش‌نمایش زنده، دانلود → فایل
    if (s && action === 'demo' && s.demoUrl) {
      window.open(s.demoUrl, '_blank', 'noopener');
      return;
    }
    if (s && action === 'download' && s.downloadUrl) {
      const a = document.createElement('a');
      a.href = s.downloadUrl;
      a.setAttribute('download', '');
      document.body.appendChild(a);
      a.click();
      a.remove();
      return;
    }
    openModal(parseInt(btn.dataset.idx, 10), action);
  });

  searchEl.addEventListener('input', () => renderGrid(false));

  /* ── سایدبار موبایل ── */
  const sidebar = document.getElementById('sidebar');
  const sideBackdrop = document.getElementById('side-backdrop');
  function closeSidebar() {
    sidebar.classList.remove('open');
    sideBackdrop.classList.remove('show');
  }
  document.getElementById('hamburger').addEventListener('click', () => {
    sidebar.classList.add('open');
    sideBackdrop.classList.add('show');
  });
  sideBackdrop.addEventListener('click', closeSidebar);

  /* ═══ لودر + ورود ═══ */
  function playIntro() {
    if (reduceMotion || !gsap) return;
    gsap.fromTo('.sidebar', { x: 40, opacity: 0 }, { x: 0, opacity: 1, duration: 0.7, ease: 'power3.out' });
    gsap.fromTo('.topbar', { y: -24, opacity: 0 }, { y: 0, opacity: 1, duration: 0.6, ease: 'power3.out', delay: 0.12 });
    gsap.fromTo('.content > *', { opacity: 0, y: 22 }, { opacity: 1, y: 0, duration: 0.6, stagger: 0.1, ease: 'power3.out', delay: 0.22, clearProps: 'transform' });
    gsap.fromTo('.tab-item', { x: 26, opacity: 0 }, { x: 0, opacity: 1, duration: 0.45, stagger: 0.05, ease: 'power3.out', delay: 0.3, clearProps: 'transform' });
  }

  function boot() {
    renderNav();
    renderRoadmap();

    const fill = document.getElementById('loader-fill');
    const prog = { v: 0 };
    const finish = () => {
      const loader = document.getElementById('loader');
      gsap.to(loader, {
        autoAlpha: 0, duration: 0.45, ease: 'power2.inOut',
        onComplete: () => { loader.style.display = 'none'; },
      });
      playIntro();
      countUp();
      ScrollTriggerRefresh();
    };

    if (reduceMotion || !gsap) {
      if (fill) fill.style.transform = 'scaleX(1)';
      document.getElementById('loader').style.display = 'none';
      countUp();
    } else {
      gsap.to(prog, {
        v: 1, duration: 0.85, ease: 'power2.inOut',
        onUpdate: () => { fill.style.transform = `scaleX(${prog.v})`; },
        onComplete: finish,
      });
    }

    // Lenis برای اسکرول نرم محتوا
    if (!reduceMotion && window.Lenis) {
      const lenis = new window.Lenis({ duration: 1.05, smoothWheel: true });
      lenis.on('scroll', ScrollTriggerRefresh);
      gsap.ticker.add((t) => lenis.raf(t * 1000));
      gsap.ticker.lagSmoothing(0);
    }

    // مسیر hash
    const initial = (location.hash || '').replace('#', '');
    setTab(initial || 'overview', true);

    window.addEventListener('hashchange', () => {
      setTab((location.hash || '#overview').replace('#', ''), true);
    });
  }

  function ScrollTriggerRefresh() {
    if (window.ScrollTrigger) window.ScrollTrigger.refresh();
  }

  boot();
})();

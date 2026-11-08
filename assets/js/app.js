/* =============================================================================
   app.js — the atelier dashboard.
   No framework, no build step: this file is the whole runtime. State lives in
   one object, every tab is a pure render function, and the URL is the router.
   ========================================================================== */

import { LOCALES, DEFAULT_LOCALE, translate, localeCoverage, pick } from './i18n.js';
import { COMPONENTS, GROUPS, PLUGINS, CHANGELOG, componentBySlug } from './catalog.js';
import {
  COLOR_GROUPS,
  RADIUS_TOKENS,
  SPACE_TOKENS,
  ELEVATION_TOKENS,
  FONT_FAMILIES,
  TYPE_SCALE,
  SPECIMEN,
  EASINGS,
  DURATIONS,
  TOKEN_COUNT,
} from './system-data.js';

/* -------------------------------------------------------------------------- */
/* state                                                                      */
/* -------------------------------------------------------------------------- */

const STORE = { locale: 'ariagh.locale', theme: 'ariagh.theme' };

const TABS = [
  { id: 'overview', section: 'product', glyph: 'grid' },
  { id: 'components', section: 'product', glyph: 'layers', count: COMPONENTS.length },
  { id: 'tokens', section: 'system', glyph: 'droplet' },
  { id: 'typography', section: 'system', glyph: 'type' },
  { id: 'motion', section: 'system', glyph: 'wave' },
  { id: 'plugins', section: 'roadmap', glyph: 'plug', count: PLUGINS.length },
  { id: 'locales', section: 'roadmap', glyph: 'globe', count: Object.keys(LOCALES).length },
  { id: 'changelog', section: 'roadmap', glyph: 'clock' },
];

const DEVICES = [
  { id: 'desktop', w: 1440, h: 860 },
  { id: 'tablet', w: 834, h: 940 },
  { id: 'mobile', w: 390, h: 760 },
];

const state = {
  tab: 'overview',
  locale: read(STORE.locale, DEFAULT_LOCALE),
  theme: read(STORE.theme, 'dark'),
  group: 'all',
  selected: COMPONENTS[0].slug,
  device: 'desktop',
  dir: null, // null = follow the locale
  fit: true,
};

function read(key, fallback) {
  try {
    return localStorage.getItem(key) ?? fallback;
  } catch {
    return fallback;
  }
}

function write(key, value) {
  try {
    localStorage.setItem(key, value);
  } catch {
    /* private mode — the app still works, it just forgets */
  }
}

/* -------------------------------------------------------------------------- */
/* helpers                                                                    */
/* -------------------------------------------------------------------------- */

const $ = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];

const t = (key) => translate(state.locale, key);
const tx = (value) => pick(value, state.locale);

function esc(value) {
  return String(value).replace(/[&<>"']/g, (c) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
  })[c]);
}

const GLYPHS = {
  grid: '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
  layers: '<path d="M12 3 3 8l9 5 9-5-9-5Z"/><path d="M3 13l9 5 9-5"/>',
  droplet: '<path d="M12 3.5s5.5 5.6 5.5 9.1a5.5 5.5 0 0 1-11 0C6.5 9.1 12 3.5 12 3.5Z"/>',
  type: '<path d="M4 6V4h16v2M12 4v16M9 20h6"/>',
  wave: '<path d="M2 12c2.5-6 4.5-6 7 0s4.5 6 7 0 4.5-6 6-3"/>',
  plug: '<path d="M9 3v5M15 3v5M7 8h10v3a5 5 0 0 1-10 0V8ZM12 16v5"/>',
  globe: '<circle cx="12" cy="12" r="8.5"/><path d="M3.5 12h17M12 3.5c2.4 2.3 3.6 5.2 3.6 8.5S14.4 18.2 12 20.5c-2.4-2.3-3.6-5.2-3.6-8.5S9.6 5.8 12 3.5Z"/>',
  clock: '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
};

const glyph = (name) =>
  `<svg class="navitem__glyph" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${GLYPHS[name] ?? ''}</svg>`;

const MOTIF = {
  hero: '<i class="m-rings"></i><i class="m-orb"></i>',
  about: '<i class="m-panel a"></i><i class="m-panel b"></i>',
  carousel: '<i class="m-rail"><b></b><b class="on"></b><b></b><b></b></i>',
  chrome: '<i class="m-bar"></i><i class="m-bar short"></i><i class="m-block"></i>',
};

const STATUS_BADGE = {
  ready: 'badge--ready',
  wip: 'badge--wip',
  planned: 'badge--planned',
  blocked: 'badge--blocked',
};

let toastTimer;
function toast(message) {
  const el = $('#toast');
  el.textContent = message;
  el.dataset.on = 'true';
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => (el.dataset.on = 'false'), 1800);
}

async function copyText(value, message) {
  try {
    await navigator.clipboard.writeText(value);
    toast(message ?? t('components.copied'));
  } catch {
    toast(value);
  }
}

function panel({ title, note, body, extra = '' }) {
  return `
    <section class="panel">
      ${title ? `<div class="panel__head"><h2 class="panel__title">${esc(title)}</h2>${note ? `<p class="panel__note">${esc(note)}</p>` : ''}</div>${extra}` : ''}
      <div class="panel__body">${body}</div>
    </section>`;
}

/* -------------------------------------------------------------------------- */
/* shell                                                                      */
/* -------------------------------------------------------------------------- */

function renderRail() {
  const sections = [
    ['product', t('nav.section.product')],
    ['system', t('nav.section.system')],
    ['roadmap', t('nav.section.roadmap')],
  ];

  $('#railNav').innerHTML = sections
    .map(([id, label]) => {
      const items = TABS.filter((tab) => tab.section === id);
      if (!items.length) return '';
      return `
        <div class="navgroup">
          <p class="navgroup__label">${esc(label)}</p>
          <ul class="navgroup__list">
            ${items
              .map((tab, i) => {
                const index = TABS.indexOf(tab) + 1;
                const active = state.tab === tab.id;
                return `<li>
                  <a class="navitem" href="#/${tab.id}" ${active ? 'aria-current="page"' : ''} data-tab="${tab.id}">
                    ${glyph(tab.glyph)}
                    <span>${esc(t(`nav.${tab.id}`))}</span>
                    ${tab.count != null ? `<span class="navitem__count">${tab.count}</span>` : `<kbd class="navitem__key">${index}</kbd>`}
                  </a>
                </li>`;
              })
              .join('')}
          </ul>
        </div>`;
    })
    .join('');
}

function renderSegments() {
  $('#localeSeg').innerHTML = Object.values(LOCALES)
    .map((l) => {
      const active = state.locale === l.code;
      const ready = l.coverage > 0;
      return `<button class="seg__btn" type="button" data-locale="${l.code}" aria-pressed="${active}"
        ${ready ? '' : 'disabled'} title="${ready ? esc(l.english) : t('locales.status.pending')}">${esc(l.native)}</button>`;
    })
    .join('');

  $('#themeSeg').innerHTML = ['dark', 'light']
    .map(
      (mode) =>
        `<button class="seg__btn" type="button" data-theme="${mode}" aria-pressed="${state.theme === mode}">${esc(t(`top.theme.${mode}`))}</button>`,
    )
    .join('');
}

function applyDocument() {
  const html = document.documentElement;
  const loc = LOCALES[state.locale] ?? LOCALES[DEFAULT_LOCALE];
  html.lang = loc.code;
  html.dir = loc.dir;
  html.dataset.theme = state.theme;
  document.title = `${tx({ fa: 'آریا قاسمی · کارگاه طراحی', en: 'Aria Ghasemi · Design Atelier' })}`;
  $$('[data-i18n]').forEach((el) => {
    el.textContent = t(el.dataset.i18n);
  });
  $('#crumb').textContent = t(`nav.${state.tab}`);
  $('#buildBadge').textContent = `v${CHANGELOG[0].version}`;
}

/* -------------------------------------------------------------------------- */
/* tabs                                                                       */
/* -------------------------------------------------------------------------- */

function renderOverview() {
  const stats = [
    { value: COMPONENTS.length, label: t('overview.stat.components') },
    { value: PLUGINS.length, label: t('overview.stat.plugins') },
    { value: Object.keys(LOCALES).length, label: t('overview.stat.locales') },
    { value: TOKEN_COUNT, label: t('overview.stat.tokens') },
  ];

  const recent = [...COMPONENTS].sort((a, b) => b.updated.localeCompare(a.updated)).slice(0, 5);
  const next = PLUGINS.filter((p) => p.status !== 'ready').slice(0, 4);

  return `
    <div class="page">
      <header class="page__head">
        <p class="page__eyebrow">${esc(t('overview.eyebrow'))}</p>
        <h1 class="page__title">${esc(t('overview.title'))}</h1>
        <p class="overview__lede">${esc(t('overview.lede'))}</p>
      </header>

      <div class="grid grid--stats">
        ${stats
          .map(
            (s) => `<div class="panel stat">
              <span class="stat__value">${s.value}</span>
              <span class="stat__label">${esc(s.label)}</span>
            </div>`,
          )
          .join('')}
      </div>

      <div class="overview__cols">
        ${panel({
          title: t('overview.recent'),
          note: t('overview.recent.note'),
          body: `<div class="list">${recent
            .map(
              (c) => `<a class="list__row" href="#/components/${c.slug}">
                <span class="list__main">
                  <span class="list__title">${esc(tx(c.name))}</span>
                  <span class="list__meta">${esc(c.version)} · ${esc(c.updated)}</span>
                </span>
                <span class="list__end"><span class="badge ${STATUS_BADGE[c.status]}"><i class="badge__dot"></i>${esc(t(`plugins.status.${c.status}`))}</span></span>
              </a>`,
            )
            .join('')}</div>`,
        })}
        ${panel({
          title: t('overview.next'),
          note: t('overview.next.note'),
          body: `<div class="list">${next
            .map(
              (p) => `<div class="list__row">
                <span class="list__main">
                  <span class="list__title">${esc(tx(p.name))}</span>
                  <span class="list__meta mono">${esc(p.slug)}</span>
                </span>
                <span class="list__end"><span class="badge ${STATUS_BADGE[p.status]}"><i class="badge__dot"></i>${esc(t(`plugins.status.${p.status}`))}</span></span>
              </div>`,
            )
            .join('')}</div>`,
        })}
      </div>
    </div>`;
}

function renderComponents() {
  const current = componentBySlug(state.selected) ?? COMPONENTS[0];
  const visible = state.group === 'all' ? COMPONENTS : COMPONENTS.filter((c) => c.group === state.group);

  return `
    <div class="page">
      <header class="page__head">
        <h1 class="page__title">${esc(t('components.title'))}</h1>
        <p class="page__lede">${esc(t('components.note'))}</p>
      </header>

      ${renderStage(current)}

      <div class="chips" role="group" aria-label="${esc(t('components.title'))}">
        <button class="chip" type="button" data-group="all" aria-pressed="${state.group === 'all'}">${esc(t('components.filterAll'))}</button>
        ${GROUPS.map(
          (g) =>
            `<button class="chip" type="button" data-group="${g.id}" aria-pressed="${state.group === g.id}">${esc(tx(g.name))} <span class="mono">${COMPONENTS.filter((c) => c.group === g.id).length}</span></button>`,
        ).join('')}
      </div>

      <div class="cards">
        ${visible
          .map((c, i) => {
            const selected = c.slug === state.selected;
            return `
            <article class="card" data-slug="${c.slug}" data-selected="${selected}" style="animation-delay:${i * 45}ms">
              <div class="card__thumb" aria-hidden="true">${MOTIF[c.group] ?? ''}</div>
              <div class="card__body">
                <div class="card__top">
                  <h3 class="card__title">${esc(tx(c.name))}</h3>
                  <span class="badge ${STATUS_BADGE[c.status]}" style="margin-inline-start:auto"><i class="badge__dot"></i>${esc(t(`plugins.status.${c.status}`))}</span>
                </div>
                <p class="card__summary">${esc(tx(c.summary))}</p>
                <div class="card__tags">${c.tags.map((tag) => `<span class="tag">${esc(tag)}</span>`).join('')}</div>
              </div>
              <div class="card__foot">
                <button class="btn btn--sm btn--icon" type="button" data-copy="${esc(c.slug)}" title="${esc(t('components.copy'))}" aria-label="${esc(t('components.copy'))}">
                  <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M15 5.5A1.5 1.5 0 0 0 13.5 4h-8A1.5 1.5 0 0 0 4 5.5v8A1.5 1.5 0 0 0 5.5 15"/></svg>
                </button>
                <a class="btn btn--sm btn--icon" href="samples/${esc(c.slug)}/index.html" target="_blank" rel="noreferrer noopener" title="${esc(t('components.newTab'))}" aria-label="${esc(t('components.newTab'))}">
                  <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M14 5h5v5M19 5l-8 8M17 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h4"/></svg>
                </a>
                <a class="btn btn--sm" href="downloads/${esc(c.slug)}.zip" download title="${esc(t('components.download'))}">
                  <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v11m0 0 4-4m-4 4-4-4M5 19h14"/></svg>
                  ${esc(t('components.download'))}
                </a>
                <button class="btn btn--sm btn--accent" type="button" data-select="${esc(c.slug)}">${esc(t('components.show'))}</button>
              </div>
            </article>`;
          })
          .join('')}
      </div>
    </div>`;
}

function renderStage(component) {
  const dir = state.dir ?? (LOCALES[state.locale]?.dir ?? 'rtl');
  return `
    <section class="panel stage" id="stage">
      <div class="stage__bar">
        <div class="stage__name">
          <span>${esc(tx(component.name))}</span>
          <small class="mono">samples/${esc(component.slug)}/</small>
        </div>
        <div class="stage__tools">
          <div class="seg" role="group" aria-label="${esc(t('components.width'))}">
            ${DEVICES.map(
              (d) =>
                `<button class="seg__btn" type="button" data-device="${d.id}" aria-pressed="${state.device === d.id}">${d.id === 'desktop' ? '1440' : d.id === 'tablet' ? '834' : '390'}</button>`,
            ).join('')}
          </div>
          <div class="seg" role="group" aria-label="${esc(t('components.direction'))}">
            <button class="seg__btn" type="button" data-dir="rtl" aria-pressed="${dir === 'rtl'}">RTL</button>
            <button class="seg__btn" type="button" data-dir="ltr" aria-pressed="${dir === 'ltr'}">LTR</button>
          </div>
          <button class="btn btn--sm btn--ghost" type="button" data-fit aria-pressed="${state.fit}">
            ${esc(state.fit ? t('components.fit') : t('components.actual'))}
          </button>
          <button class="btn btn--sm btn--icon" type="button" data-reload title="${esc(t('components.reload'))}" aria-label="${esc(t('components.reload'))}">
            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M20 11a8 8 0 1 0-2.3 5.7M20 5v6h-6"/></svg>
          </button>
          <a class="btn btn--sm btn--icon" href="samples/${esc(component.slug)}/index.html" target="_blank" rel="noreferrer noopener" title="${esc(t('components.newTab'))}" aria-label="${esc(t('components.newTab'))}">
            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M14 5h5v5M19 5l-8 8M17 14v4a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h4"/></svg>
          </a>
        </div>
      </div>
      <div class="stage__viewport" id="viewport">
        <div class="stage__canvas" id="canvas">
          <iframe id="frame" title="${esc(tx(component.name))}"></iframe>
        </div>
        <div class="stage__loading" id="stageLoading"><span class="spinner"></span></div>
      </div>
    </section>`;
}

function renderTokens() {
  return `
    <div class="page">
      <header class="page__head">
        <h1 class="page__title">${esc(t('tokens.title'))}</h1>
        <p class="page__lede">${esc(t('tokens.note'))}</p>
      </header>

      ${COLOR_GROUPS.map((group) =>
        panel({
          title: tx(group.name),
          note: tx(group.note),
          body: `<div class="swatchgrid">${group.swatches
            .map(
              (s) => `<button class="swatch" type="button" data-token="${esc(s.token)}">
                <span class="swatch__chip" style="background:var(${esc(s.token)})"></span>
                <span class="swatch__body">
                  <span class="swatch__token">${esc(s.token)}</span>
                  <span class="swatch__role">${esc(tx(s.role))}</span>
                </span>
              </button>`,
            )
            .join('')}</div>`,
        }),
      ).join('')}

      <div class="grid grid--two">
        ${panel({
          title: t('tokens.radius'),
          note: t('tokens.radius.note'),
          body: `<div class="rows">${RADIUS_TOKENS.map(
            (r) => `<button class="row" type="button" data-token="${esc(r.token)}">
              <span class="row__token">${esc(r.token)}</span>
              <span class="row__value">${esc(r.value)}</span>
              <span class="row__use">${esc(tx(r.use))}</span>
              <span class="row__demo"><span style="width:34px;height:22px;background:var(--surface-3);border:1px solid var(--brass-line);border-radius:var(${esc(r.token)})"></span></span>
            </button>`,
          ).join('')}</div>`,
        })}
        ${panel({
          title: t('tokens.space'),
          note: t('tokens.space.note'),
          body: `<div class="rows">${SPACE_TOKENS.map(
            (s) => `<button class="row" type="button" data-token="${esc(s.token)}">
              <span class="row__token">${esc(s.token)}</span>
              <span class="row__value">${esc(s.value)}</span>
              <span class="row__demo"><span class="row__space" style="width:var(${esc(s.token)})"></span></span>
            </button>`,
          ).join('')}</div>`,
        })}
      </div>

      ${panel({
        title: t('tokens.elevation'),
        note: t('tokens.elevation.note'),
        body: `<div class="rows">${ELEVATION_TOKENS.map(
          (e) => `<button class="row" type="button" data-token="${esc(e.token)}">
            <span class="row__token">${esc(e.token)}</span>
            <span class="row__use">${esc(tx(e.use))}</span>
            <span class="row__demo"><span class="row__shadow" style="box-shadow:var(${esc(e.token)})"></span></span>
          </button>`,
        ).join('')}</div>`,
      })}
    </div>`;
}

function renderTypography() {
  return `
    <div class="page">
      <header class="page__head">
        <h1 class="page__title">${esc(t('typography.title'))}</h1>
        <p class="page__lede">${esc(t('typography.note'))}</p>
      </header>

      ${FONT_FAMILIES.map((f) => {
        const spec = tx(f.specimen);
        return panel({
          title: '',
          body: `<div class="fam">
            <div class="fam__head">
              <h2 class="fam__name" style="font-family:${esc(f.css)}">${esc(f.name)}</h2>
              <p class="fam__role">${esc(tx(f.role))}</p>
            </div>
            <p class="fam__specimen" data-font="${f.name === 'Instrument Serif' ? 'serif' : 'sans'}" style="font-family:${esc(f.css)}" dir="${/[\u0600-\u06FF]/.test(spec) ? 'rtl' : 'ltr'}" lang="${/[\u0600-\u06FF]/.test(spec) ? 'fa' : 'en'}">${esc(spec)}</p>
            <div class="fam__meta">
              <span class="mono">${esc(f.css)}</span>
              <span>${esc(t('typography.weights'))}: ${esc(f.weights)}</span>
            </div>
            <div class="fam__rules">
              <div class="rulecard rulecard--do"><p class="rulecard__head">${esc(t('typography.do'))}</p><p class="rulecard__body">${esc(tx(f.do))}</p></div>
              <div class="rulecard rulecard--dont"><p class="rulecard__head">${esc(t('typography.dont'))}</p><p class="rulecard__body">${esc(tx(f.dont))}</p></div>
            </div>
          </div>`,
        });
      }).join('')}

      ${panel({
        title: t('typography.weights'),
        note: t('typography.weights.note'),
        body: `<div class="weights">${[200, 300, 400, 500, 600, 700, 800]
          .map((w) => `<span style="font-weight:${w}">${tx({ fa: 'وزن', en: 'Weight' })} ${w}</span>`)
          .join('')}</div>`,
      })}

      ${panel({
        title: t('typography.scale'),
        note: t('typography.scale.note'),
        body: `<div class="rows">${TYPE_SCALE.map(
          (s) => `<div class="row">
            <span class="row__token">${esc(s.token)}</span>
            <span class="row__use" style="flex:1;font-size:var(${esc(s.token)});color:var(--text);line-height:var(--lh-heading)">${esc(tx(s.sample))}</span>
            <span class="row__value" style="margin-inline-start:auto">${esc(tx(s.use))}</span>
          </div>`,
        ).join('')}</div>`,
      })}

      ${panel({
        title: t('typography.specimen'),
        note: t('typography.specimen.note'),
        body: `<div class="specimen">${SPECIMEN.map(
          (s) => `<div class="specimen__line" dir="${s.dir}" lang="${s.lang}">
            <span class="specimen__label">${esc(s.label)} · ${s.dir.toUpperCase()}</span>
            <p class="specimen__text">${esc(s.text)}</p>
          </div>`,
        ).join('')}</div>`,
      })}
    </div>`;
}

function renderMotion() {
  return `
    <div class="page">
      <header class="page__head">
        <h1 class="page__title">${esc(t('motion.title'))}</h1>
        <p class="page__lede">${esc(t('motion.note'))}</p>
      </header>

      ${panel({
        title: t('motion.easing'),
        note: t('motion.easing.note'),
        body: `<div class="curves">${EASINGS.map(
          (e) => `<button class="curve" type="button" data-ease="${esc(e.value)}">
            <span class="curve__track"><i class="curve__dot"><b></b></i></span>
            <span class="curve__name">${esc(e.token)}</span>
            <span class="curve__value">${esc(e.value)}</span>
            <span class="curve__use">${esc(tx(e.use))}</span>
            <span class="curve__note">${esc(tx(e.note))}</span>
          </button>`,
        ).join('')}</div>`,
      })}

      ${panel({
        title: t('motion.duration'),
        note: t('motion.duration.note'),
        body: `<div class="rows">${DURATIONS.map(
          (d) => `<div class="row">
            <span class="row__token">${esc(d.token)}</span>
            <span class="row__value">${esc(d.value)}</span>
            <span class="row__use">${esc(tx(d.use))}</span>
            <span class="row__demo"><span class="row__space" style="width:${Math.max(10, parseInt(d.value, 10) / 12)}px"></span></span>
          </div>`,
        ).join('')}</div>`,
      })}

      <h2 class="page__title" style="font-size:var(--fs-h2)">${esc(t('motion.principles'))}</h2>
      <div class="principles">
        ${[1, 2, 3, 4]
          .map(
            (n) => `<article class="panel principle">
              <span class="principle__n">${n}</span>
              <h3 class="principle__title">${esc(t(`motion.principle.${n}.title`))}</h3>
              <p class="principle__body">${esc(t(`motion.principle.${n}.body`))}</p>
            </article>`,
          )
          .join('')}
      </div>
    </div>`;
}

function renderPlugins() {
  return `
    <div class="page">
      <header class="page__head">
        <h1 class="page__title">${esc(t('plugins.title'))}</h1>
        <p class="page__lede">${esc(t('plugins.note'))}</p>
      </header>

      ${panel({
        title: '',
        body: `<div class="tablewrap"><table class="table">
          <thead>
            <tr>
              <th>${esc(t('plugins.plugin'))}</th>
              <th>${esc(t('plugins.purpose'))}</th>
              <th>${esc(t('plugins.widgets'))}</th>
              <th>${esc(t('plugins.status'))}</th>
              <th>${esc(t('plugins.version'))}</th>
            </tr>
          </thead>
          <tbody>
            ${PLUGINS.map(
              (p) => `<tr>
                <td><div class="table__name">${esc(tx(p.name))}</div><div class="table__slug">${esc(p.slug)}</div></td>
                <td class="table__purpose">${esc(tx(p.purpose))}</td>
                <td>${p.widgets.map((w) => `<span class="tag">${esc(tx(w))}</span>`).join(' ')}</td>
                <td><span class="badge ${STATUS_BADGE[p.status]}"><i class="badge__dot"></i>${esc(t(`plugins.status.${p.status}`))}</span></td>
                <td class="mono">${esc(p.version)}</td>
              </tr>`,
            ).join('')}
          </tbody>
        </table></div>`,
      })}
    </div>`;
}

function renderLocales() {
  return `
    <div class="page">
      <header class="page__head">
        <h1 class="page__title">${esc(t('locales.title'))}</h1>
        <p class="page__lede">${esc(t('locales.note'))}</p>
      </header>

      ${panel({
        title: '',
        body: `<div class="tablewrap"><table class="table">
          <thead>
            <tr>
              <th>${esc(t('locales.language'))}</th>
              <th>${esc(t('locales.direction'))}</th>
              <th>${esc(t('locales.font'))}</th>
              <th>${esc(t('locales.coverage'))}</th>
              <th>${esc(t('locales.status'))}</th>
            </tr>
          </thead>
          <tbody>
            ${Object.values(LOCALES)
              .map((l) => {
                const cover = localeCoverage(l.code);
                return `<tr>
                  <td><div class="table__name" dir="${l.dir}" lang="${l.code}">${esc(l.native)}</div><div class="table__slug">${esc(l.english)} · ${esc(l.code)}</div></td>
                  <td><span class="dirflag">${esc(l.dir.toUpperCase())}</span> <span class="row__use">${esc(l.dir === 'rtl' ? t('locales.rtl') : t('locales.ltr'))}</span></td>
                  <td class="mono">${l.code === 'en' ? 'Instrument Serif + Vazirmatn' : 'Vazirmatn'}</td>
                  <td>
                    <div class="coverage">
                      <span class="coverage__bar"><span class="coverage__fill" style="width:${cover}%"></span></span>
                      <span class="coverage__num">${cover}%</span>
                    </div>
                  </td>
                  <td><span class="badge ${cover > 0 ? 'badge--ready' : 'badge--planned'}"><i class="badge__dot"></i>${esc(cover > 0 ? t('locales.status.live') : t('locales.status.pending'))}</span></td>
                </tr>`;
              })
              .join('')}
          </tbody>
        </table></div>`,
      })}
    </div>`;
}

function renderChangelog() {
  return `
    <div class="page">
      <header class="page__head">
        <h1 class="page__title">${esc(t('changelog.title'))}</h1>
        <p class="page__lede">${esc(t('changelog.note'))}</p>
      </header>

      <div class="log">
        ${CHANGELOG.map(
          (r) => `<article class="panel" style="padding:var(--sp-5)">
            <div class="release">
              <div class="release__side">
                <span class="release__version">${esc(r.version)}</span>
                <span class="release__date">${esc(r.date)}</span>
              </div>
              <div class="release__list">
                ${r.entries
                  .map(
                    (e) => `<p class="release__item">
                      <span class="kind kind--${esc(e.kind)}">${esc(t(`changelog.kind.${e.kind}`))}</span>
                      <span>${esc(tx(e.text))}</span>
                    </p>`,
                  )
                  .join('')}
              </div>
            </div>
          </article>`,
        ).join('')}
      </div>
    </div>`;
}

const RENDERERS = {
  overview: renderOverview,
  components: renderComponents,
  tokens: renderTokens,
  typography: renderTypography,
  motion: renderMotion,
  plugins: renderPlugins,
  locales: renderLocales,
  changelog: renderChangelog,
};

/* -------------------------------------------------------------------------- */
/* preview stage                                                              */
/* -------------------------------------------------------------------------- */

let frameSignature = '';

function syncStage() {
  const frame = $('#frame');
  const viewport = $('#viewport');
  const canvas = $('#canvas');
  if (!frame || !viewport || !canvas) return;

  const device = DEVICES.find((d) => d.id === state.device) ?? DEVICES[0];
  const dir = state.dir ?? (LOCALES[state.locale]?.dir ?? 'rtl');
  const component = componentBySlug(state.selected) ?? COMPONENTS[0];
  const src = `samples/${component.slug}/index.html?dir=${dir}&lang=${state.locale}&theme=${state.theme}`;
  const signature = `${src}|${device.id}`;

  if (signature !== frameSignature || frame.dataset.sig !== signature) {
    frameSignature = signature;
    frame.dataset.sig = signature;
    const loading = $('#stageLoading');
    if (loading) loading.dataset.on = 'true';
    frame.onload = () => loading && (loading.dataset.on = 'false');
    frame.src = src;
  }

  canvas.style.width = `${device.w}px`;
  canvas.style.height = `${device.h}px`;

  const available = viewport.clientWidth - 2;
  const zoom = state.fit && available > 0 ? Math.min(1, available / device.w) : 1;
  canvas.style.transform = `scale(${zoom})`;
  viewport.style.height = `${Math.round(device.h * zoom)}px`;
}

/* -------------------------------------------------------------------------- */
/* routing + render                                                           */
/* -------------------------------------------------------------------------- */

function parseHash() {
  const raw = location.hash.replace(/^#\/?/, '');
  const [tab, slug] = raw.split('/');
  return { tab: TABS.some((x) => x.id === tab) ? tab : 'overview', slug };
}

function render() {
  applyDocument();
  renderRail();
  renderSegments();
  $('#view').innerHTML = (RENDERERS[state.tab] ?? renderOverview)();
  if (state.tab === 'components') syncStage();
  else frameSignature = '';
}

function navigate(hash) {
  if (location.hash === hash) render();
  else location.hash = hash;
  $('#rail')?.removeAttribute('data-open');
  $('.scrim')?.remove();
}

/* -------------------------------------------------------------------------- */
/* events                                                                     */
/* -------------------------------------------------------------------------- */

function onHashChange() {
  const { tab, slug } = parseHash();
  state.tab = tab;
  if (slug && componentBySlug(slug)) state.selected = slug;
  render();
  $('#view').scrollTop = 0;
  window.scrollTo({ top: 0 });
}

function bindEvents() {
  window.addEventListener('hashchange', onHashChange);

  document.addEventListener('click', (event) => {
    const target = event.target;

    const nav = target.closest('[data-tab]');
    if (nav) {
      event.preventDefault();
      navigate(`#/${nav.dataset.tab}`);
      return;
    }

    const localeBtn = target.closest('[data-locale]');
    if (localeBtn && !localeBtn.disabled) {
      state.locale = localeBtn.dataset.locale;
      state.dir = null;
      write(STORE.locale, state.locale);
      render();
      return;
    }

    const themeBtn = target.closest('[data-theme]');
    if (themeBtn) {
      state.theme = themeBtn.dataset.theme;
      write(STORE.theme, state.theme);
      render();
      return;
    }

    const groupBtn = target.closest('[data-group]');
    if (groupBtn) {
      state.group = groupBtn.dataset.group;
      render();
      return;
    }

    const selectBtn = target.closest('[data-select]');
    if (selectBtn) {
      state.selected = selectBtn.dataset.select;
      render();
      $('#stage')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      return;
    }

    const copyBtn = target.closest('[data-copy]');
    if (copyBtn) {
      copyText(`samples/${copyBtn.dataset.copy}/index.html`, t('components.copied'));
      return;
    }

    const deviceBtn = target.closest('[data-device]');
    if (deviceBtn) {
      state.device = deviceBtn.dataset.device;
      render();
      return;
    }

    const dirBtn = target.closest('[data-dir]');
    if (dirBtn) {
      state.dir = dirBtn.dataset.dir;
      render();
      return;
    }

    const fitBtn = target.closest('[data-fit]');
    if (fitBtn) {
      state.fit = !state.fit;
      render();
      return;
    }

    const reloadBtn = target.closest('[data-reload]');
    if (reloadBtn) {
      frameSignature = '';
      syncStage();
      return;
    }

    const tokenBtn = target.closest('[data-token]');
    if (tokenBtn) {
      copyText(`var(${tokenBtn.dataset.token})`);
      return;
    }

    const curve = target.closest('[data-ease]');
    if (curve) {
      runCurve(curve);
      return;
    }

    if (target.closest('.scrim')) {
      $('#rail')?.removeAttribute('data-open');
      $('.scrim')?.remove();
    }
  });

  $('#railToggle').addEventListener('click', () => {
    const rail = $('#rail');
    const open = rail.dataset.open === 'true';
    if (open) {
      rail.removeAttribute('data-open');
      $('.scrim')?.remove();
    } else {
      rail.dataset.open = 'true';
      const scrim = document.createElement('button');
      scrim.className = 'scrim';
      scrim.setAttribute('aria-label', 'بستن');
      document.body.append(scrim);
    }
    $('#railToggle').setAttribute('aria-expanded', String(!open));
  });

  // 1–8 jump straight to a tab
  document.addEventListener('keydown', (event) => {
    if (event.metaKey || event.ctrlKey || event.altKey) return;
    const tag = event.target.tagName;
    if (tag === 'INPUT' || tag === 'TEXTAREA' || event.target.isContentEditable) return;
    const n = Number(event.key);
    if (Number.isInteger(n) && n >= 1 && n <= TABS.length) navigate(`#/${TABS[n - 1].id}`);
  });

  const ro = new ResizeObserver(() => {
    if (state.tab === 'components') syncStage();
  });
  ro.observe($('#view'));
}

function runCurve(card) {
  const track = $('.curve__track', card);
  const dot = $('.curve__dot', card);
  const bead = $('b', dot);
  if (!track || !dot) return;

  const travel = Math.max(0, track.clientWidth - 12);
  const rise = Math.max(0, track.clientHeight - 12);
  const sign = document.documentElement.dir === 'rtl' ? -1 : 1;
  const duration = 1100;
  const options = { duration, fill: 'forwards' };

  dot.getAnimations().forEach((a) => a.cancel());
  bead.getAnimations().forEach((a) => a.cancel());

  dot.animate(
    [{ transform: 'translateX(0)' }, { transform: `translateX(${sign * travel}px)` }],
    { ...options, easing: 'linear' },
  );
  bead.animate([{ transform: 'translateY(0)' }, { transform: `translateY(${-rise}px)` }], {
    ...options,
    easing: card.dataset.ease,
  });
}

/* -------------------------------------------------------------------------- */
/* boot                                                                       */
/* -------------------------------------------------------------------------- */

function boot() {
  if (!LOCALES[state.locale]) state.locale = DEFAULT_LOCALE;
  const { tab, slug } = parseHash();
  state.tab = tab;
  if (slug && componentBySlug(slug)) state.selected = slug;

  bindEvents();
  render();

  // Curves play once on arrival — one orchestrated moment, not a loop.
  if (state.tab === 'motion') requestAnimationFrame(() => $$('.curve').forEach(runCurve));
}

boot();

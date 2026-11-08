/* =============================================================================
   sample-shell.js — the runtime every sample shares.
   Reads the preview query string (?dir= ?theme= ?motion=) so the dashboard can
   drive a sample from outside, and hands each sample a few small helpers.
   Nothing here is WordPress-specific: the same helpers ship inside the plugins.
   ========================================================================== */

(function () {
  const params = new URLSearchParams(location.search);
  const html = document.documentElement;

  const dir = params.get('dir');
  if (dir === 'rtl' || dir === 'ltr') html.dir = dir;

  const theme = params.get('theme');
  if (theme === 'light' || theme === 'dark') html.dataset.theme = theme;

  // Samples ship fa + en content today. Anything else falls back to Persian
  // rather than rendering an empty page.
  const WITH_CONTENT = ['fa', 'en'];
  const lang = params.get('lang');
  if (WITH_CONTENT.includes(lang)) html.lang = lang;
  else if (lang) html.lang = 'fa';

  // "motion=off" lets the dashboard freeze a sample for a stable screenshot.
  const forced = params.get('motion');
  const query = window.matchMedia('(prefers-reduced-motion: reduce)');
  const reduced = forced === 'off' ? true : forced === 'on' ? false : query.matches;

  html.dataset.motion = reduced ? 'reduced' : 'full';

  const clamp = (n, min, max) => Math.min(max, Math.max(min, n));
  const lerp = (a, b, n) => a + (b - a) * n;

  window.AriSample = {
    reduced,
    params,
    clamp,
    lerp,
    gsap: window.gsap ?? null,

    /** Numbers that animate once when an element first scrolls into view. */
    countUp(el, to, { duration = 1400, suffix = '', prefix = '' } = {}) {
      const render = (value) => {
        el.textContent = prefix + Math.round(value).toLocaleString(html.lang || 'en') + suffix;
      };
      if (reduced) {
        render(to);
        return;
      }
      const start = performance.now();
      const tick = (now) => {
        const n = clamp((now - start) / duration, 0, 1);
        const eased = 1 - Math.pow(1 - n, 3);
        render(to * eased);
        if (n < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    },

    /**
     * Pointer parallax. Writes --px / --py (-1 … 1) onto `target` and eases
     * them every frame, so CSS can do the actual moving.
     */
    pointer(zone, target, { strength = 1, onFrame } = {}) {
      if (reduced) return () => {};
      let tx = 0;
      let ty = 0;
      let cx = 0;
      let cy = 0;
      let raf = 0;

      const read = (event) => {
        const rect = zone.getBoundingClientRect();
        const point = event.touches ? event.touches[0] : event;
        tx = clamp(((point.clientX - rect.left) / rect.width) * 2 - 1, -1, 1) * strength;
        ty = clamp(((point.clientY - rect.top) / rect.height) * 2 - 1, -1, 1) * strength;
      };

      const frame = () => {
        cx = lerp(cx, tx, 0.06);
        cy = lerp(cy, ty, 0.06);
        target.style.setProperty('--px', cx.toFixed(4));
        target.style.setProperty('--py', cy.toFixed(4));
        onFrame?.(cx, cy);
        raf = requestAnimationFrame(frame);
      };

      zone.addEventListener('pointermove', read, { passive: true });
      zone.addEventListener('pointerleave', () => {
        tx = 0;
        ty = 0;
      });
      zone.addEventListener(
        'touchmove',
        (e) => {
          read(e);
        },
        { passive: true },
      );
      raf = requestAnimationFrame(frame);

      return () => {
        cancelAnimationFrame(raf);
        zone.removeEventListener('pointermove', read);
      };
    },

    /** Runs `enter` once per element as it scrolls in. Takes a selector, an
     *  element, or a list of elements. */
    reveal(target, enter, { threshold = 0.2, root = null } = {}) {
      const items =
        typeof target === 'string'
          ? [...document.querySelectorAll(target)]
          : target instanceof Element
            ? [target]
            : [...(target ?? [])];
      if (!items.length) return;
      if (reduced || typeof IntersectionObserver === 'undefined') {
        items.forEach((el) => enter(el, 0));
        return;
      }
      const io = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            enter(entry.target, items.indexOf(entry.target));
            io.unobserve(entry.target);
          });
        },
        { threshold, rootMargin: '0px 0px -8% 0px', root },
      );
      items.forEach((el) => io.observe(el));
    },
  };
})();

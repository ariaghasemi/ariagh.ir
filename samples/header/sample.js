/* =============================================================================
   header — condensing on scroll, magnetic underline, mega menu with keyboard
   support, and a language switch that re-points the whole document direction.
   ========================================================================== */

(function () {
  const S = window.AriSample;

  /* ---- condense ---------------------------------------------------------- */
  const header = document.getElementById('header');
  let condensed = false;

  const onScroll = () => {
    const next = (window.scrollY || document.documentElement.scrollTop) > 40;
    if (next === condensed) return;
    condensed = next;
    header.dataset.condensed = String(next);
  };

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---- magnetic underline ------------------------------------------------ */
  const nav = document.getElementById('nav');
  const ink = document.getElementById('ink');
  const items = nav ? [...nav.querySelectorAll('.nav__item')] : [];

  const moveInk = (item) => {
    if (!ink || !item) return;
    const navRect = nav.getBoundingClientRect();
    const rect = item.getBoundingClientRect();
    const x = rect.left - navRect.left;
    ink.style.width = `${rect.width - 24}px`;
    ink.style.opacity = '1';
    // RTL: the element is laid out from the right, so translate from the left
    // edge works in both directions because we measure real client rects.
    ink.style.transform = `translateX(${x + 12}px)`;
  };

  const hideInk = () => ink && (ink.style.opacity = '0');

  items.forEach((item) => {
    item.addEventListener('pointerenter', () => moveInk(item));
    item.addEventListener('focus', () => moveInk(item));
  });
  nav?.addEventListener('pointerleave', hideInk);
  nav?.addEventListener('focusout', hideInk);
  window.addEventListener('resize', hideInk);
  window.addEventListener('scroll', hideInk, { passive: true });

  /* ---- mega menu --------------------------------------------------------- */
  const mega = document.getElementById('mega');
  const panels = mega ? [...mega.querySelectorAll('.mega__panel')] : [];
  let closeTimer = 0;

  const openMega = (name) => {
    clearTimeout(closeTimer);
    let found = false;
    panels.forEach((panel) => {
      const on = panel.dataset.panel === name;
      panel.dataset.visible = String(on);
      if (on) found = true;
    });
    if (!found) return;
    mega.dataset.open = 'true';
    mega.setAttribute('aria-hidden', 'false');
    items.forEach((i) => i.setAttribute('aria-expanded', String(i.dataset.mega === name)));
  };

  const closeMega = () => {
    clearTimeout(closeTimer);
    // a short grace period so moving the pointer from the item to the panel
    // does not feel like walking off a cliff
    closeTimer = setTimeout(() => {
      mega.dataset.open = 'false';
      mega.setAttribute('aria-hidden', 'true');
      items.forEach((i) => i.removeAttribute('aria-expanded'));
    }, 140);
  };

  items.forEach((item) => {
    if (!item.dataset.mega) return;
    item.addEventListener('pointerenter', () => openMega(item.dataset.mega));
    item.addEventListener('focus', () => openMega(item.dataset.mega));
    item.addEventListener('click', (e) => {
      e.preventDefault();
      mega.dataset.open === 'true' ? closeMega() : openMega(item.dataset.mega);
    });
  });

  mega?.addEventListener('pointerenter', () => clearTimeout(closeTimer));
  mega?.addEventListener('pointerleave', closeMega);
  mega?.addEventListener('focusout', (e) => {
    if (!mega.contains(e.relatedTarget)) closeMega();
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMega();
  });

  /* ---- language switch --------------------------------------------------- */
  const DIRS = { fa: 'rtl', en: 'ltr', ar: 'rtl' };
  const buttons = [...document.querySelectorAll('#langsw button')];

  buttons.forEach((button) => {
    button.addEventListener('click', () => {
      const code = button.dataset.langCode;
      buttons.forEach((b) => b.setAttribute('aria-pressed', String(b === button)));
      document.documentElement.lang = code;
      document.documentElement.dir = DIRS[code] ?? 'ltr';
      // re-measure the underline after the layout flips
      requestAnimationFrame(hideInk);
    });
  });

  /* ---- entrance ---------------------------------------------------------- */
  if (!S.reduced && window.gsap) {
    gsap.fromTo(
      '.sitehead__inner > *',
      { opacity: 0, y: -12 },
      { opacity: 1, y: 0, duration: 0.8, stagger: 0.08, ease: 'expo.out' },
    );
    gsap.fromTo(
      '.headbody__hero > *',
      { opacity: 0, y: 22 },
      { opacity: 1, y: 0, duration: 0.9, stagger: 0.1, delay: 0.25, ease: 'expo.out' },
    );
  }
})();

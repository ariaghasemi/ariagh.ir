/* =============================================================================
   footer — newsletter validation, locale switch, back to top.
   ========================================================================== */

(function () {
  const S = window.AriSample;

  /* ---- newsletter -------------------------------------------------------- */
  const form = document.getElementById('news');
  const msg = document.getElementById('newsMsg');

  form?.addEventListener('submit', (event) => {
    event.preventDefault();
    const input = form.querySelector('input');
    const value = input.value.trim();
    const ok = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value);

    msg.dataset.error = String(!ok);
    msg.textContent = ok
      ? document.documentElement.lang === 'fa'
        ? 'عضو شدید. یک نامه در ماه، نه بیشتر.'
        : 'You are in. One letter a month, no more.'
      : document.documentElement.lang === 'fa'
        ? 'این نشانی کامل نیست — یک رایانامهٔ معتبر بنویسید.'
        : 'That address is not complete — enter a valid email.';

    if (ok) form.reset();

    if (!S.reduced && window.gsap) {
      gsap.fromTo(msg, { opacity: 0, y: 6 }, { opacity: 1, y: 0, duration: 0.4, ease: 'power2.out' });
      if (!ok) gsap.fromTo(form, { x: -6 }, { x: 0, duration: 0.45, ease: 'elastic.out(1, 0.35)' });
    }
  });

  /* ---- locale ------------------------------------------------------------ */
  const DIRS = { fa: 'rtl', en: 'ltr', ar: 'rtl' };
  const buttons = [...document.querySelectorAll('.sitefoot__lang button')];

  buttons.forEach((button) => {
    button.addEventListener('click', () => {
      buttons.forEach((b) => b.setAttribute('aria-pressed', String(b === button)));
      document.documentElement.lang = button.dataset.langCode;
      document.documentElement.dir = DIRS[button.dataset.langCode] ?? 'ltr';
    });
  });

  /* ---- back to top ------------------------------------------------------- */
  document.getElementById('toTop')?.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: S.reduced ? 'auto' : 'smooth' });
  });

  /* ---- entrance ---------------------------------------------------------- */
  if (!S.reduced && window.gsap) {
    if (window.ScrollTrigger) gsap.registerPlugin(ScrollTrigger);

    gsap.fromTo(
      '.sitefoot__rule',
      { scaleX: 0, transformOrigin: 'center' },
      { scaleX: 1, duration: 1.2, ease: 'expo.out' },
    );

    if (window.ScrollTrigger) {
      gsap.fromTo(
        ['.sitefoot__top > *', '.sitefoot__map > div'].join(','),
        { opacity: 0, y: 24 },
        {
          opacity: 1,
          y: 0,
          duration: 0.85,
          stagger: 0.07,
          ease: 'expo.out',
          scrollTrigger: { trigger: '.sitefoot', start: 'top 92%' },
        },
      );

      gsap.fromTo(
        '.sitefoot__wordmark span',
        { yPercent: 30, opacity: 0 },
        {
          yPercent: 0,
          opacity: 1,
          duration: 1.2,
          ease: 'expo.out',
          scrollTrigger: { trigger: '.sitefoot__wordmark', start: 'top 96%' },
        },
      );
    }
  }
})();

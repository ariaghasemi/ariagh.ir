/* =============================================================================
   about-motion — reveals on enter, bars fill once, signature draws itself.
   ========================================================================== */

(function () {
  const S = window.AriSample;

  const drawSignature = () => {
    document.querySelectorAll('.sign path').forEach((path) => {
      const len = Math.ceil(path.getTotalLength());
      path.style.setProperty('--len', len);
      if (S.reduced || !window.gsap) {
        path.style.strokeDashoffset = 0;
        return;
      }
      gsap.fromTo(
        path,
        { strokeDashoffset: len },
        { strokeDashoffset: 0, duration: 1.4, ease: 'power2.out' },
      );
    });
  };

  if (S.reduced || !window.gsap) {
    document.querySelectorAll('[data-count]').forEach((el) => (el.textContent = el.dataset.count));
    document.querySelectorAll('.skills__bar b').forEach((b) => (b.style.width = b.style.getPropertyValue('--v')));
    drawSignature();
    return;
  }

  document.documentElement.classList.add('js-anim');
  gsap.defaults({ ease: 'expo.out' });
  if (window.ScrollTrigger) gsap.registerPlugin(ScrollTrigger);

  gsap.to('.js-anim .aboutstory__aside > *', {
    opacity: 1,
    y: 0,
    duration: 0.9,
    stagger: 0.12,
    startAt: { y: 20 },
    delay: 0.1,
  });

  gsap.to(['.aboutstory__main > .s-kicker', '.aboutstory__title'].join(','), {
    opacity: 1,
    y: 0,
    duration: 0.9,
    stagger: 0.1,
    startAt: { y: 18 },
    delay: 0.15,
  });

  // Lines rise the first time they enter view — not on a timer.
  S.reveal('.line i', (el, i) => {
    gsap.to(el, { yPercent: 0, duration: 1, delay: i * 0.06 });
  });

  // Bars and numbers fill once on screen; the number runs with the bar.
  document.querySelectorAll('.skills li').forEach((li) => {
    const bar = li.querySelector('.skills__bar b');
    const num = li.querySelector('.skills__num');
    const target = bar?.style.getPropertyValue('--v') ?? '0%';
    const value = Number(target.replace('%', ''));

    S.reveal(li, () => {
      gsap.to(bar, { width: target, duration: 1.3, ease: 'expo.out' });
      gsap.to(num, { opacity: 1, duration: 0.4 });
      S.countUp(num, value, { duration: 1300, suffix: '' });
    });
  });

  S.reveal('.sign', () => drawSignature());
})();

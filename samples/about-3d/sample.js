/* =============================================================================
   about-3d — the box opens on scroll, tilts with the pointer, facts count once.
   ========================================================================== */

(function () {
  const S = window.AriSample;
  const stage = document.getElementById('stage');
  const box = document.getElementById('box');
  if (!stage || !box) return;

  S.pointer(stage, stage);

  const counters = [...document.querySelectorAll('[data-count]')];
  const run = () => counters.forEach((el) => S.countUp(el, Number(el.dataset.count), { duration: 1400 }));

  if (S.reduced || !window.gsap) {
    box.style.setProperty('--open', 1);
    run();
    return;
  }

  document.documentElement.classList.add('js-anim');
  gsap.defaults({ ease: 'expo.out' });

  if (window.ScrollTrigger) gsap.registerPlugin(ScrollTrigger);

  // the panels fan open as the stage travels through the viewport
  if (window.ScrollTrigger) {
    gsap.to(box, {
      '--open': 1,
      ease: 'none',
      scrollTrigger: { trigger: stage, start: 'top 88%', end: 'center 42%', scrub: 0.6 },
    });
  } else {
    box.style.setProperty('--open', 1);
  }

  gsap.fromTo(
    '.about3d__text > *',
    { opacity: 0, y: 22 },
    { opacity: 1, y: 0, duration: 0.9, stagger: 0.1, delay: 0.15 },
  );

  gsap.fromTo(
    '.chip3d',
    { opacity: 0, y: 20, scale: 0.9 },
    { opacity: 1, y: 0, scale: 1, duration: 0.9, stagger: 0.12, delay: 0.7, onStart: run },
  );

  // a slow idle float so the scene is never completely still
  gsap.to('.chip3d', {
    yPercent: -4,
    duration: 3.2,
    ease: 'sine.inOut',
    repeat: -1,
    yoyo: true,
    stagger: 0.4,
    delay: 1.6,
  });
})();

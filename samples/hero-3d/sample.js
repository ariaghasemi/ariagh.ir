/* =============================================================================
   hero-3d — entrance choreography + pointer parallax.
   The field's transform belongs to CSS (it owns the parallax); the timeline
   only animates custom properties and opacity, so the two never fight.
   ========================================================================== */

(function () {
  const S = window.AriSample;
  const scene = document.getElementById('scene');
  const field = document.getElementById('field');
  if (!scene || !field) return;

  S.pointer(scene, field);

  document.querySelectorAll('[data-count]').forEach((el) => {
    S.countUp(el, Number(el.dataset.count), { duration: 1500 });
  });

  if (S.reduced || !window.gsap) return;

  document.documentElement.classList.add('js-anim');
  gsap.defaults({ ease: 'expo.out' });

  const tl = gsap.timeline({ delay: 0.15 });

  tl.fromTo('.ring', { '--s-scale': 0.35, opacity: 0 }, { '--s-scale': 1, opacity: 1, duration: 1.5, stagger: 0.11 }, 0)
    .fromTo('.orb', { '--s-scale': 0.2, opacity: 0 }, { '--s-scale': 1, opacity: 1, duration: 1.6 }, 0.15)
    .fromTo('.dust', { opacity: 0 }, { opacity: 0.55, duration: 1.2, stagger: 0.08 }, 0.4)
    .fromTo(
      '.hero3d .s-kicker',
      { opacity: 0, y: 16 },
      { opacity: 1, y: 0, duration: 0.8 },
      0.35,
    )
    .fromTo(
      '.hero3d .word',
      { opacity: 0, z: -280, rotateX: -34 },
      { opacity: 1, z: 0, rotateX: 0, duration: 1.25, stagger: 0.13 },
      0.45,
    )
    .fromTo('.hero3d .s-lede', { opacity: 0, y: 18 }, { opacity: 1, y: 0, duration: 0.9 }, 0.85)
    .fromTo(
      '.hero3d__actions',
      { opacity: 0, y: 16 },
      { opacity: 1, y: 0, duration: 0.8 },
      1,
    )
    .fromTo('.hero3d__meta', { opacity: 0, y: 16 }, { opacity: 1, y: 0, duration: 0.8 }, 1.15);
})();

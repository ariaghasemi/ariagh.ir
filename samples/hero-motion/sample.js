/* =============================================================================
   hero-motion — masked word reveal + a marquee driven by scroll velocity.
   ========================================================================== */

(function () {
  const S = window.AriSample;
  if (S.reduced || !window.gsap) return;

  document.documentElement.classList.add('js-anim');
  gsap.defaults({ ease: 'expo.out' });

  const tl = gsap.timeline({ delay: 0.1 });

  tl.fromTo('.heroflow .s-kicker', { opacity: 0, y: 14 }, { opacity: 1, y: 0, duration: 0.7 })
    .fromTo(
      '.heroflow__title .w i',
      { yPercent: 105 },
      { yPercent: 0, duration: 1.15, stagger: 0.075 },
      0.15,
    )
    .fromTo('.heroflow .s-lede', { opacity: 0, y: 16 }, { opacity: 1, y: 0, duration: 0.9 }, 0.7)
    .fromTo('.heroflow__actions', { opacity: 0, y: 14 }, { opacity: 1, y: 0, duration: 0.8 }, 0.85)
    .fromTo('.heroflow__cue', { opacity: 0 }, { opacity: 1, duration: 0.8 }, 1.1);

  /* ---- marquee ---------------------------------------------------------- */
  const track = document.getElementById('marquee');
  if (!track) return;

  // Duplicate the content so the loop has no visible seam.
  track.innerHTML += track.innerHTML;

  const loop = gsap.to(track, {
    xPercent: -50,
    duration: 26,
    ease: 'none',
    repeat: -1,
  });

  if (!window.ScrollTrigger) return;
  gsap.registerPlugin(ScrollTrigger);

  // Speed and direction follow how fast the page is being scrolled.
  ScrollTrigger.create({
    trigger: document.body,
    start: 'top top',
    end: 'bottom bottom',
    onUpdate: (self) => {
      const boost = gsap.utils.clamp(-6, 6, self.getVelocity() / 260);
      gsap.to(loop, { timeScale: 1 + boost, duration: 0.5, overwrite: true });
    },
  });

  // Scrolling back to rest returns the marquee to its base speed.
  const settle = gsap.utils.debounce?.(() => gsap.to(loop, { timeScale: 1, duration: 1.2 }), 180);
  window.addEventListener('scroll', settle ?? (() => {}), { passive: true });

  // The hero drifts up and dims as you leave it.
  gsap.to('.heroflow__content', {
    yPercent: -12,
    opacity: 0.15,
    ease: 'none',
    scrollTrigger: { trigger: '.heroflow', start: 'top top', end: 'bottom top', scrub: true },
  });
})();

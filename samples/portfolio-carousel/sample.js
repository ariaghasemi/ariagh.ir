/* =============================================================================
   portfolio-carousel — slide switching with parallax artwork, thumbnail rail,
   keyboard control and a pointer-driven drift on the active composition.
   ========================================================================== */

(function () {
  const S = window.AriSample;
  const stage = document.getElementById('stage');
  const thumbs = document.getElementById('thumbs');
  if (!stage) return;

  const slides = [...stage.querySelectorAll('.fslide')];
  let index = 0;
  let timer = 0;

  /* ---- build the thumbnail rail from the slides themselves --------------- */
  if (thumbs) {
    thumbs.innerHTML = slides
      .map(
        (slide, i) => `
        <button class="fthumb" type="button" role="tab" data-go="${i}" aria-selected="${i === 0}">
          <span class="f-art f-art--${slide.dataset.art}"><i></i><i></i><i></i></span>
          <b>${String(i + 1).padStart(2, '0')}</b>
        </button>`,
      )
      .join('');
  }

  function show(next, { silent = false } = {}) {
    const i = S.clamp(next, 0, slides.length - 1);
    const changed = i !== index;
    index = i;

    slides.forEach((slide, n) => slide.classList.toggle('is-active', n === index));
    thumbs?.querySelectorAll('.fthumb').forEach((b, n) => b.setAttribute('aria-selected', String(n === index)));

    const art = slides[index].querySelector('.f-art');
    if (art && !S.reduced && window.gsap && changed) {
      const from = window.gsap.utils.random([60, -60]);
      gsap.fromTo(
        art,
        { x: from, scale: 1.14, rotate: from / 22 },
        { x: 0, scale: 1, rotate: 0, duration: 1.25, ease: 'expo.out', overwrite: true },
      );
    }

    if (!silent) restart();
  }

  function restart() {
    clearInterval(timer);
    // Slow enough to read, and it stops the moment someone takes over.
    timer = setInterval(() => show(index + 1), 6500);
  }

  thumbs?.addEventListener('click', (event) => {
    const btn = event.target.closest('[data-go]');
    if (btn) show(Number(btn.dataset.go));
  });

  document.getElementById('next')?.addEventListener('click', () => show(index + 1));
  document.getElementById('prev')?.addEventListener('click', () => show(index - 1));

  document.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowLeft') show(index + (document.documentElement.dir === 'rtl' ? 1 : -1));
    else if (event.key === 'ArrowRight') show(index + (document.documentElement.dir === 'rtl' ? -1 : 1));
    else return;
    event.preventDefault();
  });

  // Pause while the pointer is over the stage — reading beats autoplay.
  stage.addEventListener('pointerenter', () => clearInterval(timer));
  stage.addEventListener('pointerleave', restart);

  if (!S.reduced && window.gsap) {
    gsap.fromTo(
      '.folio__head > *',
      { opacity: 0, y: 18 },
      { opacity: 1, y: 0, duration: 0.9, stagger: 0.1, ease: 'expo.out', delay: 0.1 },
    );
    // writes --px / --py onto the stage; the frames pick them up through
    // inheritance, so the parallax survives slide changes.
    S.pointer(stage, stage, { strength: 0.7 });
  }

  restart();
})();

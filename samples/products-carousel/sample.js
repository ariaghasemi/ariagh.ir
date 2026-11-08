/* =============================================================================
   products-carousel — perspective falloff + progress rail + keyboard nav.
   Everything is measured from geometry, so it behaves identically in RTL.
   ========================================================================== */

(function () {
  const S = window.AriSample;
  const rail = document.getElementById('rail');
  if (!rail) return;

  const cards = [...rail.querySelectorAll('.pcard')];
  const fill = document.getElementById('progress');
  const idxEl = document.getElementById('idx');
  const totalEl = document.getElementById('total');
  const pad = (n) => String(n).padStart(2, '0');

  if (totalEl) totalEl.textContent = pad(cards.length);

  let raf = 0;
  let active = 0;

  function measure() {
    const railRect = rail.getBoundingClientRect();
    const center = railRect.left + railRect.width / 2;
    let nearest = 0;
    let nearestDist = Infinity;

    cards.forEach((card, i) => {
      const r = card.getBoundingClientRect();
      const offset = (r.left + r.width / 2 - center) / railRect.width; // -1 … 1
      const away = Math.min(1, Math.abs(offset) * 1.9);
      card.style.setProperty('--dist', offset.toFixed(4));
      card.style.setProperty('--away', away.toFixed(4));

      const d = Math.abs(offset);
      if (d < nearestDist) {
        nearestDist = d;
        nearest = i;
      }
    });

    if (nearest !== active) {
      active = nearest;
      if (idxEl) idxEl.textContent = pad(active + 1);
    }

    if (fill) {
      const max = rail.scrollWidth - rail.clientWidth;
      const progress = max > 1 ? Math.abs(rail.scrollLeft) / max : 0;
      fill.style.width = `${(progress * 100).toFixed(2)}%`;
    }
  }

  function schedule() {
    if (raf) return;
    raf = requestAnimationFrame(() => {
      raf = 0;
      measure();
    });
  }

  rail.addEventListener('scroll', schedule, { passive: true });
  window.addEventListener('resize', schedule);
  measure();

  function goTo(index) {
    const target = cards[S.clamp(index, 0, cards.length - 1)];
    target?.scrollIntoView({
      behavior: S.reduced ? 'auto' : 'smooth',
      inline: 'center',
      block: 'nearest',
    });
  }

  document.getElementById('next')?.addEventListener('click', () => goTo(active + 1));
  document.getElementById('prev')?.addEventListener('click', () => goTo(active - 1));

  rail.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowLeft') goTo(active + (document.documentElement.dir === 'rtl' ? 1 : -1));
    else if (event.key === 'ArrowRight') goTo(active + (document.documentElement.dir === 'rtl' ? -1 : 1));
    else return;
    event.preventDefault();
  });

  // Drag-to-pan on pointer devices; touch already scrolls natively.
  let dragging = false;
  let startX = 0;
  let startScroll = 0;

  rail.addEventListener('pointerdown', (e) => {
    if (e.pointerType === 'touch') return;
    dragging = true;
    startX = e.clientX;
    startScroll = rail.scrollLeft;
    rail.setPointerCapture?.(e.pointerId);
    rail.style.cursor = 'grabbing';
    rail.style.scrollSnapType = 'none';
  });

  rail.addEventListener('pointermove', (e) => {
    if (!dragging) return;
    rail.scrollLeft = startScroll - (e.clientX - startX);
  });

  const endDrag = () => {
    if (!dragging) return;
    dragging = false;
    rail.style.cursor = 'grab';
    rail.style.scrollSnapType = 'x mandatory';
    goTo(active);
  };

  rail.addEventListener('pointerup', endDrag);
  rail.addEventListener('pointercancel', endDrag);
  rail.addEventListener('pointerleave', endDrag);

  // Entrance: cards settle in from the sides, one after another.
  if (!S.reduced && window.gsap) {
    gsap.fromTo(
      cards,
      { opacity: 0, y: 26 },
      { opacity: 1, y: 0, duration: 0.9, stagger: 0.08, delay: 0.15, ease: 'expo.out', clearProps: 'opacity,y' },
    );
  }
})();

/* ═══════════════════════════════════════════════
   مونتاژ بلوک‌ها — دموی پیش‌نمایش سه‌بعدی
   Three.js + GSAP ScrollTrigger + Lenis
   ═══════════════════════════════════════════════ */
import * as THREE from 'three';

const gsap = window.gsap;
const ScrollTrigger = window.ScrollTrigger;
gsap.registerPlugin(ScrollTrigger);

const isMobile = window.matchMedia('(max-width: 768px)').matches;
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const COUNT = isMobile ? 72 : 140;

/* ── RNG قطعی (برای پایداری فریم به فریم) ── */
function mulberry32(seed) {
  let a = seed >>> 0;
  return function () {
    a |= 0; a = (a + 0x6D2B79F5) | 0;
    let t = Math.imul(a ^ (a >>> 15), 1 | a);
    t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
    return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
  };
}
const clamp01 = (v) => Math.max(0, Math.min(1, v));
const smooth = (t) => t * t * (3 - 2 * t);
const lerp = (a, b, t) => a + (b - a) * t;

/* ═══════ Lenis ═══════ */
let lenis = null;
if (!reduceMotion && window.Lenis) {
  lenis = new window.Lenis({ duration: 1.2, smoothWheel: true });
  lenis.on('scroll', ScrollTrigger.update);
  lenis.stop(); // تا پایان لودر
}

/* ═══════ Three.js ═══════ */
const canvasWrap = document.getElementById('canvas-wrap');
const canvasEl = document.getElementById('canvas');
let renderer = null, scene = null, camera = null, blockGroup = null;
const blocks = [];            // { mesh, baseScale }
const formPos = [];           // 7 × Float32Array(COUNT*3)
const formRot = [];
const gearMeta = [];          // برای چرخ‌دنده‌ها: {cx,cy,R,ang,z}
const wavePhase = new Float32Array(COUNT);
const camStops = [
  [0, 0, 17], [0.6, 0, 15.5], [1, 0, 16.5], [-0.4, 0.5, 18],
  [0.4, 0, 17], [0, -0.4, 15.5], [-0.4, 0, 16.2],
];

function setP(arr, i, x, y, z) { arr[i * 3] = x; arr[i * 3 + 1] = y; arr[i * 3 + 2] = z; }
function setR(arr, i, x, y, z) { arr[i * 3] = x; arr[i * 3 + 1] = y; arr[i * 3 + 2] = z; }

/* ── ساخت فریم‌های هدف (۷ تشکیل) ── */
function buildFormations() {
  for (let f = 0; f < 7; f++) {
    formPos.push(new Float32Array(COUNT * 3));
    formRot.push(new Float32Array(COUNT * 3));
  }

  // F0 — ابر کروی مداری (هیرو)
  {
    const rng = mulberry32(11);
    const p = formPos[0], r = formRot[0];
    for (let i = 0; i < COUNT; i++) {
      const u = rng() * Math.PI * 2;
      const v = Math.acos(2 * rng() - 1);
      const rad = 7.5 + rng() * 4.5;
      setP(p, i,
        rad * Math.sin(v) * Math.cos(u),
        rad * Math.cos(v) * 0.72,
        rad * Math.sin(v) * Math.sin(u) * 0.6 - 1
      );
      setR(r, i, (rng() - 0.5) * 0.8, (rng() - 0.5) * 0.8, (rng() - 0.5) * 0.8);
    }
  }

  // F1 — دیوارِ «صفحهٔ وب» (چپ، مقابل متنِ راست)
  {
    const rng = mulberry32(22);
    const cols = isMobile ? 7 : 10;
    const rows = Math.ceil(COUNT / cols);
    const p = formPos[1], r = formRot[1];
    for (let i = 0; i < COUNT; i++) {
      const c = i % cols, rw = Math.floor(i / cols);
      setP(p, i,
        (c - (cols - 1) / 2) * 1.15 - 4.8,
        ((rows - 1) / 2 - rw) * 1.05,
        (rng() - 0.5) * 0.7
      );
      setR(r, i, 0, 0, 0);
    }
  }

  // F2 — سه «چرخ‌دنده» افزونه‌ها (راست، مقابل متنِ چپ)
  {
    const rng = mulberry32(33);
    const p = formPos[2], r = formRot[2];
    const centers = [[-3.6, 1.1], [1.4, -1.3], [6.3, 1.2]];
    const per = Math.ceil(COUNT / 3);
    for (let i = 0; i < COUNT; i++) {
      const ring = Math.min(2, Math.floor(i / per));
      const idx = i - ring * per;
      const ang = (idx / per) * Math.PI * 2 + ring * 0.8;
      const R = 2.25 + (idx % 2) * 0.35;
      const [cx, cy] = centers[ring];
      const z = (rng() - 0.5) * 0.8;
      setP(p, i, cx + Math.cos(ang) * R, cy + Math.sin(ang) * R, z);
      setR(r, i, 0, 0, ang + Math.PI / 2);
      gearMeta.push({ cx, cy, R, ang, z });
    }
    while (gearMeta.length < COUNT) gearMeta.push({ cx: 0, cy: 0, R: 1, ang: 0, z: 0 });
  }

  // F3 — قفسهٔ قالب‌ها (۴ کارت، چپ/وسط)
  {
    const rng = mulberry32(44);
    const p = formPos[3], r = formRot[3];
    const per = Math.ceil(COUNT / 4);
    const cols = 5, rows = Math.ceil(per / cols);
    const centers = [-9.2, -4.0, 1.2, 6.4];
    const tilt = [0.16, -0.05, 0.05, -0.16];
    for (let i = 0; i < COUNT; i++) {
      const card = Math.min(3, Math.floor(i / per));
      const idx = i - card * per;
      const c = idx % cols, rw = Math.floor(idx / cols);
      setP(p, i,
        centers[card] + (c - (cols - 1) / 2) * 0.98,
        ((rows - 1) / 2 - rw) * 0.95,
        (rng() - 0.5) * 0.4
      );
      setR(r, i, 0, tilt[card], 0);
    }
  }

  // F4 — قاب‌های نمونه‌کار (راست/وسط)
  {
    const rng = mulberry32(55);
    const p = formPos[4], r = formRot[4];
    const frames = 5;
    const per = Math.floor(COUNT / frames);
    const W = 3.4, H = 2.4, perim = 2 * (W + H);
    const xs = [-6, -2.2, 1.6, 5.4, 9.2];
    let i = 0;
    for (let k = 0; k < frames; k++) {
      const n = k === frames - 1 ? COUNT - per * (frames - 1) : per;
      const fy = k % 2 ? 0.95 : -0.95;
      const fz = (k - 2) * 0.9;
      for (let j = 0; j < n; j++, i++) {
        const t = (j / n) * perim;
        let x, y;
        if (t < W) { x = -W / 2 + t; y = H / 2; }
        else if (t < W + H) { x = W / 2; y = H / 2 - (t - W); }
        else if (t < 2 * W + H) { x = W / 2 - (t - W - H); y = -H / 2; }
        else { x = -W / 2; y = -H / 2 + (t - 2 * W - H); }
        setP(p, i, xs[k] + x, fy + y, fz + (rng() - 0.5) * 0.3);
        setR(r, i, 0, (k - 2) * 0.06, 0);
      }
    }
  }

  // F5 — خط مونتاژ (پایین، عرض صفحه)
  {
    const rng = mulberry32(66);
    const p = formPos[5], r = formRot[5];
    for (let i = 0; i < COUNT; i++) {
      setP(p, i, -11 + (i / (COUNT - 1)) * 22, -3.1 + (rng() - 0.5) * 0.35, (rng() - 0.5) * 1.3);
      setR(r, i, (rng() - 0.5) * 0.5, (rng() - 0.5) * 0.9, (rng() - 0.5) * 0.5);
      wavePhase[i] = i * 0.37;
    }
  }

  // F6 — مانیتور (قاب مستطیل + پایه) — لحظهٔ امضای پایان
  {
    const rng = mulberry32(77);
    const p = formPos[6], r = formRot[6];
    const W = 11, H = 6.4, cx = -2.4, cy = 0.3;
    const standN = Math.max(5, Math.round(COUNT * 0.05));
    const baseN = 4;
    const perimN = COUNT - standN - baseN;
    const perim = 2 * (W + H);
    for (let i = 0; i < COUNT; i++) {
      if (i < perimN) {
        const t = (i / perimN) * perim;
        let x, y;
        if (t < W) { x = -W / 2 + t; y = H / 2; }
        else if (t < W + H) { x = W / 2; y = H / 2 - (t - W); }
        else if (t < 2 * W + H) { x = W / 2 - (t - W - H); y = -H / 2; }
        else { x = -W / 2; y = -H / 2 + (t - 2 * W - H); }
        setP(p, i, cx + x, cy + y, (rng() - 0.5) * 0.35);
        setR(r, i, 0, 0, 0);
      } else if (i < perimN + standN) {
        const k = i - perimN;
        setP(p, i, cx, cy - H / 2 - 0.9 - k * 0.95, 0);
        setR(r, i, 0, 0, 0);
      } else {
        const k = i - perimN - standN;
        setP(p, i, cx + (k - (baseN - 1) / 2) * 1.15, cy - H / 2 - 0.9 - standN * 0.95 - 0.45, 0);
        setR(r, i, 0, 0, 0);
      }
    }
  }
}

/* ── ساخت صحنه ── */
function initThree() {
  buildFormations();
  try {
    renderer = new THREE.WebGLRenderer({ canvas: canvasEl, antialias: !isMobile, powerPreference: 'high-performance' });
  } catch (e) {
    canvasWrap.style.display = 'none';
    return;
  }
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.setSize(window.innerWidth, window.innerHeight, false);
  renderer.setClearColor(0x0a0a0c, 1);

  scene = new THREE.Scene();
  scene.fog = new THREE.Fog(0x0a0a0c, 17, 44);

  camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 100);
  camera.position.set(0, 0, 17);

  scene.add(new THREE.AmbientLight(0xffffff, 0.55));
  const dir = new THREE.DirectionalLight(0xffffff, 1.15);
  dir.position.set(6, 9, 11);
  scene.add(dir);
  const accent = new THREE.PointLight(0x3858e9, 60, 60);
  accent.position.set(-7, 3, 8);
  scene.add(accent);

  blockGroup = new THREE.Group();
  scene.add(blockGroup);

  const geo = new THREE.BoxGeometry(1, 1, 1);
  const rng = mulberry32(2026);
  for (let i = 0; i < COUNT; i++) {
    const pick = rng();
    let mat;
    if (pick < 0.42) mat = new THREE.MeshStandardMaterial({ color: 0x17171d, roughness: 0.78, metalness: 0.08 });
    else if (pick < 0.66) mat = new THREE.MeshStandardMaterial({ color: 0x23232b, roughness: 0.7, metalness: 0.1 });
    else if (pick < 0.79) mat = new THREE.MeshStandardMaterial({ color: 0x2e2e38, roughness: 0.62, metalness: 0.12 });
    else if (pick < 0.93) mat = new THREE.MeshStandardMaterial({ color: 0x3858e9, roughness: 0.4, metalness: 0.15, emissive: 0x0b1c52, emissiveIntensity: 0.7 });
    else mat = new THREE.MeshStandardMaterial({ color: 0xe8e8ee, roughness: 0.5, metalness: 0.05 });

    const mesh = new THREE.Mesh(geo, mat);
    const sx = 0.55 + rng() * 1.1;
    const sy = 0.55 + rng() * 1.1;
    const sz = rng() < 0.35 ? 0.22 + rng() * 0.25 : 0.55 + rng() * 0.9;
    mesh.scale.set(0.001, 0.001, 0.001); // تا پایان لودر
    mesh.userData.baseScale = new THREE.Vector3(sx, sy, sz);
    const s = formPos[0];
    mesh.position.set(s[i * 3], s[i * 3 + 1], s[i * 3 + 2]);
    blockGroup.add(mesh);
    blocks.push({ mesh });
  }
}

/* ═══════ حلقهٔ رندر + ترکیب فریم‌ها ═══════ */
let fTarget = 0, fCur = 0;
let mouse = { x: 0, y: 0 }, par = { x: 0, y: 0 };
const camBase = { x: 0, y: 0, z: 17 };
const _pos = new THREE.Vector3();

function renderTick(time, deltaMS) {
  if (!renderer) return;
  const dt = Math.min(deltaMS / 1000, 0.05);
  fCur += (fTarget - fCur) * Math.min(1, dt * 5.5);

  const seg = Math.min(5, Math.floor(fCur));
  const te = smooth(clamp01(fCur - seg));
  const w0 = clamp01(1 - fCur);
  const w2 = clamp01(1 - Math.abs(fCur - 2));
  const w5 = clamp01(1 - Math.abs(fCur - 5));
  const t = reduceMotion ? 0 : time;

  const A = formPos[seg], B = formPos[seg + 1];
  const RA = formRot[seg], RB = formRot[seg + 1];

  // چرخ‌دنده‌ها: θ بر پایهٔ زمان
  const theta = t * 0.45;
  for (let i = 0; i < COUNT; i++) {
    const b = blocks[i].mesh;
    let x = lerp(A[i * 3], B[i * 3], te);
    let y = lerp(A[i * 3 + 1], B[i * 3 + 1], te);
    let z = lerp(A[i * 3 + 2], B[i * 3 + 2], te);

    if (w2 > 0.001) {
      const g = gearMeta[i];
      const cA = Math.cos(g.ang), sA = Math.sin(g.ang);
      const cB = Math.cos(g.ang + theta), sB = Math.sin(g.ang + theta);
      x += (g.cx + cB * g.R - (g.cx + cA * g.R)) * w2;
      y += (g.cy + sB * g.R - (g.cy + sA * g.R)) * w2;
    }
    if (w5 > 0.001) {
      x += Math.sin(t * 0.55 + wavePhase[i]) * 0.9 * w5;
      y += Math.sin(t * 1.15 + wavePhase[i]) * 0.28 * w5;
    }

    b.position.set(x, y, z);
    b.rotation.set(
      lerp(RA[i * 3], RB[i * 3], te),
      lerp(RA[i * 3 + 1], RB[i * 3 + 1], te),
      lerp(RA[i * 3 + 2], RB[i * 3 + 2], te)
    );
  }

  // چرخش کل ابر در هیرو (با w0 محو می‌شود)
  blockGroup.rotation.y = t * 0.1 * w0;

  // دوربین: توقف‌های هر فصل + پارالاکس ماوس
  const cs = camStops[seg], cn = camStops[seg + 1];
  camBase.x = lerp(cs[0], cn[0], te);
  camBase.y = lerp(cs[1], cn[1], te);
  camBase.z = lerp(cs[2], cn[2], te);
  par.x += (mouse.x * 1.1 - par.x) * Math.min(1, dt * 4);
  par.y += (-mouse.y * 0.7 - par.y) * Math.min(1, dt * 4);
  camera.position.set(camBase.x + par.x, camBase.y + par.y, camBase.z);
  camera.lookAt(0, 0, 0);

  renderer.render(scene, camera);
}

window.addEventListener('pointermove', (e) => {
  mouse.x = (e.clientX / window.innerWidth) * 2 - 1;
  mouse.y = (e.clientY / window.innerHeight) * 2 - 1;
}, { passive: true });

window.addEventListener('resize', () => {
  if (!renderer) return;
  camera.aspect = window.innerWidth / window.innerHeight;
  camera.updateProjectionMatrix();
  renderer.setSize(window.innerWidth, window.innerHeight, false);
});

/* ═══════ اسکرول‌تریگرها ═══════ */

// واپاش دایره‌ای هیرو + محو محتوا
gsap.timeline({
  scrollTrigger: { trigger: '#hero', start: 'top top', end: 'bottom top', scrub: true },
})
  .fromTo(canvasWrap,
    { clipPath: 'circle(0% at 50% 50%)' },
    { clipPath: 'circle(80% at 50% 50%)', ease: 'none' }, 0)
  .to('.hero-inner', { backgroundColor: 'rgba(10,10,12,0)', ease: 'none', duration: 0.55 }, 0)
  .to('.hero-content', { y: -70, opacity: 0, ease: 'none' }, 0.15)
  .to('.scroll-hint', { opacity: 0, ease: 'none', duration: 0.2 }, 0);

// پیشرفت فصل‌ها → شاخص ترکیب فریم‌ها (۰ تا ۶)
ScrollTrigger.create({
  trigger: '#chapters',
  start: 'top center',
  end: 'bottom center',
  onUpdate: (self) => { fTarget = self.progress * 6; },
});

// مارکی: نمایش در اواسط سفر، لغزش افقی
const marqueeEl = document.getElementById('marquee');
ScrollTrigger.create({
  trigger: '#chapters', start: 'top bottom', end: 'bottom top', scrub: true,
  onUpdate: (self) => {
    const f = self.progress * 6;
    marqueeEl.style.opacity = clamp01((f - 0.15) / 0.3) * clamp01((3.4 - f) / 0.5);
  },
});
gsap.to('.marquee-text', {
  xPercent: -28, ease: 'none',
  scrollTrigger: { trigger: '#chapters', start: 'top bottom', end: 'bottom top', scrub: true },
});

// تیره‌کنندهٔ آمار
gsap.timeline({
  scrollTrigger: { trigger: '.section-stats', start: 'top 80%', end: 'bottom 20%', scrub: true },
})
  .to('#dark-overlay', { opacity: 0.9, ease: 'none', duration: 0.3 })
  .to('#dark-overlay', { opacity: 0.9, ease: 'none', duration: 0.45 })
  .to('#dark-overlay', { opacity: 0, ease: 'none', duration: 0.25 });

// ورود محتوای هر فصل — نوع‌های متنوع، بدون تکرار پیاپی
const CHILD_SEL = '.section-label, .section-heading, .section-body, .section-note, .cta-button, .stat, .step';
document.querySelectorAll('.scroll-section').forEach((section) => {
  const type = section.dataset.animation;
  const children = section.querySelectorAll(CHILD_SEL);
  if (!children.length) return;
  const base = { duration: 0.95, ease: 'power3.out', stagger: 0.13, clearProps: 'transform' };
  let from = { opacity: 0, y: 50 };
  if (type === 'slide-left') from = { opacity: 0, x: -80 };
  else if (type === 'slide-right') from = { opacity: 0, x: 80 };
  else if (type === 'scale-up') from = { opacity: 0, scale: 0.85, duration: 1.05, ease: 'power2.out' };
  else if (type === 'rotate-in') from = { opacity: 0, y: 40, rotation: 3 };
  else if (type === 'stagger-up') from = { opacity: 0, y: 60, duration: 0.85 };
  else if (type === 'clip-reveal') from = { opacity: 0, clipPath: 'inset(100% 0 0 0)', duration: 1.15, ease: 'power4.inOut' };

  gsap.from(children, {
    ...base, ...from,
    scrollTrigger: {
      trigger: section,
      start: 'top 70%',
      toggleActions: section.dataset.persist === 'true' ? 'play none none none' : 'play none none reverse',
    },
  });
});

// شمارنده‌های آمار
document.querySelectorAll('.stat-number').forEach((el) => {
  const target = parseFloat(el.dataset.value);
  const suffix = el.dataset.suffix || '';
  const obj = { v: 0 };
  el.innerHTML = `<span>0</span><span class="suffix">${suffix}</span>`;
  gsap.to(obj, {
    v: target, duration: 2, ease: 'power1.out', snap: { v: 1 },
    onUpdate: () => { el.querySelector('span').textContent = String(Math.round(obj.v)); },
    scrollTrigger: { trigger: '.section-stats', start: 'top 70%', toggleActions: 'play none none reverse' },
  });
});

// هدر + نوار پیشرفت
ScrollTrigger.create({
  trigger: document.body, start: '60px top', end: 'max',
  toggleClass: { targets: '#site-header', className: 'scrolled' },
});
gsap.to('#progress-bar', {
  scaleX: 1, ease: 'none',
  scrollTrigger: { trigger: document.body, start: 'top top', end: 'bottom bottom', scrub: 0.3 },
});

/* ═══════ ناوبری ═══════ */
document.querySelectorAll('a[href^="#"]').forEach((a) => {
  a.addEventListener('click', (e) => {
    const id = a.getAttribute('href');
    if (id === '#' || id.length < 2) { e.preventDefault(); return; }
    const el = document.querySelector(id);
    if (!el) return;
    e.preventDefault();
    if (lenis) lenis.scrollTo(el, { offset: -30, duration: 1.5 });
    else el.scrollIntoView({ behavior: 'smooth' });
  });
});
document.getElementById('cta-button').addEventListener('click', (e) => {
  e.preventDefault();
  gsap.fromTo(e.currentTarget, { scale: 0.96 }, { scale: 1, duration: 0.5, ease: 'elastic.out(1, 0.45)' });
});

/* ═══════ لودر + مقدمه ═══════ */
function playIntro() {
  gsap.fromTo('.word-inner', { yPercent: 115 }, { yPercent: 0, duration: 1.15, ease: 'power4.out', stagger: 0.14, delay: 0.1 });
  gsap.from('.hero-label', { opacity: 0, y: 24, duration: 0.8, ease: 'power3.out', delay: 0.35 });
  gsap.from('.hero-tagline', { opacity: 0, y: 24, duration: 0.8, ease: 'power3.out', delay: 0.55 });
  gsap.from('.scroll-hint', { opacity: 0, duration: 0.8, delay: 0.9 });
  gsap.from('.site-header', { opacity: 0, y: -18, duration: 0.7, ease: 'power3.out', delay: 0.4 });

  // ظهور بلوک‌ها
  if (renderer && !reduceMotion) {
    blocks.forEach(({ mesh }, i) => {
      const s = mesh.userData.baseScale;
      gsap.to(mesh.scale, {
        x: s.x, y: s.y, z: s.z,
        duration: 0.9, ease: 'back.out(1.6)',
        delay: 0.25 + (i % 23) * 0.035 + Math.random() * 0.15,
      });
    });
  } else if (renderer) {
    blocks.forEach(({ mesh }) => mesh.scale.copy(mesh.userData.baseScale));
  }
}

function boot() {
  initThree();

  const bar = document.getElementById('loader-bar');
  const pct = document.getElementById('loader-percent');
  const prog = { v: 0 };
  gsap.to(prog, {
    v: 100, duration: 1.0, ease: 'power2.inOut',
    onUpdate: () => {
      bar.style.transform = `scaleX(${prog.v / 100})`;
      pct.textContent = `${Math.round(prog.v)}%`;
    },
    onComplete: async () => {
      try { await document.fonts.ready; } catch (e) { /* نادیده */ }
      const loader = document.getElementById('loader');
      gsap.to(loader, {
        autoAlpha: 0, duration: 0.55, ease: 'power2.inOut',
        onComplete: () => { loader.style.display = 'none'; },
      });
      if (lenis) lenis.start();
      playIntro();
      ScrollTrigger.refresh();
    },
  });

  // حلقهٔ رندر
  gsap.ticker.add((time, deltaMS) => {
    if (lenis) lenis.raf(time * 1000);
    renderTick(time, deltaMS);
  });
  gsap.ticker.lagSmoothing(0);
}

boot();

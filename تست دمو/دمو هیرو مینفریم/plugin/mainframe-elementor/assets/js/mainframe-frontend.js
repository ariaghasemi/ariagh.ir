/**
 * مینفریم — جاوااسکریپت فرانت‌اند
 *
 * مسیر فایل: wp-content/plugins/mainframe-elementor/assets/js/mainframe-frontend.js
 *
 * اجرا در دو جا:
 *   1) صفحهٔ عادی (front): DOMContentLoaded → همهٔ ویجت‌های موجود
 *   2) ادیتور المنتور (preview): هوک هنگام رندر مجدد یک المان → دوباره init
 *      — نکتهٔ کلیدی: هنگام رندر مجدد، Elementor روی المانِ تازه
 *        «frontend/element_ready/<data-widget_type>» و «frontend/element_ready/widget»
 *        را شلیک می‌کند (data-widget_type برابر نام ویجت با پسوند .default است).
 *
 * initها idempotent هستند: اگر المان قبلاً آماده شده باشد (data-mf-ready)، دوباره اجرا نمی‌شوند.
 */
(function () {
	'use strict';

	var $ = function (sel, ctx) { return (ctx || document).querySelector(sel); };
	var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); };

	/* ------------------------------------------------------------------
	 * ویجت ۱ — ویدیوی حرکت با ماوس (mouse-scrub)
	 *
	 *  - صف seek تک‌گام با rAF (حداکثر یک seek در هر فریم)
	 *  - onSeeked دوباره هدفِ جابه‌جاشده را اعمال می‌کند
	 *  - فرمول: offset = (delta ÷ عرض مبنا) × sensitivity × duration
	 * ------------------------------------------------------------------ */
	function initScrub(wrap) {
		if (!wrap || wrap.dataset.mfReady) { return; }
		var video = wrap.querySelector('video');
		if (!video) { return; }

		wrap.dataset.mfReady = '1';
		wrap.classList.add('mf-ready');

		var baseTime = 0;                 // زمان مبنا = offset هنگام شروع مکث
		var lastMouseX = null;            // آخرین X اندازه‌گیری‌شده
		var sensitivity = parseFloat(wrap.dataset.sensitivity || '0.8');
		if (isNaN(sensitivity) || sensitivity <= 0) { sensitivity = 0.8; }
		var reverse = wrap.dataset.reverse === '1';
		var touchOn = wrap.dataset.touch !== '0';
		var startOffset = parseFloat(wrap.dataset.start || '0') || 0;

		var targetTime = startOffset;
		var seekPending = false;
		var rafId = 0;

		/* عرض مبنای محاسبه: در حالت fixed پنجره، در fill/box عرض خود wrap. */
		function basisWidth() {
			if (wrap.classList.contains('mf-scrub--fixed')) {
				return window.innerWidth || 1;
			}
			var r = wrap.getBoundingClientRect();
			return r.width || window.innerWidth || 1;
		}

		function applySeek() {
			rafId = 0;
			var d = video.duration;
			if (!d || !isFinite(d) || d <= 0) { return; }
			var t = Math.min(Math.max(targetTime, 0), d);
			if (seekPending) { return; }
			if (Math.abs((video.currentTime || 0) - t) < 0.02) { return; } // رسیدیم
			seekPending = true;
			try {
				video.currentTime = t;
			} catch (e) {
				seekPending = false;
			}
		}

		function scheduleSeek() {
			if (!rafId) { rafId = requestAnimationFrame(applySeek); }
		}

		video.addEventListener('seeked', function () {
			seekPending = false;
			// اگر در میانهٔ seek هدف جلوتر رفته بود، اعمال بعدی
			scheduleSeek();
		});

		function seek(delta) {
			var w = basisWidth();
			if (!w) { return; }
			var d = video.duration;
			if (!d || !isFinite(d)) { return; }
			var off = (delta / w) * sensitivity * d;
			if (reverse) { off = -off; }
			targetTime = Math.min(Math.max(baseTime + off, 0), d);
			scheduleSeek();
		}

		function onMouseMove(e) {
			if (!video.isConnected) {
				window.removeEventListener('mousemove', onMouseMove);
				return;
			}
			// در حالت fill/box فقط وقتی نشانگر روی ویدیوست اسکرول کنیم (fixed همیشه).
			if (!wrap.classList.contains('mf-scrub--fixed')) {
				var r = wrap.getBoundingClientRect();
				var inside = e.clientX >= r.left && e.clientX <= r.right && e.clientY >= r.top && e.clientY <= r.bottom;
				if (!inside) {
					lastMouseX = null;
					return;
				}
			}
			if (lastMouseX === null) { lastMouseX = e.clientX; return; }
			var dx = e.clientX - lastMouseX;
			lastMouseX = e.clientX;
			if (dx) { seek(dx); }
		}

		/* ------------------------------------------------------------------
		 * اسکرول لمسی با قفل محور: کشیدن افقی = scrub، کشیدن عمودی = اسکرول صفحه.
		 * ------------------------------------------------------------------ */
		var touchAxis = null;   // null | 'x' | 'y' | 'out'
		var touchLastX = null;
		var touchStartX = 0;
		var touchStartY = 0;

		function onTouchStart(e) {
			if (!video.isConnected) {
				window.removeEventListener('touchstart', onTouchStart);
				return;
			}
			if (!touchOn) { touchAxis = 'out'; return; }
			if (!wrap.classList.contains('mf-scrub--fixed')) {
				var t = e.touches[0];
				var r = wrap.getBoundingClientRect();
				if (!t || t.clientX < r.left || t.clientX > r.right || t.clientY < r.top || t.clientY > r.bottom) {
					touchAxis = 'out';
					return;
				}
			}
			touchAxis = null;
			touchStartX = e.touches[0].clientX;
			touchStartY = e.touches[0].clientY;
			touchLastX = touchStartX;
		}

		function onTouchMove(e) {
			if (!video.isConnected) {
				window.removeEventListener('touchmove', onTouchMove);
				return;
			}
			if (!touchOn || touchAxis === 'out') { return; }
			var t = e.touches[0];
			if (!t) { return; }
			if (!touchAxis) {
				if (touchLastX === null) { return; }
				var dx = t.clientX - touchStartX;
				var dy = t.clientY - touchStartY;
				if (Math.abs(dx) > 10 && Math.abs(dx) > Math.abs(dy)) {
					touchAxis = 'x';
				} else if (Math.abs(dy) > 10) {
					touchAxis = 'y';
				} else {
					return;
				}
			}
			if (touchAxis !== 'x') { return; }
			e.preventDefault(); // فقط در محور افقی — اسکرول عمودی دست‌نخورده می‌ماند
			var dx2 = t.clientX - touchLastX;
			touchLastX = t.clientX;
			if (dx2) { seek(dx2); }
		}

		function onTouchEnd() {
			touchAxis = null;
			touchLastX = null;
		}

		/* شروع از ثانیهٔ دلخواه */
		function applyStartOffset() {
			if (startOffset > 0 && video.readyState >= 1) {
				try {
					if (Math.abs((video.currentTime || 0) - startOffset) > 0.05) {
						video.currentTime = startOffset;
					}
				} catch (err) { /* ویدیو هنوز آماده نیست */ }
				baseTime = startOffset;
				targetTime = startOffset;
			}
		}

		video.addEventListener('loadedmetadata', applyStartOffset);
		applyStartOffset();

		window.addEventListener('mousemove', onMouseMove);
		window.addEventListener('touchstart', onTouchStart, { passive: false });
		window.addEventListener('touchmove', onTouchMove, { passive: false });
		window.addEventListener('touchend', onTouchEnd);
	}

	/* ------------------------------------------------------------------
	 * ویجت ۲ — تایپ‌رایتر
	 * ------------------------------------------------------------------ */
	function initTypewriter(p) {
		if (!p || p.dataset.mfReady) { return; }

		var text = p.dataset.text || '';
		var speed = parseFloat(p.dataset.speed) || 38;        // ms per char
		var initialDelay = parseFloat(p.dataset.delay) || 600;
		var loop = p.dataset.loop === '1';
		var loopPause = parseFloat(p.dataset.loopPause) || 1200;
		var onView = p.dataset.onview === '1';

		p.dataset.mfReady = '1';
		p.classList.add('mf-ready');

		var cursor = p.querySelector('.mf-typewriter-cursor');

		/* نمایش فوری (بدون انیمیشن) — حالت اولیه/ویرایش */
		function showInstant() {
			if (!p.isConnected) { return; }
			if (cursor && p.contains(cursor)) {
				p.textContent = text;
				p.appendChild(cursor);
			} else {
				p.textContent = text;
			}
		}

		function setChars(txt) {
			if (cursor && p.contains(cursor)) {
				p.textContent = txt;
				p.appendChild(cursor);
			} else {
				p.textContent = txt;
			}
		}

		function runOnce() {
			if (!p.isConnected) { return; }
			setTimeout(function () {
				if (!p.isConnected) { return; }
				var i = 0;
				(function step() {
					if (!p.isConnected) { return; }
					setChars(text.slice(0, i + 1));
					i++;
					if (i < text.length) {
						setTimeout(step, speed);
					} else if (loop) {
						setTimeout(function () {
							if (!p.isConnected) { return; }
							runOnce();
						}, loopPause);
					}
				})();
			}, initialDelay);
		}

		// بدون متن → نمایش فوری و توقف
		if (!text) { showInstant(); return; }

		if (onView && 'IntersectionObserver' in window) {
			// شروع فقط هنگام دیده‌شدن (برای المان‌های پایین‌صفحه)
			var io = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						io.disconnect();
						runOnce();
					}
				});
			}, { threshold: 0.25 });
			io.observe(p);
		} else {
			runOnce();
		}
	}

	/* ------------------------------------------------------------------
	 * ویجت ۳ — لیبل معرفی (دو خط)
	 * ------------------------------------------------------------------ */
	function initIntro(label) {
		if (!label || label.dataset.mfReady) { return; }
		label.dataset.mfReady = '1';
		label.classList.add('mf-ready');
		label.classList.remove('is-out');
		void label.offsetWidth; // reflow → تریگر مجدد انیمیشن
	}

	/* ------------------------------------------------------------------
	 * ویجت ۴ — ردیف دکمه‌های چسبان (پیل‌ها)
	 * ------------------------------------------------------------------ */
	function copyTextFallback(text) {
		return new Promise(function (resolve, reject) {
			var ta = document.createElement('textarea');
			ta.value = text;
			ta.setAttribute('readonly', '');
			ta.style.position = 'fixed';
			ta.style.top = '-1000px';
			ta.style.opacity = '0';
			document.body.appendChild(ta);
			ta.select();
			var ok = false;
			try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
			document.body.removeChild(ta);
			if (ok) { resolve(); } else { reject(new Error('copy failed')); }
		});
	}

	function copyText(text) {
		if (navigator.clipboard && navigator.clipboard.writeText) {
			return navigator.clipboard.writeText(text).catch(function () {
				return copyTextFallback(text);
			});
		}
		return copyTextFallback(text);
	}

	function initPills(row) {
		if (!row || row.dataset.mfReady) { return; }

		var delay = parseInt(row.dataset.fadeDelay, 10) || 0;

		row.dataset.mfReady = '1';
		row.classList.add('mf-ready');

		setTimeout(function () {
			if (row.isConnected) { row.classList.add('is-visible'); }
		}, delay);

		// کلیک‌های «#» یا href خالی → بدون پرش به بالا
		row.addEventListener('click', function (e) {
			var a = e.target && e.target.closest ? e.target.closest('a') : null;
			if (!a || !row.contains(a)) { return; }
			var href = a.getAttribute('href');
			if (!href || href === '#') { e.preventDefault(); }
		});

		$$('.mf-pill--copy', row).forEach(function (btn) {
			btn.addEventListener('click', function () {
				var value = btn.dataset.email || '';
				if (!value) { return; }
				copyText(value).then(function () {
					if (!btn.isConnected) { return; }
					// بازخورد «کپی شد» — متن دکمه موقتاً عوض می‌شود
					var labelEl = btn.querySelector('.mf-pill-label');
					if (!labelEl) { return; }
					if (!labelEl.dataset.origHtml) {
						labelEl.dataset.origHtml = labelEl.innerHTML;
					}
					var fb = btn.dataset.copied || 'کپی شد';
					var dur = parseInt(btn.dataset.copiedDur, 10) || 1600;
					labelEl.textContent = fb;
					clearTimeout(btn._mfCopyTimer);
					btn._mfCopyTimer = setTimeout(function () {
						if (labelEl.isConnected && labelEl.dataset.origHtml) {
							labelEl.innerHTML = labelEl.dataset.origHtml;
						}
					}, dur);
				}).catch(function () {
					/* کپی ناموفق — بدون بازخورد اشتباه */
				});
			});
		});
	}

	/* ------------------------------------------------------------------
	 * ویجت ۵ — نوار بالا (navbar)
	 * ------------------------------------------------------------------ */
	function initNavbar(header) {
		if (!header || header.dataset.mfReady) { return; }
		header.dataset.mfReady = '1';
		header.classList.add('mf-ready');

		var burger = header.querySelector('.mf-burger');
		var overlay = header.querySelector('.mf-nav-overlay');

		function setOpen(open) {
			if (open) {
				header.classList.remove('mf-nav--hidden');
				// بدون انیمیشن تا transform نوار از بین برود —
				// وگرنه اورلی position:fixed به نوار (دارای transform) وابسته می‌شود.
				header.style.transition = 'none';
				void header.offsetWidth;
				header.style.transition = '';
			}
			if (burger) {
				burger.setAttribute('aria-expanded', open ? 'true' : 'false');
				burger.setAttribute('aria-label', open ? 'بستن منو' : 'باز کردن منو');
				burger.classList.toggle('is-open', open);
			}
			if (overlay) {
				overlay.classList.toggle('is-open', open);
				overlay.hidden = !open;
			}
		}

		function onKeydown(e) {
			if (!header.isConnected) {
				window.removeEventListener('keydown', onKeydown);
				return;
			}
			if (e.key === 'Escape') { setOpen(false); }
		}

		if (burger) {
			burger.addEventListener('click', function () {
				var open = burger.getAttribute('aria-expanded') !== 'true';
				setOpen(open);
			});
		}
		if (overlay) {
			overlay.addEventListener('click', function (e) {
				if (e.target === overlay || e.target.tagName === 'A') { setOpen(false); }
			});
		}
		window.addEventListener('keydown', onKeydown);

		// کلیک‌های «#» → بدون پرش
		header.addEventListener('click', function (e) {
			var a = e.target && e.target.closest ? e.target.closest('a') : null;
			if (!a || !header.contains(a)) { return; }
			var href = a.getAttribute('href');
			if (!href || href === '#') { e.preventDefault(); }
		});

		/* پنهان‌شدن هنگام اسکرول به پایین (فقط وقتی فعال باشد) */
		if (header.dataset.hideScroll === '1') {
			var lastY = window.scrollY;
			var onScroll = function () {
				if (!header.isConnected) {
					window.removeEventListener('scroll', onScroll);
					return;
				}
				if (burger && burger.getAttribute('aria-expanded') === 'true') {
					return; // هنگام باز بودن منو پنهان نشود
				}
				var y = window.scrollY;
				var dy = y - lastY;
				if (Math.abs(dy) < 8) { return; }
				lastY = y;
				if (dy > 0 && y > 60) {
					header.classList.add('mf-nav--hidden');
				} else if (dy < 0) {
					header.classList.remove('mf-nav--hidden');
				}
			};
			window.addEventListener('scroll', onScroll, { passive: true });
		}
	}

	/* ------------------------------------------------------------------
	 * راه‌اندازی همهٔ ویجت‌های داخل یک المان ($scope)
	 * ------------------------------------------------------------------ */
	function initAll($scope) {
		var scope = $scope && $scope.nodeType === 1 ? $scope : document;
		$$('.mf-scrub', scope).forEach(initScrub);
		$$('.mf-typewriter', scope).forEach(initTypewriter);
		$$('.mf-intro', scope).forEach(initIntro);
		$$('.mf-pills', scope).forEach(initPills);
		$$('.mf-navbar', scope).forEach(initNavbar);
	}

	function boot() {
		initAll(document);
	}

	/* هنگام لود صفحه */
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	/* ادیتور المنتور: رندر مجدد المان/ویجت → دوباره init روی همان المان.
	 * نام‌ها:
	 *   - frontend/element_ready/widget           → همهٔ ویجت‌ها (type = 'widget')
	 *   - frontend/element_ready/<name> و <name>.default → نام دقیق ویجت‌ها
	 *   - frontend/element_ready/container|section|column → المان‌های ساختاری
	 * همه idempotent هستند. */
	function registerHooks() {
		if (!window.elementorFrontend || !window.elementorFrontend.hooks) { return false; }
		var hooks = window.elementorFrontend.hooks;
		try {
			hooks.addAction('frontend/element_ready/global', initAll);
			hooks.addAction('frontend/element_ready/widget', initAll);
			['container', 'section', 'column'].forEach(function (t) {
				hooks.addAction('frontend/element_ready/' + t, initAll);
			});
			['mainframe-scrub-video', 'mainframe-typewriter', 'mainframe-intro-label', 'mainframe-pills', 'mainframe-navbar']
				.forEach(function (n) {
					hooks.addAction('frontend/element_ready/' + n, initAll);
					hooks.addAction('frontend/element_ready/' + n + '.default', initAll);
				});
			return true;
		} catch (e) {
			return false;
		}
	}

	if (!registerHooks()) {
		// elementorFrontend هنوز آماده نیست — پس از DOMContentLoaded دوباره
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { registerHooks(); });
		} else {
			setTimeout(registerHooks, 0);
		}
	}
})();

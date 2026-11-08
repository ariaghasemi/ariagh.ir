/**
 * Mainframe Hero — front-end behaviour.
 *
 * Four independent engines, each driven by a data attribute rather than by a
 * generated id, so the same widget can appear several times on one page:
 *
 *   [data-mfh-scrub]  pointer-driven video scrubbing
 *   [data-mfh-character] reactive character that follows the pointer
 *   [data-mfh-nav]    mobile menu open / close, responsive layout class
 *   [data-mfh-type]   character-by-character typewriter
 *   [data-mfh-reveal] delayed fade-and-rise entrance
 *   [data-mfh-copy]   clipboard copy
 *
 * No dependencies. Everything is guarded so a missing element, an absent
 * video, or a browser without a modern API degrades to plain, usable markup.
 *
 * @package MainframeHero
 */

(function () {
	'use strict';

	var MFH = window.MFH || {};

	/* ---------------------------------------------------------------- */
	/* Shared helpers                                                    */
	/* ---------------------------------------------------------------- */

	/**
	 * Read a JSON payload from a data attribute.
	 *
	 * @param {Element} node Element carrying the attribute.
	 * @param {string}  key  Attribute name.
	 * @return {Object} Parsed object, or an empty object.
	 */
	function config(node, key) {
		var raw = node && node.getAttribute(key);

		if (!raw) {
			return {};
		}

		try {
			var parsed = JSON.parse(raw);
			return parsed && typeof parsed === 'object' ? parsed : {};
		} catch (error) {
			return {};
		}
	}

	/**
	 * Clamp a number between two bounds.
	 *
	 * @param {number} value Number to clamp.
	 * @param {number} min   Lower bound.
	 * @param {number} max   Upper bound.
	 * @return {number}
	 */
	function clamp(value, min, max) {
		return value < min ? min : (value > max ? max : value);
	}

	/**
	 * Does the visitor ask for less motion?
	 *
	 * @return {boolean}
	 */
	function reducedMotion() {
		if (typeof window.matchMedia !== 'function') {
			return false;
		}

		return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	/**
	 * A number, or a fallback when the value is not finite.
	 *
	 * @param {*}      value    Candidate value.
	 * @param {number} fallback Fallback.
	 * @return {number}
	 */
	function num(value, fallback) {
		var parsed = parseFloat(value);
		return isFinite(parsed) ? parsed : fallback;
	}

	/**
	 * Turn a list of nodes into an array.
	 *
	 * @param {NodeList} list Node list.
	 * @return {Array}
	 */
	function toArray(list) {
		return Array.prototype.slice.call(list || []);
	}

	/* ---------------------------------------------------------------- */
	/* Scrub engine                                                      */
	/* ---------------------------------------------------------------- */

	/**
	 * Set up pointer-driven scrubbing on one video.
	 *
	 * The video never plays. Instead the pointer's travel is converted into a
	 * position on the clip's timeline. Only one seek is ever in flight: while a
	 * seek is running, further movement is folded into the pending target and
	 * applied once the element reports it has landed. That is what keeps fast
	 * movement smooth instead of flooding the decoder with requests.
	 *
	 * @param {Element} root Element carrying [data-mfh-scrub].
	 * @return {void}
	 */
	function initScrub(root) {
		// Elementor re-renders the widget after every edit, so the engine must
		// be idempotent: leave the global listeners of an existing instance
		// alone rather than stacking a second copy on the same video.
		if (root.dataset.mfhScrubReady === '1') {
			return;
		}

		var settings = config(root, 'data-mfh-scrub');
		var video = root.querySelector('.mfh-scrub__video');

		if (!video) {
			return;
		}

		root.dataset.mfhScrubReady = '1';

		var sensitivity = num(settings.sensitivity, 0.8);
		var axis = settings.axis === 'y' ? 'y' : 'x';
		var invert = !!settings.invert;
		var wrap = !!settings.wrap;
		var start = num(settings.start, 0);
		var ease = num(settings.ease, 0.25);
		var pointerOnly = settings.pointerOnly !== false && settings.pointerOnly !== 0;
		var reducedMode = settings.reducedMotion === 'keep' ? 'keep' : 'freeze';

		var duration = 0;
		var target = start;
		var current = start;
		var seeking = false;
		var rafId = 0;
		var previous = null;

		/**
		 * Ask the element to move to the pending target, once at a time.
		 *
		 * @return {void}
		 */
		function apply() {
			if (seeking || duration <= 0) {
				return;
			}

			var next = clamp(target, 0, duration);

			if (Math.abs(next - video.currentTime) < 0.001) {
				return;
			}

			seeking = true;

			try {
				video.currentTime = next;
			} catch (error) {
				seeking = false;
			}
		}

		/**
		 * Ease the visible position toward the target when smoothing is on.
		 *
		 * @return {void}
		 */
		function tick() {
			rafId = 0;

			if (ease <= 0) {
				current = target;
				apply();
				return;
			}

			var step = ease * 0.35;
			current += (target - current) * step;

			if (Math.abs(target - current) < 0.002) {
				current = target;
			}

			apply();

			if (current !== target) {
				rafId = window.requestAnimationFrame(tick);
			}
		}

		/**
		 * Schedule a frame of easing work.
		 *
		 * @return {void}
		 */
		function schedule() {
			if (ease <= 0) {
				apply();
				return;
			}

			if (!rafId) {
				rafId = window.requestAnimationFrame(tick);
			}
		}

		/**
		 * Convert pointer travel into a timeline offset and store it.
		 *
		 * @param {number} position Current pointer position on the active axis.
		 * @return {void}
		 */
		function move(position) {
			if (duration <= 0) {
				return;
			}

			if (previous === null) {
				previous = position;
				return;
			}

			var delta = position - previous;
			previous = position;

			if (invert) {
				delta = -delta;
			}

			var span = axis === 'y' ? window.innerHeight : window.innerWidth;

			if (!span) {
				return;
			}

			var offset = (delta / span) * sensitivity * duration;
			target = target + offset;

			if (wrap) {
				var length = duration;
				target = ((target % length) + length) % length;
			} else {
				target = clamp(target, 0, duration);
			}

			schedule();
		}

		/*
		 * Metadata is the gate: before it arrives there is no duration, and any
		 * seek would be meaningless. The tiny nudge to 0.01 forces the browser
		 * to decode and paint a frame without ever starting playback, which is
		 * what prevents a black hero on a clip that has never played.
		 */
		function onMeta() {
			var candidate = video.duration;

			if (!isFinite(candidate) || candidate <= 0) {
				return;
			}

			duration = candidate;

			if (!video.dataset.mfhTouched) {
				video.dataset.mfhTouched = '1';
				video.currentTime = Math.min(start || 0.01, duration - 0.01);
				target = video.currentTime;
				current = target;
			}

			root.classList.add('mfh-scrub--ready');
		}

		video.addEventListener('loadedmetadata', onMeta);
		video.addEventListener('durationchange', onMeta);

		// A seek landing clears the in-flight flag and immediately requests the
		// newest position, so nothing is lost while the decoder was busy.
		video.addEventListener('seeked', function () {
			seeking = false;
			current = video.currentTime;
			apply();
		});

		video.addEventListener('error', function () {
			seeking = false;
		});

		if (video.readyState >= 1) {
			onMeta();
		}

		if (reducedMotion() && reducedMode === 'freeze') {
			return;
		}

		var fine = !pointerOnly ||
			(typeof window.matchMedia === 'function' &&
				window.matchMedia('(hover: hover) and (pointer: fine)').matches);

		if (!fine) {
			return;
		}

		window.addEventListener('mousemove', function (event) {
			move(axis === 'y' ? event.clientY : event.clientX);
		}, { passive: true });

		window.addEventListener('mouseout', function (event) {
			// Leaving the window resets the baseline, so re-entering does not
			// register the gap as one enormous jump.
			if (!event.relatedTarget) {
				previous = null;
			}
		}, { passive: true });
	}

	/* ---------------------------------------------------------------- */
	/* Navbar                                                            */
	/* ---------------------------------------------------------------- */

	/**
	 * Set up the mobile menu and the responsive layout class for one navbar.
	 *
	 * @param {Element} root Element carrying [data-mfh-nav].
	 * @return {void}
	 */
	function initNav(root) {
		var settings = config(root, 'data-mfh-nav');
		var breakpoint = Math.max(320, parseInt(settings.breakpoint, 10) || 767);
		var overlay = root.querySelector('.mfh-nav__overlay');
		var burger = root.querySelector('.mfh-nav__burger');
		var wide = null;

		/**
		 * Apply the layout class matching the current viewport.
		 *
		 * @param {boolean} isWide Whether the viewport is above the breakpoint.
		 * @return {void}
		 */
		function layout(isWide) {
			if (isWide === wide) {
				return;
			}

			wide = isWide;
			root.classList.toggle('mfh-nav-root--wide', isWide);
			root.classList.toggle('mfh-nav-root--compact', !isWide);

			if (isWide) {
				close(true);
			}
		}

		/**
		 * Reflect the menu state on the button and dialog.
		 *
		 * @param {boolean} open Whether the menu is open.
		 * @return {void}
		 */
		function mark(open) {
			root.classList.toggle('is-open', open);
			root.classList.toggle('is-visible', open);

			if (burger) {
				burger.setAttribute('aria-expanded', open ? 'true' : 'false');

				if (MFH.i18n) {
					burger.setAttribute(
						'aria-label',
						open ? MFH.i18n.menuClose : MFH.i18n.menuOpen
					);
				}
			}

			if (overlay) {
				overlay.setAttribute('aria-hidden', open ? 'false' : 'true');
			}

			document.body.classList.toggle('mfh-nav-lock', open);
		}

		/**
		 * Open the overlay menu.
		 *
		 * @return {void}
		 */
		function open() {
			mark(true);
		}

		/**
		 * Close the overlay menu.
		 *
		 * @param {boolean} immediate Skip the fade when closing for a layout change.
		 * @return {void}
		 */
		function close(immediate) {
			if (immediate) {
				root.classList.remove('is-visible');
			}

			mark(false);
		}

		if (burger) {
			burger.addEventListener('click', function () {
				if (root.classList.contains('is-open')) {
					close(false);
				} else {
					open();
				}
			});
		}

		if (overlay) {
			overlay.addEventListener('click', function (event) {
				if (event.target.closest('a')) {
					close(false);
				}
			});
		}

		document.addEventListener('keydown', function (event) {
			if (event.key !== 'Escape' && event.key !== 'Esc') {
				return;
			}

			if (root.classList.contains('is-open')) {
				close(false);

				if (burger) {
					burger.focus();
				}
			}
		});

		/**
		 * Recompute the layout class from the live viewport width.
		 *
		 * @return {void}
		 */
		function measure() {
			layout(window.innerWidth > breakpoint);
		}

		if (typeof window.matchMedia === 'function') {
			var query = window.matchMedia('(min-width: ' + (breakpoint + 1) + 'px)');
			var handler = function (event) {
				layout(event.matches);
			};

			if (typeof query.addEventListener === 'function') {
				query.addEventListener('change', handler);
			} else if (typeof query.addListener === 'function') {
				query.addListener(handler);
			}
		}

		window.addEventListener('resize', measure, { passive: true });
		window.addEventListener('orientationchange', measure, { passive: true });
		measure();
	}

	/* ---------------------------------------------------------------- */
	/* Reactive character                                                */
	/* ---------------------------------------------------------------- */

	/**
	 * Drive one reactive character.
	 *
	 * Two things move at once. In "scrub" mode the pointer's travel scrubs the
	 * clip's timeline, exactly like the background video. In both modes the
	 * character also leans toward the pointer — a small translation and
	 * rotation, eased every frame — and breathes gently when nothing moves.
	 *
	 * @param {Element} root Element carrying [data-mfh-character].
	 * @return {void}
	 */
	function initCharacter(root) {
		if (root.dataset.mfhCharacterReady === '1') {
			return;
		}

		var settings = config(root, 'data-mfh-character');
		var video = root.querySelector('.mfh-character__video');

		if (!video) {
			return;
		}

		root.dataset.mfhCharacterReady = '1';

		var mode = settings.mode === 'autoplay' ? 'autoplay' : 'scrub';
		var sensitivity = num(settings.sensitivity, 0.8);
		var followX = num(settings.followX, 26);
		var followY = num(settings.followY, 18);
		var rotate = num(settings.rotate, 3);
		var smoothing = clamp(num(settings.smoothing, 0.06), 0.01, 1);
		var idle = !!settings.idle;
		var idleAmount = num(settings.idleAmount, 10);
		var initial = num(settings.initial, 0);

		var duration = 0;
		var seeking = false;
		var target = initial;
		var previous = null;

		var pointerX = 0;      // -1 .. 1, eased
		var pointerY = 0;
		var wantedX = 0;       // -1 .. 1, raw
		var wantedY = 0;
		var rafId = 0;
		var started = Date.now();

		/**
		 * Ask the element to move to the pending time, once at a time.
		 *
		 * @return {void}
		 */
		function apply() {
			if (seeking || duration <= 0) {
				return;
			}

			var next = clamp(target, 0, duration);

			if (Math.abs(next - video.currentTime) < 0.001) {
				return;
			}

			seeking = true;

			try {
				video.currentTime = next;
			} catch (error) {
				seeking = false;
			}
		}

		/**
		 * Advance the ease and paint the transform for one frame.
		 *
		 * @return {void}
		 */
		function frame() {
			rafId = window.requestAnimationFrame(frame);

			pointerX += (wantedX - pointerX) * smoothing;
			pointerY += (wantedY - pointerY) * smoothing;

			var bob = 0;

			if (idle && !reducedMotion()) {
				bob = Math.sin((Date.now() - started) / 1400) * idleAmount;
			}

			var x = pointerX * followX;
			var y = pointerY * followY + bob;
			var r = pointerX * rotate;

			root.style.setProperty('--mfh-char-x', x.toFixed(2) + 'px');
			root.style.setProperty('--mfh-char-y', y.toFixed(2) + 'px');
			root.style.setProperty('--mfh-char-r', r.toFixed(2) + 'deg');

			if (mode === 'scrub') {
				apply();
			}
		}

		function onMeta() {
			var candidate = video.duration;

			if (!isFinite(candidate) || candidate <= 0) {
				return;
			}

			duration = candidate;

			if (!video.dataset.mfhTouched) {
				video.dataset.mfhTouched = '1';
				video.currentTime = Math.min(initial || 0.01, duration - 0.01);
				target = video.currentTime;
			}

			root.classList.add('mfh-character--ready');
		}

		video.addEventListener('loadedmetadata', onMeta);
		video.addEventListener('durationchange', onMeta);

		video.addEventListener('seeked', function () {
			seeking = false;
			apply();
		});

		if (video.readyState >= 1) {
			onMeta();
		}

		if (mode === 'autoplay') {
			var attempt = video.play();

			if (attempt && typeof attempt.catch === 'function') {
				attempt.catch(function () {
					/* Autoplay was refused; the poster still shows. */
				});
			}
		}

		var fine = typeof window.matchMedia !== 'function' ||
			window.matchMedia('(hover: hover) and (pointer: fine)').matches;

		if (fine) {
			window.addEventListener('mousemove', function (event) {
				wantedX = clamp((event.clientX / window.innerWidth) * 2 - 1, -1, 1);
				wantedY = clamp((event.clientY / window.innerHeight) * 2 - 1, -1, 1);

				if (mode === 'scrub' && duration > 0) {
					var position = event.clientX;

					if (previous !== null) {
						var delta = position - previous;
						target = clamp(
							target + (delta / window.innerWidth) * sensitivity * duration,
							0,
							duration
						);
					}

					previous = position;
				}
			}, { passive: true });
		}

		if (reducedMotion()) {
			// No bob, no lean: the character simply sits where it is put.
			idle = false;
			followX = 0;
			followY = 0;
			rotate = 0;
		}

		rafId = window.requestAnimationFrame(frame);
	}

	/* ---------------------------------------------------------------- */
	/* Typewriter                                                        */
	/* ---------------------------------------------------------------- */

	/**
	 * Reveal one sentence a character at a time.
	 *
	 * @param {Element} root Element carrying [data-mfh-type].
	 * @return {void}
	 */
	function initType(root) {
		var settings = config(root, 'data-mfh-type');
		var out = root.querySelector('.mfh-type__text');

		if (!out) {
			return;
		}

		var text = typeof settings.text === 'string' ? settings.text : '';
		var speed = Math.max(5, parseInt(settings.speed, 10) || 38);
		var delay = Math.max(0, parseInt(settings.startDelay, 10) || 0);
		var when = settings.when === 'visible' ? 'visible' : 'load';
		var reducedMode = settings.reducedMotion === 'keep' ? 'keep' : 'instant';

		/**
		 * Print the whole sentence at once.
		 *
		 * @return {void}
		 */
		function showAll() {
			out.textContent = text;
			root.classList.add('is-done');
		}

		/**
		 * Reveal the sentence character by character.
		 *
		 * @return {void}
		 */
		function type() {
			var index = 0;

			var step = window.setInterval(function () {
				index += 1;
				out.textContent = text.slice(0, index);

				if (index >= text.length) {
					window.clearInterval(step);
					root.classList.add('is-done');
				}
			}, speed);
		}

		if (!text) {
			root.classList.add('is-done');
			return;
		}

		if (reducedMotion() && reducedMode === 'instant') {
			showAll();
			return;
		}

		if (when === 'load') {
			window.setTimeout(type, delay);
			return;
		}

		if (!('IntersectionObserver' in window)) {
			window.setTimeout(type, delay);
			return;
		}

		var observer = new IntersectionObserver(function (entries, self) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) {
					return;
				}

				self.disconnect();
				window.setTimeout(type, delay);
			});
		}, { threshold: 0.25 });

		observer.observe(root);
	}

	/* ---------------------------------------------------------------- */
	/* Reveal                                                            */
	/* ---------------------------------------------------------------- */

	/**
	 * Fade and lift an element in on its own clock.
	 *
	 * @param {Element} root Element carrying [data-mfh-reveal].
	 * @return {void}
	 */
	function initReveal(root) {
		var settings = config(root, 'data-mfh-reveal');
		var delay = Math.max(0, parseInt(settings.delay, 10) || 0);
		var distance = num(settings.distance, 8);
		var duration = num(settings.duration, 0.4);

		root.style.setProperty('--mfh-reveal-rise', distance + 'px');
		root.style.transitionDuration = duration + 's';
		root.style.transitionDelay = delay + 'ms';

		/**
		 * Add the visible class on the next frame so the transition runs.
		 *
		 * @return {void}
		 */
		function reveal() {
			window.requestAnimationFrame(function () {
				root.classList.add('is-in');
			});
		}

		if (reducedMotion()) {
			root.style.transitionDuration = '1ms';
			root.style.transitionDelay = '0ms';
			reveal();
			return;
		}

		window.setTimeout(reveal, 20);
	}

	/* ---------------------------------------------------------------- */
	/* Copy                                                              */
	/* ---------------------------------------------------------------- */

	/**
	 * Copy a value to the clipboard and confirm it briefly.
	 *
	 * @param {Element} button Element carrying [data-mfh-copy].
	 * @return {void}
	 */
	function initCopy(button) {
		var value = button.getAttribute('data-mfh-copy') || '';

		/**
		 * Show the confirmation state.
		 *
		 * @return {void}
		 */
		function confirm() {
			button.classList.add('is-copied');

			window.setTimeout(function () {
				button.classList.remove('is-copied');
			}, 400);
		}

		/**
		 * Fallback for browsers or contexts without the async clipboard API:
		 * create an offscreen field, select it, and run the legacy command.
		 *
		 * @return {boolean} Whether the copy succeeded.
		 */
		function legacy() {
			var field = document.createElement('textarea');

			field.value = value;
			field.setAttribute('readonly', 'readonly');
			field.style.position = 'fixed';
			field.style.top = '-1000px';
			field.style.opacity = '0';

			document.body.appendChild(field);
			field.select();

			var ok = false;

			try {
				ok = document.execCommand('copy');
			} catch (error) {
				ok = false;
			}

			document.body.removeChild(field);

			return ok;
		}

		button.addEventListener('click', function (event) {
			event.preventDefault();

			if (!value) {
				return;
			}

			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(value).then(confirm).catch(function () {
					if (legacy()) {
						confirm();
					} else if (MFH.i18n && MFH.i18n.copyFailed) {
						announce(MFH.i18n.copyFailed);
					}
				});

				return;
			}

			if (legacy()) {
				confirm();
			} else if (MFH.i18n && MFH.i18n.copyFailed) {
				announce(MFH.i18n.copyFailed);
			}
		});
	}

	/**
	 * Announce a short message to assistive technology.
	 *
	 * @param {string} message Message to announce.
	 * @return {void}
	 */
	function announce(message) {
		var region = document.getElementById('mfh-live');

		if (!region) {
			region = document.createElement('div');
			region.id = 'mfh-live';
			region.setAttribute('role', 'status');
			region.setAttribute('aria-live', 'polite');
			region.style.position = 'absolute';
			region.style.width = '1px';
			region.style.height = '1px';
			region.style.overflow = 'hidden';
			region.style.clip = 'rect(0 0 0 0)';
			document.body.appendChild(region);
		}

		region.textContent = message;
	}

	/* ---------------------------------------------------------------- */
	/* Boot                                                              */
	/* ---------------------------------------------------------------- */

	/**
	 * Start every engine across the page.
	 *
	 * @return {void}
	 */
	function boot() {
		toArray(document.querySelectorAll('[data-mfh-scrub]')).forEach(initScrub);
		toArray(document.querySelectorAll('[data-mfh-character]')).forEach(initCharacter);
		toArray(document.querySelectorAll('[data-mfh-nav]')).forEach(initNav);
		toArray(document.querySelectorAll('[data-mfh-type]')).forEach(initType);
		toArray(document.querySelectorAll('[data-mfh-reveal]')).forEach(initReveal);
		toArray(document.querySelectorAll('[data-mfh-copy]')).forEach(initCopy);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	/*
	 * Elementor re-renders a widget after every edit, so the engines have to
	 * run again for the fresh markup. The editor also loads this file, which is
	 * why each init is idempotent per element and cheap to repeat.
	 */
	if (window.elementorFrontend && window.elementorFrontend.hooks) {
		window.elementorFrontend.hooks.addAction(
			'frontend/element_ready/global',
			function (scope) {
				if (!scope) {
					return;
				}

				toArray(scope.querySelectorAll('[data-mfh-scrub]')).forEach(initScrub);
				toArray(scope.querySelectorAll('[data-mfh-character]')).forEach(initCharacter);
				toArray(scope.querySelectorAll('[data-mfh-nav]')).forEach(initNav);
				toArray(scope.querySelectorAll('[data-mfh-type]')).forEach(initType);
				toArray(scope.querySelectorAll('[data-mfh-reveal]')).forEach(initReveal);
				toArray(scope.querySelectorAll('[data-mfh-copy]')).forEach(initCopy);
			}
		);
	}

	window.MFH = MFH;
}());

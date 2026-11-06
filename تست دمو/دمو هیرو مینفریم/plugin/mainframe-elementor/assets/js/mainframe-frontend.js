/**
 * مین‌فریم — دارایی جلوی صفحه
 * چهار رفتار، بدون وابستگی، ایمن برای ویرایشگر المنتور:
 *  1) scrub      — ویدیو با ماوس (delta → offset → clamp → صف seek)
 *  2) typewriter — تایپ حرف‌به‌حرف با بصر چشمک‌زن
 *  3) pills      — ورود fade دکمه‌ها + کپی ایمیل
 *  4) navbar     — باز/بسته شدن منوی موبایل
 * همه initها با data-mf-ready علامت می‌خورند تا چندبار اجرا نشوند.
 */
(function () {
  'use strict';

  /* ─────────── 1) ویدیوی حرکت با ماوس ─────────── */
  function initScrub(root) {
    var wraps = root.querySelectorAll('.mf-scrub:not([data-mf-ready])');
    Array.prototype.forEach.call(wraps, function (wrap) {
      wrap.setAttribute('data-mf-ready', '1');

      var video = wrap.querySelector('video');
      if (!video) return;

      var sensitivity = parseFloat(wrap.getAttribute('data-sensitivity') || '0.8');
      if (isNaN(sensitivity)) sensitivity = 0.8;

      var prevX = null;
      var targetTime = 0;
      var seeking = false;

      function onMouseMove(e) {
        var prev = prevX;
        prevX = e.clientX;
        if (prev === null) return;
        if (!video.duration || !isFinite(video.duration)) return;

        var delta = e.clientX - prev;
        var offset = (delta / window.innerWidth) * sensitivity * video.duration;
        var next = Math.min(Math.max(targetTime + offset, 0), video.duration);
        targetTime = next;

        // فقط یک seek همزمان — بقیه در targetTime انبار می‌شوند
        if (!seeking) {
          seeking = true;
          video.currentTime = next;
        }
      }

      function onSeeked() {
        seeking = false;
        // اگر در حین seek هدف جلو رفته، seek بعدی را همین‌جا اجرا کن
        if (Math.abs(video.currentTime - targetTime) > 0.05) {
          seeking = true;
          video.currentTime = targetTime;
        }
      }

      window.addEventListener('mousemove', onMouseMove);
      video.addEventListener('seeked', onSeeked);
    });
  }

  /* ─────────── 2) تایپ‌نویسر ─────────── */
  function initTypewriter(root) {
    var els = root.querySelectorAll('.mf-typewriter:not([data-mf-ready])');
    Array.prototype.forEach.call(els, function (el) {
      el.setAttribute('data-mf-ready', '1');

      var text = el.getAttribute('data-text') || '';
      var speed = parseInt(el.getAttribute('data-speed'), 10);
      var delay = parseInt(el.getAttribute('data-delay'), 10);
      if (isNaN(speed) || speed < 1) speed = 38;
      if (isNaN(delay) || delay < 0) delay = 600;

      var textSpan = el.querySelector('.mf-typewriter-text');
      var cursor = el.querySelector('.mf-typewriter-cursor');
      if (!textSpan) return;

      var intervalId = null;
      var timeoutId = window.setTimeout(function () {
        var i = 0;
        intervalId = window.setInterval(function () {
          i += 1;
          textSpan.textContent = text.slice(0, i);
          if (i >= text.length) {
            window.clearInterval(intervalId);
            if (cursor) cursor.style.display = 'none';
          }
        }, speed);
      }, delay);

      // پاک‌سازی در صورت تخریب DOM توسط ویرایشگر
      el.addEventListener('elementor:widget:destroy', function () {
        window.clearTimeout(timeoutId);
        if (intervalId) window.clearInterval(intervalId);
      });
    });
  }

  /* ─────────── 3) دکمه‌های قرصی ─────────── */
  function initPills(root) {
    var els = root.querySelectorAll('.mf-pills:not([data-mf-ready])');
    Array.prototype.forEach.call(els, function (el) {
      el.setAttribute('data-mf-ready', '1');

      var delayRaw = el.style.getPropertyValue('--mf-pill-fade-delay') || '400ms';
      var delay = parseInt(delayRaw, 10);
      if (isNaN(delay) || delay < 0) delay = 400;

      if (delay === 0) {
        el.classList.add('is-visible');
      } else {
        window.setTimeout(function () {
          el.classList.add('is-visible');
        }, delay);
      }

      var copies = el.querySelectorAll('.mf-pill--copy');
      Array.prototype.forEach.call(copies, function (btn) {
        btn.addEventListener('click', function () {
          var email = btn.getAttribute('data-email') || '';
          if (!email) return;
          if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(email).catch(function () {});
          }
        });
      });
    });
  }

  /* ─────────── 4) نوار بالا ─────────── */
  function initNavbar(root) {
    var navs = root.querySelectorAll('.mf-navbar:not([data-mf-ready])');
    Array.prototype.forEach.call(navs, function (nav) {
      nav.setAttribute('data-mf-ready', '1');

      var burger = nav.querySelector('.mf-burger');
      var overlay = nav.querySelector('.mf-nav-overlay');
      if (!burger) return;

      function setOpen(open) {
        nav.classList.toggle('is-open', open);
        burger.setAttribute('aria-expanded', open ? 'true' : 'false');
        burger.setAttribute('aria-label', open ? 'بستن منو' : 'باز کردن منو');
      }

      burger.addEventListener('click', function () {
        setOpen(!nav.classList.contains('is-open'));
      });

      if (overlay) {
        var links = overlay.querySelectorAll('a');
        Array.prototype.forEach.call(links, function (a) {
          a.addEventListener('click', function () { setOpen(false); });
        });
      }

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && nav.classList.contains('is-open')) setOpen(false);
      });
    });
  }

  /* ─────────── اجرا ─────────── */
  function initAll(scope) {
    var root = document;
    if (scope) {
      if (scope.querySelectorAll) root = scope;
      else if (scope[0] && scope[0].querySelectorAll) root = scope[0];
    }
    initScrub(root);
    initTypewriter(root);
    initPills(root);
    initNavbar(root);
  }

  function boot() {
    initAll(document);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

  // در ویرایشگر المنتور: هر بار رندر مجدد یک ویجت، رفتار آن دوباره وصل شود
  if (window.elementorFrontend && window.elementorFrontend.hooks) {
    var widgetNames = [
      'mainframe-scrub-video',
      'mainframe-typewriter',
      'mainframe-intro-label',
      'mainframe-pills',
      'mainframe-navbar',
      'container',
      'section',
      'column',
    ];
    Array.prototype.forEach.call(widgetNames, function (name) {
      window.elementorFrontend.hooks.addAction(
        'frontend/element_ready/' + name,
        function ($scope) {
          initAll($scope);
        }
      );
    });
  }
})();

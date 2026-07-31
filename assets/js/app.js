/* ============================================================
   BALI PROJECT — Global App Script
   ============================================================ */
(function () {
  'use strict';

  const root = document.documentElement;

  /* ---------- THEME TOGGLE ---------- */
  const themeBtn = document.getElementById('theme-toggle');
  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      root.setAttribute('data-theme', next);
      try { localStorage.setItem('bali-theme', next); } catch (_) {}
    });
  }

  /* ---------- HAMBURGER ---------- */
  const nav = document.getElementById('site-nav');
  const hamburger = document.getElementById('nav-hamburger');
  const navMenu = document.getElementById('nav-menu');
  let closeTopbarMenus = function () {};

  if (nav && hamburger && navMenu) {
    const closeMenu = () => {
      nav.classList.remove('is-open');
      hamburger.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    };
    const openMenu = () => {
      closeTopbarMenus();
      nav.classList.add('is-open');
      hamburger.setAttribute('aria-expanded', 'true');
      if (window.innerWidth < 1100) document.body.style.overflow = 'hidden';
    };

    hamburger.addEventListener('click', () => {
      if (nav.classList.contains('is-open')) closeMenu();
      else openMenu();
    });

    navMenu.querySelectorAll('a').forEach((a) => a.addEventListener('click', closeMenu));

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeMenu();
    });

    document.addEventListener('click', (e) => {
      if (!nav.contains(e.target) && nav.classList.contains('is-open')) closeMenu();
    });

    window.addEventListener('resize', () => {
      if (window.innerWidth >= 1100) closeMenu();
    });
  }

  /* ---------- TOPBAR DROPDOWNS ---------- */
  const topbarMenus = Array.from(document.querySelectorAll('[data-topbar-menu]'));

  if (topbarMenus.length) {
    closeTopbarMenus = function (exceptMenu) {
      topbarMenus.forEach((menu) => {
        if (exceptMenu && menu === exceptMenu) return;
        menu.classList.remove('is-open');
        const toggle = menu.querySelector('[data-topbar-toggle]');
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
      });
    };

    topbarMenus.forEach((menu) => {
      const toggle = menu.querySelector('[data-topbar-toggle]');
      const dropdown = menu.querySelector('[data-topbar-dropdown]');
      if (!toggle || !dropdown) return;

      toggle.addEventListener('click', (event) => {
        event.stopPropagation();
        const willOpen = !menu.classList.contains('is-open');
        closeTopbarMenus(menu);

        if (willOpen) {
          menu.classList.add('is-open');
          toggle.setAttribute('aria-expanded', 'true');
          if (nav && hamburger) {
            nav.classList.remove('is-open');
            hamburger.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
          }
        } else {
          menu.classList.remove('is-open');
          toggle.setAttribute('aria-expanded', 'false');
        }
      });

      dropdown.addEventListener('click', (event) => {
        event.stopPropagation();
      });
    });

    document.addEventListener('click', (event) => {
      if (!event.target.closest('[data-topbar-menu]')) closeTopbarMenus();
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') closeTopbarMenus();
    });

    window.addEventListener('resize', () => closeTopbarMenus());
  }

  /* ---------- NAVBAR SCROLLED STATE ---------- */
  if (nav) {
    const onScroll = () => {
      if (window.scrollY > 12) nav.classList.add('is-scrolled');
      else nav.classList.remove('is-scrolled');
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---------- TOPBAR INVOICE NAV SYNC ---------- */
  const invoiceNav = document.querySelector('[data-invoice-nav]');
  if (invoiceNav) {
    const badge = invoiceNav.querySelector('[data-invoice-count]');
    const countUrl = invoiceNav.getAttribute('data-invoice-count-url');
    let syncingInvoiceCount = false;

    const setInvoiceCount = (count) => {
      if (!badge) return;
      const safeCount = Math.max(0, parseInt(count || '0', 10) || 0);
      badge.textContent = safeCount > 99 ? '99+' : String(safeCount);
      badge.classList.toggle('is-empty', safeCount === 0);
      badge.classList.remove('is-syncing');
    };

    const syncInvoiceCount = () => {
      if (!countUrl || syncingInvoiceCount || document.hidden) return;
      syncingInvoiceCount = true;
      if (badge) badge.classList.add('is-syncing');

      fetch(countUrl, {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then((response) => {
          if (!response.ok) throw new Error('invoice-count-failed');
          return response.json();
        })
        .then((data) => {
          setInvoiceCount(data && data.ok ? data.count : 0);
        })
        .catch(() => {
          if (badge) {
            badge.textContent = '!';
            badge.classList.remove('is-empty');
            badge.classList.remove('is-syncing');
          }
        })
        .finally(() => {
          syncingInvoiceCount = false;
        });
    };

    syncInvoiceCount();
    window.addEventListener('focus', syncInvoiceCount);
    document.addEventListener('visibilitychange', syncInvoiceCount);
    window.setInterval(syncInvoiceCount, 10000);
  }

  /* ---------- REVEAL ON SCROLL ---------- */
  const revealEls = document.querySelectorAll('[data-reveal]');
  if (revealEls.length && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver(
      (entries, observer) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
    );
    revealEls.forEach((el) => io.observe(el));
  } else {
    revealEls.forEach((el) => el.classList.add('is-visible'));
  }

  /* ---------- COUNT UP ---------- */
  const counters = document.querySelectorAll('[data-count]');
  if (counters.length && 'IntersectionObserver' in window) {
    const animate = (el) => {
      const target = parseFloat(el.getAttribute('data-count')) || 0;
      const duration = parseInt(el.getAttribute('data-count-duration') || '1600', 10);
      const decimals = parseInt(el.getAttribute('data-count-decimals') || '0', 10);
      const suffix = el.getAttribute('data-count-suffix') || '';
      const prefix = el.getAttribute('data-count-prefix') || '';
      const start = performance.now();
      const tick = (now) => {
        const p = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - p, 3);
        const value = (target * eased).toFixed(decimals);
        el.textContent = prefix + value + suffix;
        if (p < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    };

    const cio = new IntersectionObserver(
      (entries, observer) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            animate(entry.target);
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.4 }
    );
    counters.forEach((el) => cio.observe(el));
  }

  /* ---------- TOAST ---------- */
  let activeToast = null;
  window.showToast = function (msg, opts) {
    opts = opts || {};
    if (activeToast) activeToast.remove();
    const t = document.createElement('div');
    t.className = 'toast toast-enter';
    t.innerHTML =
      '<span class="toast__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>' +
      '<span>' + (msg || '') + '</span>';
    document.body.appendChild(t);
    activeToast = t;
    const ms = opts.duration || 3000;
    setTimeout(() => {
      t.classList.remove('toast-enter');
      t.classList.add('toast-exit');
      setTimeout(() => { t.remove(); if (activeToast === t) activeToast = null; }, 320);
    }, ms);
  };

  /* ---------- LIGHTBOX (auto-init for [data-lightbox]) ---------- */
  document.querySelectorAll('[data-lightbox]').forEach((img) => {
    img.style.cursor = 'zoom-in';
    img.addEventListener('click', () => openLightbox(img));
  });

  let lightboxEl = null;
  function ensureLightbox() {
    if (lightboxEl) return lightboxEl;
    lightboxEl = document.createElement('div');
    lightboxEl.className = 'modal-overlay';
    lightboxEl.innerHTML =
      '<div class="modal" style="padding: 12px; background: transparent; box-shadow: none; max-width: 92vw;">' +
      '<button class="modal__close" type="button" aria-label="Close">' +
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
      '</button>' +
      '<img alt="" style="max-width: 92vw; max-height: 86vh; border-radius: var(--r-2xl); box-shadow: var(--shadow-xl);" />' +
      '</div>';
    document.body.appendChild(lightboxEl);
    lightboxEl.addEventListener('click', (e) => {
      if (e.target === lightboxEl || e.target.closest('.modal__close')) closeLightbox();
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeLightbox();
    });
    return lightboxEl;
  }

  function openLightbox(srcEl) {
    const lb = ensureLightbox();
    lb.querySelector('img').src = srcEl.src;
    lb.querySelector('img').alt = srcEl.alt || '';
    lb.classList.add('is-active');
  }
  function closeLightbox() {
    if (lightboxEl) lightboxEl.classList.remove('is-active');
  }

  /* ---------- BUTTON RIPPLE ---------- */
  document.addEventListener('mousedown', (e) => {
    const btn = e.target.closest('.btn');
    if (!btn) return;
    const rect = btn.getBoundingClientRect();
    btn.style.setProperty('--rx', ((e.clientX - rect.left) / rect.width) * 100 + '%');
    btn.style.setProperty('--ry', ((e.clientY - rect.top) / rect.height) * 100 + '%');
  });

  /* ---------- LAZY-LOAD POLYFILL ---------- */
  if (!('loading' in HTMLImageElement.prototype) && 'IntersectionObserver' in window) {
    const lazyImgs = document.querySelectorAll('img[loading="lazy"][data-src]');
    const lio = new IntersectionObserver((entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.src = entry.target.dataset.src;
          obs.unobserve(entry.target);
        }
      });
    });
    lazyImgs.forEach((i) => lio.observe(i));
  }
})();

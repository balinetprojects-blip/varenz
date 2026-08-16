(() => {
  'use strict';

  /*
  |--------------------------------------------------------------------------
  | Varenz Supplies Ltd
  | Public Website Application Controller
  |--------------------------------------------------------------------------
  |
  | COMPLETE REPLACEMENT FILE
  |
  | Includes:
  | - Progressive enhancement
  | - Theme switching
  | - Mobile navigation
  | - Search
  | - Hero carousel
  | - Request form
  | - Challenges
  | - Product categories
  | - Featured products
  | - Procurement process
  | - Organisation showcase
  | - Why Varenz orbit
  | - Legacy team profile support
  | - CONTINUOUS PARTNER LOGO CAROUSEL
  | - FAQ
  | - Floating action hub
  | - Image fallbacks
  | - Responsive recalculation
  | - Accessibility
  | - Reduced-motion support
  |
  */

  const app =
    window.VARENZ_APP &&
    typeof window.VARENZ_APP === 'object'
      ? window.VARENZ_APP
      : {};

  const site =
    app.site &&
    typeof app.site === 'object'
      ? app.site
      : {};

  const baseUrl = String(
    app.baseUrl || '/'
  ).replace(/\/?$/, '/');

  const csrfToken =
    String(app.csrf || '');

  const prefersReducedMotion =
    window.matchMedia?.(
      '(prefers-reduced-motion: reduce)'
    ).matches === true;

  const finePointer =
    window.matchMedia?.(
      '(hover: hover) and (pointer: fine)'
    ).matches === true;

  const timers = new Set();
  const intervals = new Set();
  const observers = new Set();

  let resizeTimer = 0;


  /*
  |--------------------------------------------------------------------------
  | HELPERS
  |--------------------------------------------------------------------------
  */

  const qs = (
    selector,
    root = document
  ) => root.querySelector(selector);

  const qsa = (
    selector,
    root = document
  ) => Array.from(
    root.querySelectorAll(selector)
  );

  const text = value =>
    String(value ?? '');

  function escapeHtml(value) {
    return text(value).replace(
      /[&<>"']/g,
      character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      }[character])
    );
  }

  function normalizeSlug(value) {
    return text(value)
      .toLowerCase()
      .replace(/['’]/g, '')
      .replace(/&/g, ' and ')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  function assetUrl(path) {
    const clean = text(path)
      .trim()
      .replace(/^\/+/, '');

    if (!clean) {
      return '';
    }

    if (
      /^https?:\/\//i.test(clean) ||
      clean.startsWith('data:') ||
      clean.startsWith('blob:')
    ) {
      return clean;
    }

    if (clean.startsWith('assets/')) {
      return `${baseUrl}${clean}`;
    }

    return `${baseUrl}assets/${clean}`;
  }

  function logoFallbackUrl() {
    return assetUrl(
      'images/logo/varenz-icon-logo-clean.png'
    );
  }

  function managedTimeout(
    callback,
    delay
  ) {
    const timer =
      window.setTimeout(
        () => {
          timers.delete(timer);
          callback();
        },
        delay
      );

    timers.add(timer);

    return timer;
  }

  function clearManagedTimeout(timer) {
    if (!timer) {
      return;
    }

    window.clearTimeout(timer);
    timers.delete(timer);
  }

  function managedInterval(
    callback,
    delay
  ) {
    const timer =
      window.setInterval(
        callback,
        delay
      );

    intervals.add(timer);

    return timer;
  }

  function clearManagedInterval(timer) {
    if (!timer) {
      return;
    }

    window.clearInterval(timer);
    intervals.delete(timer);
  }

  function createObserver(
    callback,
    options
  ) {
    if (
      !(
        'IntersectionObserver'
        in window
      )
    ) {
      return null;
    }

    const observer =
      new IntersectionObserver(
        callback,
        options
      );

    observers.add(observer);

    return observer;
  }

  function whenNear(
    target,
    callback,
    rootMargin = '500px 0px'
  ) {
    const element =
      typeof target === 'string'
        ? qs(target)
        : target;

    if (!element) {
      return;
    }

    const observer =
      createObserver(
        entries => {
          if (
            !entries.some(
              entry =>
                entry.isIntersecting
            )
          ) {
            return;
          }

          observer?.disconnect();
          observers.delete(observer);

          callback(element);
        },
        {
          rootMargin,
          threshold: 0
        }
      );

    if (!observer) {
      callback(element);
      return;
    }

    observer.observe(element);
  }

  function smoothScrollTo(selector) {
    if (
      !selector ||
      !selector.startsWith('#')
    ) {
      return;
    }

    const element = qs(selector);

    if (!element) {
      return;
    }

    element.scrollIntoView({
      behavior:
        prefersReducedMotion
          ? 'auto'
          : 'smooth',
      block: 'start'
    });
  }


  /*
  |--------------------------------------------------------------------------
  | ACCESSIBILITY
  |--------------------------------------------------------------------------
  */

  function focusableElements(root) {
    if (!root) {
      return [];
    }

    return qsa(
      [
        'a[href]',
        'button:not([disabled])',
        'input:not([disabled]):not([type="hidden"])',
        'select:not([disabled])',
        'textarea:not([disabled])',
        '[tabindex]:not([tabindex="-1"])'
      ].join(','),
      root
    ).filter(
      element =>
        !element.hidden &&
        element.getClientRects()
          .length > 0
    );
  }

  function trapTab(
    event,
    root
  ) {
    if (
      event.key !== 'Tab' ||
      !root
    ) {
      return;
    }

    const controls =
      focusableElements(root);

    if (!controls.length) {
      return;
    }

    const first = controls[0];
    const last =
      controls[
        controls.length - 1
      ];

    if (
      event.shiftKey &&
      document.activeElement === first
    ) {
      event.preventDefault();
      last.focus();

      return;
    }

    if (
      !event.shiftKey &&
      document.activeElement === last
    ) {
      event.preventDefault();
      first.focus();
    }
  }

  function activateButtons(
    buttons,
    activeButton
  ) {
    buttons.forEach(button => {
      const active =
        button === activeButton;

      button.classList.toggle(
        'active',
        active
      );

      if (
        button.hasAttribute(
          'aria-pressed'
        )
      ) {
        button.setAttribute(
          'aria-pressed',
          active
            ? 'true'
            : 'false'
        );
      }

      if (
        button.hasAttribute(
          'aria-selected'
        ) ||
        button.getAttribute(
          'role'
        ) === 'tab'
      ) {
        button.setAttribute(
          'aria-selected',
          active
            ? 'true'
            : 'false'
        );

        button.tabIndex =
          active
            ? 0
            : -1;
      }
    });
  }


  /*
  |--------------------------------------------------------------------------
  | BODY LOCK
  |--------------------------------------------------------------------------
  */

  function setBodyLock(
    key,
    locked
  ) {
    const locks =
      new Set(
        text(
          document.body.dataset
            .vslLocks
        )
          .split(',')
          .map(item =>
            item.trim()
          )
          .filter(Boolean)
      );

    if (locked) {
      locks.add(key);
    } else {
      locks.delete(key);
    }

    document.body.dataset.vslLocks =
      Array.from(locks)
        .join(',');

    document.body.classList.toggle(
      'is-scroll-locked',
      locks.size > 0
    );

    document.body.style.overflow =
      locks.size
        ? 'hidden'
        : '';
  }


  /*
  |--------------------------------------------------------------------------
  | FETCH
  |--------------------------------------------------------------------------
  */

  async function fetchJson(
    url,
    options = {}
  ) {
    const response =
      await fetch(
        url,
        {
          credentials:
            'same-origin',

          redirect:
            'same-origin',

          cache:
            'no-store',

          ...options,

          headers: {
            Accept:
              'application/json',

            'X-Requested-With':
              'XMLHttpRequest',

            ...(options.headers || {})
          }
        }
      );

    let payload = null;

    try {
      payload =
        await response.json();
    } catch (_) {
      payload = null;
    }

    if (!response.ok) {
      throw new Error(
        payload?.message ||
        payload?.error ||
        'The request could not be completed.'
      );
    }

    return payload;
  }


  /*
  |--------------------------------------------------------------------------
  | TOASTS
  |--------------------------------------------------------------------------
  */

  function showToast(
    message,
    type = 'success',
    duration = 5200
  ) {
    const stack =
      qs('#toastStack');

    if (!stack) {
      return;
    }

    const toast =
      document.createElement(
        'div'
      );

    toast.className = [
      'toast',
      'vsl-glass-toast',
      type === 'error'
        ? 'error'
        : type === 'info'
          ? 'info'
          : 'success'
    ].join(' ');

    toast.setAttribute(
      'role',
      type === 'error'
        ? 'alert'
        : 'status'
    );

    toast.innerHTML = `
      <span
        class="toast-indicator"
        aria-hidden="true"
      ></span>

      <span class="toast-copy">
        ${escapeHtml(message)}
      </span>
    `;

    stack.appendChild(toast);

    requestAnimationFrame(
      () => {
        toast.classList.add(
          'is-visible'
        );
      }
    );

    managedTimeout(
      () => {
        toast.classList.remove(
          'is-visible'
        );

        managedTimeout(
          () => toast.remove(),
          prefersReducedMotion
            ? 0
            : 320
        );
      },
      duration
    );
  }


  /*
  |--------------------------------------------------------------------------
  | IMAGE RECOVERY
  |--------------------------------------------------------------------------
  */

  function recoverImage(image) {
    if (
      !(
        image instanceof
        HTMLImageElement
      ) ||
      image.dataset
        .vslFallbackBound ===
        'true'
    ) {
      return;
    }

    image.dataset.vslFallbackBound =
      'true';

    image.addEventListener(
      'load',
      () => {
        image.classList.add(
          'is-loaded'
        );

        image
          .closest(
            [
              '.product-card',
              '.ajax-card',
              '.team-card',
              '.partner-card',
              '.step-image',
              '.org-stage',
              '.hero-showcase-image',
              '.challenge-image'
            ].join(',')
          )
          ?.classList.add(
            'has-loaded-image'
          );
      }
    );

    image.addEventListener(
      'error',
      () => {
        const holder =
          image.closest(
            [
              '.product-card',
              '.ajax-card',
              '.team-card',
              '.partner-card',
              '.step-image',
              '.org-stage',
              '.hero-showcase-image',
              '.challenge-image',
              '.hero-slide-media'
            ].join(',')
          );

        holder?.classList.add(
          'image-unavailable'
        );

        if (
          image.dataset
            .vslFallbackApplied ===
          'true'
        ) {
          image.hidden = true;

          return;
        }

        image.dataset
          .vslFallbackApplied =
            'true';

        image.classList.add(
          'is-fallback-image'
        );

        image.src =
          logoFallbackUrl();

        image.alt =
          image.alt ||
          'Varenz Supplies Ltd';
      }
    );
  }

  function bindImageRecovery(
    root = document
  ) {
    qsa(
      'img',
      root
    ).forEach(
      recoverImage
    );
  }

  bindImageRecovery();


  /*
  |--------------------------------------------------------------------------
  | PREMIUM SURFACES
  |--------------------------------------------------------------------------
  */

  const premiumCardSelector = [
    '.ajax-card',
    '.product-card',
    '.result-card',
    '.guide-tile',
    '.org-pill',
    '.team-card',
    '.partner-card',
    '.why-detail',
    '.request-assistant',
    '.cta-form-shell',
    '.hero-showcase-frame',
    '.vsl-choice-card',
    '.vsl-opportunity-card',
    '.faq-item'
  ].join(',');

  function enhancePremiumSurface(
    surface
  ) {
    if (
      !surface ||
      surface.dataset.vslEnhanced ===
        'true'
    ) {
      return;
    }

    surface.dataset.vslEnhanced =
      'true';

    surface.classList.add(
      'vsl-premium-surface'
    );

    if (
      !finePointer ||
      prefersReducedMotion
    ) {
      return;
    }

    surface.addEventListener(
      'pointermove',
      event => {
        const rect =
          surface.getBoundingClientRect();

        if (
          rect.width <= 0 ||
          rect.height <= 0
        ) {
          return;
        }

        const x =
          (
            event.clientX -
            rect.left
          ) / rect.width;

        const y =
          (
            event.clientY -
            rect.top
          ) / rect.height;

        surface.style.setProperty(
          '--vsl-pointer-x',
          `${x * 100}%`
        );

        surface.style.setProperty(
          '--vsl-pointer-y',
          `${y * 100}%`
        );

        surface.style.setProperty(
          '--vsl-tilt-x',
          `${
            (0.5 - y) * 1.2
          }deg`
        );

        surface.style.setProperty(
          '--vsl-tilt-y',
          `${
            (x - 0.5) * 1.6
          }deg`
        );
      }
    );

    surface.addEventListener(
      'pointerleave',
      () => {
        surface.style
          .removeProperty(
            '--vsl-tilt-x'
          );

        surface.style
          .removeProperty(
            '--vsl-tilt-y'
          );
      }
    );
  }

  function enhancePremiumSurfaces(
    root = document
  ) {
    qsa(
      premiumCardSelector,
      root
    ).forEach(
      enhancePremiumSurface
    );
  }

  enhancePremiumSurfaces();


  /*
  |--------------------------------------------------------------------------
  | SECTION REVEALS
  |--------------------------------------------------------------------------
  */

  function initializeSectionReveals() {
    const sections =
      qsa('main > section');

    if (
      prefersReducedMotion ||
      !(
        'IntersectionObserver'
        in window
      )
    ) {
      sections.forEach(section => {
        section.classList.add(
          'is-revealed'
        );
      });

      return;
    }

    const observer =
      createObserver(
        entries => {
          entries.forEach(
            entry => {
              if (
                !entry.isIntersecting
              ) {
                return;
              }

              entry.target.classList
                .add(
                  'is-revealed'
                );

              observer.unobserve(
                entry.target
              );
            }
          );
        },
        {
          rootMargin:
            '0px 0px -8% 0px',

          threshold:
            0.08
        }
      );

    if (!observer) {
      return;
    }

    sections.forEach(section => {
      section.classList.add(
        'vsl-reveal-section'
      );

      observer.observe(section);
    });
  }

  initializeSectionReveals();


  /*
  |--------------------------------------------------------------------------
  | THEME
  |--------------------------------------------------------------------------
  */

  const themeToggle =
    qs('#themeToggle');

  function currentTheme() {
    return (
      document.documentElement
        .dataset.theme === 'dark'
    )
      ? 'dark'
      : 'light';
  }

  function updateThemeButton() {
    if (!themeToggle) {
      return;
    }

    const dark =
      currentTheme() === 'dark';

    themeToggle.setAttribute(
      'aria-pressed',
      dark
        ? 'true'
        : 'false'
    );

    themeToggle.setAttribute(
      'aria-label',
      dark
        ? 'Use light color theme'
        : 'Use dark color theme'
    );
  }

  themeToggle?.addEventListener(
    'click',
    () => {
      const next =
        currentTheme() === 'dark'
          ? 'light'
          : 'dark';

      document.documentElement
        .dataset.theme = next;

      try {
        localStorage.setItem(
          'vsl-theme',
          next
        );
      } catch (_) {
        // Storage unavailable.
      }

      updateThemeButton();

      showToast(
        next === 'dark'
          ? 'Dark theme enabled.'
          : 'Light theme enabled.',
        'info',
        2200
      );
    }
  );

  updateThemeButton();


  /*
  |--------------------------------------------------------------------------
  | MOBILE DRAWER
  |--------------------------------------------------------------------------
  */

  const mobileDrawer =
    qs('#mobileDrawer');

  const openDrawer =
    qs('#openVhiDrawer');

  const closeDrawer =
    qs('#closeVhiDrawer');

  const drawerOverlay =
    qs('#drawerOverlay');

  let drawerReturnFocus = null;

  function drawerIsOpen() {
    return Boolean(
      mobileDrawer?.classList
        .contains('is-open') ||
      mobileDrawer?.classList
        .contains('open')
    );
  }

  function setDrawer(open) {
    if (!mobileDrawer) {
      return;
    }

    if (
      open &&
      drawerIsOpen()
    ) {
      return;
    }

    if (
      !open &&
      !drawerIsOpen()
    ) {
      return;
    }

    if (open) {
      drawerReturnFocus =
        document.activeElement
        instanceof HTMLElement
          ? document.activeElement
          : null;
    }

    mobileDrawer.classList.toggle(
      'is-open',
      open
    );

    mobileDrawer.classList.toggle(
      'open',
      open
    );

    mobileDrawer.setAttribute(
      'aria-hidden',
      open
        ? 'false'
        : 'true'
    );

    openDrawer?.setAttribute(
      'aria-expanded',
      open
        ? 'true'
        : 'false'
    );

    document.body.classList.toggle(
      'drawer-open',
      open
    );

    setBodyLock(
      'drawer',
      open
    );

    if (open) {
      managedTimeout(
        () => {
          focusableElements(
            mobileDrawer
          )[0]?.focus();
        },
        prefersReducedMotion
          ? 0
          : 80
      );
    } else {
      drawerReturnFocus
        ?.focus?.();

      drawerReturnFocus = null;
    }
  }

  openDrawer?.addEventListener(
    'click',
    () => setDrawer(true)
  );

  closeDrawer?.addEventListener(
    'click',
    () => setDrawer(false)
  );

  drawerOverlay?.addEventListener(
    'click',
    () => setDrawer(false)
  );

  mobileDrawer?.addEventListener(
    'keydown',
    event => {
      trapTab(
        event,
        mobileDrawer
      );
    }
  );

  mobileDrawer?.addEventListener(
    'click',
    event => {
      const link =
        event.target.closest(
          'a[href]'
        );

      if (!link) {
        return;
      }

      setDrawer(false);
    }
  );


  /*
  |--------------------------------------------------------------------------
  | HERO CAROUSEL
  |--------------------------------------------------------------------------
  */

  const heroSlider =
    qs('#heroSlider');

  const heroSlides =
    qsa('[data-hero-slide]');

  const heroDots =
    qsa('[data-hero-dot]');

  const heroPause =
    qs('#vprHeroPause');

  let heroIndex = 0;
  let heroTimer = 0;
  let heroVisible = true;
  let heroPaused = false;
  let heroTouchStartX = 0;

  function hydrateHero(slide) {
    if (
      !slide ||
      slide.dataset.hydrated ===
        'true'
    ) {
      return;
    }

    qsa(
      '[data-srcset]',
      slide
    ).forEach(source => {
      if (
        source.dataset.srcset
      ) {
        source.srcset =
          source.dataset.srcset;
      }

      source.removeAttribute(
        'data-srcset'
      );
    });

    qsa(
      'img[data-src]',
      slide
    ).forEach(image => {
      if (image.dataset.src) {
        image.src =
          image.dataset.src;
      }

      image.removeAttribute(
        'data-src'
      );

      recoverImage(image);
    });

    slide.dataset.hydrated =
      'true';
  }

  function preloadHero(index) {
    if (!heroSlides.length) {
      return;
    }

    const normalized =
      (
        index +
        heroSlides.length
      ) % heroSlides.length;

    hydrateHero(
      heroSlides[normalized]
    );
  }

  function showHero(
    index,
    focusTab = false
  ) {
    if (!heroSlides.length) {
      return;
    }

    heroIndex =
      (
        index +
        heroSlides.length
      ) % heroSlides.length;

    hydrateHero(
      heroSlides[heroIndex]
    );

    heroSlides.forEach(
      (slide, slideIndex) => {
        const active =
          slideIndex === heroIndex;

        slide.classList.toggle(
          'is-active',
          active
        );

        slide.setAttribute(
          'aria-hidden',
          active
            ? 'false'
            : 'true'
        );

        if ('inert' in slide) {
          slide.inert = !active;
        }
      }
    );

    heroDots.forEach(
      (dot, dotIndex) => {
        const active =
          dotIndex === heroIndex;

        dot.classList.toggle(
          'active',
          active
        );

        dot.setAttribute(
          'aria-selected',
          active
            ? 'true'
            : 'false'
        );

        dot.tabIndex =
          active
            ? 0
            : -1;

        if (
          active &&
          focusTab
        ) {
          dot.focus();
        }
      }
    );

    heroSlider?.style
      .setProperty(
        '--vsl-hero-progress',
        `${
          (
            (
              heroIndex + 1
            ) / heroSlides.length
          ) * 100
        }%`
      );

    managedTimeout(
      () => {
        preloadHero(
          heroIndex + 1
        );
      },
      260
    );
  }

  function stopHero() {
    clearManagedInterval(
      heroTimer
    );

    heroTimer = 0;
  }

  function restartHero() {
    stopHero();

    if (
      prefersReducedMotion ||
      document.hidden ||
      !heroVisible ||
      heroPaused ||
      heroSlides.length < 2
    ) {
      return;
    }

    heroTimer =
      managedInterval(
        () => {
          showHero(
            heroIndex + 1
          );
        },
        7600
      );
  }

  qs('#heroPrev')
    ?.addEventListener(
      'click',
      () => {
        showHero(
          heroIndex - 1
        );

        restartHero();
      }
    );

  qs('#heroNext')
    ?.addEventListener(
      'click',
      () => {
        showHero(
          heroIndex + 1
        );

        restartHero();
      }
    );

  heroPause?.addEventListener(
    'click',
    () => {
      heroPaused = !heroPaused;

      if (heroPaused) {
        stopHero();
      } else {
        restartHero();
      }

      heroPause.setAttribute(
        'aria-pressed',
        heroPaused
          ? 'true'
          : 'false'
      );

      heroPause.setAttribute(
        'aria-label',
        heroPaused
          ? 'Resume hero autoplay'
          : 'Pause hero autoplay'
      );

      const label =
        qs(
          'span',
          heroPause
        );

      if (label) {
        label.textContent =
          heroPaused
            ? '▶'
            : 'Ⅱ';
      }
    }
  );

  heroDots.forEach(dot => {
    dot.addEventListener(
      'click',
      () => {
        showHero(
          Number(
            dot.dataset.heroDot ||
            0
          )
        );

        restartHero();
      }
    );

    dot.addEventListener(
      'keydown',
      event => {
        if (
          ![
            'ArrowLeft',
            'ArrowRight',
            'Home',
            'End'
          ].includes(event.key)
        ) {
          return;
        }

        event.preventDefault();

        let next =
          heroIndex;

        if (
          event.key === 'Home'
        ) {
          next = 0;
        } else if (
          event.key === 'End'
        ) {
          next =
            heroSlides.length - 1;
        } else if (
          event.key ===
          'ArrowLeft'
        ) {
          next =
            heroIndex - 1;
        } else {
          next =
            heroIndex + 1;
        }

        showHero(
          next,
          true
        );

        restartHero();
      }
    );
  });

  heroSlider?.addEventListener(
    'touchstart',
    event => {
      heroTouchStartX =
        event.changedTouches[0]
          ?.clientX || 0;
    },
    {
      passive: true
    }
  );

  heroSlider?.addEventListener(
    'touchend',
    event => {
      const endX =
        event.changedTouches[0]
          ?.clientX || 0;

      const distance =
        endX -
        heroTouchStartX;

      if (
        Math.abs(distance) < 48
      ) {
        return;
      }

      showHero(
        heroIndex +
        (
          distance < 0
            ? 1
            : -1
        )
      );

      restartHero();
    },
    {
      passive: true
    }
  );

  heroSlider?.addEventListener(
    'mouseenter',
    stopHero
  );

  heroSlider?.addEventListener(
    'mouseleave',
    restartHero
  );

  heroSlider?.addEventListener(
    'focusin',
    stopHero
  );

  heroSlider?.addEventListener(
    'focusout',
    restartHero
  );

  if (
    heroSlider &&
    'IntersectionObserver'
      in window
  ) {
    const observer =
      createObserver(
        entries => {
          heroVisible =
            entries.some(
              entry =>
                entry.isIntersecting
            );

          document.body
            .classList.toggle(
              'hero-in-view',
              heroVisible
            );

          restartHero();
        },
        {
          threshold: 0.12
        }
      );

    observer?.observe(
      heroSlider
    );
  }

  if (heroSlides.length) {
    showHero(0);
    restartHero();
  }


  /*
  |--------------------------------------------------------------------------
  | SEARCH
  |--------------------------------------------------------------------------
  */

  const searchOverlay =
    qs('#searchOverlay');

  const searchPanel =
    qs(
      '.search-panel',
      searchOverlay || document
    );

  const searchInput =
    qs('#siteSearchInput');

  const searchResults =
    qs('#siteSearchResults');

  let searchTimer = 0;
  let searchRequest = null;
  let searchReturnFocus = null;

  function searchIsOpen() {
    return Boolean(
      searchOverlay?.classList
        .contains('open')
    );
  }

  function setSearch(open) {
    if (!searchOverlay) {
      return;
    }

    if (
      open &&
      searchIsOpen()
    ) {
      return;
    }

    if (
      !open &&
      !searchIsOpen()
    ) {
      return;
    }

    if (open) {
      searchReturnFocus =
        document.activeElement
        instanceof HTMLElement
          ? document.activeElement
          : null;
    }

    searchOverlay.classList.toggle(
      'open',
      open
    );

    searchOverlay.setAttribute(
      'aria-hidden',
      open
        ? 'false'
        : 'true'
    );

    qs('#openSearch')
      ?.setAttribute(
        'aria-expanded',
        open
          ? 'true'
          : 'false'
      );

    document.body.classList.toggle(
      'search-open',
      open
    );

    setBodyLock(
      'search',
      open
    );

    if (open) {
      managedTimeout(
        () => {
          searchInput?.focus();
        },
        prefersReducedMotion
          ? 0
          : 80
      );
    } else {
      searchRequest?.abort();
      searchRequest = null;

      searchReturnFocus
        ?.focus?.();

      searchReturnFocus = null;
    }
  }

  [
    qs('#openSearch'),
    qs('#footerSearch')
  ]
    .filter(Boolean)
    .forEach(button => {
      button.addEventListener(
        'click',
        () => setSearch(true)
      );
    });

  [
    qs('#closeSearch'),
    qs('#closeSearchButton')
  ]
    .filter(Boolean)
    .forEach(button => {
      button.addEventListener(
        'click',
        () => setSearch(false)
      );
    });

  qs('#drawerSearchAction')
    ?.addEventListener(
      'click',
      () => {
        setDrawer(false);

        managedTimeout(
          () => setSearch(true),
          prefersReducedMotion
            ? 0
            : 100
        );
      }
    );

  searchOverlay?.addEventListener(
    'keydown',
    event => {
      trapTab(
        event,
        searchPanel ||
        searchOverlay
      );
    }
  );

  function safeSearchUrl(value) {
    const candidate =
      text(value).trim();

    if (!candidate) {
      return '#';
    }

    if (
      candidate.startsWith('#')
    ) {
      return candidate;
    }

    try {
      const parsed =
        new URL(
          candidate,
          window.location.origin
        );

      if (
        parsed.origin !==
        window.location.origin
      ) {
        return '#';
      }

      return (
        parsed.pathname +
        parsed.search +
        parsed.hash
      );
    } catch (_) {
      return '#';
    }
  }

  function renderSearch(items) {
    if (!searchResults) {
      return;
    }

    searchResults.setAttribute(
      'aria-busy',
      'false'
    );

    if (
      !Array.isArray(items) ||
      !items.length
    ) {
      searchResults.innerHTML = `
        <div class="search-empty">
          <strong>
            No matching content found.
          </strong>

          <p>
            Try a product, organisation,
            procurement step or company name.
          </p>
        </div>
      `;

      return;
    }

    searchResults.innerHTML =
      items.map(item => `
        <a
          class="search-result"
          href="${escapeHtml(
            safeSearchUrl(
              item?.url
            )
          )}"
        >
          <strong>
            ${escapeHtml(
              item?.title
            )}
          </strong>

          <span>
            ${escapeHtml(
              item?.summary
            )}
          </span>

          <span
            class="search-result-arrow"
            aria-hidden="true"
          >
            →
          </span>
        </a>
      `).join('');

    enhancePremiumSurfaces(
      searchResults
    );
  }

  searchInput?.addEventListener(
    'input',
    () => {
      clearManagedTimeout(
        searchTimer
      );

      const term =
        searchInput.value.trim();

      if (
        term.length < 2
      ) {
        searchRequest?.abort();

        if (searchResults) {
          searchResults.innerHTML = `
            <p>
              Type at least two characters
              to search.
            </p>
          `;
        }

        return;
      }

      searchTimer =
        managedTimeout(
          async () => {
            searchRequest?.abort();

            searchRequest =
              new AbortController();

            if (searchResults) {
              searchResults
                .setAttribute(
                  'aria-busy',
                  'true'
                );

              searchResults.innerHTML = `
                <div class="search-loading">

                  <span
                    class="vsl-loader"
                    aria-hidden="true"
                  ></span>

                  <p>
                    Searching Varenz…
                  </p>

                </div>
              `;
            }

            try {
              const payload =
                await fetchJson(
                  `${baseUrl}api/search?q=${encodeURIComponent(term)}`,
                  {
                    signal:
                      searchRequest.signal
                  }
                );

              renderSearch(
                payload?.ok &&
                Array.isArray(
                  payload.data
                )
                  ? payload.data
                  : []
              );
            } catch (error) {
              if (
                error instanceof
                  DOMException &&
                error.name ===
                  'AbortError'
              ) {
                return;
              }

              const fallbackItems = typeof window.VarenzFuzzySearch === 'function'
                ? window.VarenzFuzzySearch(term)
                : [];

              if (fallbackItems.length) {
                renderSearch(fallbackItems);
              } else if (searchResults) {
                searchResults
                  .setAttribute(
                    'aria-busy',
                    'false'
                  );

                searchResults.innerHTML = `
                  <div class="search-empty">

                    <strong>
                      Search is temporarily
                      unavailable.
                    </strong>

                    <p>
                      Please try again.
                    </p>

                  </div>
                `;
              }
            }
          },
          240
        );
    }
  );


  /*
  |--------------------------------------------------------------------------
  | REQUEST FORM
  |--------------------------------------------------------------------------
  */

  const requestForm =
    qs('#vslRequestForm');

  const feedbackTypeInput =
    qs('#feedbackTypeInput');

  const feedbackButtons =
    qsa(
      '#feedbackTypes button'
    );

  const assistant =
    qs('#requestAssistant');

  const assistantScore =
    qs('#assistantScore');

  const assistantSummary =
    qs('#assistantSummary');

  const qualityInput =
    qs('#requestQualityInput');

  const intelligenceInput =
    qs('#requestIntelligenceInput');

  const requestMessage =
    qs(
      '#vslRequestForm textarea[name="message"]'
    );

  const requestCategory =
    qs(
      '#vslRequestForm select[name="category"]'
    );

  const requiredBy =
    qs(
      '#vslRequestForm input[name="required_by"]'
    );

  const phoneInput =
    qs(
      '#vslRequestForm input[name="phone"]'
    );

  const emailInput =
    qs(
      '#vslRequestForm input[name="email"]'
    );

  const attachmentInput =
    qs(
      '#vslRequestForm input[name="attachment"]'
    );

  feedbackButtons.forEach(
    button => {
      button.addEventListener(
        'click',
        () => {
          activateButtons(
            feedbackButtons,
            button
          );

          if (feedbackTypeInput) {
            feedbackTypeInput.value =
              button.dataset.type ||
              'quotation';
          }

          updateRequestAssistant();
        }
      );
    }
  );

  function containsTerm(
    source,
    terms
  ) {
    return terms.some(term => {
      const escaped =
        term.replace(
          /[.*+?^${}()|[\]\\]/g,
          '\\$&'
        );

      return new RegExp(
        `(^|[^a-z0-9])${escaped}([^a-z0-9]|$)`,
        'i'
      ).test(source);
    });
  }

  function requestSignals() {
    const source = [
      feedbackTypeInput?.value,
      requestCategory?.value,
      requestMessage?.value,
      requiredBy?.value
    ]
      .map(value =>
        text(value)
          .toLowerCase()
      )
      .join(' ');

    return {
      product:
        containsTerm(
          source,
          [
            'contrast',
            'pacemaker',
            'stent',
            'enfit',
            'syringe',
            'ng tube',
            'nasogastric',
            'giving set',
            'irrigation',
            'product',
            'device',
            'brand'
          ]
        ),

      specification:
        containsTerm(
          source,
          [
            'size',
            'model',
            'reference',
            'specification',
            'spec',
            'strength',
            'connector',
            'gauge',
            'diameter',
            'length',
            'volume'
          ]
        ) ||
        /\b\d+(?:\.\d+)?\s*(?:ml|l|mg|mcg|g|kg|mm|cm|fr|ch|ga|gauge|%)\b/i
          .test(source),

      quantity:
        /\b\d+(?:[.,]\d+)?\s*(?:pcs?|pieces?|boxes?|cartons?|units?|packs?|sets?|vials?|bottles?|tubes?|bags?|cases?|kits?)\b/i
          .test(source),

      timeline:
        Boolean(
          requiredBy?.value
        ) ||
        containsTerm(
          source,
          [
            'urgent',
            'today',
            'tomorrow',
            'this week',
            'next week',
            'delivery',
            'deadline',
            'required by',
            'needed by',
            'asap'
          ]
        )
    };
  }

  function updateRequestAssistant() {
    if (!assistant) {
      return;
    }

    const signals =
      requestSignals();

    const score =
      Object.values(signals)
        .filter(Boolean)
        .length;

    const quality =
      score >= 4
        ? 'complete'
        : score >= 2
          ? 'reviewable'
          : 'basic';

    const missing =
      Object.entries(signals)
        .filter(
          ([, complete]) =>
            !complete
        )
        .map(
          ([name]) => name
        );

    const label =
      quality === 'complete'
        ? 'Strong request'
        : quality === 'reviewable'
          ? 'Reviewable request'
          : 'Basic request';

    const summary =
      quality === 'complete'
        ? 'You have included the key information needed for efficient review.'
        : missing.length
          ? `If available, add ${missing.join(', ')} details. You can still submit now.`
          : 'Send the information you already have and Varenz can help clarify the rest.';

    if (assistantScore) {
      assistantScore.textContent =
        label;
    }

    if (assistantSummary) {
      assistantSummary.textContent =
        summary;
    }

    if (qualityInput) {
      qualityInput.value =
        quality;
    }

    if (intelligenceInput) {
      intelligenceInput.value =
        `${label}: ${summary}`;
    }

    qsa(
      '[data-check]',
      assistant
    ).forEach(item => {
      const key =
        item.dataset.check || '';

      item.classList.toggle(
        'is-complete',
        Boolean(signals[key])
      );
    });

    assistant.dataset.quality =
      quality;

    assistant.style.setProperty(
      '--vsl-readiness',
      `${score * 25}%`
    );
  }

  [
    requestMessage,
    requestCategory,
    requiredBy
  ]
    .filter(Boolean)
    .forEach(control => {
      control.addEventListener(
        'input',
        updateRequestAssistant
      );

      control.addEventListener(
        'change',
        updateRequestAssistant
      );
    });

  updateRequestAssistant();

  function validEmail(value) {
    const email =
      text(value).trim();

    if (
      email === '' ||
      email.length > 254
    ) {
      return false;
    }

    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      .test(email);
  }

  function validateContact() {
    const phone =
      text(
        phoneInput?.value
      ).trim();

    const email =
      text(
        emailInput?.value
      ).trim();

    phoneInput
      ?.setCustomValidity('');

    emailInput
      ?.setCustomValidity('');

    if (
      phone === '' &&
      email === ''
    ) {
      const message =
        'Provide a telephone number or email address so Varenz can respond.';

      const target =
        phoneInput ||
        emailInput;

      target?.setCustomValidity(
        message
      );

      target?.reportValidity();

      return false;
    }

    if (
      email !== '' &&
      !validEmail(email)
    ) {
      emailInput
        ?.setCustomValidity(
          'Enter a valid email address or leave the email field blank.'
        );

      emailInput
        ?.reportValidity();

      return false;
    }

    return true;
  }

  phoneInput?.addEventListener(
    'input',
    () => {
      phoneInput
        .setCustomValidity('');

      emailInput
        ?.setCustomValidity('');
    }
  );

  emailInput?.addEventListener(
    'input',
    () => {
      phoneInput
        ?.setCustomValidity('');

      emailInput
        .setCustomValidity('');
    }
  );

  attachmentInput?.addEventListener(
    'change',
    () => {
      const file =
        attachmentInput.files?.[0];

      attachmentInput
        .closest('.field')
        ?.classList.toggle(
          'has-file',
          Boolean(file)
        );
    }
  );

  function setFormBusy(busy) {
    if (!requestForm) {
      return;
    }

    requestForm.classList.toggle(
      'is-submitting',
      busy
    );

    requestForm.setAttribute(
      'aria-busy',
      busy
        ? 'true'
        : 'false'
    );
  }

  requestForm?.addEventListener(
    'submit',
    async event => {
      event.preventDefault();

      const submitButton =
        qs('#vslSubmitButton');

      const status =
        qs('#vslFormStatus');

      if (
        !requestForm.checkValidity()
      ) {
        requestForm
          .reportValidity();

        return;
      }

      if (!validateContact()) {
        return;
      }

      const originalButtonHtml =
        submitButton?.innerHTML ||
        '';

      const formData =
        new FormData(
          requestForm
        );

      setFormBusy(true);

      if (submitButton) {
        submitButton.disabled =
          true;

        submitButton.innerHTML = `
          <span
            class="vsl-loader"
            aria-hidden="true"
          ></span>

          Submitting…
        `;
      }

      if (status) {
        status.textContent =
          'Submitting securely. Please keep this page open.';
      }

      try {
        const payload =
          await fetchJson(
            requestForm.action,
            {
              method: 'POST',
              body: formData,

              headers:
                csrfToken
                  ? {
                      'X-CSRF-Token':
                        csrfToken
                    }
                  : {}
            }
          );

        if (
          !payload ||
          !payload.ok
        ) {
          throw new Error(
            payload?.message ||
            'The request could not be submitted.'
          );
        }

        const reference =
          text(
            payload.reference
          ).trim();

        const confirmation =
          reference
            ? `Request received. Reference: ${reference}`
            : 'Request received successfully.';

        requestForm.classList
          .add(
            'is-success'
          );

        if (status) {
          status.innerHTML = `
            <strong>
              ${escapeHtml(
                confirmation
              )}
            </strong>

            <span>
              Our team can now review
              the request and follow up.
            </span>
          `;
        }

        showToast(
          confirmation,
          'success',
          6500
        );

        requestForm.reset();

        if (feedbackTypeInput) {
          feedbackTypeInput.value =
            'quotation';
        }

        if (
          feedbackButtons.length
        ) {
          activateButtons(
            feedbackButtons,
            feedbackButtons[0]
          );
        }

        updateRequestAssistant();

        managedTimeout(
          () => {
            requestForm.classList
              .remove(
                'is-success'
              );
          },
          9000
        );
      } catch (error) {
        const message =
          error instanceof Error
            ? error.message
            : 'The request could not be submitted.';

        requestForm.classList
          .add(
            'has-error'
          );

        if (status) {
          status.textContent =
            message;
        }

        showToast(
          message,
          'error',
          7000
        );

        managedTimeout(
          () => {
            requestForm.classList
              .remove(
                'has-error'
              );
          },
          4200
        );
      } finally {
        setFormBusy(false);

        if (submitButton) {
          submitButton.disabled =
            false;

          submitButton.innerHTML =
            originalButtonHtml;
        }
      }
    }
  );


  /*
  |--------------------------------------------------------------------------
  | CHALLENGES
  |--------------------------------------------------------------------------
  */

  const challenges =
    Array.isArray(
      site.challenges
    )
      ? site.challenges
      : [];

  const challengeNav =
    qs('#challengeNav');

  const challengeDisplay =
    qs('#challengeDisplay');

  let challengeIndex = 0;
  let challengeTimer = 0;
  let challengeReady = false;

  function challengeMarkup(item) {
    const results =
      Array.isArray(
        item?.results
      )
        ? item.results
        : [];

    return `
      <div class="challenge-stage active">

        <div class="intro">

          <span class="eyebrow">
            Active Challenge
          </span>

          <h3>
            ${escapeHtml(
              item?.title
            )}
          </h3>

          <p>
            ${escapeHtml(
              item?.intro
            )}
          </p>

        </div>

        <div class="challenge-media">

          <div class="challenge-image">

            <img
              loading="lazy"
              decoding="async"
              src="${escapeHtml(
                assetUrl(
                  item?.image
                )
              )}"
              alt="${escapeHtml(
                item?.imageTitle ||
                item?.title
              )}"
            >

            <div class="challenge-image-overlay">

              <h4>
                ${escapeHtml(
                  item?.imageTitle
                )}
              </h4>

              <p>
                ${escapeHtml(
                  item?.imageDesc
                )}
              </p>

            </div>

          </div>

          <div class="mini-video">

            <div
              class="play-ring"
              aria-hidden="true"
            >
              <svg class="icon">
                <use href="#i-play"></use>
              </svg>
            </div>

            <h4>
              ${escapeHtml(
                item?.videoTitle
              )}
            </h4>

            <p>
              ${escapeHtml(
                item?.videoDesc
              )}
            </p>

          </div>

        </div>

        <div class="result-list">

          ${results.map(
            result => `
              <div class="result-card">

                <strong>
                  ${escapeHtml(
                    result?.[0]
                  )}
                </strong>

                <p>
                  ${escapeHtml(
                    result?.[1]
                  )}
                </p>

              </div>
            `
          ).join('')}

        </div>

      </div>
    `;
  }

  function renderChallenges() {
    if (
      !challengeNav ||
      !challengeDisplay ||
      !challenges.length
    ) {
      return;
    }

    challengeNav.innerHTML =
      challenges.map(
        (item, index) => {
          const active =
            index ===
            challengeIndex;

          return `
            <button
              class="challenge-btn ${
                active
                  ? 'active'
                  : ''
              }"
              type="button"
              data-challenge="${index}"
              aria-pressed="${
                active
                  ? 'true'
                  : 'false'
              }"
            >

              <h4>
                ${String(
                  index + 1
                ).padStart(
                  2,
                  '0'
                )}
                ·
                ${escapeHtml(
                  item?.title
                )}
              </h4>

              <p>
                ${escapeHtml(
                  item?.intro
                )}
              </p>

            </button>
          `;
        }
      ).join('');

    challengeDisplay.innerHTML =
      challengeMarkup(
        challenges[
          challengeIndex
        ]
      );

    bindImageRecovery(
      challengeDisplay
    );

    enhancePremiumSurfaces(
      challengeDisplay
    );
  }

  function stopChallenges() {
    clearManagedInterval(
      challengeTimer
    );

    challengeTimer = 0;
  }

  function restartChallenges() {
    stopChallenges();

    if (
      prefersReducedMotion ||
      document.hidden ||
      !challengeReady ||
      challenges.length < 2
    ) {
      return;
    }

    challengeTimer =
      managedInterval(
        () => {
          challengeIndex =
            (
              challengeIndex + 1
            ) % challenges.length;

          renderChallenges();
        },
        5200
      );
  }

  challengeNav?.addEventListener(
    'click',
    event => {
      const button =
        event.target.closest(
          '[data-challenge]'
        );

      if (!button) {
        return;
      }

      challengeIndex =
        Number(
          button.dataset
            .challenge ||
          0
        );

      renderChallenges();
      restartChallenges();
    }
  );

  whenNear(
    '#challenges',
    () => {
      challengeReady = true;

      if (challenges.length) {
        renderChallenges();
      }

      restartChallenges();
    }
  );


  /*
  |--------------------------------------------------------------------------
  | PRODUCT CATEGORIES
  |--------------------------------------------------------------------------
  */

  const categories =
    Array.isArray(
      site.categories
    )
      ? site.categories
      : [];

  const categoryTabs =
    qs('#categoryTabs');

  const categoryMedia =
    qs('#categoryMedia');

  const categoryGrid =
    qs('#categoryGrid');

  let categoryIndex = 0;

  function renderCategory() {
    if (
      !categoryTabs ||
      !categoryMedia ||
      !categoryGrid ||
      !categories.length
    ) {
      return;
    }

    const category =
      categories[
        categoryIndex
      ] || {};

    qsa(
      '[data-category]',
      categoryTabs
    ).forEach(
      (button, index) => {
        const active =
          index ===
          categoryIndex;

        button.classList.toggle(
          'active',
          active
        );

        button.setAttribute(
          'aria-selected',
          active
            ? 'true'
            : 'false'
        );

        button.tabIndex =
          active
            ? 0
            : -1;
      }
    );

    categoryMedia.innerHTML = `
      <img
        loading="lazy"
        decoding="async"
        src="${escapeHtml(
          assetUrl(
            category?.media_image
          )
        )}"
        alt="${escapeHtml(
          category?.title
        )}"
      >

      <div class="cat-media-content">

        <span class="eyebrow light">
          ${escapeHtml(
            category?.label
          )}
        </span>

        <h3>
          ${escapeHtml(
            category?.title
          )}
        </h3>

        <p>
          ${escapeHtml(
            category?.desc
          )}
        </p>

      </div>
    `;

    const cards =
      Array.isArray(
        category?.cards
      )
        ? category.cards
        : [];

    categoryGrid.innerHTML =
      cards.map(card => `
        <article class="ajax-card">

          <img
            loading="lazy"
            decoding="async"
            src="${escapeHtml(
              assetUrl(
                card?.image
              )
            )}"
            alt="${escapeHtml(
              card?.title
            )}"
          >

          <div class="ajax-card-body">

            <h4>
              ${escapeHtml(
                card?.title
              )}
            </h4>

            <p>
              ${escapeHtml(
                card?.description
              )}
            </p>

            <div class="ajax-badges">

              ${
                (
                  Array.isArray(
                    card?.tags
                  )
                    ? card.tags
                    : []
                ).map(tag => `
                  <span>
                    ${escapeHtml(
                      tag
                    )}
                  </span>
                `).join('')
              }

            </div>

          </div>

        </article>
      `).join('');

    bindImageRecovery(
      categoryMedia
    );

    bindImageRecovery(
      categoryGrid
    );

    enhancePremiumSurfaces(
      categoryGrid
    );
  }

  categoryTabs?.addEventListener(
    'click',
    event => {
      const button =
        event.target.closest(
          '[data-category]'
        );

      if (!button) {
        return;
      }

      categoryIndex =
        Number(
          button.dataset
            .category ||
          0
        );

      renderCategory();
    }
  );

  if (categories.length) {
    renderCategory();
  }


  /*
  |--------------------------------------------------------------------------
  | FEATURED PRODUCTS
  |--------------------------------------------------------------------------
  */

  const featured =
    Array.isArray(
      site.featured
    )
      ? site.featured
      : [];

  const rowOne =
    qs('#featuredRow1');

  const rowTwo =
    qs('#featuredRow2');

  let featuredOffset = 0;
  let featuredTimer = 0;
  let featuredReady = false;

  function productMarkup(item) {
    const meta =
      Array.isArray(
        item?.meta
      )
        ? item.meta
        : [];

    return `
      <article class="product-card">

        <div class="product-card-media">

          <img
            loading="lazy"
            decoding="async"
            src="${escapeHtml(
              assetUrl(
                item?.image
              )
            )}"
            alt="${escapeHtml(
              item?.title
            )}"
          >

        </div>

        <div class="product-card-body">

          <span class="sku">
            ${escapeHtml(
              item?.sku
            )}
          </span>

          <h4>
            ${escapeHtml(
              item?.title
            )}
          </h4>

          <p>
            ${escapeHtml(
              item?.text
            )}
          </p>

          <div class="meta">

            ${meta.map(tag => `
              <span>
                ${escapeHtml(tag)}
              </span>
            `).join('')}

          </div>

        </div>

      </article>
    `;
  }

  function featuredPerView() {
    if (
      window.innerWidth <= 980
    ) {
      return 2;
    }

    return 4;
  }

  function updateFeatured() {
    if (
      !rowOne ||
      !rowTwo
    ) {
      return;
    }

    const container =
      rowOne.parentElement;

    if (!container) {
      return;
    }

    const gap =
      window.innerWidth <= 760
        ? 10
        : 16;

    const perView =
      featuredPerView();

    const width =
      container.clientWidth;

    if (width <= 0) {
      return;
    }

    const cardWidth =
      (
        width -
        gap *
        (
          perView - 1
        )
      ) / perView;

    [
      rowOne,
      rowTwo
    ].forEach(row => {
      qsa(
        '.product-card',
        row
      ).forEach(card => {
        card.style.flexBasis =
          `${cardWidth}px`;
      });
    });

    const shift =
      featuredOffset *
      (
        cardWidth + gap
      );

    rowOne.style.transform =
      `translate3d(${-shift}px,0,0)`;

    rowTwo.style.transform =
      `translate3d(${shift}px,0,0)`;
  }

  function renderFeatured() {
    if (
      !rowOne ||
      !rowTwo ||
      !featured.length
    ) {
      return;
    }

    const repeated = [
      ...featured,
      ...featured
    ];

    rowOne.innerHTML =
      repeated
        .map(productMarkup)
        .join('');

    const reversed =
      [...featured]
        .reverse();

    rowTwo.innerHTML =
      reversed
        .concat(reversed)
        .map(productMarkup)
        .join('');

    bindImageRecovery(
      rowOne
    );

    bindImageRecovery(
      rowTwo
    );

    updateFeatured();
  }

  function stopFeatured() {
    clearManagedInterval(
      featuredTimer
    );

    featuredTimer = 0;
  }

  function restartFeatured() {
    stopFeatured();

    if (
      prefersReducedMotion ||
      document.hidden ||
      !featuredReady ||
      featured.length < 2
    ) {
      return;
    }

    featuredTimer =
      managedInterval(
        () => {
          featuredOffset =
            (
              featuredOffset + 1
            ) % featured.length;

          updateFeatured();
        },
        4500
      );
  }

  qs('#featNext')
    ?.addEventListener(
      'click',
      () => {
        if (!featured.length) {
          return;
        }

        featuredOffset =
          (
            featuredOffset + 1
          ) % featured.length;

        updateFeatured();
        restartFeatured();
      }
    );

  qs('#featPrev')
    ?.addEventListener(
      'click',
      () => {
        if (!featured.length) {
          return;
        }

        featuredOffset =
          (
            featuredOffset -
            1 +
            featured.length
          ) % featured.length;

        updateFeatured();
        restartFeatured();
      }
    );

  whenNear(
    '#featured',
    () => {
      featuredReady = true;

      if (featured.length) {
        renderFeatured();
      }

      restartFeatured();
    }
  );


  /*
  |--------------------------------------------------------------------------
  | PROCUREMENT
  |--------------------------------------------------------------------------
  */

  const procurement =
    Array.isArray(
      site.procurement
    )
      ? site.procurement
      : [];

  const procurementSteps =
    qs('#procureSteps');

  const procurementDisplay =
    qs('#procureDisplay');

  const procurementProgress =
    qs('#procureProgress');

  let procurementIndex = 0;
  let procurementTimer = 0;
  let procurementReady = false;
  let procurementPaused = false;

  function renderProcurement() {
    if (
      !procurementSteps ||
      !procurementDisplay ||
      !procurement.length
    ) {
      return;
    }

    qsa(
      '[data-procurement]',
      procurementSteps
    ).forEach(
      (button, index) => {
        const active =
          index ===
          procurementIndex;

        button.classList.toggle(
          'active',
          active
        );

        button.setAttribute(
          'aria-pressed',
          active
            ? 'true'
            : 'false'
        );
      }
    );

    const item =
      procurement[
        procurementIndex
      ] || {};

    const tiles =
      Array.isArray(
        item?.tiles
      )
        ? item.tiles
        : [];

    procurementDisplay.innerHTML = `
      <div class="step-panel active">

        <div class="step-copy">

          <span class="eyebrow">
            Step ${
              procurementIndex + 1
            }
          </span>

          <h3>
            ${escapeHtml(
              item?.step
            )}
          </h3>

          <p>
            ${escapeHtml(
              item?.detail
            )}
          </p>

        </div>

        <div class="step-image">

          <img
            loading="lazy"
            decoding="async"
            src="${escapeHtml(
              assetUrl(
                item?.image
              )
            )}"
            alt="${escapeHtml(
              item?.step
            )}"
          >

          <span
            class="step-image-fallback"
            role="status"
          >
            Image temporarily unavailable
          </span>

        </div>

        <div class="step-guides">

          ${tiles.map(tile => `
            <div class="guide-tile">

              <strong>
                ${escapeHtml(
                  tile?.[0]
                )}
              </strong>

              <p>
                ${escapeHtml(
                  tile?.[1]
                )}
              </p>

            </div>
          `).join('')}

        </div>

      </div>
    `;

    if (procurementProgress) {
      procurementProgress
        .style.width =
          `${
            (
              (
                procurementIndex + 1
              ) /
              procurement.length
            ) * 100
          }%`;
    }

    bindImageRecovery(
      procurementDisplay
    );

    enhancePremiumSurfaces(
      procurementDisplay
    );
  }

  function stopProcurement() {
    clearManagedInterval(
      procurementTimer
    );

    procurementTimer = 0;
  }

  function restartProcurement() {
    stopProcurement();

    if (
      prefersReducedMotion ||
      document.hidden ||
      !procurementReady ||
      procurementPaused ||
      procurement.length < 2
    ) {
      return;
    }

    procurementTimer =
      managedInterval(
        () => {
          procurementIndex =
            (
              procurementIndex + 1
            ) %
            procurement.length;

          renderProcurement();
        },
        4600
      );
  }

  procurementSteps?.addEventListener(
    'click',
    event => {
      const button =
        event.target.closest(
          '[data-procurement]'
        );

      if (!button) {
        return;
      }

      procurementIndex =
        Number(
          button.dataset
            .procurement ||
          0
        );

      renderProcurement();
      restartProcurement();
    }
  );

  whenNear(
    '#procedure',
    () => {
      procurementReady = true;

      if (procurement.length) {
        renderProcurement();
      }

      restartProcurement();

      const section =
        qs('#procedure');

      section?.addEventListener(
        'pointerenter',
        () => {
          procurementPaused =
            true;

          stopProcurement();
        }
      );

      section?.addEventListener(
        'pointerleave',
        () => {
          procurementPaused =
            false;

          restartProcurement();
        }
      );
    }
  );


  /*
  |--------------------------------------------------------------------------
  | ORGANISATIONS
  |--------------------------------------------------------------------------
  */

  const organizations =
    Array.isArray(
      site.organizations
    )
      ? site.organizations
      : [];

  const orgNav =
    qs('#orgNav');

  const orgViewer =
    qs('#orgViewer');

  let orgIndex = 0;
  let orgTimer = 0;
  let orgReady = false;
  let orgPaused = false;

  function renderOrganizations() {
    if (
      !orgNav ||
      !orgViewer ||
      !organizations.length
    ) {
      return;
    }

    qsa(
      '[data-organization]',
      orgNav
    ).forEach(
      (button, index) => {
        const active =
          index === orgIndex;

        button.classList.toggle(
          'active',
          active
        );

        button.setAttribute(
          'aria-pressed',
          active
            ? 'true'
            : 'false'
        );
      }
    );

    const item =
      organizations[
        orgIndex
      ] || {};

    const bullets =
      Array.isArray(
        item?.bullets
      )
        ? item.bullets
        : [];

    orgViewer.innerHTML = `
      <article class="org-stage active">

        <img
          loading="lazy"
          decoding="async"
          src="${escapeHtml(
            assetUrl(
              item?.image
            )
          )}"
          alt="${escapeHtml(
            item?.name
          )}"
        >

        <div class="org-content">

          <span class="eyebrow light">
            Organisation Support
          </span>

          <h3>
            ${escapeHtml(
              item?.name
            )}
          </h3>

          <p>
            ${escapeHtml(
              item?.summary
            )}
          </p>

          <div class="org-bullets">

            ${bullets.map(bullet => `
              <span>
                ${escapeHtml(
                  bullet
                )}
              </span>
            `).join('')}

          </div>

        </div>

      </article>
    `;

    bindImageRecovery(
      orgViewer
    );
  }

  function stopOrganizations() {
    clearManagedInterval(
      orgTimer
    );

    orgTimer = 0;
  }

  function restartOrganizations() {
    stopOrganizations();

    if (
      prefersReducedMotion ||
      document.hidden ||
      !orgReady ||
      orgPaused ||
      organizations.length < 2
    ) {
      return;
    }

    orgTimer =
      managedInterval(
        () => {
          orgIndex =
            (
              orgIndex + 1
            ) %
            organizations.length;

          renderOrganizations();
        },
        3600
      );
  }

  orgNav?.addEventListener(
    'click',
    event => {
      const button =
        event.target.closest(
          '[data-organization]'
        );

      if (!button) {
        return;
      }

      orgIndex =
        Number(
          button.dataset
            .organization ||
          0
        );

      renderOrganizations();
      restartOrganizations();
    }
  );

  whenNear(
    '#organizations',
    () => {
      orgReady = true;

      if (
        organizations.length
      ) {
        renderOrganizations();
      }

      restartOrganizations();

      const section =
        qs('#organizations');

      section?.addEventListener(
        'pointerenter',
        () => {
          orgPaused = true;
          stopOrganizations();
        }
      );

      section?.addEventListener(
        'pointerleave',
        () => {
          orgPaused = false;
          restartOrganizations();
        }
      );
    }
  );


  /*
  |--------------------------------------------------------------------------
  | WHY VARENZ
  |--------------------------------------------------------------------------
  */

  const whyItems =
    Array.isArray(
      site.why
    )
      ? site.why
      : [];

  const orbitBox =
    qs('#orbitBox');

  const whyDetail =
    qs('#whyDetail');

  const orbitNodes = [];

  let whyIndex = 0;
  let orbitRotation = 0;
  let whyTimer = 0;
  let orbitReady = false;

  function positionOrbit() {
    if (
      !orbitBox ||
      !orbitNodes.length
    ) {
      return;
    }

    if (
      window.innerWidth <= 760 ||
      orbitBox.clientWidth < 650
    ) {
      orbitNodes.forEach(node => {
        node.style.left = '';
        node.style.top = '';
      });

      return;
    }

    const width =
      orbitBox.clientWidth;

    const height =
      orbitBox.clientHeight;

    const cx =
      width / 2;

    const cy =
      height / 2;

    const rx =
      Math.min(
        width * 0.34,
        245
      );

    const ry =
      Math.min(
        height * 0.34,
        220
      );

    orbitNodes.forEach(
      (node, index) => {
        const angle =
          (
            (
              Math.PI * 2
            ) /
            orbitNodes.length
          ) *
          index -
          Math.PI / 2 +
          orbitRotation;

        const nodeWidth =
          node.offsetWidth;

        const nodeHeight =
          node.offsetHeight;

        const x =
          cx +
          Math.cos(angle) *
          rx -
          nodeWidth / 2;

        const y =
          cy +
          Math.sin(angle) *
          ry -
          nodeHeight / 2;

        node.style.left =
          `${
            Math.max(
              12,
              Math.min(
                width -
                nodeWidth -
                12,
                x
              )
            )
          }px`;

        node.style.top =
          `${
            Math.max(
              12,
              Math.min(
                height -
                nodeHeight -
                12,
                y
              )
            )
          }px`;
      }
    );
  }

  function focusWhy(
    index,
    rotate = true
  ) {
    if (!whyItems.length) {
      return;
    }

    whyIndex =
      (
        index +
        whyItems.length
      ) %
      whyItems.length;

    if (
      rotate &&
      window.innerWidth > 760
    ) {
      orbitRotation +=
        Math.PI / 72;
    }

    orbitNodes.forEach(
      (node, nodeIndex) => {
        const active =
          nodeIndex ===
          whyIndex;

        node.classList.toggle(
          'active',
          active
        );

        node.setAttribute(
          'aria-pressed',
          active
            ? 'true'
            : 'false'
        );
      }
    );

    const item =
      whyItems[
        whyIndex
      ] || {};

    if (whyDetail) {
      whyDetail.innerHTML = `
        <span class="eyebrow">
          Focused Value
        </span>

        <h3>
          ${escapeHtml(
            item?.title
          )}
        </h3>

        <p>
          ${escapeHtml(
            item?.desc
          )}
        </p>
      `;
    }

    requestAnimationFrame(
      positionOrbit
    );
  }

  function stopWhy() {
    clearManagedInterval(
      whyTimer
    );

    whyTimer = 0;
  }

  function restartWhy() {
    stopWhy();

    if (
      prefersReducedMotion ||
      document.hidden ||
      !orbitReady ||
      whyItems.length < 2
    ) {
      return;
    }

    whyTimer =
      managedInterval(
        () => {
          focusWhy(
            whyIndex + 1
          );
        },
        3400
      );
  }

  function initializeOrbit() {
    if (
      orbitReady ||
      !orbitBox ||
      !whyItems.length
    ) {
      return;
    }

    orbitReady = true;

    whyItems.forEach(
      (item, index) => {
        const node =
          document.createElement(
            'button'
          );

        node.type =
          'button';

        node.className =
          'orbit-node';

        node.style.setProperty(
          '--float-delay',
          `${index * -0.55}s`
        );

        node.setAttribute(
          'aria-pressed',
          index === 0
            ? 'true'
            : 'false'
        );

        node.innerHTML = `
          <h4>
            ${escapeHtml(
              item?.title
            )}
          </h4>

          <p>
            ${escapeHtml(
              item?.desc
            )}
          </p>
        `;

        node.addEventListener(
          'click',
          () => {
            focusWhy(index);
            restartWhy();
          }
        );

        orbitBox.appendChild(
          node
        );

        orbitNodes.push(
          node
        );
      }
    );

    focusWhy(
      0,
      false
    );

    restartWhy();
  }

  whenNear(
    '#why',
    initializeOrbit,
    '650px 0px'
  );


  /*
  |--------------------------------------------------------------------------
  | LEGACY TEAM SUPPORT
  |--------------------------------------------------------------------------
  */

  const team =
    Array.isArray(
      site.team
    )
      ? site.team
      : [];

  const teamModal =
    qs('#teamModal');

  function normalizePhone(value) {
    const source =
      text(value).trim();

    const match =
      source.match(
        /\+?\d[\d\s().-]{6,}/
      );

    if (!match) {
      return '';
    }

    const plus =
      match[0]
        .trim()
        .startsWith('+');

    const digits =
      match[0]
        .replace(/\D/g, '');

    if (!digits) {
      return '';
    }

    return plus
      ? `+${digits}`
      : digits;
  }

  function whatsappNumber(value) {
    let digits =
      normalizePhone(value)
        .replace(/\D/g, '');

    if (
      digits.startsWith('0')
    ) {
      digits =
        `256${digits.slice(1)}`;
    }

    return digits;
  }

  function findTeamMember(value) {
    const requested =
      normalizeSlug(value);

    if (!requested) {
      return null;
    }

    return (
      team.find(member => {
        const aliases = [
          member?.slug,

          ...(
            Array.isArray(
              member?.aliases
            )
              ? member.aliases
              : []
          )
        ]
          .filter(Boolean)
          .map(normalizeSlug);

        return aliases.includes(
          requested
        );
      }) || null
    );
  }

  function openTeam(member) {
    if (
      !member ||
      !teamModal
    ) {
      return;
    }

    const assignText = (
      selector,
      value
    ) => {
      const element =
        qs(selector);

      if (element) {
        element.textContent =
          text(value);
      }
    };

    assignText(
      '#modalName',
      member.name
    );

    assignText(
      '#modalRole',
      member.role
    );

    assignText(
      '#modalDept',
      member.department
    );

    assignText(
      '#modalResp',
      member.responsibility
    );

    assignText(
      '#modalSummary',
      member.summary
    );

    assignText(
      '#modalNumber',
      member.number ||
      'VSL'
    );

    const image =
      qs('#modalImage');

    if (image) {
      image.hidden = false;

      image.src =
        member.image
          ? assetUrl(
              member.image
            )
          : logoFallbackUrl();

      image.alt =
        text(member.name);

      recoverImage(image);
    }

    const phone =
      normalizePhone(
        member.phone
      );

    const whatsApp =
      whatsappNumber(
        member.phone
      );

    const call =
      qs('#modalCall');

    const wa =
      qs('#modalWhatsApp');

    if (call) {
      call.hidden =
        !phone;

      call.href =
        phone
          ? `tel:${phone}`
          : '#';
    }

    if (wa) {
      wa.hidden =
        !whatsApp;

      wa.href =
        whatsApp
          ? `https://wa.me/${whatsApp}`
          : '#';
    }

    teamModal.classList
      .add('open');

    teamModal.setAttribute(
      'aria-hidden',
      'false'
    );

    setBodyLock(
      'team-modal',
      true
    );
  }

  function closeTeam() {
    if (!teamModal) {
      return;
    }

    teamModal.classList
      .remove('open');

    teamModal.setAttribute(
      'aria-hidden',
      'true'
    );

    setBodyLock(
      'team-modal',
      false
    );
  }

  qs('#teamModalClose')
    ?.addEventListener(
      'click',
      closeTeam
    );

  qs('#teamModalCloseBtn')
    ?.addEventListener(
      'click',
      closeTeam
    );

  const requestedTeam =
    app.initialTeamSlug ||
    new URLSearchParams(
      window.location.search
    ).get('team') ||
    new URLSearchParams(
      window.location.search
    ).get('profile') ||
    '';

  if (requestedTeam) {
    const member =
      findTeamMember(
        requestedTeam
      );

    if (member) {
      managedTimeout(
        () => openTeam(member),
        180
      );
    }
  }


  /*
  |--------------------------------------------------------------------------
  | CONTINUOUS PARTNER LOGO CAROUSEL
  |--------------------------------------------------------------------------
  |
  | IMPORTANT:
  |
  | This is now an INFINITE RAIL, not a normal paged slider.
  |
  | Original sequence:
  |
  | NDA → URA → Wizbiotech
  |
  | Runtime sequence:
  |
  | ... URA → Wizbiotech → NDA → URA → Wizbiotech → NDA ...
  |
  | Clones are generated automatically.
  |
  | Mobile:
  | - one main card
  | - next card intentionally visible
  | - no wrapping
  | - finger drag
  | - continuous loop
  |
  */

  const partnerCarousel =
    qs('#partnerCarousel');

  const partnerViewport =
    qs('#partnerViewport');

  const partnerTrack =
    qs('#partnerTrack');

  const partnerPrev =
    qs('#partnerPrev');

  const partnerNext =
    qs('#partnerNext');

  const partnerDots =
    qs('#partnerDots');

  let partnerOriginalCards =
    [];

  let partnerCards = [];

  let partnerRealIndex = 0;
  let partnerTrackIndex = 0;
  let partnerCloneOffset = 0;

  let partnerTimer = 0;
  let partnerReady = false;
  let partnerPaused = false;
  let partnerAnimating = false;
  let partnerBuilt = false;
  let partnerBuilding = false;

  let partnerPointerActive =
    false;

  let partnerPointerId =
    null;

  let partnerPointerStartX =
    0;

  let partnerPointerStartY =
    0;

  let partnerPointerCurrentX =
    0;

  let partnerPointerBaseOffset =
    0;

  let partnerPointerHorizontal =
    false;

  const partnerAutoplayDelay =
    3000;

  const partnerTransitionMs =
    620;

  function partnerModulo(
    value,
    length
  ) {
    if (!length) {
      return 0;
    }

    return (
      (
        value % length
      ) +
      length
    ) % length;
  }

  function readPartnerOriginals() {
    if (!partnerTrack) {
      partnerOriginalCards =
        [];

      return;
    }

    partnerOriginalCards =
      qsa(
        '.partner-card:not([data-partner-clone="true"])',
        partnerTrack
      );
  }

  function refreshPartnerCards() {
    if (!partnerTrack) {
      partnerCards = [];

      return;
    }

    partnerCards =
      qsa(
        '.partner-card',
        partnerTrack
      );
  }

  function partnerCardStep() {
    if (
      !partnerTrack ||
      !partnerCards.length
    ) {
      return 0;
    }

    const first =
      partnerCards[0];

    const rect =
      first.getBoundingClientRect();

    const styles =
      getComputedStyle(
        partnerTrack
      );

    const gap =
      parseFloat(
        styles.columnGap ||
        styles.gap ||
        '0'
      ) || 0;

    return (
      rect.width +
      gap
    );
  }

  function partnerOffsetForIndex(
    index
  ) {
    return (
      partnerCardStep() *
      index
    );
  }

  function partnerCurrentOffset() {
    return partnerOffsetForIndex(
      partnerTrackIndex
    );
  }

  function setPartnerTransform(
    offset,
    animate = true
  ) {
    if (!partnerTrack) {
      return;
    }

    if (
      !animate ||
      prefersReducedMotion
    ) {
      partnerTrack.classList
        .add(
          'is-jump-reset'
        );

      partnerTrack.style
        .transition =
          'none';
    } else {
      partnerTrack.classList
        .remove(
          'is-jump-reset'
        );

      partnerTrack.style
        .transition =
          `transform ${partnerTransitionMs}ms cubic-bezier(.2,.75,.24,1)`;
    }

    partnerTrack.style.transform =
      `translate3d(${-offset}px,0,0)`;

    if (
      !animate ||
      prefersReducedMotion
    ) {
      requestAnimationFrame(
        () => {
          partnerTrack
            ?.classList.remove(
              'is-jump-reset'
            );
        }
      );
    }
  }

  function clonePartnerCard(
    source,
    realIndex
  ) {
    const clone =
      source.cloneNode(true);

    clone.dataset.partnerClone =
      'true';

    clone.dataset.partnerRealIndex =
      String(realIndex);

    clone.setAttribute(
      'aria-hidden',
      'true'
    );

    clone.removeAttribute('id');

    clone
      .querySelectorAll('[id]')
      .forEach(element => {
        element.removeAttribute(
          'id'
        );
      });

    clone
      .querySelectorAll(
        'a,button,[tabindex]'
      )
      .forEach(control => {
        control.setAttribute(
          'tabindex',
          '-1'
        );
      });

    return clone;
  }

  function renderPartnerDots() {
    if (!partnerDots) {
      return;
    }

    const count =
      partnerOriginalCards.length;

    if (count <= 1) {
      partnerDots.innerHTML =
        '';

      return;
    }

    partnerDots.innerHTML =
      partnerOriginalCards
        .map(
          (card, index) => {
            const active =
              index ===
              partnerRealIndex;

            const name =
              card.dataset
                .partnerName ||
              `Partner ${index + 1}`;

            return `
              <button
                class="partner-carousel-dot ${
                  active
                    ? 'active'
                    : ''
                }"
                type="button"
                data-partner-page="${index}"
                aria-label="Show ${escapeHtml(
                  name
                )}"
                aria-current="${
                  active
                    ? 'true'
                    : 'false'
                }"
              ></button>
            `;
          }
        )
        .join('');
  }

  function updatePartnerAccessibility() {
    partnerCards.forEach(card => {
      const clone =
        card.dataset
          .partnerClone ===
        'true';

      if (clone) {
        card.setAttribute(
          'aria-hidden',
          'true'
        );

        return;
      }

      const realIndex =
        Number(
          card.dataset
            .partnerRealIndex ||
          0
        );

      card.setAttribute(
        'aria-current',
        realIndex ===
          partnerRealIndex
          ? 'true'
          : 'false'
      );
    });
  }

  function syncPartnerState() {
    if (
      !partnerOriginalCards.length
    ) {
      return;
    }

    partnerRealIndex =
      partnerModulo(
        partnerTrackIndex -
        partnerCloneOffset,
        partnerOriginalCards.length
      );

    renderPartnerDots();
    updatePartnerAccessibility();
  }

  function buildPartnerLoop() {
    if (
      !partnerTrack ||
      partnerBuilding
    ) {
      return;
    }

    partnerBuilding = true;

    qsa(
      '.partner-card[data-partner-clone="true"]',
      partnerTrack
    ).forEach(clone => {
      clone.remove();
    });

    readPartnerOriginals();

    const count =
      partnerOriginalCards.length;

    if (!count) {
      partnerCards = [];

      partnerBuilding = false;

      return;
    }

    partnerOriginalCards.forEach(
      (card, index) => {
        card.dataset
          .partnerRealIndex =
            String(index);

        const name =
          qs(
            '.partner-card-kicker',
            card
          )?.textContent
            ?.trim() ||
          qs(
            '.partner-card-copy h3',
            card
          )?.textContent
            ?.trim() ||
          `Partner ${index + 1}`;

        card.dataset.partnerName =
          name;
      }
    );

    /*
     * Large buffer means that even on desktop,
     * where more card widths are visible,
     * no empty end of track appears.
     */

    const cloneBuffer =
      Math.max(
        6,
        count * 3
      );

    const before =
      document.createDocumentFragment();

    const after =
      document.createDocumentFragment();

    for (
      let offset = cloneBuffer;
      offset > 0;
      offset -= 1
    ) {
      const realIndex =
        partnerModulo(
          count - offset,
          count
        );

      before.appendChild(
        clonePartnerCard(
          partnerOriginalCards[
            realIndex
          ],
          realIndex
        )
      );
    }

    for (
      let offset = 0;
      offset < cloneBuffer;
      offset += 1
    ) {
      const realIndex =
        partnerModulo(
          offset,
          count
        );

      after.appendChild(
        clonePartnerCard(
          partnerOriginalCards[
            realIndex
          ],
          realIndex
        )
      );
    }

    partnerTrack.insertBefore(
      before,
      partnerTrack.firstChild
    );

    partnerTrack.appendChild(
      after
    );

    partnerCloneOffset =
      cloneBuffer;

    partnerRealIndex =
      partnerModulo(
        partnerRealIndex,
        count
      );

    partnerTrackIndex =
      partnerCloneOffset +
      partnerRealIndex;

    refreshPartnerCards();

    partnerBuilt = true;
    partnerBuilding = false;

    bindImageRecovery(
      partnerTrack
    );

    enhancePremiumSurfaces(
      partnerTrack
    );

    setPartnerTransform(
      partnerCurrentOffset(),
      false
    );

    syncPartnerState();
  }

  function resetPartnerLoopBoundary() {
    if (
      !partnerBuilt ||
      !partnerOriginalCards.length
    ) {
      return;
    }

    const count =
      partnerOriginalCards.length;

    const minIndex =
      partnerCloneOffset;

    const maxIndex =
      partnerCloneOffset +
      count -
      1;

    let corrected =
      partnerTrackIndex;

    while (
      corrected >
      maxIndex
    ) {
      corrected -= count;
    }

    while (
      corrected <
      minIndex
    ) {
      corrected += count;
    }

    if (
      corrected ===
      partnerTrackIndex
    ) {
      return;
    }

    partnerTrackIndex =
      corrected;

    setPartnerTransform(
      partnerCurrentOffset(),
      false
    );

    syncPartnerState();
  }

  function updatePartners(
    animate = true
  ) {
    if (
      !partnerTrack ||
      !partnerViewport
    ) {
      return;
    }

    if (!partnerBuilt) {
      buildPartnerLoop();
    }

    refreshPartnerCards();

    if (!partnerCards.length) {
      return;
    }

    setPartnerTransform(
      partnerCurrentOffset(),
      animate
    );

    syncPartnerState();

    if (partnerPrev) {
      partnerPrev.disabled =
        partnerOriginalCards.length < 2;
    }

    if (partnerNext) {
      partnerNext.disabled =
        partnerOriginalCards.length < 2;
    }
  }

  function stopPartners() {
    clearManagedInterval(
      partnerTimer
    );

    partnerTimer = 0;
  }

  function restartPartners() {
    stopPartners();

    if (
      prefersReducedMotion ||
      document.hidden ||
      !partnerReady ||
      partnerPaused ||
      partnerPointerActive ||
      partnerOriginalCards.length < 2
    ) {
      return;
    }

    partnerTimer =
      managedInterval(
        () => {
          movePartner(
            1,
            false
          );
        },
        partnerAutoplayDelay
      );
  }

  function movePartner(
    direction,
    restart = true
  ) {
    if (
      partnerAnimating ||
      partnerPointerActive ||
      partnerOriginalCards.length < 2
    ) {
      return;
    }

    partnerAnimating = true;

    partnerTrackIndex +=
      direction > 0
        ? 1
        : -1;

    updatePartners(true);

    if (restart) {
      restartPartners();
    }

    if (prefersReducedMotion) {
      partnerAnimating =
        false;

      resetPartnerLoopBoundary();
    }
  }

  function goToPartner(
    realIndex
  ) {
    if (
      !partnerOriginalCards.length ||
      partnerAnimating
    ) {
      return;
    }

    const target =
      partnerModulo(
        realIndex,
        partnerOriginalCards.length
      );

    partnerRealIndex =
      target;

    partnerTrackIndex =
      partnerCloneOffset +
      target;

    partnerAnimating =
      true;

    updatePartners(true);
    restartPartners();

    if (prefersReducedMotion) {
      partnerAnimating =
        false;
    }
  }

  partnerTrack?.addEventListener(
    'transitionend',
    event => {
      if (
        event.target !==
          partnerTrack ||
        event.propertyName !==
          'transform'
      ) {
        return;
      }

      partnerAnimating =
        false;

      resetPartnerLoopBoundary();
      syncPartnerState();
    }
  );

  partnerPrev?.addEventListener(
    'click',
    () => {
      movePartner(-1);
    }
  );

  partnerNext?.addEventListener(
    'click',
    () => {
      movePartner(1);
    }
  );

  partnerDots?.addEventListener(
    'click',
    event => {
      const dot =
        event.target.closest(
          '[data-partner-page]'
        );

      if (!dot) {
        return;
      }

      goToPartner(
        Number(
          dot.dataset
            .partnerPage ||
          0
        )
      );
    }
  );

  partnerViewport?.addEventListener(
    'keydown',
    event => {
      if (
        ![
          'ArrowLeft',
          'ArrowRight',
          'Home',
          'End'
        ].includes(event.key)
      ) {
        return;
      }

      event.preventDefault();

      if (
        event.key === 'Home'
      ) {
        goToPartner(0);

        return;
      }

      if (
        event.key === 'End'
      ) {
        goToPartner(
          partnerOriginalCards.length -
          1
        );

        return;
      }

      movePartner(
        event.key ===
          'ArrowLeft'
          ? -1
          : 1
      );
    }
  );


  /*
  |--------------------------------------------------------------------------
  | PARTNER TOUCH / POINTER DRAGGING
  |--------------------------------------------------------------------------
  */

  function beginPartnerPointer(
    event
  ) {
    if (
      !partnerViewport ||
      !partnerTrack ||
      partnerOriginalCards.length < 2
    ) {
      return;
    }

    if (
      event.button !== undefined &&
      event.button !== 0
    ) {
      return;
    }

    partnerPointerActive =
      true;

    partnerPointerId =
      event.pointerId ??
      null;

    partnerPointerStartX =
      event.clientX;

    partnerPointerStartY =
      event.clientY;

    partnerPointerCurrentX =
      event.clientX;

    partnerPointerHorizontal =
      false;

    partnerPointerBaseOffset =
      partnerCurrentOffset();

    partnerPaused =
      true;

    stopPartners();

    partnerTrack.classList.add(
      'is-dragging'
    );

    if (
      event.pointerId !==
      undefined
    ) {
      partnerViewport
        .setPointerCapture?.(
          event.pointerId
        );
    }
  }

  function movePartnerPointer(
    event
  ) {
    if (
      !partnerPointerActive
    ) {
      return;
    }

    if (
      partnerPointerId !== null &&
      event.pointerId !==
        partnerPointerId
    ) {
      return;
    }

    const deltaX =
      event.clientX -
      partnerPointerStartX;

    const deltaY =
      event.clientY -
      partnerPointerStartY;

    partnerPointerCurrentX =
      event.clientX;

    if (
      !partnerPointerHorizontal
    ) {
      if (
        Math.abs(deltaX) < 6
      ) {
        return;
      }

      /*
       * User is scrolling vertically.
       * Do not hijack normal mobile page scroll.
       */

      if (
        Math.abs(deltaY) >
        Math.abs(deltaX)
      ) {
        endPartnerPointer(
          event,
          true
        );

        return;
      }

      partnerPointerHorizontal =
        true;
    }

    event.preventDefault();

    const offset =
      partnerPointerBaseOffset -
      deltaX * 0.94;

    partnerTrack.style.transition =
      'none';

    partnerTrack.style.transform =
      `translate3d(${-offset}px,0,0)`;
  }

  function endPartnerPointer(
    event,
    cancelled = false
  ) {
    if (
      !partnerPointerActive
    ) {
      return;
    }

    const currentX =
      event?.clientX ??
      partnerPointerCurrentX;

    const deltaX =
      currentX -
      partnerPointerStartX;

    const step =
      partnerCardStep();

    const threshold =
      Math.min(
        76,
        Math.max(
          32,
          step * 0.16
        )
      );

    partnerPointerActive =
      false;

    if (
      partnerPointerId !== null
    ) {
      partnerViewport
        ?.releasePointerCapture?.(
          partnerPointerId
        );
    }

    partnerPointerId =
      null;

    partnerTrack
      ?.classList.remove(
        'is-dragging'
      );

    if (
      !cancelled &&
      partnerPointerHorizontal &&
      Math.abs(deltaX) >=
        threshold
    ) {
      partnerAnimating =
        false;

      partnerTrackIndex +=
        deltaX < 0
          ? 1
          : -1;

      partnerAnimating =
        true;

      updatePartners(true);
    } else {
      updatePartners(true);
    }

    partnerPointerHorizontal =
      false;

    partnerPaused =
      false;

    restartPartners();
  }

  if (
    partnerViewport &&
    'PointerEvent' in window
  ) {
    partnerViewport
      .addEventListener(
        'pointerdown',
        beginPartnerPointer
      );

    partnerViewport
      .addEventListener(
        'pointermove',
        movePartnerPointer,
        {
          passive: false
        }
      );

    partnerViewport
      .addEventListener(
        'pointerup',
        event => {
          endPartnerPointer(
            event
          );
        }
      );

    partnerViewport
      .addEventListener(
        'pointercancel',
        event => {
          endPartnerPointer(
            event,
            true
          );
        }
      );
  }


  /*
  |--------------------------------------------------------------------------
  | PARTNER PAUSE / RESUME
  |--------------------------------------------------------------------------
  */

  function pausePartners() {
    partnerPaused =
      true;

    stopPartners();
  }

  function resumePartners(
    event
  ) {
    if (
      event?.type ===
        'focusout' &&
      event.relatedTarget &&
      partnerCarousel
        ?.contains(
          event.relatedTarget
        )
    ) {
      return;
    }

    if (partnerPointerActive) {
      return;
    }

    partnerPaused =
      false;

    restartPartners();
  }

  partnerCarousel?.addEventListener(
    'mouseenter',
    pausePartners
  );

  partnerCarousel?.addEventListener(
    'mouseleave',
    resumePartners
  );

  partnerCarousel?.addEventListener(
    'focusin',
    pausePartners
  );

  partnerCarousel?.addEventListener(
    'focusout',
    resumePartners
  );

  if (
    partnerCarousel &&
    partnerTrack &&
    !partnerCarousel.hasAttribute('data-embla')
  ) {
    whenNear(
      partnerCarousel,
      () => {
        readPartnerOriginals();

        if (
          partnerOriginalCards.length
        ) {
          buildPartnerLoop();

          partnerReady =
            true;

          updatePartners(false);
          restartPartners();
        }
      },
      '500px 0px'
    );
  }


  /*
  |--------------------------------------------------------------------------
  | FAQ
  |--------------------------------------------------------------------------
  */

  const faqs =
    Array.isArray(
      site.faqs
    )
      ? site.faqs
      : [];

  const faqAccordion =
    qs('#faqAccordion');

  function renderFaqs() {
    if (
      !faqAccordion ||
      !faqs.length
    ) {
      return;
    }

    faqAccordion.innerHTML =
      faqs.map(
        (item, index) => {
          const panelId =
            `faq-panel-${index}`;

          const buttonId =
            `faq-button-${index}`;

          const open =
            index === 0;

          return `
            <article
              class="faq-item ${
                open
                  ? 'open'
                  : ''
              }"
            >

              <button
                class="faq-head"
                id="${buttonId}"
                type="button"
                aria-expanded="${
                  open
                    ? 'true'
                    : 'false'
                }"
                aria-controls="${panelId}"
              >

                <h4>
                  ${escapeHtml(
                    item?.question
                  )}
                </h4>

                <span
                  class="faq-plus"
                  aria-hidden="true"
                >
                  +
                </span>

              </button>

              <div
                class="faq-body"
                id="${panelId}"
                role="region"
                aria-labelledby="${buttonId}"
              >

                <p>
                  ${escapeHtml(
                    item?.answer
                  )}
                </p>

              </div>

            </article>
          `;
        }
      ).join('');

    enhancePremiumSurfaces(
      faqAccordion
    );
  }

  if (
    faqAccordion &&
    faqs.length
  ) {
    renderFaqs();
  }

  faqAccordion?.addEventListener(
    'click',
    event => {
      const button =
        event.target.closest(
          '.faq-head'
        );

      if (!button) {
        return;
      }

      const item =
        button.closest(
          '.faq-item'
        );

      if (!item) {
        return;
      }

      const opening =
        !item.classList
          .contains('open');

      qsa(
        '.faq-item',
        faqAccordion
      ).forEach(other => {
        other.classList.remove(
          'open'
        );

        qs(
          '.faq-head',
          other
        )?.setAttribute(
          'aria-expanded',
          'false'
        );
      });

      item.classList.toggle(
        'open',
        opening
      );

      button.setAttribute(
        'aria-expanded',
        opening
          ? 'true'
          : 'false'
      );
    }
  );


  /*
  |--------------------------------------------------------------------------
  | SLIDER LIFE DECORATION
  |--------------------------------------------------------------------------
  */

  qsa(
    [
      '.hero-slider',
      '.track-wrap',
      '.step-showcase',
      '.org-viewer',
      '.partner-carousel-viewport'
    ].join(',')
  ).forEach(slider => {
    if (
      qs(
        ':scope > .slider-life',
        slider
      )
    ) {
      return;
    }

    const life =
      document.createElement(
        'div'
      );

    life.className =
      'slider-life';

    life.setAttribute(
      'aria-hidden',
      'true'
    );

    life.innerHTML = `
      <span></span>
      <span></span>
      <span></span>
    `;

    slider.appendChild(life);
  });


  /*
  |--------------------------------------------------------------------------
  | FLOATING ACTION HUB
  |--------------------------------------------------------------------------
  */

  const actionHub =
    qs('#vslActionHub');

  const actionHubToggle =
    qs('#vslActionHubToggle');

  function setActionHub(open) {
    if (
      !actionHub ||
      !actionHubToggle
    ) {
      return;
    }

    actionHub.classList.toggle(
      'is-open',
      open
    );

    actionHubToggle.setAttribute(
      'aria-expanded',
      open
        ? 'true'
        : 'false'
    );
  }

  actionHubToggle?.addEventListener(
    'click',
    event => {
      event.stopPropagation();

      setActionHub(
        !actionHub.classList
          .contains(
            'is-open'
          )
      );
    }
  );

  document.addEventListener(
    'click',
    event => {
      if (
        actionHub?.classList
          .contains(
            'is-open'
          ) &&
        !actionHub.contains(
          event.target
        )
      ) {
        setActionHub(false);
      }
    }
  );

  function setRequestIntent(intent) {
    const settings = {
      quotation: {
        type:
          'quotation',

        category:
          'Institutional procurement',

        prompt:
          'Describe the product, specification, quantity and timeline you know.',

        status:
          'Quotation mode selected.'
      },

      order: {
        type:
          'quotation',

        category:
          'Single product request',

        prompt:
          'Tell us the product, quantity and delivery timeline for this order.',

        status:
          'Order route selected.'
      },

      feedback: {
        type:
          'feedback',

        category:
          'Service feedback',

        prompt:
          'Share what happened and the response you would like from Varenz.',

        status:
          'Feedback mode selected.'
      }
    };

    const selected =
      settings[intent] ||
      settings.quotation;

    const modeButton =
      feedbackButtons.find(
        button =>
          button.dataset.type ===
          selected.type
      );

    if (modeButton) {
      activateButtons(
        feedbackButtons,
        modeButton
      );
    }

    if (feedbackTypeInput) {
      feedbackTypeInput.value =
        selected.type;
    }

    if (requestCategory) {
      requestCategory.value =
        selected.category;
    }

    if (requestMessage) {
      requestMessage.setAttribute(
        'placeholder',
        selected.prompt
      );
    }

    updateRequestAssistant();
    setActionHub(false);

    showToast(
      selected.status,
      'info',
      3000
    );
  }

  qsa('[data-vsl-intent]')
    .forEach(element => {
      element.addEventListener(
        'click',
        () => {
          setRequestIntent(
            text(
              element.dataset
                .vslIntent
            )
          );
        }
      );
    });


  /*
  |--------------------------------------------------------------------------
  | ROLE LINK SHORTCUTS
  |--------------------------------------------------------------------------
  */

  qsa(
    '[role="link"][data-vpr-target]'
  ).forEach(element => {
    const navigate = () => {
      smoothScrollTo(
        text(
          element.dataset
            .vprTarget
        )
      );
    };

    element.addEventListener(
      'click',
      navigate
    );

    element.addEventListener(
      'keydown',
      event => {
        if (
          event.key === 'Enter' ||
          event.key === ' '
        ) {
          event.preventDefault();
          navigate();
        }
      }
    );
  });


  /*
  |--------------------------------------------------------------------------
  | ANCHOR NAVIGATION
  |--------------------------------------------------------------------------
  */

  document.addEventListener(
    'click',
    event => {
      const link =
        event.target.closest(
          'a[href^="#"]'
        );

      if (!link) {
        return;
      }

      const target =
        link.getAttribute(
          'href'
        );

      if (
        !target ||
        target === '#'
      ) {
        return;
      }

      const element =
        qs(target);

      if (!element) {
        return;
      }

      event.preventDefault();

      smoothScrollTo(target);

      try {
        history.replaceState(
          null,
          '',
          target
        );
      } catch (_) {
        // URL state unavailable.
      }
    }
  );


  /*
  |--------------------------------------------------------------------------
  | GLOBAL KEYBOARD
  |--------------------------------------------------------------------------
  */

  document.addEventListener(
    'keydown',
    event => {
      if (
        event.key === 'Escape'
      ) {
        if (
          actionHub?.classList
            .contains(
              'is-open'
            )
        ) {
          setActionHub(false);

          return;
        }

        if (
          teamModal?.classList
            .contains('open')
        ) {
          closeTeam();

          return;
        }

        if (searchIsOpen()) {
          setSearch(false);

          return;
        }

        if (drawerIsOpen()) {
          setDrawer(false);
        }
      }

      if (
        (
          event.ctrlKey ||
          event.metaKey
        ) &&
        event.key
          .toLowerCase() ===
          'k'
      ) {
        event.preventDefault();
        setSearch(true);
      }
    }
  );


  /*
  |--------------------------------------------------------------------------
  | TAB VISIBILITY
  |--------------------------------------------------------------------------
  */

  document.addEventListener(
    'visibilitychange',
    () => {
      restartHero();
      restartChallenges();
      restartFeatured();
      restartProcurement();
      restartOrganizations();
      restartWhy();
      restartPartners();
    }
  );


  /*
  |--------------------------------------------------------------------------
  | RESPONSIVE RECALCULATION
  |--------------------------------------------------------------------------
  */

  function refreshLayout() {
    updateFeatured();
    positionOrbit();

    if (
      partnerTrack &&
      partnerBuilt
    ) {
      updatePartners(false);
    }
  }

  if (
    'ResizeObserver'
    in window
  ) {
    const resizeObserver =
      new ResizeObserver(
        () => {
          clearManagedTimeout(
            resizeTimer
          );

          resizeTimer =
            managedTimeout(
              refreshLayout,
              100
            );
        }
      );

    [
      rowOne?.parentElement,
      orbitBox,
      partnerViewport
    ]
      .filter(Boolean)
      .forEach(element => {
        resizeObserver.observe(
          element
        );
      });

    observers.add(
      resizeObserver
    );
  } else {
    window.addEventListener(
      'resize',
      () => {
        clearManagedTimeout(
          resizeTimer
        );

        resizeTimer =
          managedTimeout(
            refreshLayout,
            100
          );
      },
      {
        passive: true
      }
    );
  }


  /*
  |--------------------------------------------------------------------------
  | READY
  |--------------------------------------------------------------------------
  */

  document.documentElement
    .classList.add(
      'vsl-js'
    );

  requestAnimationFrame(
    () => {
      document.body.classList.add(
        'vsl-app-ready'
      );

      bindImageRecovery();
      enhancePremiumSurfaces();

      if (
        partnerCarousel &&
        partnerTrack &&
        !partnerCarousel.hasAttribute('data-embla') &&
        !partnerBuilt
      ) {
        readPartnerOriginals();

        if (
          partnerOriginalCards.length
        ) {
          buildPartnerLoop();

          partnerReady = true;

          updatePartners(false);
          restartPartners();
        }
      }
    }
  );


  /*
  |--------------------------------------------------------------------------
  | CLEANUP
  |--------------------------------------------------------------------------
  */

  window.addEventListener(
    'pagehide',
    () => {
      stopHero();
      stopChallenges();
      stopFeatured();
      stopProcurement();
      stopOrganizations();
      stopWhy();
      stopPartners();

      searchRequest?.abort();

      timers.forEach(timer => {
        window.clearTimeout(timer);
      });

      timers.clear();

      intervals.forEach(timer => {
        window.clearInterval(timer);
      });

      intervals.clear();

      observers.forEach(observer => {
        observer.disconnect?.();
      });

      observers.clear();
    },
    {
      once: true
    }
  );
})();

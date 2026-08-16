(() => {
  'use strict';

  const ready = (callback) => {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', callback, { once: true });
      return;
    }
    callback();
  };

  ready(() => {
    document.documentElement.classList.add('vsl-recovery-ready');

    const formShell = document.querySelector('.cta-form-shell');
    const requestForm = document.getElementById('vslRequestForm');
    const requestTypeButtons = Array.from(document.querySelectorAll('#feedbackTypes button'));

    const openRequestForm = (focusForm = false) => {
      if (!formShell || !requestForm) return;
      formShell.classList.add('is-form-open');
      requestForm.removeAttribute('inert');
      requestForm.setAttribute('aria-hidden', 'false');
      if (focusForm) {
        requestForm.querySelector('input:not([type="hidden"]), select, textarea')?.focus({ preventScroll: true });
      }
    };

    if (requestForm && formShell) {
      requestForm.setAttribute('aria-hidden', 'true');
      requestForm.setAttribute('inert', '');

      requestTypeButtons.forEach((button) => {
        button.addEventListener('click', () => openRequestForm(false));
      });

      document.querySelectorAll('[data-vsl-intent]').forEach((control) => {
        control.addEventListener('click', () => {
          openRequestForm(false);
          const intent = control.getAttribute('data-vsl-intent');
          if (!intent) return;
          document.querySelector(`#feedbackTypes [data-type="${CSS.escape(intent)}"]`)?.click();
        });
      });
    }

    const hub = document.getElementById('vslActionHub');
    const hubToggle = document.getElementById('vslActionHubToggle');
    const hubClose = document.getElementById('vslActionHubClose');
    const hubPanel = document.getElementById('vslActionHubPanel');
    let scrim = null;

    const closeHub = (restoreFocus = true) => {
      if (!hub || !hubToggle) return;
      hub.classList.remove('is-open');
      hubToggle.setAttribute('aria-expanded', 'false');
      scrim?.remove();
      scrim = null;
      if (restoreFocus) hubToggle.focus();
    };

    const ensureScrim = () => {
      if (!hub || scrim || !hub.classList.contains('is-open')) return;
      scrim = document.createElement('button');
      scrim.type = 'button';
      scrim.className = 'vsl-action-hub__scrim';
      scrim.setAttribute('aria-label', 'Close Varenz support options');
      scrim.addEventListener('click', () => closeHub(false));
      hub.prepend(scrim);
    };

    hubToggle?.addEventListener('click', () => window.requestAnimationFrame(ensureScrim));
    hubClose?.addEventListener('click', () => closeHub());
    hubPanel?.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => closeHub(false));
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && hub?.classList.contains('is-open')) closeHub();
    });

    const footer = document.querySelector('.vsl-footer');
    if (hub && footer && 'IntersectionObserver' in window) {
      const footerObserver = new IntersectionObserver(([entry]) => {
        hub.classList.toggle('is-near-footer', entry.isIntersecting);
      }, { rootMargin: '0px 0px 120px 0px' });
      footerObserver.observe(footer);
    }

    const params = new URLSearchParams(window.location.search);
    const intent = params.get('intent');
    const typeButton = intent
      ? document.querySelector(`#feedbackTypes [data-type="${CSS.escape(intent)}"]`)
      : null;
    if (typeButton) {
      openRequestForm(false);
      typeButton.click();
    }

    const message = document.querySelector('#vslRequestForm textarea[name="message"]');
    let productSlugs = [];
    const productsParam = params.get('products');
    const productParam = params.get('product');

    if (productsParam) {
      try {
        const parsed = JSON.parse(productsParam);
        if (Array.isArray(parsed)) {
          productSlugs = parsed
            .filter((item) => item && typeof item.slug === 'string')
            .slice(0, 25)
            .map((item) => `${item.slug}:${Math.min(99999, Math.max(1, Number.parseInt(item.quantity, 10) || 1))}`);
        }
      } catch (_) {
        productSlugs = [];
      }
    } else if (productParam) {
      productSlugs = [productParam];
    } else {
      try {
        const stored = JSON.parse(sessionStorage.getItem('vsl-product-request-list-v1') || '[]');
        if (Array.isArray(stored)) {
          productSlugs = stored
            .filter((item) => item && typeof item.slug === 'string')
            .slice(0, 25)
            .map((item) => `${item.slug}:${Math.min(99999, Math.max(1, Number.parseInt(item.quantity, 10) || 1))}`);
        }
      } catch (_) {
        productSlugs = [];
      }
    }

    if (message && productSlugs.length) {
      const productNames = productSlugs.map((slug) => slug
        .split(':')[0]
        .split('-')
        .filter(Boolean)
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' '));
      const quantities = productSlugs.map((slug) => slug.split(':')[1] || '1');
      const lines = productNames.map((name, index) => `• ${name} — quantity ${quantities[index]}`);
      message.value = `Please provide specifications and availability for:\n${lines.join('\n')}\n\nAdditional details: `;
      message.dispatchEvent(new Event('input', { bubbles: true }));
    }

    document.querySelectorAll('[data-vsl-product]').forEach((link) => {
      link.addEventListener('click', () => {
        const slug = link.getAttribute('data-vsl-product') || '';
        if (message && slug && !message.value.includes(slug)) {
          message.value = `Product enquiry: ${slug.replaceAll('-', ' ')}\n\n`;
          message.dispatchEvent(new Event('input', { bubbles: true }));
        }
      });
    });
  });
})();

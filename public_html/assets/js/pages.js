(() => {
    'use strict';

    const header = document.querySelector('[data-page-header]');
    const megaTrigger = document.querySelector('[data-page-mega-trigger]');
    const mega = document.querySelector('[data-page-mega]');
    const menuTrigger = document.querySelector('[data-page-menu-trigger]');
    const drawer = document.querySelector('[data-page-drawer]');
    const drawerClose = document.querySelector('[data-page-menu-close]');
    let previousFocus = null;

    const setMega = (open) => {
        if (!mega || !megaTrigger) return;
        mega.hidden = !open;
        megaTrigger.setAttribute('aria-expanded', String(open));
    };

    const setDrawer = (open) => {
        if (!drawer || !menuTrigger) return;
        drawer.hidden = !open;
        menuTrigger.setAttribute('aria-expanded', String(open));

        if (open) {
            previousFocus = document.activeElement;
            drawer.querySelector('button, a')?.focus();
        } else if (previousFocus instanceof HTMLElement) {
            previousFocus.focus();
        }
    };

    megaTrigger?.addEventListener('click', () => {
        setMega(Boolean(mega?.hidden));
    });

    menuTrigger?.addEventListener('click', () => setDrawer(true));
    drawerClose?.addEventListener('click', () => setDrawer(false));
    drawer?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setDrawer(false)));

    document.addEventListener('click', (event) => {
        if (!header || mega?.hidden) return;
        if (!header.contains(event.target)) setMega(false);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        setMega(false);
        if (drawer && !drawer.hidden) setDrawer(false);
    });

    document.querySelectorAll('[data-page-faq] .vsl-page-faq__item > button').forEach((button) => {
        button.addEventListener('click', () => {
            const expanded = button.getAttribute('aria-expanded') === 'true';
            const panelId = button.getAttribute('aria-controls');
            const panel = panelId ? document.getElementById(panelId) : null;
            const item = button.closest('.vsl-page-faq__item');

            button.setAttribute('aria-expanded', String(!expanded));
            if (panel) panel.hidden = expanded;
            item?.classList.toggle('is-open', !expanded);
        });
    });

    if ('IntersectionObserver' in window) {
        if (!window.matchMedia?.('(prefers-reduced-motion: reduce)').matches) {
            document.documentElement.classList.add('vsl-reveal-enabled');
        }
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

        document.querySelectorAll('.vsl-neumo-card, .vsl-deep-card, .vsl-image-card, .vsl-process-card, .vsl-partner-card, .vsl-resource-card, .vsl-contact-card').forEach((card) => observer.observe(card));
    }
})();

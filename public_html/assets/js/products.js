(() => {
  'use strict';

  const STORAGE_KEY = 'vsl-product-request-list-v1';
  const qs = (selector, root = document) => root.querySelector(selector);
  const qsa = (selector, root = document) => Array.from(root.querySelectorAll(selector));
  const safeText = value => String(value ?? '').trim();
  let lastFocused = null;
  let filterController = null;

  function readList() {
    try {
      const parsed = JSON.parse(sessionStorage.getItem(STORAGE_KEY) || '[]');
      if (!Array.isArray(parsed)) return [];
      return parsed
        .filter(item => item && typeof item.slug === 'string' && typeof item.title === 'string')
        .slice(0, 25)
        .map(item => ({slug: safeText(item.slug).slice(0, 80), title: safeText(item.title).slice(0, 160), quantity: Math.min(99999, Math.max(1, Number.parseInt(item.quantity, 10) || 1))}));
    } catch (_) {
      return [];
    }
  }

  function writeList(items) {
    try { sessionStorage.setItem(STORAGE_KEY, JSON.stringify(items)); } catch (_) {}
    renderList(items);
  }

  function renderList(items = readList()) {
    qsa('[data-request-count]').forEach(node => { node.textContent = String(items.length); });
    const container = qs('[data-request-list-items]');
    const empty = qs('[data-request-list-empty]');
    const submit = qs('[data-request-list-submit]');
    if (!container || !empty) return;

    container.innerHTML = items.map(item => `
      <div class="vsl-request-list__item" data-request-item="${escapeHtml(item.slug)}">
        <strong>${escapeHtml(item.title)}</strong>
        <label><span class="sr-only">Quantity for ${escapeHtml(item.title)}</span><input type="number" min="1" max="99999" value="${item.quantity}" data-request-quantity="${escapeHtml(item.slug)}"></label>
        <button type="button" data-request-remove="${escapeHtml(item.slug)}" aria-label="Remove ${escapeHtml(item.title)}">Remove</button>
      </div>`).join('');

    empty.hidden = items.length > 0;
    if (submit) {
      submit.hidden = items.length === 0;
      const target = new URL(submit.href, window.location.href);
      target.search = '?intent=quotation';
      target.hash = 'cta';
      submit.href = target.toString();
    }
  }

  function escapeHtml(value) {
    const node = document.createElement('span');
    node.textContent = safeText(value);
    return node.innerHTML;
  }

  function addProduct(button) {
    const slug = safeText(button.dataset.slug).slice(0, 80);
    const title = safeText(button.dataset.title).slice(0, 160);
    if (!slug || !title) return;
    const quantity = Math.min(99999, Math.max(1, Number.parseInt(qs('[data-product-quantity]')?.value, 10) || 1));
    const items = readList();
    const existing = items.find(item => item.slug === slug);
    if (existing) existing.quantity = Math.min(99999, existing.quantity + quantity);
    else items.push({slug, title, quantity});
    writeList(items);
    button.textContent = 'Added to Request';
    window.setTimeout(() => { button.textContent = 'Add to Request'; }, 1800);
  }

  function setOverlay(element, open, trigger = null) {
    if (!element) return;
    if (open) {
      lastFocused = trigger || document.activeElement;
      element.hidden = false;
      document.body.classList.add('vsl-overlay-open');
      window.requestAnimationFrame(() => qs('button, a, input', element)?.focus());
    } else {
      element.hidden = true;
      document.body.classList.remove('vsl-overlay-open');
      lastFocused?.focus?.();
    }
  }

  function productCardMarkup(product) {
    const tags = Array.isArray(product.tags) ? product.tags : [];
    const base = safeText(window.VARENZ_APP?.baseUrl || '/').replace(/\/?$/, '/');
    return `<article class="vsl-product-card" data-product-card data-product-slug="${escapeHtml(product.slug)}">
      <a class="vsl-product-card__media" href="${base}products/${encodeURIComponent(product.slug)}"><img src="${base}assets/${escapeHtml(product.image)}" alt="${escapeHtml(product.title)}" width="720" height="520" loading="lazy" decoding="async"></a>
      <div class="vsl-product-card__body"><span>${escapeHtml(product.category)}</span><h2>${escapeHtml(product.title)}</h2><p>${escapeHtml(product.short_description)}</p><div class="vsl-product-tags">${tags.map(tag => `<span>${escapeHtml(tag)}</span>`).join('')}</div><div class="vsl-product-card__actions"><a href="${base}products/${encodeURIComponent(product.slug)}">View Product</a><button type="button" data-add-product data-slug="${escapeHtml(product.slug)}" data-title="${escapeHtml(product.title)}">Add to Request</button></div></div>
    </article>`;
  }

  async function updateProducts(form) {
    const grid = qs('[data-product-grid]');
    if (!grid) return;
    filterController?.abort();
    filterController = new AbortController();
    const params = new URLSearchParams(new FormData(form));
    const url = `${form.action}?${params.toString()}`;
    grid.setAttribute('aria-busy', 'true');

    try {
      const timeout = window.setTimeout(() => filterController.abort(), 8000);
      const response = await fetch(`${safeText(window.VARENZ_APP?.baseUrl || '/').replace(/\/?$/, '/')}api/products?${params.toString()}`, {headers:{Accept:'application/json'}, signal:filterController.signal});
      window.clearTimeout(timeout);
      if (!response.ok) throw new Error('Catalogue request failed');
      const payload = await response.json();
      const products = Array.isArray(payload.data) ? payload.data : [];
      grid.innerHTML = products.map(productCardMarkup).join('');
      qs('[data-product-count]').textContent = String(products.length);
      qs('[data-product-empty]').hidden = products.length > 0;
      history.replaceState({}, '', url);
    } catch (error) {
      if (error.name !== 'AbortError') form.submit();
    } finally {
      grid.setAttribute('aria-busy', 'false');
    }
  }

  document.addEventListener('click', event => {
    const add = event.target.closest('[data-add-product]');
    if (add) { event.preventDefault(); addProduct(add); return; }

    const remove = event.target.closest('[data-request-remove]');
    if (remove) { writeList(readList().filter(item => item.slug !== remove.dataset.requestRemove)); return; }

    const listOpen = event.target.closest('[data-request-list-open]');
    if (listOpen) { setOverlay(qs('[data-request-list]'), true, listOpen); listOpen.setAttribute('aria-expanded', 'true'); return; }
    if (event.target.closest('[data-request-list-close]')) { setOverlay(qs('[data-request-list]'), false); qs('[data-request-list-open]')?.setAttribute('aria-expanded', 'false'); return; }

    const menu = event.target.closest('[data-catalog-menu]');
    if (menu) { setOverlay(qs('[data-catalog-drawer]'), true, menu); menu.setAttribute('aria-expanded', 'true'); return; }
    if (event.target.closest('[data-catalog-close]')) { setOverlay(qs('[data-catalog-drawer]'), false); qs('[data-catalog-menu]')?.setAttribute('aria-expanded', 'false'); }
  });

  document.addEventListener('change', event => {
    const input = event.target.closest('[data-request-quantity]');
    if (!input) return;
    const items = readList();
    const item = items.find(entry => entry.slug === input.dataset.requestQuantity);
    if (item) { item.quantity = Math.min(99999, Math.max(1, Number.parseInt(input.value, 10) || 1)); writeList(items); }
  });

  document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') return;
    const list = qs('[data-request-list]');
    const drawer = qs('[data-catalog-drawer]');
    if (list && !list.hidden) { setOverlay(list, false); qs('[data-request-list-open]')?.setAttribute('aria-expanded', 'false'); }
    else if (drawer && !drawer.hidden) { setOverlay(drawer, false); qs('[data-catalog-menu]')?.setAttribute('aria-expanded', 'false'); }
  });

  const form = qs('[data-product-filter-form]');
  form?.addEventListener('submit', event => { if ('fetch' in window) { event.preventDefault(); updateProducts(form); } });
  let searchTimer = 0;
  qs('[data-product-search]')?.addEventListener('input', () => { window.clearTimeout(searchTimer); searchTimer = window.setTimeout(() => updateProducts(form), 320); });
  qs('[data-product-category]')?.addEventListener('change', () => updateProducts(form));

  renderList();
})();

# Varenz Supplies — Release Test Plan

## Automated checks completed in this workspace

1. Run `node tests/validate-static.mjs` from `public_html/`.
2. Run `node --check` for `assets/js/app.js`, `pages.js` and `recovery-2026.js`.
3. Run `npm run build` from `frontend-effects/`.
4. Run `npm run render:php` (or the timeout-safe equivalent) and confirm these routes render: `/`, `/about`, `/products`, `/procurement`, `/quality-compliance`, `/partners`, `/resources`, `/faq`, `/contact`.
5. Confirm every rendered route includes the location footer target; every dedicated route includes the shared drawer scrim; and the homepage includes the Embla partner root.

## Staging browser matrix required before launch

Test current Chrome, Firefox and Safari at 1440×900, 1024×768, 390×844 and 320×568.

- Header: no lateral slide-in; Explore opens by click and keyboard; Escape/outside click closes it.
- Mobile drawer: scrim, focus path, close button, body scroll lock and navigation links work.
- Hero and core content remain usable with JavaScript disabled and with WebGL disabled.
- Partner carousel: arrows, dots, keyboard, drag/swipe, autoplay pause on hover/focus and reduced motion.
- Search: exact and misspelled product terms return useful catalogue results.
- FAQ: only intended panels expand; plus/minus state and focus styling remain visible.
- Request form: type-first reveal, validation, CSRF, honeypot and success/error feedback.
- Request list: add/remove/clear behavior and query-before-fragment handoff to the request form.
- Support hub: no overlap at 320 px; desktop panel stays within the viewport.
- Footer: full viewport width, no floating card radius, two columns on tablet and one column on narrow mobile.

## Accessibility and performance gates

- Run axe with zero critical/serious violations on the home, products, product-detail, FAQ and contact views.
- Complete keyboard-only traversal with a visible focus indicator and no traps.
- Verify headings are sequential, landmarks are unique and controls have accessible names.
- Run Lighthouse mobile: accessibility ≥95, best practices ≥90, SEO ≥95. Treat performance as a monitored budget because the optional WebGL bundle is deferred.
- Verify the page remains functional under `prefers-reduced-motion: reduce` and 200% text zoom.

## Security and operations

- Search the deployment archive for secrets before upload.
- Use HTTPS, secure session cookies and production error logging with display errors disabled.
- Confirm upload permissions only allow the application to write to its intended request storage/log location.
- Back up the current site and test a rollback using the deployment ZIP checksum.

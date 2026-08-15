# Varenz Supplies Ltd — Release Checklist

## Completed in this package

- [x] Existing PHP MVC routes and protected submission flow preserved.
- [x] Hybrid neumorphic/glass header, top bar and mega menus implemented.
- [x] All five namespaced VSL UI kit catalogue components applied.
- [x] Quote, order, WhatsApp and feedback entry points verified.
- [x] Supplied PDF, PPTX and brochure downloads published.
- [x] Poppins typography and brand palette applied.
- [x] Desktop and 390 × 844 mobile browser QA completed.
- [x] JavaScript syntax, route rendering, local assets and hash links checked.
- [x] SEO title, description, Open Graph image, structured data, sitemap and robots file checked.
- [x] Visual QA result recorded as `passed` in `design-qa.md`.
- [x] Canonical catalogue and product detail routes added without prices, fake stock or unverified regulatory claims.
- [x] Request list and AJAX filtering implemented with progressive fallbacks.
- [x] Dead public product/team destinations repaired.
- [x] Action hub, FAQ expansion, mobile footer and partner asset delivery repaired.
- [x] Static repair suite and all JavaScript syntax checks passed on 2026-08-15.

## Required at live deployment

- [ ] Back up the current `public_html` and database.
- [ ] Extract to staging and confirm PHP 8.1+ plus Apache rewrite support.
- [ ] Set the live base URL and production environment in `config/app.php` if required by the host.
- [ ] Confirm `storage/cache`, `storage/sessions`, `storage/logs` and `storage/private` are writable by PHP.
- [ ] Confirm HTTPS redirects and response security headers on the public domain.
- [ ] Submit one real test request and record its reference number.
- [ ] Run PHP syntax checks and route smoke tests on the PHP 8.1+ staging host.
- [ ] Configure and verify an approved email or CRM notification provider.
- [ ] Submit `sitemap.xml` in Google Search Console after launch.

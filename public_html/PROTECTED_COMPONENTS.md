# Protected Components

These components are part of the public contract and must not be removed or renamed without a migration note and full regression test.

## Secure request workflow

- `#vslRequestForm`
- `#feedbackTypes`
- hidden CSRF, form-start, type, source, request-quality and intelligence fields
- honeypot field named `website`
- attachment input and its 8 MB server policy
- `POST /api/submissions`

The quote, order and feedback shortcuts may preselect this form, but must not bypass it.

## Navigation and discovery

- sticky `.site-header` and `#headerIsland`
- `#megaPanel`, `#mobileDrawer` and `#searchOverlay`
- stable homepage section IDs used by header and search results
- escape-to-close and focus-return behaviour

## Team identity routes

- `GET /team/{slug}`
- canonical slugs and legacy aliases in `data/site.php`
- team modal and deep-link handling

Printed QR codes may depend on these routes; renaming requires an alias and redirect plan.

## Customer-facing content

- server-rendered product, organisation, procurement, team and FAQ fallbacks
- contact details sourced from `config/app.php`
- supplied company profile, capability presentation and brochure downloads

## Public visual identity

- approved Varenz logo files in `assets/images/logo`
- deep-blue/teal colour variables
- Poppins typography
- `prefers-reduced-motion`, `prefers-reduced-transparency` and focus styles

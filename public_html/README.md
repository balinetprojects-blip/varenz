# Varenz Supplies Ltd — MVC Hybrid Website

A custom, dependency-free MVC PHP website built from the approved luxury redesign and selectively strengthened with verified assets and stable links recovered from the original Varenz website.

The old `public_html.zip` is **not** the application in this package. It was used only as a source library for approved logos, final hero images, Varenz-branded product media, team portraits, the original header/footer design language, supporting imagery and historic QR-profile aliases.

## Requirements

- PHP 8.1 or newer
- Apache with `mod_rewrite`, or PHP's built-in development server
- Writable `storage/private` and `storage/logs` directories

No Composer packages, WordPress installation, database server or JavaScript framework is required.

## Local preview

From the extracted project directory:

```bash
php -S 127.0.0.1:8099 router.php
```

Open:

```text
http://127.0.0.1:8099
```

Keep that terminal running. To start it in the background on Linux:

```bash
nohup php -S 127.0.0.1:8099 router.php > storage/logs/local-server.log 2>&1 &
```

## Production deployment

Upload the **contents of this project** to the target web root, such as `public_html` on Hostinger. Do not place the old archive inside the live web root and do not overwrite this project with the old site's code.

Recommended permissions:

```text
Directories: 755
Files:       644
storage/private: writable by PHP
storage/logs:    writable by PHP
```

Recommended Apache environment values:

```apache
SetEnv APP_ENV production
SetEnv APP_DEBUG 0
SetEnv APP_URL https://varenzsupplies.com
```

## MVC structure

```text
app/
  Controllers/     Page, API and team-profile orchestration
  Core/            Router, request, response and security utilities
  Models/          Structured site data and protected submissions
  Views/           Homepage, layout and stable team-profile pages
assets/
  css/             Full-width responsive visual system
  js/              Sliders, responsive filters, modal, search and forms
  images/          Curated original Varenz media plus new theme art
  downloads/       Approved downloadable documents
config/            Application configuration and bootstrap
data/              Structured website content and stable aliases
storage/private/   Protected submissions and procurement uploads
storage/logs/      Local/runtime logs
```

## Main routes

```text
GET  /
GET  /team/{slug}
GET  /api/challenges/{id}
GET  /api/categories/{id}
GET  /api/procurement/{id}
GET  /api/organizations/{id}
GET  /api/team
GET  /api/search?q=...
POST /api/submissions
```

## Selective original-site improvements

The hybrid build includes:

- Original Varenz wordmark in the header
- Original Varenz icon mark in the footer and profile system
- Original full-width header composition rebuilt responsively
- Original full-width dotted blue footer composition
- Six final desktop hero scenes and six mobile hero scenes
- Seven Varenz-branded medical product images
- Approved old-site team portraits
- About, operations, challenge, healthcare, logistics and collaboration imagery
- Downloadable company profile
- Legacy QR/deep-link aliases from the former team popup system

A detailed inclusion/exclusion record is available in:

```text
docs/OLD-SITE-ASSET-MERGE.md
```

## Responsive header behavior

The original header's appearance is retained, but its weak responsive behavior has been replaced:

- Full-width top contact strip
- Flexible contact pills that hide lower-priority details progressively
- Main navigation remains usable while the desktop window is minimized
- Navigation collapses into a right-side mobile drawer before overlap occurs
- Logo proportions remain intact
- Search and Request Support remain prominent at useful widths
- No artificial gap between header and hero

## Full-width visual system

The homepage is no longer constrained to a small card floating in the center of a large monitor. Major backgrounds span the full browser width, while readable content uses responsive inner gutters. Section spacing is reduced and adjacent areas share live gradients, artwork and motion so they visually connect.

The Varenz blue-and-teal animated art background is stored as:

```text
assets/images/varenz-art-wide.webp
assets/images/varenz-art-portrait.webp
```

Reduced-motion users automatically receive a calmer static experience.

## Hero system

The homepage uses the original six final hero scenes with:

- Desktop and mobile `<picture>` sources
- Automatic rotation
- Manual previous/next controls
- Pagination dots
- Accessible text and calls to action
- Dark readability scrim
- Responsive typography and positioning

## Interactive systems

- Responsive common-challenge explanations
- Responsive product category filtering
- Two opposing featured-product slider rows
- Automatic procurement procedure with manual controls
- Automatic supported-organisation slider
- Animated Why Varenz orbit
- Team department filters
- Team profile popup and dedicated profile pages
- Legacy QR/deep-link resolution
- Luxury FAQ accordion
- Website search overlay
- Quotation, feedback, suggestion and support form
- Fast quote, order, WhatsApp and feedback hub
- Procurement resource centre with company profile, capability deck and brochure
- Five namespaced UI-kit interactions with touch and reduced-motion fallbacks
- Search discovery files, route-aware JSON-LD and a verified 1200×630 social preview

## Team QR-link protection

Canonical team profile routes use:

```text
/team/{slug}
```

The application also recognizes old query-string and hash formats. Canonical slugs and aliases are stored in `data/site.php` and resolved by `SiteRepository`.

Do not rename a canonical slug or remove an alias until all printed and digital QR codes have been audited.

## Submission security

The main request form includes:

- Session CSRF validation
- Honeypot spam detection
- Minimum completion time
- IP-based rate limiting
- Upload extension, MIME-type and size validation
- Private file storage outside public assets
- Monthly JSON Lines submission records
- Visible inquiry-reference generation

## Editing content

Most homepage data is in:

```text
data/site.php
```

Contact details, upload limits and environment values are in:

```text
config/app.php
```

## Before live replacement

1. Back up the current live `public_html` completely.
2. Confirm every QR code against the canonical and alias team routes.
3. Confirm the two team members without imported portraits and add approved images if available.
4. Confirm all names, roles, telephone numbers, email addresses and social URLs.
5. Test every form with live PHP write permissions.
6. Configure email or CRM delivery if immediate submission notifications are required.
7. Test on real phones, tablets, minimized desktop windows and large monitors.
8. Keep `APP_DEBUG=0` and HTTPS enabled in production.

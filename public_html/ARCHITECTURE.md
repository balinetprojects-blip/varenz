# Varenz Website Architecture

## Folder Structure

```text
app/
  Controllers/     HTTP route handlers and page/API orchestration
  Core/            Router, request, response, security and rate-limit helpers
  Models/          Site data access, submission storage and request intelligence
  Views/           PHP templates for homepage, layout and team profiles
assets/
  css/             Site styles, header island and brand correction layer
  js/              Header, slider, search, form, carousel and modal behaviour
  images/          Product, hero, team, organisation and procurement media
  downloads/       Public downloads such as company profile
config/
  app.php          Environment, contact, storage and upload settings
data/
  site.php         Main public content model for dynamic sections
storage/
  private/         Submission records, uploads and rate-limit state
```

## Runtime Flow

1. `router.php` protects private folders and serves assets safely.
2. `index.php` registers routes.
3. Controllers read from `SiteRepository` and render views or JSON.
4. Homepage embeds `window.VARENZ_APP` so JavaScript can render dynamic sections.
5. Submissions pass through CSRF, honeypot, rate limit, upload checks and request intelligence.
6. `SubmissionRepository` stores JSONL records and private uploads.

## Section Framework To Preserve

```text
Header
Hero Slider
CTA/Form
Challenges
Categories
Featured Products
Procurement Procedure
Organisations
Why Varenz
Team
FAQ
Footer
```

## Extension Points

- Add public content in `data/site.php`.
- Add reusable behaviour in `assets/js/app.js`.
- Add visual polish at the end of `assets/css/app.css`.
- Add backend request analysis in `app/Models/RequestIntelligence.php`.
- Add new API routes through `index.php` and `ApiController`.

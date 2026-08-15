# Decisions

## 2026-08-03 - Preserve MVC Website Framework

Decision: Keep the custom PHP MVC package and upgrade it in place.

Why: The existing site already has controllers, repositories, security helpers, dynamic data, forms, team routes and assets.

Rejected: Replacing it with a new static site or Sites starter.

Revisit If: A future deployment plan formally moves the site to another framework.

## 2026-08-03 - Use Deterministic Request Intelligence First

Decision: Add request triage without calling the OpenAI API.

Why: The site can immediately improve submissions without needing secrets, paid API setup or live network dependency.

Rejected: Adding placeholder API code that would fail without `OPENAI_API_KEY`.

Revisit If: Varenz approves an OpenAI-powered classifier and completes the credential setup gate.

## 2026-08-03 - Remove Placeholder Social Links

Decision: Remove fake header social bubbles until official URLs are available.

Why: `href="#"` social links damage trust and behave like unfinished work.

Rejected: Keeping placeholders for visual decoration.

Revisit If: Official Varenz social links are provided.

## 2026-08-03 - Brochure Visual System Is The Design Source

Decision: Use the uploaded Varenz brochure style as the visual reference: white clinical space, deep blue, teal, rounded cards, icon strips and clear product imagery.

Why: The brochures already express the premium healthcare supply identity better than generic web decorations.

Rejected: Purple casts, abstract UI language and developer-facing section copy.

Revisit If: A new approved brand guide replaces the current references.

## 2026-08-11 - Apply The UI Kit As Namespaced Varenz Components

Decision: Adapt all five supplied UI-kit effects under `vsl-kit-###` namespaces and place each effect once where it supports a real customer task.

Why: The effects add recognisable premium interactions without replacing semantic content, the secure request workflow or the established MVC structure.

Rejected: Copying generic `.card` selectors, using hover-only information, or adding decorative animation to clinical specifications.

Revisit If: Usability evidence shows that a motion effect distracts from product or procurement information.

## 2026-08-11 - Use One Secure Conversion Workflow

Decision: Route quotation, order, service feedback and suggestions through the existing protected request form; offer WhatsApp as a direct communication alternative.

Why: Customers receive clear entry points while CSRF, honeypot, timing, upload validation, rate limiting and private storage remain consistent.

Rejected: Parallel unprotected forms, browser-only order storage, and an unconfigured third-party CRM dependency.

Revisit If: Varenz approves and configures a verified CRM or transactional-email provider.

## 2026-08-11 - Use Approved Resources And Poppins

Decision: Publish the supplied company profile, capability presentation and healthcare supply brochure, and use Poppins as the public brand typeface.

Why: These supplied documents and the brand guidelines are stronger trust signals than generic marketing assets.

Rejected: Placeholder downloads and continuing with a non-brand heading typeface.

Revisit If: A newer approved resource pack or brand guideline is issued.

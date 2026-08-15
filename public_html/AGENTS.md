# Agent Rules

## Before Editing
- Read `PRD.md`, `ARCHITECTURE.md`, `DECISIONS.md`, `TASKS.md` and this file.
- Inspect the existing section/component before changing it.
- Preserve the current PHP MVC structure unless a decision entry approves otherwise.
- Do not remove functioning features without an explicit task and rollback note.

## Coding Rules
- Keep customer-facing copy clinical, calm, precise and procurement-focused.
- Avoid developer-facing words on the public site.
- Use official Varenz colors: deep blue, teal, clean white and cool pale surfaces.
- Use real images already in `assets/images` for product and healthcare visuals.
- Keep route aliases for team profiles stable.
- Keep forms protected by CSRF, honeypot, rate limiting and private storage.
- Do not add an OpenAI API dependency unless the credential gate is completed first.

## Validation Rules
- Check PHP syntax when PHP is available.
- Check JavaScript syntax with Node where possible.
- Scan for dead anchors and placeholder links.
- Create a visual review artifact for website UI changes.
- Package upgrades separately from the source ZIP used as input.

## Accessibility Rules
- Preserve focus states, keyboard activation and escape-to-close behaviour.
- Keep mobile touch controls large enough and unclipped.
- Honour `prefers-reduced-motion`.
- Avoid critical information that only appears on hover.

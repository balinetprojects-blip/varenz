# Varenz Supplies — Known Issues

## Open

1. **Live notification delivery is not configured.** Requests are validated and stored by the PHP workflow, but deployment-specific email/CRM credentials and delivery ownership still need to be supplied.
2. **Final browser screenshot regression is environment-blocked.** The cloud browser could not reach the isolated local preview port and no local Chromium binary was available. Nine PHP routes were rendered and structurally verified; run the browser matrix in `TEST_PLAN.md` on staging before launch.
3. **Effects bundle size.** The optional WebGL/React bundle is approximately 2.2 MB uncompressed (about 623 KB gzip). It is deferred and core flows do not depend on it, but future work should split the WebGL scene from the smaller carousel/search helpers.

## Closed in the August 2026 recovery

- Removed conflicting legacy partner-carousel initialization when Embla owns the component.
- Stabilized the shared header and route-specific mobile drawer.
- Added a real drawer scrim, body scroll locking and escape/outside close paths.
- Consolidated full-width, zero-radius footers and restored the location target on every route.
- Converted the long homepage form into a progressive request-type-first flow.
- Added typo-tolerant catalogue search fallback, collision-safe support positioning and reduced-motion-aware reveals.

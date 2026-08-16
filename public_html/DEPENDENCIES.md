# Varenz Supplies — Verified Dependencies

## Runtime

- PHP 8.1+ with sessions and standard JSON/filesystem extensions.
- Apache with `mod_rewrite` for clean routes. The packaged `.htaccess` provides the front-controller fallback.
- No database is required for the catalogue or request-list workflow.

## Browser bundle

The source for the progressive enhancement bundle is in `frontend-effects/`. Its production output is `public_html/assets/js/varenz-effects.js`.

| Package | Version | Purpose | License |
|---|---:|---|---|
| React / React DOM | 19.2.0 | Progressive visual enhancement mount | MIT |
| Three.js | 0.181.2 | WebGL product-space rendering | MIT |
| React Three Fiber / Drei | 9.4.0 / 10.7.7 | React bindings and scene helpers | MIT |
| ShaderGradient | 2.3.3 | Brand-gradient hero shader | MIT |
| React Three Postprocessing | 3.0.4 | GPU post-processing | MIT |
| GSAP | 3.13.0 | Reduced-motion-aware section reveals | Standard no-charge license |
| Embla Carousel | 8.6.0 | Accessible touch/keyboard partner carousel | MIT |
| Fuse.js | 7.1.0 | Client-side typo-tolerant product search fallback | Apache-2.0 |
| Floating UI DOM | 1.7.4 | Collision-safe desktop support panel placement | MIT |

## Development and verification

- Vite 7.2.4 and `@vitejs/plugin-react` build the effects bundle.
- `@php-wasm/node` and `@php-wasm/universal` render PHP routes in environments without a native PHP binary.
- `@playwright/test` and `@axe-core/playwright` are declared for browser and accessibility regression tests when a Chromium runtime is available.
- `public_html/tests/validate-static.mjs` is the no-browser deployment integrity suite.

All core navigation, forms, catalogue content and request-list behavior remain server-rendered. JavaScript and WebGL enhancements are non-blocking.

# Varenz Supplies Ltd — Release Test Results

Date: 2026-08-15

| Check | Result | Evidence |
| --- | --- | --- |
| Static regression suite | Pass | `node --test tests/validate-static.mjs`: 8/8 tests passed. |
| PHP route rendering | Pass | PHP 8.4 WebAssembly rendered `/`, `/about`, `/products`, `/procurement`, `/quality-compliance`, `/partners`, `/resources`, `/faq` and `/contact`. |
| Rendered asset resolution | Pass | All local stylesheet, script, image and download references in the nine rendered routes resolve. |
| JavaScript syntax | Pass | `pages.js`, `header-island.js`, `products.js` and the compiled `varenz-effects.js` pass `node --check`. |
| React effects production build | Pass | Vite built the React Three Fiber, shader-gradient and liquid-logo bundle from 669 modules. |
| Mega-menu destinations | Pass | Every primary and secondary destination points to an implemented route or a valid homepage section. |
| Progressive enhancement | Pass | Static content remains visible without JavaScript or WebGL; effects observe reduced-motion and low-capability fallbacks. |
| Existing business flow | Pass | Product filtering, product detail routes, session request list and secure Request Centre handoff remain registered and covered. |
| Browser visual capture | Not run | The managed cloud browser blocked the local preview URL. No screenshot-based visual-pass claim is made for this release. |
| Live submission delivery | Deployment check | Submit one real staging request over HTTPS and confirm its reference/storage result. Email/CRM notification requires approved provider credentials. |

Re-run the included static suite with `node --test tests/validate-static.mjs`.

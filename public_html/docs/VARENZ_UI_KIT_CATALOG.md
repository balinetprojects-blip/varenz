# Varenz Website UI Kit Catalogue

Rolling register of externally sourced interface effects proposed for the Varenz Supplies Ltd website.

## Governance

- Status: adapted and applied to the production website with Varenz namespaces and accessibility fallbacks.
- Preserve every original source before adaptation.
- Never copy generic selectors such as `.card`, `.content`, `.border`, `.line`, or ID selectors directly into the production stylesheet.
- Production versions must use the `vsl-kit-###` namespace, semantic HTML, keyboard/focus parity, touch behavior, and `prefers-reduced-motion` fallbacks.
- Varenz theme: deep medical blue `#31459A`, bright teal `#1DB9C2`, clinical white, pale blue-gray, and charcoal copy.
- Typography and content will be replaced with approved Varenz text and real product/service information.
- Source credits must remain documented even if comments are removed from minified production assets.

## Rolling index

| No. | Tool ID | Captured component | Main behavior | Proposed Varenz use | Current status |
|---:|---|---|---|---|---|
| 1 | `VSL-KIT-001` | Rotating three-card deck with luminous baseline | Three overlapping cards exchange centre/side positions on a 12-second loop; layered glow lines | Procurement Resources preview deck | Applied; static three-card reduced-motion fallback |
| 2 | `VSL-KIT-002` | Dual-corner expansion/reveal card | Opposing clinical blue/teal corners expand on hover or keyboard focus | Start Here cards and downloadable resource cards | Applied; content remains semantic and visible on touch |
| 3 | `VSL-KIT-003` | Static soft-neumorphic card | Raised surface made with paired dark/light shadows and a glass layer | Start Here cards, resource cards and fast contact panel | Applied; dark-theme and reduced-transparency support included |
| 4 | `VSL-KIT-004` | Expanding vertical accordion panels | Active, hovered or keyboard-focused organisation expands from `flex:1` to `flex:4` | Organisations We Support navigation | Applied; mobile retains the existing horizontal snap-card interaction |
| 5 | `VSL-KIT-005` | Luxury logo/brand reveal card | Approved mark transitions to full wordmark with a restrained light trail | Footer brand signature | Applied; wordmark is permanently visible on touch and reduced-motion devices |

## Production implementation map

| Component | Markup | Styling | Interaction |
|---|---|---|---|
| `VSL-KIT-001` | `app/Views/home/index.php` → `#resources` | `assets/css/app.css` | CSS animation only; motion preference respected |
| `VSL-KIT-002` | Start Here and resource cards | `assets/css/app.css` | Hover and `:focus-within`; decorative layers only |
| `VSL-KIT-003` | Start Here, resource cards, fast contact panel | `assets/css/app.css` | Static surface primitive |
| `VSL-KIT-004` | `#organizations .org-nav` | `assets/css/app.css` | Existing button selection in `assets/js/app.js`; CSS expands selected/focused panel |
| `VSL-KIT-005` | Footer brand link | `assets/css/app.css` | Hover/focus reveal; touch and reduced-motion use visible wordmark state |

## 1. VSL-KIT-001 — Rotating three-card deck and glow lines

Source: Uiverse.io, credited in the supplied code to `ilkhoeri`.

### Captured mechanics

- CSS-variable geometry controls width, height, rotation, horizontal inset, and wrapper width.
- Three absolutely positioned cards cycle through left, centre, and right states.
- The centre card receives the top stacking order.
- `nth-child()` controls timing, gradient, position, transform, and z-index.
- Icon and oversized outlined-number layers use delayed opacity animations.
- Two pseudo-element glow-line pairs create broad blurred light and sharp one-pixel highlights.
- Original animation is infinite and has no pause or reduced-motion handling.

### Varenz adaptation

- Namespace as `.vsl-kit-001`, `.vsl-kit-001__card`, `.vsl-kit-001__content`, and `.vsl-kit-001__lines`.
- Replace yellow/blue/purple gradients with deep-blue, teal, and clinical aqua variants.
- Replace decorative numbers/SVGs with approved product-family icons and meaningful labels.
- Use real buttons only when cards navigate; add accessible names and visible focus.
- Pause on hover/focus and when the document is hidden; use a static three-card layout for reduced motion.
- Do not place essential information exclusively in timed fades.

### Original CSS

```css
/* From Uiverse.io by ilkhoeri */ 
.wrap_card {
  position: relative;
  overflow: hidden;
  width: var(--w-wrap-card);
  height: calc(var(--h-card) / 1.25);
  display: flex;
  align-items: center;
  justify-content: center;
  --w-card: 150px;
  --h-card: 200px;
  --rotate-card: 15deg;
  --insetX-card: 28px;
  --t-card: calc(var(--insetX-card) * 1.25);
  --w-wrap-card: calc(var(--w-card) + calc(calc(var(--w-card) / 2) * 2));
}

.content {
  background-color: #ffffff50;
  overflow: hidden;
  position: relative;
  width: calc(100% - calc(var(--pd) * 2));
  height: calc(100% - calc(var(--pd) * 2));
  border-radius: calc(var(--round) - var(--pd));
}
.content > span {
  font-size: 300px;
  font-weight: 800;
  line-height: 0.75;
  position: absolute;
  width: 100%;
  height: 100%;
  inset: 50% 0 0 50%;
  transform: translate(-50%, -50%);
  background-clip: text;
  -webkit-text-stroke-width: 3px;
  color: transparent;
  opacity: 0;
  background-image: linear-gradient(-45deg, #ffffff25 0%, #ffffffac 100%);
  animation: opacity 0s cubic-bezier(1, 0, 0, 1) forwards var(--delay) reverse;
}
.content > svg {
  height: 66px;
  width: 66px;
  position: absolute;
  inset: 50% 0 0 50%;
  opacity: 1;
  animation: opacity 8.4s cubic-bezier(1, 0, 0, 1) forwards
    calc(var(--delay) - 4.3s);
  transform: translate(-50%, -50%);
}
.card:nth-child(1) { --delay: 4.3s; }
.card:nth-child(2) { --delay: 7.3s; }
.card:nth-child(3) { --delay: 10.3s; }
@keyframes opacity { from { opacity: 1; } to { opacity: 0; } }

.card {
  display: flex;
  align-items: center;
  justify-content: center;
  position: absolute;
  overflow: hidden;
  animation: rotating 9s cubic-bezier(0.75, 0, 0, 1.01) infinite 0s;
  border-radius: var(--round);
  background: var(--bg);
  order: var(--order);
  width: var(--w-card);
  height: var(--h-card);
  z-index: var(--z1);
  top: var(--t1);
  left: var(--l1);
  right: var(--r1);
  transform: var(--trans1);
  --pd: 4px;
  --round: 16px;
  --x1: var(--insetX-card);
  --x2: calc(var(--w-wrap-card) - calc(var(--w-card) + var(--insetX-card)));
  --to-left: rotate(calc(var(--rotate-card) * -1));
  --to-center: calc(var(--w-card) / 2);
  --to-right: rotate(calc(var(--rotate-card) * 1));
}

.card:nth-child(1) {
  --order: 2;
  --bg: radial-gradient(circle, rgba(252, 240, 142, 1) 0%, rgba(246, 173, 32, 1) 40%, rgba(192, 142, 8, 1) 100%);
  --z1: 2; --t1: 0; --l1: var(--to-center); --r1: var(--to-center); --trans1: rotate(calc(var(--rotate-card) * 0));
  --z2: 0; --t2: var(--t-card); --l2: var(--x1); --r2: var(--x2); --trans2: var(--to-left);
  --z3: 0; --t3: var(--t-card); --l3: var(--x2); --r3: var(--x1); --trans3: var(--to-right);
}
.card:nth-child(2) {
  --order: 3;
  --bg: radial-gradient(circle, rgba(142, 249, 252, 1) 0%, rgba(32, 164, 246, 1) 40%, rgba(8, 81, 192, 1) 100%);
  --z1: 0; --t1: var(--t-card); --l1: var(--x2); --r1: var(--x1); --trans1: var(--to-right);
  --z2: 2; --t2: 0; --l2: var(--to-center); --r2: var(--to-center); --trans2: rotate(calc(var(--rotate-card) * 0));
  --z3: 0; --t3: var(--t-card); --l3: var(--x1); --r3: var(--x2); --trans3: var(--to-left);
}
.card:nth-child(3) {
  --order: 1;
  --bg: radial-gradient(circle, rgba(222, 128, 233, 1) 0%, rgba(213, 32, 246, 1) 40%, rgba(139, 6, 157, 1) 100%);
  --z1: 0; --t1: var(--t-card); --l1: var(--x1); --r1: var(--x2); --trans1: var(--to-left);
  --z2: 0; --t2: var(--t-card); --l2: var(--x2); --r2: var(--x1); --trans2: var(--to-right);
  --z3: 2; --t3: 0; --l3: var(--to-center); --r3: var(--to-center); --trans3: rotate(calc(var(--rotate-card) * 0));
}
@keyframes rotating {
  0%, 99.99% { z-index: var(--z1); top: var(--t1); left: var(--l1); right: var(--r1); transform: var(--trans1); }
  33.33% { z-index: var(--z2); top: var(--t2); left: var(--l2); right: var(--r2); transform: var(--trans2); }
  66.66% { z-index: var(--z3); top: var(--t3); left: var(--l3); right: var(--r3); transform: var(--trans3); }
}

.lines { position: absolute; inset: auto 0 0; width: 100%; display: flex; align-items: center; justify-content: center; z-index: 4; }
.lines::after {
  content: ""; width: 100%; height: 0px; position: absolute; z-index: 2; inset: 0;
  --mask-bg: #e8e8e8; background: var(--mask-bg);
  mask-image: radial-gradient(50% 200px at top, transparent 20%, var(--mask-bg));
}
.line { position: absolute; width: 100%; display: flex; align-items: center; justify-content: center; }
.line::before, .line::after {
  content: ""; position: absolute; inset: auto;
  background: linear-gradient(to right, var(--gradient-a-line, #0000), var(--gradient-b-line, #0000), var(--gradient-c-line, #0000));
  filter: var(--blur-line); width: var(--w-line); height: var(--h-line);
}
.line:nth-child(1)::before { --blur-line: blur(4px); --w-line: 100%; --h-line: 5px; --gradient-b-line: #2f69f2; }
.line:nth-child(1)::after { --w-line: 100%; --h-line: 1px; --gradient-b-line: #6366f1; }
.line:nth-child(2)::before { --blur-line: blur(4px); --w-line: 50%; --h-line: 5px; --gradient-b-line: #84ccfc; }
.line:nth-child(2)::after { --w-line: 50%; --h-line: 1px; --gradient-b-line: #14d3f5; }
```

## 2. VSL-KIT-002 — Dual-corner expansion/reveal card

Source: Uiverse.io, credited in the supplied code to `eslam-hany`.

### Captured mechanics and adaptation

- Two pseudo-elements begin at opposite corners at 20% width/height.
- On hover, both expand to 100%, producing a wipe/cover effect.
- The lower layer injects `HELLO` through generated content; this is not suitable for semantic Varenz content.
- Replace generated text with real HTML and treat the pseudo-elements as decoration.
- Add `:focus-visible` and explicit tap/expanded state; disable full-cover motion for reduced motion.
- Recommended for short service or CTA cards, not dense product specifications.

### Original CSS

```css
/* From Uiverse.io by eslam-hany */ 
.card {
  position: relative;
  width: 220px;
  height: 320px;
  background: mediumturquoise;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 25px;
  font-weight: bold;
  border-radius: 15px;
  cursor: pointer;
}
.card::before, .card::after {
  position: absolute;
  content: "";
  width: 20%;
  height: 20%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 25px;
  font-weight: bold;
  background-color: lightblue;
  transition: all 0.5s;
}
.card::before { top: 0; right: 0; border-radius: 0 15px 0 100%; }
.card::after { bottom: 0; left: 0; border-radius: 0 100%  0 15px; }
.card:hover::before, .card:hover:after {
  width: 100%; height: 100%; border-radius: 15px; transition: all 0.5s;
}
.card:hover:after { content: "HELLO"; }
```

## 3. VSL-KIT-003 — Static soft-neumorphic card

Source: Uiverse.io, credited in the supplied code to `adamgiebl`.

### Captured mechanics and adaptation

- A neutral raised surface uses one dark and one light shadow.
- No animation, JavaScript, or missing markup dependency.
- This is the closest match to the existing Varenz premium-neumorphism direction.
- Production form will use responsive sizing, a shallower blue-tinted shadow, stronger focus treatment, and sufficient surface contrast.
- Suitable as a shared surface primitive rather than a one-off component.

### Original CSS

```css
/* From Uiverse.io by adamgiebl */ 
.card {
  width: 190px;
  height: 254px;
  border-radius: 30px;
  background: #e0e0e0;
  box-shadow: 15px 15px 30px #bebebe,
             -15px -15px 30px #ffffff;
}
```

## 4. VSL-KIT-004 — Expanding vertical accordion panels

Source: Uiverse.io, credited in the supplied code to `joe-watson-sbf`.

### Captured mechanics and adaptation

- Child panels share equal space through `flex: 1`.
- The hovered panel expands to four shares while siblings contract.
- Labels rotate from vertical to horizontal as the panel expands.
- Production version needs buttons/links inside each panel, keyboard-selected state, `aria-expanded` only if content truly expands, and touch support.
- Desktop can retain the expanding strip; mobile should use horizontal cards or a standard stacked accordion.
- Replace black/pink with clinical white/deep blue/teal and actual Varenz categories.

### Original CSS

```css
/* From Uiverse.io by joe-watson-sbf */ 
.card {
  width: 210px;
  height: 254px;
  border-radius: 4px;
  background: #212121;
  display: flex;
  gap: 5px;
  padding: .4em;
}
.card p {
  height: 100%; flex: 1; overflow: hidden; cursor: pointer; border-radius: 2px;
  transition: all .5s; background: #212121; border: 1px solid #ff5a91;
  display: flex; justify-content: center; align-items: center;
}
.card p:hover { flex: 4; }
.card p span {
  min-width: 14em; padding: .5em; text-align: center; transform: rotate(-90deg);
  transition: all .5s; text-transform: uppercase; color: #ff568e; letter-spacing: .1em;
}
.card p:hover span { transform: rotate(0); }
```

## 5. VSL-KIT-005 — Luxury logo/brand reveal card

Source: Uiverse.io, credited in the supplied code to `Smit-Prajapati`.

### Captured mechanics

- A dark brand card contains two staged logo parts, a light-trail layer, an inset border, bottom caption, and secondary logo caption.
- Hover changes corner radius, scales the card, widens the logo viewport, straightens/reveals the border, reveals captions, and runs a directional light trail.
- Logo expansion lasts one second; most other transitions last 0.5 seconds.
- The effect relies on missing HTML and SVG IDs (`#logo-main`, `#logo-second`) and therefore is not standalone.

### Varenz adaptation

- Rebuild with the approved Varenz mark and wordmark; do not trace or distort the logo.
- Replace charcoal/gold with deep blue, teal, white, and a restrained cyan highlight.
- Use this once as a premium brand moment, not on every card.
- Replace hover-only reveal with focus/tap support and keep the brand name visible to assistive technology.
- Limit hover scale to avoid overlap and layout clipping; provide a motion-free state.

### Original CSS

```css
/* From Uiverse.io by Smit-Prajapati */ 
.card {
  width: 300px; height: 200px; background: #243137; position: relative;
  display: grid; place-content: center; border-radius: 10px; overflow: hidden;
  transition: all 0.5s ease-in-out;
}
#logo-main, #logo-second { height: 100%; }
#logo-main { fill: #bd9f67; }
#logo-second { padding-bottom: 10px; fill: none; stroke: #bd9f67; stroke-width: 1px; }
.border {
  position: absolute; inset: 0px; border: 2px solid #bd9f67; opacity: 0;
  transform: rotate(10deg); transition: all 0.5s ease-in-out;
}
.bottom-text {
  position: absolute; left: 50%; bottom: 13px; transform: translateX(-50%);
  font-size: 6px; text-transform: uppercase; padding: 0px 5px 0px 8px;
  color: #bd9f67; background: #243137; opacity: 0; letter-spacing: 7px;
  transition: all 0.5s ease-in-out;
}
.content { transition: all 0.5s ease-in-out; }
.content .logo {
  height: 35px; position: relative; width: 33px; overflow: hidden;
  transition: all 1s ease-in-out;
}
.content .logo .logo1 { height: 33px; position: absolute; left: 0; }
.content .logo .logo2 { height: 33px; position: absolute; left: 33px; }
.content .logo .trail { position: absolute; right: 0; height: 100%; width: 100%; opacity: 0; }
.content .logo-bottom-text {
  position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);
  margin-top: 30px; color: #bd9f67; padding-left: 8px; font-size: 11px;
  opacity: 0; letter-spacing: none; transition: all 0.5s ease-in-out 0.5s;
}
.card:hover { border-radius: 0; transform: scale(1.1); }
.card:hover .logo { width: 134px; animation: opacity 1s ease-in-out; }
.card:hover .border { inset: 15px; opacity: 1; transform: rotate(0); }
.card:hover .bottom-text { letter-spacing: 3px; opacity: 1; transform: translateX(-50%); }
.card:hover .content .logo-bottom-text { opacity: 1; letter-spacing: 9.5px; }
.card:hover .trail { animation: trail 1s ease-in-out; }
@keyframes opacity {
  0% { border-right: 1px solid transparent; }
  10% { border-right: 1px solid #bd9f67; }
  80% { border-right: 1px solid #bd9f67; }
  100% { border-right: 1px solid transparent; }
}
@keyframes trail {
  0% { background: linear-gradient(90deg, rgba(189, 159, 103, 0) 90%, rgb(189, 159, 103) 100%); opacity: 0; }
  30% { background: linear-gradient(90deg, rgba(189, 159, 103, 0) 70%, rgb(189, 159, 103) 100%); opacity: 1; }
  70% { background: linear-gradient(90deg, rgba(189, 159, 103, 0) 70%, rgb(189, 159, 103) 100%); opacity: 1; }
  95% { background: linear-gradient(90deg, rgba(189, 159, 103, 0) 90%, rgb(189, 159, 103) 100%); opacity: 0; }
}
```

## Application priority

1. `VSL-KIT-003` should become the shared card surface first because it directly supports the approved Varenz neumorphism system and has the lowest functional risk.
2. `VSL-KIT-001` can become a featured-product or trust-pillar motion component after the exact target section is approved.
3. `VSL-KIT-004` is suitable for organisations/product categories after keyboard, touch, and mobile redesign.
4. `VSL-KIT-005` is a single premium signature effect for About/partnership/brand introduction.
5. `VSL-KIT-002` should be used sparingly for a small CTA or service reveal and never for critical text.

## Next-entry protocol

For every newly pasted kit:

1. Assign the next sequential number and stable ID.
2. Preserve the original code and credit.
3. Name the visual behavior and list CSS/JS/markup dependencies.
4. Record selector/keyframe conflicts and production defects.
5. Map it to a specific Varenz section and approved content.
6. Define brand, responsive, accessibility, touch, performance, and reduced-motion adaptations.
7. Keep it in `Captured` status until a separate preview is approved.
8. After approval, record the exact project files changed and verification results.

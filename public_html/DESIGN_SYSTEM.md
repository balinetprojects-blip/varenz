# Varenz Supplies Website Design System

## Brand foundation

- Type: Poppins, with system sans-serif fallbacks.
- Core colours: deep blue `#31459A`, bright teal `#1DB9C2`, clinical white, pale blue-grey and charcoal copy.
- Voice: clinical, calm, precise and procurement-focused.
- Source truth: the supplied Varenz brand guidelines, company profile, capability presentation and healthcare supply brochures.

## Surface language

The interface combines translucent glass with shallow neumorphic elevation. Cards retain readable contrast, restrained shadows and a visible one-pixel boundary. Deep-blue sections use white copy and pale-teal accents; light sections preserve generous clinical white space.

## Interaction rules

- Primary action: brand blue-to-teal gradient, minimum 44-pixel touch target.
- Keyboard focus: visible teal outline with four-pixel separation.
- Motion: purposeful, reversible and disabled by `prefers-reduced-motion`.
- Touch: no critical content depends on hover.
- Glass: removed when `prefers-reduced-transparency` is enabled.
- Forms: labels remain visible and native controls preserve browser validation semantics.

## Applied UI kit

The five adapted components are documented in `docs/VARENZ_UI_KIT_CATALOG.md`. Production selectors use the `vsl-kit-###` namespace and do not override generic `.card` selectors.

## Responsive breakpoints

- Wide desktop: above 1220px.
- Compact desktop/tablet: 821–1220px.
- Mobile: 820px and below.
- Small mobile: 560px and below.

The mega menu becomes a drawer, organisation panels become horizontal snap cards, resource cards stack, and the hero removes perspective transforms on small phones.

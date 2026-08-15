# Visual Effects Implementation

## Purpose

The enhancement layer adds a premium liquid-medical visual language while the PHP MVC application remains the source of truth for content, routes, forms and security.

## React Three Fiber

- `Canvas` creates isolated WebGL surfaces only inside marked visual regions.
- `useFrame` updates the liquid-logo time uniform and eases pointer position.
- `useThree` scales the logo plane to the available viewport.
- `useTexture` loads the approved transparent Varenz mark from the same origin.
- `shaderMaterial` defines the custom liquid-logo vertex and fragment shaders.
- `EffectComposer` and `Bloom` provide a restrained highlight on capable desktop devices only.

## Shader Gradient

- `ShaderGradientCanvas` and `ShaderGradient` create the hero and quotation ambience.
- The palette is limited to Varenz navy, medical blue, cyan, teal and clinical white.
- `waterPlane` motion is used as a low-contrast background, never behind small text at full strength.

## Liquid Logo

- A subdivided plane uses the approved transparent PNG as its alpha mask.
- The vertex shader combines a slow surface wave with a pointer-centred ripple.
- The fragment shader applies small UV refraction, teal sheen and edge light.
- Motion stops when reduced motion is requested.

## Liquid Glass and Neumorphism

- Navigation and the mega menu use translucent clinical-white surfaces, saturation blur, bright rims and deep navy shadows.
- Primary cards use paired light/dark shadows for raised surfaces.
- Active, open and pressed states switch to inset shadows.
- Deep sections use navy-on-navy relief to preserve the requested intensive neumorphic feel without reducing text contrast.

## Performance and Accessibility

- Effects are mounted with `IntersectionObserver` close to the viewport.
- `navigator.connection.saveData`, WebGL support, memory, CPU and viewport size determine whether WebGL and Bloom are enabled.
- Device pixel ratio is capped.
- Static CSS lighting replaces WebGL when unsupported.
- `prefers-reduced-motion` disables animation and reveal transitions.
- `prefers-reduced-transparency` removes backdrop blur.
- Effects have no pointer events and cannot block navigation or forms.

## Source and Build

- Source: `frontend-effects/src/main.jsx` in the project repository.
- Production output: `assets/js/varenz-effects.js`.
- Build command: `npm install && npm run build` from `frontend-effects`.

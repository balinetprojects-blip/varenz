import React, { Suspense, useMemo, useRef } from 'react';
import { createRoot } from 'react-dom/client';
import { Canvas, extend, useFrame, useThree } from '@react-three/fiber';
import { shaderMaterial, useTexture } from '@react-three/drei';
import { EffectComposer, Bloom } from '@react-three/postprocessing';
import { ShaderGradientCanvas, ShaderGradient } from '@shadergradient/react';
import * as THREE from 'three';

const liquidVertex = `
  uniform float uTime;
  uniform vec2 uPointer;
  uniform float uMotion;
  varying vec2 vUv;
  varying float vWave;

  void main() {
    vUv = uv;
    vec3 p = position;
    float distanceToPointer = distance(uv, uPointer);
    float ripple = sin((distanceToPointer * 18.0) - (uTime * 2.6)) * exp(-distanceToPointer * 4.2);
    float surface = sin((uv.x * 7.0) + (uTime * 0.8)) * cos((uv.y * 6.0) - (uTime * 0.65));
    vWave = (ripple * 0.7 + surface * 0.3) * uMotion;
    p.z += vWave * 0.18;
    gl_Position = projectionMatrix * modelViewMatrix * vec4(p, 1.0);
  }
`;

const liquidFragment = `
  uniform sampler2D uTexture;
  uniform float uTime;
  uniform vec2 uPointer;
  uniform float uMotion;
  varying vec2 vUv;
  varying float vWave;

  void main() {
    vec2 delta = vUv - uPointer;
    float lens = exp(-dot(delta, delta) * 14.0) * uMotion;
    vec2 wobble = vec2(
      sin((vUv.y * 9.0) + uTime) * 0.006,
      cos((vUv.x * 8.0) - uTime * 0.85) * 0.006
    ) * uMotion;
    vec4 mark = texture2D(uTexture, vUv + wobble + delta * lens * 0.035);
    float rim = smoothstep(.38, .02, abs(mark.a - .5));
    vec3 sheen = mix(mark.rgb, vec3(.42, .95, 1.0), max(0.0, vWave) * .75 + lens * .15);
    sheen += rim * vec3(.05, .16, .23) * .2;
    gl_FragColor = vec4(sheen, mark.a * .94);
  }
`;

const LiquidMaterial = shaderMaterial(
  {
    uTime: 0,
    uPointer: new THREE.Vector2(.5, .5),
    uMotion: 1,
    uTexture: null
  },
  liquidVertex,
  liquidFragment,
  (material) => {
    material.transparent = true;
    material.depthWrite = false;
    material.side = THREE.DoubleSide;
  }
);

extend({ LiquidMaterial });

function LiquidMark({ reducedMotion }) {
  const material = useRef();
  const texture = useTexture(`${window.VARENZ_APP?.baseUrl || '/'}assets/images/logo/varenz-icon-logo-clean.png`);
  const pointer = useMemo(() => new THREE.Vector2(.5, .5), []);
  const { viewport } = useThree();

  texture.colorSpace = THREE.SRGBColorSpace;

  useFrame(({ clock, pointer: normalizedPointer }, delta) => {
    if (!material.current) return;
    material.current.uTime = reducedMotion ? 0 : clock.elapsedTime;
    pointer.set(normalizedPointer.x * .5 + .5, normalizedPointer.y * .5 + .5);
    material.current.uPointer.lerp(pointer, Math.min(1, delta * 4));
  });

  return (
    <mesh scale={[Math.min(viewport.width * .78, 4.2), Math.min(viewport.width * .62, 3.35), 1]}>
      <planeGeometry args={[1, .79, 72, 72]} />
      <liquidMaterial ref={material} uTexture={texture} uMotion={reducedMotion ? 0 : 1} />
    </mesh>
  );
}

function LiquidLogo({ reducedMotion, enableBloom }) {
  return (
    <Canvas
      camera={{ position: [0, 0, 3.4], fov: 40 }}
      dpr={[1, 1.6]}
      gl={{ alpha: true, antialias: true, powerPreference: 'high-performance' }}
    >
      <Suspense fallback={null}>
        <ambientLight intensity={1.1} />
        <pointLight color="#45d5dc" intensity={6} position={[2, 2, 3]} />
        <LiquidMark reducedMotion={reducedMotion} />
        {enableBloom ? (
          <EffectComposer multisampling={0}>
            <Bloom intensity={.22} luminanceThreshold={.72} mipmapBlur />
          </EffectComposer>
        ) : null}
      </Suspense>
    </Canvas>
  );
}

function GradientScene({ variant, animate }) {
  const isQuotation = variant === 'quotation-gradient';
  return (
    <ShaderGradientCanvas
      pixelDensity={Math.min(window.devicePixelRatio || 1, 1.45)}
      pointerEvents="none"
      style={{ height: '100%', width: '100%' }}
    >
      <ShaderGradient
        animate={animate ? 'on' : 'off'}
        axesHelper="off"
        brightness={isQuotation ? 1.15 : 1.08}
        cAzimuthAngle={180}
        cDistance={3.6}
        cPolarAngle={110}
        cameraZoom={1}
        color1={isQuotation ? '#071b45' : '#f4f8fc'}
        color2={isQuotation ? '#153f93' : '#08aabd'}
        color3={isQuotation ? '#24c3c9' : '#d8e7f6'}
        envPreset="city"
        grain="off"
        lightType="3d"
        positionX={0}
        positionY={0}
        positionZ={0}
        range="enabled"
        rangeEnd={40}
        rangeStart={0}
        reflection={.12}
        rotationX={50}
        rotationY={0}
        rotationZ={isQuotation ? 18 : 42}
        type="waterPlane"
        uAmplitude={isQuotation ? 1.25 : .85}
        uDensity={1.2}
        uFrequency={isQuotation ? 4.8 : 3.6}
        uSpeed={animate ? .12 : 0}
        uStrength={isQuotation ? 2.3 : 1.55}
        wireframe={false}
      />
    </ShaderGradientCanvas>
  );
}

function supportsWebGL() {
  try {
    const canvas = document.createElement('canvas');
    return Boolean(window.WebGLRenderingContext && (canvas.getContext('webgl2') || canvas.getContext('webgl')));
  } catch (_) {
    return false;
  }
}

function capability() {
  const reducedMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches ?? false;
  const saveData = navigator.connection?.saveData === true;
  const lowMemory = typeof navigator.deviceMemory === 'number' && navigator.deviceMemory < 4;
  const smallCpu = typeof navigator.hardwareConcurrency === 'number' && navigator.hardwareConcurrency < 4;
  return {
    reducedMotion,
    allowWebGL: supportsWebGL() && !saveData,
    enableBloom: !reducedMotion && !lowMemory && !smallCpu && window.innerWidth >= 960
  };
}

function mountEffect(node, settings) {
  const type = node.dataset.vslEffect;
  const root = createRoot(node);

  if (type === 'liquid-logo') {
    root.render(<LiquidLogo reducedMotion={settings.reducedMotion} enableBloom={settings.enableBloom} />);
    return;
  }

  root.render(<GradientScene variant={type} animate={!settings.reducedMotion && document.visibilityState === 'visible'} />);
}

function boot() {
  const nodes = Array.from(document.querySelectorAll('[data-vsl-effect]'));
  if (!nodes.length) return;

  const settings = capability();
  if (!settings.allowWebGL) {
    nodes.forEach((node) => node.classList.add('vsl-effect-fallback'));
    return;
  }

  if (!('IntersectionObserver' in window)) {
    nodes.forEach((node) => mountEffect(node, settings));
    return;
  }

  const mounted = new WeakSet();
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting || mounted.has(entry.target)) return;
      mounted.add(entry.target);
      mountEffect(entry.target, settings);
      observer.unobserve(entry.target);
    });
  }, { rootMargin: '240px 0px', threshold: .01 });

  nodes.forEach((node) => observer.observe(node));
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
  boot();
}

import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'node:path';

export default defineConfig({
  plugins: [react()],
  build: {
    emptyOutDir: false,
    lib: {
      entry: resolve(import.meta.dirname, 'src/main.jsx'),
      name: 'VarenzEffects',
      formats: ['iife'],
      fileName: () => 'varenz-effects.js'
    },
    outDir: resolve(import.meta.dirname, '../public_html/assets/js'),
    rollupOptions: {
      output: {
        assetFileNames: 'varenz-effects-[name][extname]'
      }
    }
  }
});

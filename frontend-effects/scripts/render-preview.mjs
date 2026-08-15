import { mkdirSync, writeFileSync } from 'node:fs';
import { resolve } from 'node:path';
import { PHP } from '@php-wasm/universal';
import { createNodeFsMountHandler, loadNodeRuntime } from '@php-wasm/node';

const publicRoot = resolve(import.meta.dirname, '../../public_html');
const outputRoot = resolve(import.meta.dirname, '../../preview');
const routes = ['/', '/about', '/products', '/procurement', '/quality-compliance', '/partners', '/resources', '/faq', '/contact'];

mkdirSync(outputRoot, { recursive: true });

for (const [index, route] of routes.entries()) {
  const php = new PHP(await loadNodeRuntime('8.4', { emscriptenOptions: { processId: 7000 + index } }));
  await php.mount('/varenz', createNodeFsMountHandler(publicRoot));
  const code = `<?php
chdir('/varenz');
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = ${JSON.stringify(route)};
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = '/varenz/index.php';
$_SERVER['DOCUMENT_ROOT'] = '/varenz';
$_SERVER['HTTP_HOST'] = '127.0.0.1:4173';
$_SERVER['SERVER_NAME'] = '127.0.0.1';
$_SERVER['SERVER_PORT'] = '4173';
$_SERVER['HTTPS'] = 'off';
include '/varenz/index.php';
`;
  const result = await php.runStream({ code });
  const html = await result.stdoutText;
  const error = await result.stderrText;

  if (error.trim()) console.error(route, error);
  if (!html.includes('<!DOCTYPE html>') && !html.includes('<!doctype html>')) {
    throw new Error(`Route ${route} did not render an HTML document.`);
  }

  const name = route === '/' ? 'index' : route.slice(1).replaceAll('/', '-');
  writeFileSync(resolve(outputRoot, `${name}.html`), html);
}

console.log(`Rendered ${routes.length} PHP routes to ${outputRoot}`);

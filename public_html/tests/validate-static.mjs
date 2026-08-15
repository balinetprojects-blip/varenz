import test from 'node:test';
import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const root = resolve(import.meta.dirname, '..');
const read = path => readFileSync(resolve(root, path), 'utf8');

const productSlugs = [
  'enfit-syringes',
  'two-way-nasogastric-tubes',
  'photoprotective-giving-sets',
  'coronary-stents',
  'pacemakers',
  'urological-irrigation-sets',
  'ct-contrast-media',
];

test('catalogue routes and controllers are registered', () => {
  const index = read('index.php');
  assert.match(index, /'\/products'/);
  assert.match(index, /'\/products\/\{slug\}'/);
  assert.match(index, /'\/api\/products'/);
  assert.ok(existsSync(resolve(root, 'app/Controllers/ProductController.php')));
});

test('all mega-menu pages are registered and have a shared shell', () => {
  const index = read('index.php');
  const header = read('app/Views/shared/site-header.php');
  const pages = ['about', 'procurement', 'quality-compliance', 'partners', 'resources', 'faq', 'contact'];

  for (const page of pages) {
    assert.ok(index.includes(`'/${page}'`), `missing route /${page}`);
    assert.ok(header.includes(`url('/${page}')`), `missing mega-menu destination /${page}`);
  }

  assert.ok(existsSync(resolve(root, 'app/Controllers/PageController.php')));
  assert.ok(existsSync(resolve(root, 'app/Views/pages/show.php')));
});

test('canonical products, images and sitemap entries are complete', () => {
  const data = read('data/site.php');
  const sitemap = read('sitemap.xml');

  for (const slug of productSlugs) {
    assert.ok(data.includes(`'slug' => '${slug}'`), `missing product ${slug}`);
    assert.ok(sitemap.includes(`/products/${slug}`), `missing sitemap entry ${slug}`);
  }

  const productBlock = data.slice(data.indexOf("'products' => ["), data.indexOf("'featured' => ["));
  const images = [...productBlock.matchAll(/'image'\s*=>\s*'([^']+)'/g)].map(match => match[1]);
  assert.equal(images.length, productSlugs.length);
  for (const image of images) assert.ok(existsSync(resolve(root, 'assets', image)), `missing ${image}`);
});

test('all data-file images and downloads resolve locally', () => {
  const data = read('data/site.php');
  const paths = [...data.matchAll(/'(images|downloads)\/([^']+)'/g)].map(match => `${match[1]}/${match[2]}`);
  assert.ok(paths.length > 20, 'expected a populated local asset catalogue');
  for (const path of paths) assert.ok(existsSync(resolve(root, 'assets', path)), `missing assets/${path}`);
});

test('new stylesheet braces are balanced', () => {
  for (const file of ['assets/css/recovery-2026.css', 'assets/css/products.css', 'assets/css/pages.css', 'assets/css/effects.css']) {
    const css = read(file).replace(/\/\*[\s\S]*?\*\//g, '');
    let depth = 0;
    for (const char of css) {
      if (char === '{') depth += 1;
      if (char === '}') depth -= 1;
      assert.ok(depth >= 0, `${file} closes a block too early`);
    }
    assert.equal(depth, 0, `${file} has unclosed blocks`);
  }
});

test('progressive visual effects are packaged without becoming a form dependency', () => {
  const bundle = resolve(root, 'assets/js/varenz-effects.js');
  const controller = read('app/Controllers/HomeController.php');
  assert.ok(existsSync(bundle), 'missing built visual-effects bundle');
  assert.match(controller, /js\/varenz-effects\.js/);
  assert.match(read('EFFECTS-IMPLEMENTATION.md'), /prefers-reduced-motion/);
  assert.doesNotMatch(read('assets/js/pages.js'), /api\/submissions/);
});

test('known broken public destinations are absent', () => {
  const header = read('assets/js/header-island.js');
  const homepage = read('app/Views/home/index.php');
  assert.doesNotMatch(header, /Leadership:\s*\n\s*'#team'/);
  assert.doesNotMatch(header, /'Our Team':\s*\n\s*'#team'/);
  assert.match(homepage, /vslActionHubClose/);
  assert.match(homepage, /url\('\/products'\)/);
});

test('request-list handoff uses query before fragment', () => {
  const files = [
    'app/Views/products/_header.php',
    'app/Views/products/_footer.php',
    'app/Views/products/index.php',
    'app/Views/products/show.php',
  ];
  for (const file of files) assert.doesNotMatch(read(file), /#cta[^"']*\?intent=/, file);
});

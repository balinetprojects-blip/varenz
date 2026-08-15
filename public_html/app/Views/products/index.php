<?php
declare(strict_types=1);

$products = isset($products) && is_array($products) ? $products : [];
$categories = isset($categories) && is_array($categories) ? $categories : [];
$activeTerm = trim((string) ($activeTerm ?? ''));
$activeCategory = trim((string) ($activeCategory ?? ''));

require __DIR__ . '/_header.php';
?>
<main class="vsl-catalog-main" id="mainContent">
    <section class="vsl-catalog-hero">
        <div class="vsl-effect-layer" data-vsl-effect="hero-gradient" aria-hidden="true"></div>
        <div>
            <span class="vsl-catalog-eyebrow">VARENZ PRODUCT CATALOGUE</span>
            <h1>Selected medical supply solutions, clearly organised.</h1>
            <p>Search the approved product portfolio, review support information and add requirements to one secure procurement request.</p>
            <div class="vsl-catalog-hero__trust">
                <span>Quality-focused sourcing</span>
                <span>Documentation support</span>
                <span>Responsive coordination</span>
            </div>
        </div>
        <div class="vsl-catalog-hero__visual">
            <img src="<?= e(asset('images/hero/final/scene-01-overview.webp')) ?>" alt="Varenz medical supply and logistics support" width="1200" height="760" fetchpriority="high">
            <div class="vsl-liquid-logo" data-vsl-effect="liquid-logo" aria-hidden="true"></div>
        </div>
    </section>

    <section class="vsl-catalog-tools" aria-label="Product catalogue tools">
        <form method="get" action="<?= e(url('/products')) ?>" data-product-filter-form>
            <label>
                <span>Search products</span>
                <input type="search" name="q" value="<?= e($activeTerm) ?>" placeholder="Search by product, category or support need" autocomplete="off" data-product-search>
            </label>
            <label>
                <span>Category</span>
                <select name="category" data-product-category>
                    <option value="">All categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= e((string) $category) ?>"<?= strcasecmp((string) $category, $activeCategory) === 0 ? ' selected' : '' ?>><?= e((string) $category) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit">Apply filters</button>
        </form>
        <p aria-live="polite"><strong data-product-count><?= count($products) ?></strong> approved products shown</p>
    </section>

    <section class="vsl-product-grid" data-product-grid aria-busy="false">
        <?php foreach ($products as $product): ?>
            <?php $tags = isset($product['tags']) && is_array($product['tags']) ? $product['tags'] : []; ?>
            <article class="vsl-product-card" data-product-card data-product-slug="<?= e((string) ($product['slug'] ?? '')) ?>">
                <a class="vsl-product-card__media" href="<?= e(url('/products/' . rawurlencode((string) ($product['slug'] ?? '')))) ?>">
                    <img src="<?= e(asset((string) ($product['image'] ?? ''))) ?>" alt="<?= e((string) ($product['title'] ?? 'Medical product')) ?>" width="720" height="520" loading="lazy" decoding="async">
                </a>
                <div class="vsl-product-card__body">
                    <span><?= e((string) ($product['category'] ?? 'Medical supplies')) ?></span>
                    <h2><?= e((string) ($product['title'] ?? 'Medical product')) ?></h2>
                    <p><?= e((string) ($product['short_description'] ?? '')) ?></p>
                    <div class="vsl-product-tags">
                        <?php foreach ($tags as $tag): ?><span><?= e((string) $tag) ?></span><?php endforeach; ?>
                    </div>
                    <div class="vsl-product-card__actions">
                        <a href="<?= e(url('/products/' . rawurlencode((string) ($product['slug'] ?? '')))) ?>">View Product</a>
                        <button type="button" data-add-product data-slug="<?= e((string) ($product['slug'] ?? '')) ?>" data-title="<?= e((string) ($product['title'] ?? '')) ?>">Add to Request</button>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <div class="vsl-product-empty" data-product-empty<?= $products !== [] ? ' hidden' : '' ?>>
        <h2>No matching products</h2>
        <p>Clear the filters or send Varenz the product reference you already have.</p>
        <a href="<?= e(url('/')) ?>?intent=quotation#cta">Request sourcing support</a>
    </div>
</main>
<?php require __DIR__ . '/_footer.php'; ?>

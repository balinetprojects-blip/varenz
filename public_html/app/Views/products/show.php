<?php
declare(strict_types=1);

$product = isset($product) && is_array($product) ? $product : [];
$relatedProducts = isset($relatedProducts) && is_array($relatedProducts) ? $relatedProducts : [];
$tags = isset($product['tags']) && is_array($product['tags']) ? $product['tags'] : [];
$slug = (string) ($product['slug'] ?? '');

require __DIR__ . '/_header.php';
?>
<main class="vsl-catalog-main" id="mainContent">
    <nav class="vsl-breadcrumb" aria-label="Breadcrumb">
        <a href="<?= e(url('/')) ?>">Home</a><span>›</span>
        <a href="<?= e(url('/products')) ?>">Products</a><span>›</span>
        <span aria-current="page"><?= e((string) ($product['title'] ?? 'Product')) ?></span>
    </nav>

    <article class="vsl-product-detail">
        <div class="vsl-product-detail__media">
            <img src="<?= e(asset((string) ($product['image'] ?? ''))) ?>" alt="<?= e((string) ($product['title'] ?? 'Medical product')) ?>" width="900" height="760" fetchpriority="high">
        </div>
        <div class="vsl-product-detail__copy">
            <span class="vsl-catalog-eyebrow"><?= e((string) ($product['category'] ?? 'MEDICAL SUPPLIES')) ?></span>
            <h1><?= e((string) ($product['title'] ?? 'Medical product')) ?></h1>
            <p class="vsl-product-detail__lead"><?= e((string) ($product['description'] ?? $product['short_description'] ?? '')) ?></p>
            <div class="vsl-product-tags"><?php foreach ($tags as $tag): ?><span><?= e((string) $tag) ?></span><?php endforeach; ?></div>
            <div class="vsl-product-note">
                <strong>Specification clarification</strong>
                <p><?= e((string) ($product['enquiry_notes'] ?? 'Provide the product reference, quantity and required-by date available.')) ?></p>
            </div>
            <label class="vsl-quantity-field">Requested quantity <input type="number" min="1" max="99999" value="1" inputmode="numeric" data-product-quantity></label>
            <div class="vsl-product-detail__actions">
                <button class="vsl-catalog-primary" type="button" data-add-product data-slug="<?= e($slug) ?>" data-title="<?= e((string) ($product['title'] ?? '')) ?>">Add to Request List</button>
                <a href="<?= e(url('/')) ?>?intent=quotation&amp;product=<?= e(rawurlencode($slug)) ?>#cta">Request a Quote</a>
            </div>
            <p class="vsl-availability-note">Availability, final specifications and supporting documentation are confirmed during enquiry.</p>
        </div>
    </article>

    <?php if ($relatedProducts !== []): ?>
        <section class="vsl-related-products">
            <span class="vsl-catalog-eyebrow">RELATED PRODUCTS</span>
            <h2>Continue exploring</h2>
            <div>
                <?php foreach ($relatedProducts as $related): ?>
                    <a href="<?= e(url('/products/' . rawurlencode((string) ($related['slug'] ?? '')))) ?>">
                        <img src="<?= e(asset((string) ($related['image'] ?? ''))) ?>" alt="" width="480" height="320" loading="lazy">
                        <strong><?= e((string) ($related['title'] ?? 'Medical product')) ?></strong>
                        <span><?= e((string) ($related['short_description'] ?? '')) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/_footer.php'; ?>

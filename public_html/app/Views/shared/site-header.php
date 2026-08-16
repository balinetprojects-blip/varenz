<?php
declare(strict_types=1);

$currentPage = trim((string) ($pageKey ?? ''));
$isCurrent = static fn (string $key): string => $currentPage === $key ? ' aria-current="page" class="is-current"' : '';
?>
<header class="vsl-page-header" data-page-header>
    <a class="vsl-page-brand" href="<?= e(url('/')) ?>" aria-label="Varenz Supplies Ltd home">
        <img src="<?= e(asset('images/logo/varenz-word-logo-clean.png')) ?>" alt="Varenz Supplies Ltd" width="320" height="96">
    </a>

    <nav class="vsl-page-nav" aria-label="Primary navigation">
        <a href="<?= e(url('/')) ?>">Home</a>
        <button type="button" data-page-mega-trigger aria-expanded="false" aria-haspopup="true" aria-controls="vslPageMega">Explore</button>
        <a href="<?= e(url('/products')) ?>"<?= $isCurrent('products') ?>>Products</a>
        <a href="<?= e(url('/contact')) ?>"<?= $isCurrent('contact') ?>>Contact</a>
    </nav>

    <div class="vsl-page-header__actions">
        <button type="button" class="vsl-page-menu-button" data-page-menu-trigger aria-expanded="false" aria-controls="vslPageDrawer">Menu</button>
        <a class="vsl-page-primary" href="<?= e(url('/')) ?>?intent=quotation#cta">Request a Quote</a>
    </div>

    <section class="vsl-page-mega" id="vslPageMega" data-page-mega hidden aria-label="Varenz website sections">
        <div class="vsl-page-mega__intro">
            <span>VARENZ NETWORK</span>
            <h2>Navigate the complete supply experience.</h2>
            <p>Move from company information and product discovery to procurement guidance, documents and direct support.</p>
        </div>
        <div class="vsl-page-mega__links">
            <a href="<?= e(url('/about')) ?>"<?= $isCurrent('about') ?>><strong>About Varenz</strong><span>Company, capabilities and organisations served</span></a>
            <a href="<?= e(url('/procurement')) ?>"<?= $isCurrent('procurement') ?>><strong>Procurement</strong><span>Requirement review through delivery</span></a>
            <a href="<?= e(url('/products')) ?>"><strong>Products</strong><span>Search the selected product portfolio</span></a>
            <a href="<?= e(url('/quality-compliance')) ?>"<?= $isCurrent('quality-compliance') ?>><strong>Quality &amp; Compliance</strong><span>Documentation-led supply support</span></a>
            <a href="<?= e(url('/partners')) ?>"<?= $isCurrent('partners') ?>><strong>Partners</strong><span>Institutions and organisations supported</span></a>
            <a href="<?= e(url('/resources')) ?>"<?= $isCurrent('resources') ?>><strong>Resources</strong><span>Profiles, presentations and brochures</span></a>
            <a href="<?= e(url('/faq')) ?>"<?= $isCurrent('faq') ?>><strong>FAQs</strong><span>Fast answers about enquiries and orders</span></a>
            <a href="<?= e(url('/contact')) ?>"<?= $isCurrent('contact') ?>><strong>Contact</strong><span>Request, call, email or WhatsApp</span></a>
        </div>
    </section>
</header>

<button class="vsl-page-drawer-scrim" type="button" data-page-menu-scrim hidden aria-label="Close navigation"></button>

<aside class="vsl-page-drawer" id="vslPageDrawer" data-page-drawer hidden aria-label="Mobile navigation">
    <button type="button" data-page-menu-close aria-label="Close navigation">Close</button>
    <a href="<?= e(url('/')) ?>">Home</a>
    <a href="<?= e(url('/about')) ?>">About Varenz</a>
    <a href="<?= e(url('/products')) ?>">Products</a>
    <a href="<?= e(url('/procurement')) ?>">Procurement</a>
    <a href="<?= e(url('/quality-compliance')) ?>">Quality &amp; Compliance</a>
    <a href="<?= e(url('/partners')) ?>">Partners</a>
    <a href="<?= e(url('/resources')) ?>">Resources</a>
    <a href="<?= e(url('/faq')) ?>">FAQs</a>
    <a href="<?= e(url('/contact')) ?>">Contact</a>
    <a class="vsl-page-primary" href="<?= e(url('/')) ?>?intent=quotation#cta">Request a Quote</a>
</aside>

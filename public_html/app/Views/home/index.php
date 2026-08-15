<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Varenz Supplies Ltd — Homepage
|--------------------------------------------------------------------------
|
| Progressive enhancement policy:
|
| 1. Critical commercial content is rendered by PHP first.
| 2. JavaScript enhances/replaces dynamic components after load.
| 3. A JS failure must not leave products, organisations or cards empty.
| 4. Missing optional images fall back gracefully.
| 5. Existing IDs/classes/data hooks remain compatible with app.js and
|    header-island.js.
|
*/

$contact = is_array(config('contact'))
    ? config('contact')
    : [];

$site = isset($site) && is_array($site)
    ? $site
    : [];

$heroSlides = isset($site['hero']) && is_array($site['hero'])
    ? $site['hero']
    : [];

$challenges = isset($site['challenges']) && is_array($site['challenges'])
    ? $site['challenges']
    : [];

$categories = isset($site['categories']) && is_array($site['categories'])
    ? $site['categories']
    : [];

$featured = isset($site['featured']) && is_array($site['featured'])
    ? $site['featured']
    : [];

$products = isset($site['products']) && is_array($site['products'])
    ? $site['products']
    : [];

$spotlightSlug = trim((string) ($site['spotlight_product_slug'] ?? ''));
$spotlightProduct = null;

foreach ($products as $candidateProduct) {
    if (
        is_array($candidateProduct)
        && (string) ($candidateProduct['slug'] ?? '') === $spotlightSlug
    ) {
        $spotlightProduct = $candidateProduct;
        break;
    }
}

if ($spotlightProduct === null && isset($products[0]) && is_array($products[0])) {
    $spotlightProduct = $products[0];
}

$procurement = isset($site['procurement']) && is_array($site['procurement'])
    ? $site['procurement']
    : [];

$organizations = isset($site['organizations']) && is_array($site['organizations'])
    ? $site['organizations']
    : [];

$whyItems = isset($site['why']) && is_array($site['why'])
    ? $site['why']
    : [];

$partners = isset($site['partners']) && is_array($site['partners'])
    ? $site['partners']
    : [];

$faqs = isset($site['faqs']) && is_array($site['faqs'])
    ? $site['faqs']
    : [];

$heroSlideCount = count($heroSlides);

$phonePrimary = trim((string) ($contact['phone_primary'] ?? ''));
$phoneSecondary = trim((string) ($contact['phone_secondary'] ?? ''));
$contactEmail = trim((string) ($contact['email'] ?? ''));
$contactLocation = trim((string) ($contact['location'] ?? ''));
$contactHours = trim((string) ($contact['hours'] ?? ''));

/*
|--------------------------------------------------------------------------
| Local Helpers
|--------------------------------------------------------------------------
*/

$phoneHref = static function (string $phone): string {
    $digits = preg_replace('/\D+/', '', $phone) ?? '';

    if ($digits === '') {
        return '';
    }

    if (str_starts_with($digits, '0')) {
        return '+256' . substr($digits, 1);
    }

    if (str_starts_with($digits, '256')) {
        return '+' . $digits;
    }

    return '+' . $digits;
};

$imageAsset = static function (
    string $relative,
    string $fallback = 'images/logo/varenz-icon-logo-clean.png'
): string {
    $relative = ltrim(trim($relative), '/');

    if ($relative === '') {
        return asset($fallback);
    }

    $candidate = ROOT_PATH . '/assets/' . $relative;

    if (is_file($candidate)) {
        return asset($relative);
    }

    return asset($fallback);
};

$phonePrimaryHref = $phoneHref($phonePrimary);
$phoneSecondaryHref = $phoneHref($phoneSecondary);

$whatsAppDigits = preg_replace('/\D+/', '', $phonePrimaryHref) ?? '';
$whatsAppHref = $whatsAppDigits !== ''
    ? 'https://wa.me/' . $whatsAppDigits . '?text=' . rawurlencode(
        'Hello Varenz Supplies Ltd. I would like help with a medical supply requirement.'
    )
    : '';

$initialChallenge = isset($challenges[0]) && is_array($challenges[0])
    ? $challenges[0]
    : [];

$initialCategory = isset($categories[0]) && is_array($categories[0])
    ? $categories[0]
    : [];

$initialProcurement = isset($procurement[0]) && is_array($procurement[0])
    ? $procurement[0]
    : [];

$initialOrganization = isset($organizations[0]) && is_array($organizations[0])
    ? $organizations[0]
    : [];

$initialWhy = isset($whyItems[0]) && is_array($whyItems[0])
    ? $whyItems[0]
    : [];

/*
|--------------------------------------------------------------------------
| SVG Icon Sprite
|--------------------------------------------------------------------------
*/
?>

<svg
    width="0"
    height="0"
    style="position:absolute;width:0;height:0;overflow:hidden"
    aria-hidden="true"
    focusable="false"
>
    <symbol id="i-phone" viewBox="0 0 24 24">
        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.78 19.78 0 0 1-8.63-3.07 19.49 19.49 0 0 1-6-6A19.78 19.78 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.34 1.78.66 2.62a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.46-1.23a2 2 0 0 1 2.11-.45c.84.32 1.72.54 2.62.66A2 2 0 0 1 22 16.92z"></path>
    </symbol>

    <symbol id="i-mail" viewBox="0 0 24 24">
        <path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"></path>
        <path d="m22 6-10 7L2 6"></path>
    </symbol>

    <symbol id="i-map" viewBox="0 0 24 24">
        <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"></path>
        <circle cx="12" cy="10" r="3"></circle>
    </symbol>

    <symbol id="i-search" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"></circle>
        <path d="m21 21-4.35-4.35"></path>
    </symbol>

    <symbol id="i-menu" viewBox="0 0 24 24">
        <path d="M3 12h18M3 6h18M3 18h18"></path>
    </symbol>

    <symbol id="i-arrow-right" viewBox="0 0 24 24">
        <path d="M5 12h14"></path>
        <path d="m12 5 7 7-7 7"></path>
    </symbol>

    <symbol id="i-arrow-left" viewBox="0 0 24 24">
        <path d="M19 12H5"></path>
        <path d="m12 19-7-7 7-7"></path>
    </symbol>

    <symbol id="i-chevron-down" viewBox="0 0 24 24">
        <path d="m6 9 6 6 6-6"></path>
    </symbol>

    <symbol id="i-chevron" viewBox="0 0 24 24">
        <path d="m7 9 5 5 5-5"></path>
    </symbol>

    <symbol id="i-upload" viewBox="0 0 24 24">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
        <path d="M7 10l5-5 5 5"></path>
        <path d="M12 15V5"></path>
    </symbol>

    <symbol id="i-message" viewBox="0 0 24 24">
        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8A8.5 8.5 0 0 1 12.5 20a8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 17 0z"></path>
    </symbol>

    <symbol id="i-shield" viewBox="0 0 24 24">
        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
    </symbol>

    <symbol id="i-file" viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
        <path d="M14 2v6h6"></path>
        <path d="M16 13H8M16 17H8M10 9H8"></path>
    </symbol>

    <symbol id="i-play" viewBox="0 0 24 24">
        <polygon
            points="5 3 19 12 5 21 5 3"
            fill="currentColor"
            stroke="none"
        ></polygon>
    </symbol>

    <symbol id="i-check" viewBox="0 0 24 24">
        <path d="m20 6-11 11-5-5"></path>
    </symbol>

    <symbol id="i-users" viewBox="0 0 24 24">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
    </symbol>

    <symbol id="i-building" viewBox="0 0 24 24">
        <path d="M3 21h18"></path>
        <path d="M5 21V7l7-4 7 4v14"></path>
        <path d="M9 9h.01M9 12h.01M9 15h.01M12 9h.01M12 12h.01M12 15h.01M15 9h.01M15 12h.01M15 15h.01"></path>
    </symbol>

    <symbol id="i-help" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"></circle>
        <path d="M9.09 9a3 3 0 1 1 5.83 1c0 2-3 3-3 3"></path>
        <path d="M12 17h.01"></path>
    </symbol>

    <symbol id="i-feedback" viewBox="0 0 24 24">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        <path d="M8 9h8M8 13h5"></path>
    </symbol>

    <symbol id="i-grid" viewBox="0 0 24 24">
        <rect x="3" y="3" width="7" height="7" rx="1"></rect>
        <rect x="14" y="3" width="7" height="7" rx="1"></rect>
        <rect x="14" y="14" width="7" height="7" rx="1"></rect>
        <rect x="3" y="14" width="7" height="7" rx="1"></rect>
    </symbol>

    <symbol id="i-clock" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"></circle>
        <path d="M12 6v6l4 2"></path>
    </symbol>

    <symbol id="i-link" viewBox="0 0 24 24">
        <path d="M10 13a5 5 0 0 0 7.54.54l2.92-2.92a5 5 0 0 0-7.07-7.07L11.72 5.2"></path>
        <path d="M14 11a5 5 0 0 0-7.54-.54L3.54 13.38a5 5 0 0 0 7.07 7.07l1.67-1.67"></path>
    </symbol>

    <symbol id="i-x-social" viewBox="0 0 24 24">
        <path d="M4 4l16 16M20 4 4 20"></path>
    </symbol>

    <symbol id="i-instagram" viewBox="0 0 24 24">
        <rect x="3" y="3" width="18" height="18" rx="5"></rect>
        <circle cx="12" cy="12" r="4"></circle>
        <path d="M17.5 6.5h.01"></path>
    </symbol>

    <symbol id="i-facebook" viewBox="0 0 24 24">
        <path d="M14 8h3V4h-3c-3 0-5 2-5 5v3H6v4h3v6h4v-6h3.5l.5-4h-4V9c0-.7.3-1 1-1z"></path>
    </symbol>

    <symbol id="i-arrow" viewBox="0 0 24 24">
        <path d="M5 12h14M14 7l5 5-5 5"></path>
    </symbol>

    <symbol id="i-x" viewBox="0 0 24 24">
        <path d="M6 6l12 12M18 6 6 18"></path>
    </symbol>

    <symbol id="i-box" viewBox="0 0 24 24">
        <path d="m12 3 9 5-9 5-9-5 9-5zM3 8v9l9 5 9-5V8M12 13v9"></path>
    </symbol>

    <symbol id="i-heart" viewBox="0 0 24 24">
        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8z"></path>
    </symbol>

    <symbol id="i-star" viewBox="0 0 24 24">
        <path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14 2 9.27l6.91-1.01L12 2z"></path>
    </symbol>

    <symbol id="i-briefcase" viewBox="0 0 24 24">
        <rect x="3" y="7" width="18" height="13" rx="2"></rect>
        <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
        <path d="M3 12h18"></path>
    </symbol>
</svg>

<header
    class="site-header"
    id="siteHeader"
>
    <div class="utility-bar">
        <div class="utility-inner">
            <div class="slogan">
                <span class="slogan-dot" aria-hidden="true"></span>
                <span>Supply Support. Better Care.</span>
            </div>

            <div
                class="utility-bubbles"
                aria-label="Company contact shortcuts"
            >
                <?php if ($phonePrimary !== '' && $phonePrimaryHref !== ''): ?>
                    <a
                        class="utility-bubble bubble"
                        href="tel:<?= e($phonePrimaryHref) ?>"
                        aria-label="Call Varenz on <?= e($phonePrimary) ?>"
                    >
                        <svg class="icon" aria-hidden="true">
                            <use href="#i-phone"></use>
                        </svg>

                        <span><?= e($phonePrimary) ?></span>
                    </a>
                <?php endif; ?>

                <?php if ($phoneSecondary !== '' && $phoneSecondaryHref !== ''): ?>
                    <a
                        class="utility-bubble bubble"
                        href="tel:<?= e($phoneSecondaryHref) ?>"
                        aria-label="Call Varenz on <?= e($phoneSecondary) ?>"
                    >
                        <svg class="icon" aria-hidden="true">
                            <use href="#i-phone"></use>
                        </svg>

                        <span><?= e($phoneSecondary) ?></span>
                    </a>
                <?php endif; ?>

                <?php if ($contactEmail !== ''): ?>
                    <a
                        class="utility-bubble bubble"
                        href="mailto:<?= e($contactEmail) ?>"
                    >
                        <svg class="icon" aria-hidden="true">
                            <use href="#i-mail"></use>
                        </svg>

                        <span><?= e($contactEmail) ?></span>
                    </a>
                <?php endif; ?>

                <?php if ($whatsAppHref !== ''): ?>
                    <a
                        class="utility-bubble bubble whatsapp-bubble"
                        href="<?= e($whatsAppHref) ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Message Varenz Supplies Ltd on WhatsApp"
                    >
                        <svg class="icon" aria-hidden="true">
                            <use href="#i-message"></use>
                        </svg>

                        <span>WhatsApp</span>
                    </a>
                <?php endif; ?>

                <?php if ($contactLocation !== ''): ?>
                    <a
                        class="utility-bubble location-bubble bubble"
                        href="#location"
                    >
                        <svg class="icon" aria-hidden="true">
                            <use href="#i-map"></use>
                        </svg>

                        <span><?= e($contactLocation) ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="header-stage">
        <div
            class="header-island"
            id="headerIsland"
        >
            <a
                class="brand-link"
                href="<?= e(url('/')) ?>"
                aria-label="Varenz Supplies Ltd home"
            >
                <img
                    class="brand-logo"
                    src="<?= e($imageAsset('images/logo/varenz-word-logo-clean.png')) ?>"
                    alt="Varenz Supplies Ltd"
                    width="320"
                    height="96"
                    decoding="async"
                >
            </a>

            <nav
                class="desktop-navigation"
                aria-label="Primary navigation"
            >
                <div class="nav-bubbles">
                    <a
                        class="nav-bubble bubble is-active"
                        href="#hero"
                        aria-current="page"
                        aria-label="Home"
                    >
                        <span class="type-shell">
                            <span class="type-measure">Home</span>
                            <span class="typed" data-type="Home">Home</span>
                        </span>
                    </a>

                    <?php
                    $navItems = [
                        ['About Us', 'about'],
                        ['Solutions', 'solutions'],
                        ['Products', 'products'],
                        ['Resources', 'resources'],
                        ['Contact', 'contact'],
                    ];
                    ?>

                    <?php foreach ($navItems as [$label, $menu]): ?>
                        <button
                            class="nav-bubble bubble mega-trigger"
                            type="button"
                            data-menu="<?= e($menu) ?>"
                            aria-label="<?= e($label) ?>"
                            aria-expanded="false"
                            aria-controls="megaPanel"
                        >
                            <span class="type-shell">
                                <span class="type-measure">
                                    <?= e($label) ?>
                                </span>

                                <span
                                    class="typed"
                                    data-type="<?= e($label) ?>"
                                >
                                    <?= e($label) ?>
                                </span>
                            </span>

                            <svg class="icon nav-chevron" aria-hidden="true">
                                <use href="#i-chevron"></use>
                            </svg>
                        </button>
                    <?php endforeach; ?>
                </div>
            </nav>

            <div class="header-actions">
                <button
                    class="action-bubble search-bubble bubble"
                    id="openSearch"
                    type="button"
                    aria-label="Search website"
                    aria-haspopup="dialog"
                    aria-controls="searchOverlay"
                    aria-expanded="false"
                >
                    <span class="type-shell">
                        <span class="type-measure">Search</span>
                        <span class="typed" data-type="Search">Search</span>
                    </span>

                    <svg class="icon" aria-hidden="true">
                        <use href="#i-search"></use>
                    </svg>
                </button>

                <button
                    class="action-bubble circle-bubble theme-bubble bubble"
                    id="themeToggle"
                    type="button"
                    aria-label="Toggle color theme"
                    aria-pressed="false"
                >
                    <span
                        class="theme-moon"
                        aria-hidden="true"
                    ></span>
                </button>

                <a
                    class="action-bubble support-bubble bubble"
                    href="#cta"
                    aria-label="Request a Quote"
                >
                    <svg class="icon support-icon" aria-hidden="true">
                        <use href="#i-message"></use>
                    </svg>

                    <span class="support-desktop type-shell">
                        <span class="type-measure">
                            Request a Quote
                        </span>

                        <span
                            class="typed"
                            data-type="Request a Quote"
                        >
                            Request a Quote
                        </span>
                    </span>

                    <span
                        class="support-mobile mobile-support-label"
                        aria-hidden="true"
                    >
                        <span class="mobile-support-static">
                            Support
                        </span>

                        <span
                            class="mobile-support-typed typed"
                            data-type="Support"
                        >
                            Support
                        </span>
                    </span>

                    <span
                        class="support-arrow"
                        aria-hidden="true"
                    >
                        <svg class="icon">
                            <use href="#i-arrow"></use>
                        </svg>
                    </span>
                </a>

                <button
                    class="action-bubble menu-bubble bubble"
                    id="openVhiDrawer"
                    type="button"
                    aria-label="Open navigation menu"
                    aria-expanded="false"
                    aria-controls="mobileDrawer"
                >
                    <svg class="icon" aria-hidden="true">
                        <use href="#i-menu"></use>
                    </svg>
                </button>
            </div>

            <div
                class="mega-panel"
                id="megaPanel"
                aria-hidden="true"
            >
                <div class="mega-header">
                    <div>
                        <div
                            class="mega-kicker"
                            id="megaKicker"
                        >
                            Explore Varenz
                        </div>

                        <h2
                            class="mega-title"
                            id="megaTitle"
                        >
                            Menu
                        </h2>
                    </div>

                    <p
                        class="mega-description"
                        id="megaDescription"
                    ></p>
                </div>

                <div
                    class="mega-grid"
                    id="megaGrid"
                ></div>
            </div>
        </div>
    </div>
</header>

<div
    class="mega-scrim"
    id="megaScrim"
    aria-hidden="true"
></div>

<div
    class="mobile-drawer"
    id="mobileDrawer"
    aria-hidden="true"
>
    <div
        class="drawer-overlay"
        id="drawerOverlay"
        aria-hidden="true"
    ></div>

    <aside
        class="drawer-panel"
        role="dialog"
        aria-modal="true"
        aria-label="Mobile navigation"
    >
        <div class="drawer-head">
            <img
                class="drawer-logo"
                src="<?= e($imageAsset('images/logo/varenz-word-logo-clean.png')) ?>"
                alt="Varenz Supplies Ltd"
                width="320"
                height="96"
                decoding="async"
            >

            <button
                class="drawer-close"
                id="closeVhiDrawer"
                type="button"
                aria-label="Close navigation"
            >
                <svg class="icon" aria-hidden="true">
                    <use href="#i-x"></use>
                </svg>
            </button>
        </div>

        <nav
            class="drawer-nav"
            id="drawerNav"
            aria-label="Mobile primary navigation"
        >
            <div class="drawer-group">
                <a class="drawer-trigger" href="#hero">Home</a>
            </div>

            <div class="drawer-group">
                <a class="drawer-trigger" href="<?= e(url('/products')) ?>">Products</a>
            </div>

            <div class="drawer-group">
                <a class="drawer-trigger" href="#procedure">How We Supply</a>
            </div>

            <div class="drawer-group">
                <a class="drawer-trigger" href="#organizations">Who We Support</a>
            </div>

            <div class="drawer-group">
                <a class="drawer-trigger" href="#partners">Partners</a>
            </div>

            <div class="drawer-group">
                <a class="drawer-trigger" href="#opportunities">Work With Varenz</a>
            </div>
        </nav>

        <div class="drawer-actions">
            <button
                class="drawer-action"
                id="drawerSearchAction"
                type="button"
                aria-haspopup="dialog"
                aria-controls="searchOverlay"
            >
                <svg class="icon" aria-hidden="true">
                    <use href="#i-search"></use>
                </svg>

                Search Website
            </button>

            <a
                class="drawer-action primary"
                href="#cta"
            >
                Request Support
            </a>
        </div>
    </aside>
</div>

<div
    class="search-overlay"
    id="searchOverlay"
    aria-hidden="true"
>
    <div
        class="search-backdrop"
        id="closeSearch"
        aria-hidden="true"
    ></div>

    <section
        class="search-panel"
        role="dialog"
        aria-modal="true"
        aria-labelledby="searchTitle"
        aria-describedby="searchDescription"
    >
        <div class="search-panel-head">
            <div>
                <span class="eyebrow">
                    Website Search
                </span>

                <h2 id="searchTitle">
                    Find products, supply support and Varenz partner information.
                </h2>

                <p
                    id="searchDescription"
                    class="sr-only"
                >
                    Search public Varenz website content.
                </p>
            </div>

            <button
                class="nav-circle"
                id="closeSearchButton"
                type="button"
                aria-label="Close search"
            >
                <svg class="icon" aria-hidden="true">
                    <use href="#i-x"></use>
                </svg>
            </button>
        </div>

        <label
            class="search-field"
            for="siteSearchInput"
        >
            <svg class="icon" aria-hidden="true">
                <use href="#i-search"></use>
            </svg>

            <span class="sr-only">
                Search Varenz website
            </span>

            <input
                id="siteSearchInput"
                type="search"
                autocomplete="off"
                enterkeyhint="search"
                spellcheck="false"
                maxlength="120"
                placeholder="Search products, procurement support or partners"
            >
        </label>

        <div
            class="search-results"
            id="siteSearchResults"
            aria-live="polite"
            aria-busy="false"
        >
            <p>
                Start typing to search the Varenz website.
            </p>
        </div>
    </section>
</div>

<main id="mainContent">

    <!-- ================================================================
         HERO
         ================================================================ -->

    <section
        class="hero-section"
        id="hero"
        aria-label="Varenz medical supply highlights"
    >
        <div class="vsl-effect-layer vsl-home-gradient" data-vsl-effect="hero-gradient" aria-hidden="true"></div>
        <div class="vsl-home-liquid-logo" data-vsl-effect="liquid-logo" aria-hidden="true"></div>
        <div
            class="hero-slider"
            id="heroSlider"
            aria-roledescription="carousel"
            aria-label="Varenz supply highlights"
        >
            <?php foreach ($heroSlides as $index => $slide): ?>
                <?php
                $slide = is_array($slide) ? $slide : [];

                $eyebrow = trim((string) ($slide['eyebrow'] ?? ''));
                $title = trim((string) ($slide['title'] ?? ''));
                $highlight = trim((string) ($slide['highlight'] ?? ''));
                $bodyText = trim((string) ($slide['text'] ?? ''));

                $primary = trim((string) ($slide['primary'] ?? 'Request a Quote'));
                $secondary = trim((string) ($slide['secondary'] ?? 'Explore Products'));

                $desktopImage = trim((string) ($slide['desktop_image'] ?? ''));
                $mobileImage = trim((string) ($slide['mobile_image'] ?? ''));

                $visualImage = trim((string) ($slide['visual_image'] ?? ''));
                $visualTitle = trim((string) ($slide['visual_title'] ?? ''));
                $visualLabel = trim((string) ($slide['visual_label'] ?? ''));
                $visualText = trim((string) ($slide['visual_text'] ?? ''));

                $visualFit = trim((string) ($slide['visual_fit'] ?? 'cover'));
                $visualPosition = trim((string) ($slide['visual_position'] ?? 'center'));

                $visualChips = isset($slide['visual_chips']) && is_array($slide['visual_chips'])
                    ? $slide['visual_chips']
                    : [];

                $tabLabel = trim((string) ($slide['tab'] ?? $eyebrow));

                $isFirstSlide = $index === 0;

                $desktopImageUrl = $imageAsset($desktopImage);
                $mobileImageUrl = $mobileImage !== ''
                    ? $imageAsset(
                        $mobileImage,
                        $desktopImage !== ''
                            ? $desktopImage
                            : 'images/logo/varenz-icon-logo-clean.png'
                    )
                    : $desktopImageUrl;

                $visualImageUrl = $imageAsset(
                    $visualImage,
                    $desktopImage !== ''
                        ? $desktopImage
                        : 'images/logo/varenz-icon-logo-clean.png'
                );
                ?>

                <article
                    class="hero-slide<?= $isFirstSlide ? ' is-active' : '' ?>"
                    id="heroSlide<?= (int) $index ?>"
                    data-hero-slide="<?= (int) $index ?>"
                    role="group"
                    aria-roledescription="slide"
                    aria-label="<?= e(($index + 1) . ' of ' . max(1, $heroSlideCount)) ?>"
                    aria-hidden="<?= $isFirstSlide ? 'false' : 'true' ?>"
                >
                    <picture class="hero-slide-media">
                        <source
                            media="(max-width: 760px)"
                            srcset="<?= e($mobileImageUrl) ?>"
                        >

                        <img
                            src="<?= e($desktopImageUrl) ?>"
                            alt=""
                            width="1920"
                            height="1080"
                            loading="<?= $isFirstSlide ? 'eager' : 'lazy' ?>"
                            fetchpriority="<?= $isFirstSlide ? 'high' : 'auto' ?>"
                            decoding="async"
                        >
                    </picture>

                    <div
                        class="hero-scrim"
                        aria-hidden="true"
                    ></div>

                    <div
                        class="hero-ambient"
                        aria-hidden="true"
                    >
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                    <div class="hero-stage">
                        <div class="hero-content">
                            <?php if ($eyebrow !== ''): ?>
                                <span class="hero-eyebrow">
                                    <span
                                        class="hero-eyebrow-dot"
                                        aria-hidden="true"
                                    ></span>

                                    <?= e($eyebrow) ?>
                                </span>
                            <?php endif; ?>

                            <?php if ($title !== ''): ?>
                                <h1><?= e($title) ?></h1>
                            <?php endif; ?>

                            <?php if ($highlight !== ''): ?>
                                <h2><?= e($highlight) ?></h2>
                            <?php endif; ?>

                            <?php if ($bodyText !== ''): ?>
                                <p><?= e($bodyText) ?></p>
                            <?php endif; ?>

                            <div
                                class="hero-points"
                                aria-label="Varenz support highlights"
                            >
                                <span
                                    data-vpr-target="#categories"
                                    role="link"
                                    tabindex="0"
                                >
                                    <svg class="icon" aria-hidden="true">
                                        <use href="#i-check"></use>
                                    </svg>

                                    Product clarity
                                </span>

                                <span
                                    data-vpr-target="#cta"
                                    role="link"
                                    tabindex="0"
                                >
                                    <svg class="icon" aria-hidden="true">
                                        <use href="#i-message"></use>
                                    </svg>

                                    Responsive support
                                </span>

                                <span
                                    data-vpr-target="#procedure"
                                    role="link"
                                    tabindex="0"
                                >
                                    <svg class="icon" aria-hidden="true">
                                        <use href="#i-file"></use>
                                    </svg>

                                    Clear process
                                </span>
                            </div>

                            <div class="hero-actions">
                                <a
                                    class="btn btn-primary"
                                    href="#cta"
                                >
                                    <?= e($primary) ?>

                                    <svg class="icon" aria-hidden="true">
                                        <use href="#i-arrow-right"></use>
                                    </svg>
                                </a>

                                <a
                                    class="btn btn-secondary"
                                    href="#categories"
                                >
                                    <?= e($secondary) ?>

                                    <svg class="icon" aria-hidden="true">
                                        <use href="#i-arrow-right"></use>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div
                            class="hero-showcase"
                            aria-label="<?= e($visualTitle !== '' ? $visualTitle : 'Varenz product showcase') ?>"
                        >
                            <div class="hero-showcase-frame">
                                <div class="hero-showcase-topline">
                                    <span><?= e($visualLabel) ?></span>

                                    <strong
                                        aria-label="Slide <?= (int) ($index + 1) ?> of <?= (int) $heroSlideCount ?>"
                                    >
                                        <?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?>
                                        /
                                        <?= str_pad((string) max(1, $heroSlideCount), 2, '0', STR_PAD_LEFT) ?>
                                    </strong>
                                </div>

                                <div class="hero-showcase-image">
                                    <img
                                        src="<?= e($visualImageUrl) ?>"
                                        alt="<?= e($visualTitle) ?>"
                                        width="1200"
                                        height="900"
                                        loading="<?= $isFirstSlide ? 'eager' : 'lazy' ?>"
                                        fetchpriority="<?= $isFirstSlide ? 'high' : 'auto' ?>"
                                        decoding="async"
                                        style="object-fit:<?= e($visualFit) ?>;object-position:<?= e($visualPosition) ?>"
                                    >

                                    <div
                                        class="hero-showcase-shine"
                                        aria-hidden="true"
                                    ></div>
                                </div>

                                <div class="hero-showcase-caption">
                                    <?php if ($visualLabel !== ''): ?>
                                        <span class="hero-showcase-kicker">
                                            <?= e($visualLabel) ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($visualTitle !== ''): ?>
                                        <h3><?= e($visualTitle) ?></h3>
                                    <?php endif; ?>

                                    <?php if ($visualText !== ''): ?>
                                        <p><?= e($visualText) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div
                                class="hero-float-card hero-float-card-primary"
                                data-vpr-target="#procedure"
                                role="link"
                                tabindex="0"
                                aria-label="Open procurement support process"
                            >
                                <span
                                    class="hero-float-icon"
                                    aria-hidden="true"
                                >
                                    <svg class="icon">
                                        <use href="#i-shield"></use>
                                    </svg>
                                </span>

                                <div>
                                    <strong>
                                        Less work for you
                                    </strong>

                                    <span>
                                        Share the requirement. We help organise the details.
                                    </span>
                                </div>
                            </div>

                            <?php if ($visualChips !== []): ?>
                                <div class="hero-visual-chips">
                                    <?php foreach ($visualChips as $chipIndex => $chip): ?>
                                        <span
                                            data-vpr-target="#categories"
                                            role="link"
                                            tabindex="0"
                                        >
                                            <b aria-hidden="true">
                                                <?= str_pad((string) ($chipIndex + 1), 2, '0', STR_PAD_LEFT) ?>
                                            </b>

                                            <?= e((string) $chip) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($heroSlideCount > 0): ?>
                <div class="hero-controls">
                    <button
                        type="button"
                        id="heroPrev"
                        aria-label="Previous hero slide"
                    >
                        <svg class="icon" aria-hidden="true">
                            <use href="#i-arrow-left"></use>
                        </svg>
                    </button>

                    <div
                        class="hero-tab-rail"
                        id="heroDots"
                        role="tablist"
                        aria-label="Hero slides"
                    >
                        <?php foreach ($heroSlides as $index => $slide): ?>
                            <?php
                            $slide = is_array($slide) ? $slide : [];

                            $tabLabel = (string) (
                                $slide['tab']
                                ?? $slide['eyebrow']
                                ?? 'Slide ' . ($index + 1)
                            );

                            $slideTitle = (string) (
                                $slide['title']
                                ?? $tabLabel
                            );
                            ?>

                            <button
                                type="button"
                                class="hero-tab<?= $index === 0 ? ' active' : '' ?>"
                                data-hero-dot="<?= (int) $index ?>"
                                aria-label="Show <?= e($slideTitle) ?>"
                                aria-selected="<?= $index === 0 ? 'true' : 'false' ?>"
                                aria-controls="heroSlide<?= (int) $index ?>"
                                tabindex="<?= $index === 0 ? '0' : '-1' ?>"
                                role="tab"
                            >
                                <span aria-hidden="true">
                                    <?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?>
                                </span>

                                <strong>
                                    <?= e($tabLabel) ?>
                                </strong>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <button
                        type="button"
                        id="heroNext"
                        aria-label="Next hero slide"
                    >
                        <svg class="icon" aria-hidden="true">
                            <use href="#i-arrow-right"></use>
                        </svg>
                    </button>

                    <button
                        type="button"
                        id="vprHeroPause"
                        class="vpr-hero-pause"
                        aria-label="Pause hero autoplay"
                        aria-pressed="false"
                    >
                        <span aria-hidden="true">Ⅱ</span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </section>


    <!-- ================================================================
         FAST DECISION STRIP
         Hick + Fitts + Von Restorff
         ================================================================ -->

    <section
        class="section section-tint vsl-choice-section"
        aria-labelledby="choiceTitle"
    >
        <div class="section-inner">
            <header class="section-head">
                <span class="eyebrow">
                    Start Here
                </span>

                <h2 id="choiceTitle">
                    What do you need Varenz to help with?
                </h2>

                <p>
                    Choose one path. We will help with the complexity after that.
                </p>
            </header>

            <div class="vsl-choice-grid">
                <a
                    class="ajax-card vsl-choice-card vsl-kit-002 vsl-kit-003 is-recommended"
                    href="#cta"
                    data-vsl-intent="quotation"
                >
                    <div class="ajax-card-body">
                        <span class="badge">Most common</span>

                        <h3>
                            Request a Quote
                        </h3>

                        <p>
                            Send a product name, image, list or reference and let our team review it.
                        </p>

                        <span class="open-label">
                            Start Request
                            <svg class="icon" aria-hidden="true">
                                <use href="#i-arrow-right"></use>
                            </svg>
                        </span>
                    </div>
                </a>

                <a
                    class="ajax-card vsl-choice-card vsl-kit-002 vsl-kit-003"
                    href="#categories"
                >
                    <div class="ajax-card-body">
                        <span class="badge">Products</span>

                        <h3>
                            Explore Supply Categories
                        </h3>

                        <p>
                            Review selected medical product categories and specification support.
                        </p>

                        <span class="open-label">
                            Browse Products
                            <svg class="icon" aria-hidden="true">
                                <use href="#i-arrow-right"></use>
                            </svg>
                        </span>
                    </div>
                </a>

                <a
                    class="ajax-card vsl-choice-card vsl-kit-002 vsl-kit-003"
                    href="#opportunities"
                >
                    <div class="ajax-card-body">
                        <span class="badge">Partnerships</span>

                        <h3>
                            Work With Varenz
                        </h3>

                        <p>
                            Connect for institutional supply, partnerships, professional opportunities or collaboration.
                        </p>

                        <span class="open-label">
                            See Opportunities
                            <svg class="icon" aria-hidden="true">
                                <use href="#i-arrow-right"></use>
                            </svg>
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </section>


    <!-- ================================================================
         REQUEST / CONVERSION
         ================================================================ -->

    <section
        class="section section-dark cta-section"
        id="cta"
        aria-labelledby="ctaTitle"
    >
        <div class="vsl-effect-layer" data-vsl-effect="quotation-gradient" aria-hidden="true"></div>
        <div class="section-inner cta-grid">
            <div class="cta-copy">
                <span class="eyebrow light">
                    One Request. Clear Next Steps.
                </span>

                <h2 id="ctaTitle">
                    Send what you already have. Varenz will help organise the rest.
                </h2>

                <p>
                    A product name, photo, previous package, spreadsheet or procurement
                    list is enough to begin. We help identify missing specifications,
                    quantities and timing before the request progresses.
                </p>

                <div
                    class="cta-proof"
                    aria-label="Request support features"
                >
                    <span>
                        <svg class="icon" aria-hidden="true">
                            <use href="#i-upload"></use>
                        </svg>

                        Upload your existing list
                    </span>

                    <span>
                        <svg class="icon" aria-hidden="true">
                            <use href="#i-search"></use>
                        </svg>

                        Specification clarification
                    </span>

                    <span>
                        <svg class="icon" aria-hidden="true">
                            <use href="#i-shield"></use>
                        </svg>

                        Traceable reference number
                    </span>
                </div>

                <div class="cta-visual">
                    <img
                        src="<?= e($imageAsset('images/operations/operations-support-agent.webp')) ?>"
                        alt="Varenz operations and support"
                        width="900"
                        height="900"
                        loading="lazy"
                        decoding="async"
                    >

                    <div>
                        <strong>
                            Procurement Support Desk
                        </strong>

                        <span>
                            Requirement → review → quotation → coordination.
                        </span>
                    </div>
                </div>
            </div>

            <div class="cta-form-shell">
                <div
                    class="feedback-types"
                    id="feedbackTypes"
                    role="group"
                    aria-label="Select request type"
                >
                    <button
                        class="active"
                        type="button"
                        data-type="quotation"
                        aria-pressed="true"
                    >
                        Quotation
                    </button>

                    <button
                        type="button"
                        data-type="feedback"
                        aria-pressed="false"
                    >
                        Feedback
                    </button>

                    <button
                        type="button"
                        data-type="suggestion"
                        aria-pressed="false"
                    >
                        Suggestion
                    </button>

                    <button
                        type="button"
                        data-type="support"
                        aria-pressed="false"
                    >
                        Support
                    </button>
                </div>

                <form
                    id="vslRequestForm"
                    class="cta-form"
                    action="<?= e(url('/api/submissions')) ?>"
                    method="post"
                    enctype="multipart/form-data"
                    novalidate
                >
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e((string) $csrf) ?>"
                    >

                    <input
                        type="hidden"
                        name="form_started_at"
                        value="<?= (int) time() ?>"
                    >

                    <input
                        type="hidden"
                        name="type"
                        id="feedbackTypeInput"
                        value="quotation"
                    >

                    <input
                        type="hidden"
                        name="source"
                        value="homepage-cta"
                    >

                    <input
                        type="hidden"
                        name="request_quality"
                        id="requestQualityInput"
                        value="basic"
                    >

                    <input
                        type="hidden"
                        name="intelligence_summary"
                        id="requestIntelligenceInput"
                        value=""
                    >

                    <input
                        type="text"
                        class="hp-field"
                        name="website"
                        tabindex="-1"
                        autocomplete="off"
                        aria-hidden="true"
                        maxlength="120"
                    >

                    <div
                        class="request-assistant"
                        id="requestAssistant"
                        aria-live="polite"
                        aria-atomic="false"
                    >
                        <div>
                            <span class="assistant-kicker">
                                Request Readiness
                            </span>

                            <strong id="assistantScore">
                                Basic request
                            </strong>

                            <p id="assistantSummary">
                                Add product, specification, quantity and timeline
                                where available. You can still submit without knowing everything.
                            </p>
                        </div>

                        <div
                            class="assistant-checks"
                            id="assistantChecks"
                            aria-label="Request completeness"
                        >
                            <span data-check="product">
                                Product
                            </span>

                            <span data-check="specification">
                                Specification
                            </span>

                            <span data-check="quantity">
                                Quantity
                            </span>

                            <span data-check="timeline">
                                Timeline
                            </span>
                        </div>
                    </div>

                    <div class="field-grid">
                        <label class="field">
                            <span>
                                <svg class="icon" aria-hidden="true">
                                    <use href="#i-users"></use>
                                </svg>

                                Full name
                            </span>

                            <input
                                class="input"
                                name="name"
                                required
                                maxlength="120"
                                autocomplete="name"
                                placeholder="Your name"
                            >
                        </label>

                        <label class="field">
                            <span>
                                <svg class="icon" aria-hidden="true">
                                    <use href="#i-building"></use>
                                </svg>

                                Organisation
                            </span>

                            <input
                                class="input"
                                name="organization"
                                maxlength="180"
                                autocomplete="organization"
                                placeholder="Hospital, clinic, company or organisation"
                            >
                        </label>

                        <label class="field">
                            <span>
                                <svg class="icon" aria-hidden="true">
                                    <use href="#i-phone"></use>
                                </svg>

                                Telephone
                            </span>

                            <input
                                class="input"
                                type="tel"
                                name="phone"
                                maxlength="60"
                                autocomplete="tel"
                                inputmode="tel"
                                placeholder="Contact number"
                                aria-describedby="contactRequirement"
                            >
                        </label>

                        <label class="field">
                            <span>
                                <svg class="icon" aria-hidden="true">
                                    <use href="#i-mail"></use>
                                </svg>

                                Email
                            </span>

                            <input
                                class="input"
                                type="email"
                                name="email"
                                maxlength="180"
                                autocomplete="email"
                                inputmode="email"
                                placeholder="Email address"
                                aria-describedby="contactRequirement"
                            >
                        </label>

                        <p
                            id="contactRequirement"
                            class="sr-only"
                        >
                            Provide at least one contact method: telephone or email.
                        </p>

                        <label class="field">
                            <span>
                                <svg class="icon" aria-hidden="true">
                                    <use href="#i-grid"></use>
                                </svg>

                                What do you need?
                            </span>

                            <select
                                class="select"
                                name="category"
                            >
                                <option value="Institutional procurement">
                                    Institutional procurement
                                </option>

                                <option value="Single product request">
                                    Single product request
                                </option>

                                <option value="Service feedback">
                                    Service feedback
                                </option>

                                <option value="Website suggestion">
                                    Website suggestion
                                </option>

                                <option value="Quality or support concern">
                                    Quality or support concern
                                </option>
                            </select>
                        </label>

                        <label class="field">
                            <span>
                                <svg class="icon" aria-hidden="true">
                                    <use href="#i-message"></use>
                                </svg>

                                Preferred response
                            </span>

                            <select
                                class="select"
                                name="preferred_contact"
                            >
                                <option value="Phone call">
                                    Phone call
                                </option>

                                <option value="WhatsApp">
                                    WhatsApp
                                </option>

                                <option value="Email">
                                    Email
                                </option>
                            </select>
                        </label>

                        <label class="field">
                            <span>
                                <svg class="icon" aria-hidden="true">
                                    <use href="#i-clock"></use>
                                </svg>

                                Required by
                            </span>

                            <input
                                class="input"
                                type="date"
                                name="required_by"
                                min="<?= e(date('Y-m-d')) ?>"
                            >
                        </label>

                        <label class="field">
                            <span>
                                <svg class="icon" aria-hidden="true">
                                    <use href="#i-upload"></use>
                                </svg>

                                Upload what you have
                            </span>

                            <input
                                class="input file-input"
                                type="file"
                                name="attachment"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.jpg,.jpeg,.png,.webp"
                                aria-describedby="attachmentHelp"
                            >

                            <small id="attachmentHelp">
                                PDF, Word, Excel, CSV or image. Maximum 8 MB.
                            </small>
                        </label>

                        <label class="field full">
                            <span>
                                <svg class="icon" aria-hidden="true">
                                    <use href="#i-feedback"></use>
                                </svg>

                                Tell us what you need
                            </span>

                            <textarea
                                class="textarea"
                                name="message"
                                required
                                maxlength="5000"
                                rows="5"
                                placeholder="Example: CT contrast media, 50 units, needed next week. I have attached the previous package."
                            ></textarea>
                        </label>
                    </div>

                    <div class="form-submit-row">
                        <span
                            id="vslFormStatus"
                            role="status"
                            aria-live="polite"
                        >
                            <svg class="icon" aria-hidden="true">
                                <use href="#i-check"></use>
                            </svg>

                            You receive a reference number after submission.
                        </span>

                        <button
                            class="btn btn-primary"
                            id="vslSubmitButton"
                            type="submit"
                        >
                            Submit Request

                            <svg class="icon" aria-hidden="true">
                                <use href="#i-arrow-right"></use>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>


    <!-- ================================================================
         CHALLENGES
         Server-rendered first state
         ================================================================ -->

    <section
        class="section"
        id="challenges"
        aria-labelledby="challengesTitle"
    >
        <div class="section-inner">
            <header class="section-head">
                <span class="eyebrow">
                    Common Procurement Friction
                </span>

                <h2 id="challengesTitle">
                    The customer should not have to solve every supply problem alone.
                </h2>

                <p>
                    Varenz helps reduce the effort between an unclear requirement
                    and a usable procurement response.
                </p>
            </header>

            <div class="challenges-shell">
                <div
                    class="challenge-nav"
                    id="challengeNav"
                    aria-label="Supply challenges"
                >
                    <?php foreach ($challenges as $index => $item): ?>
                        <?php $item = is_array($item) ? $item : []; ?>

                        <button
                            class="challenge-btn<?= $index === 0 ? ' active' : '' ?>"
                            type="button"
                            data-challenge="<?= (int) $index ?>"
                            aria-pressed="<?= $index === 0 ? 'true' : 'false' ?>"
                        >
                            <h4>
                                <?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?>
                                ·
                                <?= e((string) ($item['title'] ?? 'Supply challenge')) ?>
                            </h4>

                            <p>
                                <?= e((string) ($item['intro'] ?? '')) ?>
                            </p>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div
                    class="challenge-display"
                    id="challengeDisplay"
                    aria-live="polite"
                >
                    <?php if ($initialChallenge !== []): ?>
                        <?php
                        $challengeResults = isset($initialChallenge['results'])
                            && is_array($initialChallenge['results'])
                            ? $initialChallenge['results']
                            : [];
                        ?>

                        <div class="challenge-stage active">
                            <div class="intro">
                                <span class="eyebrow">
                                    Active Challenge
                                </span>

                                <h3>
                                    <?= e((string) ($initialChallenge['title'] ?? '')) ?>
                                </h3>

                                <p>
                                    <?= e((string) ($initialChallenge['intro'] ?? '')) ?>
                                </p>
                            </div>

                            <div class="challenge-media">
                                <div class="challenge-image">
                                    <img
                                        loading="lazy"
                                        decoding="async"
                                        src="<?= e($imageAsset((string) ($initialChallenge['image'] ?? ''))) ?>"
                                        alt="<?= e((string) ($initialChallenge['imageTitle'] ?? $initialChallenge['title'] ?? 'Varenz supply support')) ?>"
                                    >

                                    <div class="challenge-image-overlay">
                                        <h4>
                                            <?= e((string) ($initialChallenge['imageTitle'] ?? '')) ?>
                                        </h4>

                                        <p>
                                            <?= e((string) ($initialChallenge['imageDesc'] ?? '')) ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="mini-video">
                                    <div
                                        class="play-ring"
                                        aria-hidden="true"
                                    >
                                        <svg class="icon">
                                            <use href="#i-play"></use>
                                        </svg>
                                    </div>

                                    <h4>
                                        <?= e((string) ($initialChallenge['videoTitle'] ?? '')) ?>
                                    </h4>

                                    <p>
                                        <?= e((string) ($initialChallenge['videoDesc'] ?? '')) ?>
                                    </p>
                                </div>
                            </div>

                            <div class="result-list">
                                <?php foreach ($challengeResults as $result): ?>
                                    <?php
                                    $result = is_array($result)
                                        ? array_values($result)
                                        : [];
                                    ?>

                                    <div class="result-card">
                                        <strong>
                                            <?= e((string) ($result[0] ?? '')) ?>
                                        </strong>

                                        <p>
                                            <?= e((string) ($result[1] ?? '')) ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>


    <!-- ================================================================
         PRODUCT CATEGORIES
         Server-rendered first state
         ================================================================ -->

    <section
        class="section section-tint"
        id="categories"
        aria-labelledby="categoriesTitle"
    >
        <div class="section-inner">
            <header class="section-head">
                <span class="eyebrow">
                    Product Categories
                </span>

                <h2 id="categoriesTitle">
                    Start with the clinical category. We help narrow the specification.
                </h2>

                <p>
                    Fewer, clearer choices reduce procurement friction and make
                    specialist enquiries easier to prepare.
                </p>
            </header>

            <div
                class="tabbar"
                id="categoryTabs"
                role="tablist"
                aria-label="Product categories"
            >
                <?php foreach ($categories as $index => $category): ?>
                    <?php $category = is_array($category) ? $category : []; ?>

                    <button
                        class="<?= $index === 0 ? 'active' : '' ?>"
                        type="button"
                        role="tab"
                        data-category="<?= (int) $index ?>"
                        aria-selected="<?= $index === 0 ? 'true' : 'false' ?>"
                        tabindex="<?= $index === 0 ? '0' : '-1' ?>"
                    >
                        <?= e((string) ($category['label'] ?? 'Category')) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="cat-stage">
                <div
                    class="cat-media active"
                    id="categoryMedia"
                >
                    <?php if ($initialCategory !== []): ?>
                        <img
                            loading="lazy"
                            decoding="async"
                            src="<?= e($imageAsset((string) ($initialCategory['media_image'] ?? ''))) ?>"
                            alt="<?= e((string) ($initialCategory['title'] ?? 'Medical supply category')) ?>"
                        >

                        <div class="cat-media-content">
                            <span class="eyebrow light">
                                <?= e((string) ($initialCategory['label'] ?? 'Products')) ?>
                            </span>

                            <h3>
                                <?= e((string) ($initialCategory['title'] ?? '')) ?>
                            </h3>

                            <p>
                                <?= e((string) ($initialCategory['desc'] ?? '')) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <div
                    class="ajax-grid"
                    id="categoryGrid"
                    aria-live="polite"
                >
                    <?php
                    $initialCards = isset($initialCategory['cards'])
                        && is_array($initialCategory['cards'])
                        ? $initialCategory['cards']
                        : [];
                    ?>

                    <?php foreach ($initialCards as $card): ?>
                        <?php
                        $card = is_array($card) ? $card : [];

                        $tags = isset($card['tags']) && is_array($card['tags'])
                            ? $card['tags']
                            : [];
                        ?>

                        <article class="ajax-card">
                            <img
                                loading="lazy"
                                decoding="async"
                                src="<?= e($imageAsset((string) ($card['image'] ?? ''))) ?>"
                                alt="<?= e((string) ($card['title'] ?? 'Medical product')) ?>"
                            >

                            <div class="ajax-card-body">
                                <h4>
                                    <?= e((string) ($card['title'] ?? '')) ?>
                                </h4>

                                <p>
                                    <?= e((string) ($card['description'] ?? '')) ?>
                                </p>

                                <div class="ajax-badges">
                                    <?php foreach ($tags as $tag): ?>
                                        <span><?= e((string) $tag) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>


    <!-- ================================================================
         PRODUCT SPOTLIGHT
         One data-driven product advertisement replaces the old card theatre.
         ================================================================ -->

    <section class="section section-dark vsl-product-spotlight" id="featured" aria-labelledby="featuredTitle">
        <div class="section-inner">
            <?php if (is_array($spotlightProduct)): ?>
                <?php $spotlightTags = isset($spotlightProduct['tags']) && is_array($spotlightProduct['tags']) ? $spotlightProduct['tags'] : []; ?>
                <article class="vsl-product-spotlight__card">
                    <div class="vsl-product-spotlight__copy">
                        <span class="eyebrow light">Product Spotlight</span>
                        <h2 id="featuredTitle"><?= e((string) ($spotlightProduct['title'] ?? 'Selected medical product')) ?></h2>
                        <p><?= e((string) ($spotlightProduct['description'] ?? $spotlightProduct['short_description'] ?? '')) ?></p>
                        <div class="vsl-product-spotlight__tags">
                            <?php foreach ($spotlightTags as $tag): ?><span><?= e((string) $tag) ?></span><?php endforeach; ?>
                        </div>
                        <div class="vsl-product-spotlight__actions">
                            <a class="btn btn-primary" href="<?= e(url('/products/' . rawurlencode((string) ($spotlightProduct['slug'] ?? '')))) ?>">View Product</a>
                            <a class="btn btn-secondary" href="#cta" data-vsl-intent="quotation" data-vsl-product="<?= e((string) ($spotlightProduct['slug'] ?? '')) ?>">Request a Quote</a>
                        </div>
                    </div>
                    <div class="vsl-product-spotlight__media">
                        <img src="<?= e($imageAsset((string) ($spotlightProduct['image'] ?? ''))) ?>" alt="<?= e((string) ($spotlightProduct['title'] ?? 'Medical product')) ?>" width="900" height="760" loading="lazy" decoding="async">
                    </div>
                </article>
            <?php endif; ?>
        </div>
    </section>


    <!-- ================================================================
         PROCUREMENT
         Chunking / Miller / Serial Position
         ================================================================ -->

    <section
        class="section"
        id="procedure"
        aria-labelledby="procedureTitle"
    >
        <div class="section-inner">
            <header class="section-head">
                <span class="eyebrow">
                    Seven Clear Steps
                </span>

                <h2 id="procedureTitle">
                    Know what happens from requirement to follow-up.
                </h2>

                <p>
                    Each stage has one job. This keeps the process easier to
                    understand, easier to approve and easier to follow.
                </p>
            </header>

            <div class="procure-shell">
                <div>
                    <div
                        class="progress-rail"
                        aria-hidden="true"
                    >
                        <span
                            id="procureProgress"
                            style="width:<?= $procurement !== [] ? e((string) (100 / count($procurement))) : '0' ?>%"
                        ></span>
                    </div>

                    <div
                        class="stepbar"
                        id="procureSteps"
                        aria-label="Procurement stages"
                    >
                        <?php foreach ($procurement as $index => $item): ?>
                            <?php $item = is_array($item) ? $item : []; ?>

                            <button
                                class="step-chip<?= $index === 0 ? ' active' : '' ?>"
                                type="button"
                                data-procurement="<?= (int) $index ?>"
                                aria-pressed="<?= $index === 0 ? 'true' : 'false' ?>"
                            >
                                <span class="n">
                                    <?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?>
                                </span>

                                <h4>
                                    <?= e((string) ($item['step'] ?? 'Step')) ?>
                                </h4>

                                <p>
                                    <?= e((string) ($item['short'] ?? '')) ?>
                                </p>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div
                    class="step-showcase"
                    id="procureDisplay"
                    aria-live="polite"
                >
                    <?php if ($initialProcurement !== []): ?>
                        <?php
                        $tiles = isset($initialProcurement['tiles'])
                            && is_array($initialProcurement['tiles'])
                            ? $initialProcurement['tiles']
                            : [];
                        ?>

                        <div class="step-panel active">
                            <div>
                                <span class="eyebrow">
                                    Step 1
                                </span>

                                <h3>
                                    <?= e((string) ($initialProcurement['step'] ?? '')) ?>
                                </h3>

                                <p>
                                    <?= e((string) ($initialProcurement['detail'] ?? '')) ?>
                                </p>
                            </div>

                            <div class="step-image">
                                <img
                                    loading="lazy"
                                    decoding="async"
                                    src="<?= e($imageAsset((string) ($initialProcurement['image'] ?? ''))) ?>"
                                    alt="<?= e((string) ($initialProcurement['step'] ?? 'Procurement step')) ?>"
                                    width="960"
                                    height="640"
                                >

                                <span
                                    class="step-image-fallback"
                                    role="status"
                                >
                                    Image temporarily unavailable
                                </span>
                            </div>

                            <div class="step-guides">
                                <?php foreach ($tiles as $tile): ?>
                                    <?php
                                    $tile = is_array($tile)
                                        ? array_values($tile)
                                        : [];
                                    ?>

                                    <div class="guide-tile">
                                        <strong>
                                            <?= e((string) ($tile[0] ?? '')) ?>
                                        </strong>

                                        <p>
                                            <?= e((string) ($tile[1] ?? '')) ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>


    <!-- ================================================================
         ORGANISATIONS
         ================================================================ -->

    <section
        class="section section-tint"
        id="organizations"
        aria-labelledby="organizationsTitle"
    >
        <div class="section-inner">
            <header class="section-head">
                <span class="eyebrow">
                    Organisations We Support
                </span>

                <h2 id="organizationsTitle">
                    Different healthcare environments need different supply support.
                </h2>

                <p>
                    Choose the environment closest to yours and see the type of
                    procurement support Varenz is structured to provide.
                </p>
            </header>

            <div class="org-shell">
                <div
                    class="org-nav vsl-kit-004"
                    id="orgNav"
                    aria-label="Organisation types"
                >
                    <?php foreach ($organizations as $index => $item): ?>
                        <?php $item = is_array($item) ? $item : []; ?>

                        <button
                            class="org-pill vsl-kit-004__panel<?= $index === 0 ? ' active' : '' ?>"
                            type="button"
                            data-organization="<?= (int) $index ?>"
                            aria-pressed="<?= $index === 0 ? 'true' : 'false' ?>"
                        >
                            <strong>
                                <?= e((string) ($item['name'] ?? 'Organisation')) ?>
                            </strong>

                            <p>
                                <?= e((string) ($item['summary'] ?? '')) ?>
                            </p>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div
                    class="org-viewer"
                    id="orgViewer"
                    aria-live="polite"
                >
                    <?php if ($initialOrganization !== []): ?>
                        <?php
                        $orgBullets = isset($initialOrganization['bullets'])
                            && is_array($initialOrganization['bullets'])
                            ? $initialOrganization['bullets']
                            : [];
                        ?>

                        <article class="org-stage active">
                            <img
                                loading="lazy"
                                decoding="async"
                                src="<?= e($imageAsset((string) ($initialOrganization['image'] ?? ''))) ?>"
                                alt="<?= e((string) ($initialOrganization['name'] ?? 'Healthcare organisation')) ?>"
                            >

                            <div class="org-content">
                                <span class="eyebrow light">
                                    Organisation Support
                                </span>

                                <h3>
                                    <?= e((string) ($initialOrganization['name'] ?? '')) ?>
                                </h3>

                                <p>
                                    <?= e((string) ($initialOrganization['summary'] ?? '')) ?>
                                </p>

                                <div class="org-bullets">
                                    <?php foreach ($orgBullets as $bullet): ?>
                                        <span>
                                            <?= e((string) $bullet) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </article>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>


    <!-- ================================================================
         WHY VARENZ
         ================================================================ -->

    <section
        class="section"
        id="why"
        aria-labelledby="whyTitle"
    >
        <div class="section-inner why-shell">
            <div
                class="orbit-box"
                id="orbitBox"
                aria-label="Varenz service principles"
            >
                <div class="center-orb">
                    VARENZ
                </div>

                <noscript>
                    <?php foreach ($whyItems as $item): ?>
                        <?php $item = is_array($item) ? $item : []; ?>

                        <article class="orbit-node">
                            <h4>
                                <?= e((string) ($item['title'] ?? '')) ?>
                            </h4>

                            <p>
                                <?= e((string) ($item['desc'] ?? '')) ?>
                            </p>
                        </article>
                    <?php endforeach; ?>
                </noscript>
            </div>

            <div class="why-copy">
                <span class="eyebrow">
                    Why Varenz
                </span>

                <h2
                    class="editorial-title"
                    id="whyTitle"
                >
                    Procurement support designed around human effort, not internal complexity.
                </h2>

                <p>
                    Clear choices, familiar interactions, concise information and
                    visible progress help customers move from enquiry to decision
                    with less friction.
                </p>

                <div
                    class="why-detail"
                    id="whyDetail"
                    aria-live="polite"
                >
                    <?php if ($initialWhy !== []): ?>
                        <span class="eyebrow">
                            Focused Value
                        </span>

                        <h3>
                            <?= e((string) ($initialWhy['title'] ?? '')) ?>
                        </h3>

                        <p>
                            <?= e((string) ($initialWhy['desc'] ?? '')) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>


    <!-- ================================================================
         TRUSTED RESOURCES
         VSL-KIT-001 rotating deck + VSL-KIT-002/003 resource surfaces
         ================================================================ -->

    <section
        class="section section-tint vsl-resources-section"
        id="resources"
        aria-labelledby="resourcesTitle"
    >
        <div class="section-inner">
            <div class="vsl-resources-intro">
                <header class="section-head">
                    <span class="eyebrow">
                        Procurement Resources
                    </span>

                    <h2 id="resourcesTitle">
                        Review Varenz before your next supply decision.
                    </h2>

                    <p>
                        Share the company profile with your procurement team,
                        review the capability presentation or download the
                        healthcare supply brochure for quick reference.
                    </p>
                </header>

                <div
                    class="vsl-kit-001"
                    aria-hidden="true"
                >
                    <div class="vsl-kit-001__card">
                        <div class="vsl-kit-001__content">
                            <img
                                src="<?= e($imageAsset('images/resources/company-profile-cover.webp')) ?>"
                                alt=""
                                width="720"
                                height="720"
                                loading="lazy"
                                decoding="async"
                            >

                            <span>01</span>
                        </div>
                    </div>

                    <div class="vsl-kit-001__card">
                        <div class="vsl-kit-001__content">
                            <img
                                src="<?= e($imageAsset('images/resources/product-portfolio-cover.webp')) ?>"
                                alt=""
                                width="720"
                                height="720"
                                loading="lazy"
                                decoding="async"
                            >

                            <span>02</span>
                        </div>
                    </div>

                    <div class="vsl-kit-001__card">
                        <div class="vsl-kit-001__content">
                            <img
                                src="<?= e($imageAsset('images/resources/partnership-contact-cover.webp')) ?>"
                                alt=""
                                width="720"
                                height="720"
                                loading="lazy"
                                decoding="async"
                            >

                            <span>03</span>
                        </div>
                    </div>

                    <div class="vsl-kit-001__lines">
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>

            <div class="vsl-resource-grid">
                <article class="vsl-resource-card vsl-kit-002 vsl-kit-003">
                    <img
                        src="<?= e($imageAsset('images/resources/company-profile-cover.webp')) ?>"
                        alt="Varenz Supplies Ltd company profile cover"
                        width="720"
                        height="720"
                        loading="lazy"
                        decoding="async"
                    >

                    <div class="vsl-resource-card__body">
                        <span class="badge">PDF · Company overview</span>

                        <h3>Company Profile</h3>

                        <p>
                            A concise introduction to Varenz, its medical supply
                            focus and support approach for healthcare organisations.
                        </p>

                        <a
                            class="btn btn-secondary"
                            href="<?= e(asset('downloads/company-profile.pdf')) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            View Profile

                            <svg class="icon" aria-hidden="true">
                                <use href="#i-arrow-right"></use>
                            </svg>
                        </a>
                    </div>
                </article>

                <article class="vsl-resource-card vsl-kit-002 vsl-kit-003">
                    <img
                        src="<?= e($imageAsset('images/resources/product-portfolio-cover.webp')) ?>"
                        alt="Varenz selected medical product portfolio"
                        width="720"
                        height="720"
                        loading="lazy"
                        decoding="async"
                    >

                    <div class="vsl-resource-card__body">
                        <span class="badge">PPTX · Capability deck</span>

                        <h3>Capability Presentation</h3>

                        <p>
                            A presentation-ready overview for internal review,
                            partnership conversations and procurement meetings.
                        </p>

                        <a
                            class="btn btn-secondary"
                            href="<?= e(asset('downloads/varenz-capability-presentation.pptx')) ?>"
                            download
                        >
                            Download Deck

                            <svg class="icon" aria-hidden="true">
                                <use href="#i-arrow-right"></use>
                            </svg>
                        </a>
                    </div>
                </article>

                <article class="vsl-resource-card vsl-kit-002 vsl-kit-003">
                    <img
                        src="<?= e($imageAsset('images/resources/partnership-contact-cover.webp')) ?>"
                        alt="Varenz healthcare supply support brochure"
                        width="720"
                        height="720"
                        loading="lazy"
                        decoding="async"
                    >

                    <div class="vsl-resource-card__body">
                        <span class="badge">PNG · Quick reference</span>

                        <h3>Supply Brochure</h3>

                        <p>
                            A shareable healthcare supply summary with product,
                            partnership and contact information.
                        </p>

                        <a
                            class="btn btn-secondary"
                            href="<?= e(asset('downloads/varenz-healthcare-supply-brochure.png')) ?>"
                            download
                        >
                            Download Brochure

                            <svg class="icon" aria-hidden="true">
                                <use href="#i-arrow-right"></use>
                            </svg>
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>


    <!-- ================================================================
         WORK WITH VARENZ / OPPORTUNITIES
         ================================================================ -->

    <section
        class="section section-tint"
        id="opportunities"
        aria-labelledby="opportunitiesTitle"
    >
        <div class="section-inner">
            <header class="section-head">
                <span class="eyebrow">
                    Work With Varenz
                </span>

                <h2 id="opportunitiesTitle">
                    More than a supply enquiry.
                </h2>

                <p>
                    Varenz welcomes relevant institutional, supplier, manufacturer
                    and professional conversations that strengthen healthcare supply.
                </p>
            </header>

            <div class="vsl-choice-grid">
                <article class="ajax-card vsl-opportunity-card is-recommended">
                    <div class="ajax-card-body">
                        <span class="badge">
                            Institutions
                        </span>

                        <h3>
                            Procurement Partnerships
                        </h3>

                        <p>
                            Hospitals, clinics, diagnostic centres, pharmacies,
                            NGOs and healthcare projects can discuss recurring or
                            multi-product requirements.
                        </p>

                        <a
                            class="btn btn-primary"
                            href="#cta"
                        >
                            Discuss Supply

                            <svg class="icon" aria-hidden="true">
                                <use href="#i-arrow-right"></use>
                            </svg>
                        </a>
                    </div>
                </article>

                <article class="ajax-card vsl-opportunity-card">
                    <div class="ajax-card-body">
                        <span class="badge">
                            Suppliers
                        </span>

                        <h3>
                            Supplier &amp; Manufacturer Collaboration
                        </h3>

                        <p>
                            Relevant manufacturers and authorised supply partners
                            can introduce healthcare products and distribution opportunities.
                        </p>

                        <a
                            class="btn btn-secondary"
                            href="#cta"
                        >
                            Start Conversation

                            <svg class="icon" aria-hidden="true">
                                <use href="#i-arrow-right"></use>
                            </svg>
                        </a>
                    </div>
                </article>

                <article class="ajax-card vsl-opportunity-card">
                    <div class="ajax-card-body">
                        <span class="badge">
                            Professionals
                        </span>

                        <h3>
                            Careers &amp; Professional Opportunities
                        </h3>

                        <p>
                            Professionals with relevant experience in healthcare,
                            procurement, sales, logistics, quality, finance or
                            operations may introduce themselves for future opportunities.
                        </p>

                        <a
                            class="btn btn-secondary"
                            href="mailto:<?= e($contactEmail !== '' ? $contactEmail : 'info@varenzsupplies.com') ?>?subject=Professional%20Opportunity%20-%20Varenz%20Supplies%20Ltd"
                        >
                            Introduce Yourself

                            <svg class="icon" aria-hidden="true">
                                <use href="#i-briefcase"></use>
                            </svg>
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>


    <!-- ================================================================
         PARTNER LOGO CAROUSEL
         Replaces the public team section. Team profile routes remain available
         separately for legacy QR/deep-link compatibility.
         ================================================================ -->

    <section
        class="section section-dark partner-section"
        id="partners"
        aria-labelledby="partnersTitle"
    >
        <div class="section-inner">
            <header class="section-head light-head partner-section-head">
                <span class="eyebrow light">
                    Trusted Relationships
                </span>

                <h2 id="partnersTitle">
                    Companies we work with.
                </h2>

                <p>
                    Explore selected organisations and companies connected to
                    Varenz through regulatory, supply and business relationships.
                </p>
            </header>

            <?php if ($partners !== []): ?>
                <div
                    class="partner-carousel"
                    id="partnerCarousel"
                    role="region"
                    aria-roledescription="carousel"
                    aria-label="Companies Varenz works with"
                >
                    <button
                        class="partner-carousel-arrow partner-carousel-prev"
                        id="partnerPrev"
                        type="button"
                        aria-label="Show previous company logos"
                    >
                        <svg class="icon" aria-hidden="true">
                            <use href="#i-arrow-left"></use>
                        </svg>
                    </button>

                    <div
                        class="partner-carousel-viewport"
                        id="partnerViewport"
                        tabindex="0"
                    >
                        <div
                            class="partner-track"
                            id="partnerTrack"
                        >
                            <?php foreach ($partners as $index => $partner): ?>
                                <?php
                                $partner = is_array($partner)
                                    ? $partner
                                    : [];

                                $partnerName = trim(
                                    (string) ($partner['name'] ?? 'Company')
                                );

                                $partnerLogo = trim(
                                    (string) ($partner['logo'] ?? '')
                                );

                                $partnerUrl = trim(
                                    (string) ($partner['url'] ?? '')
                                );

                                $partnerNote = trim(
                                    (string) ($partner['note'] ?? '')
                                );

                                $partnerLabel = trim(
                                    (string) ($partner['label'] ?? 'Company')
                                );

                                $hasLogo = $partnerLogo !== ''
                                    && is_file(
                                        ROOT_PATH
                                        . '/assets/'
                                        . ltrim($partnerLogo, '/')
                                    );
                                ?>

                                <article
                                    class="partner-card"
                                    data-partner-index="<?= (int) $index ?>"
                                    aria-label="<?= e($partnerName) ?>"
                                >
                                    <?php if ($partnerUrl !== ''): ?>
                                        <a
                                            class="partner-card-link"
                                            href="<?= e($partnerUrl) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            aria-label="Visit <?= e($partnerName) ?>"
                                        >
                                    <?php else: ?>
                                        <div class="partner-card-link">
                                    <?php endif; ?>

                                        <?php if ($hasLogo): ?>
                                            <img
                                                class="partner-card-logo-bg"
                                                src="<?= e(asset($partnerLogo)) ?>"
                                                alt="<?= e($partnerName) ?> logo"
                                                loading="lazy"
                                                decoding="async"
                                            >
                                        <?php else: ?>
                                            <span
                                                class="partner-card-logo-fallback"
                                                aria-hidden="true"
                                            >
                                                <?= e(
                                                    strtoupper(
                                                        substr(
                                                            $partnerName,
                                                            0,
                                                            2
                                                        )
                                                    )
                                                ) ?>
                                            </span>
                                        <?php endif; ?>

                                        <span
                                            class="partner-card-shade"
                                            aria-hidden="true"
                                        ></span>

                                        <span
                                            class="partner-card-border"
                                            aria-hidden="true"
                                        ></span>

                                        <div class="partner-card-content">
                                            <span class="partner-card-kicker">
                                                <?= e($partnerName) ?>
                                            </span>
                                        </div>

                                        <div class="partner-card-copy">
                                            <h3>
                                                <?= e($partnerLabel) ?>
                                            </h3>

                                            <?php if ($partnerNote !== ''): ?>
                                                <p>
                                                    <?= e($partnerNote) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>

                                    <?php if ($partnerUrl !== ''): ?>
                                        </a>
                                    <?php else: ?>
                                        </div>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button
                        class="partner-carousel-arrow partner-carousel-next"
                        id="partnerNext"
                        type="button"
                        aria-label="Show next company logos"
                    >
                        <svg class="icon" aria-hidden="true">
                            <use href="#i-arrow-right"></use>
                        </svg>
                    </button>

                    <div
                        class="partner-carousel-dots"
                        id="partnerDots"
                        aria-label="Company carousel positions"
                    ></div>
                </div>
            <?php else: ?>
                <div
                    class="partner-empty-state"
                    role="status"
                >
                    <div
                        class="partner-empty-mark"
                        aria-hidden="true"
                    >
                        <svg class="icon">
                            <use href="#i-building"></use>
                        </svg>
                    </div>

                    <div>
                        <h3>
                            Company logos are being prepared.
                        </h3>

                        <p>
                            Add an approved company name and logo to the partners
                            data set to publish it in this carousel.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>


    <!-- ================================================================
         FAQ
         Server-rendered — JS upgrades accordion behaviour
         ================================================================ -->

    <section
        class="section"
        id="faq"
        aria-labelledby="faqTitle"
    >
        <div class="section-inner faq-shell">
            <aside class="faq-intro">
                <span class="eyebrow light">
                    Frequently Asked Questions
                </span>

                <h2 id="faqTitle">
                    Clear answers before you need to ask.
                </h2>

                <p>
                    Important procurement information should be easy to find,
                    easy to scan and available before a customer needs support.
                </p>
            </aside>

            <div
                class="faq-accordion"
                id="faqAccordion"
            >
                <?php foreach ($faqs as $index => $faq): ?>
                    <?php
                    $faq = is_array($faq)
                        ? $faq
                        : [];

                    $panelId = 'faq-panel-' . $index;
                    $buttonId = 'faq-button-' . $index;

                    $open = $index === 0;
                    ?>

                    <article class="faq-item<?= $open ? ' open' : '' ?>">
                        <button
                            class="faq-head"
                            id="<?= e($buttonId) ?>"
                            type="button"
                            aria-expanded="<?= $open ? 'true' : 'false' ?>"
                            aria-controls="<?= e($panelId) ?>"
                        >
                            <h4>
                                <?= e((string) ($faq['question'] ?? '')) ?>
                            </h4>

                            <span
                                class="faq-plus"
                                aria-hidden="true"
                            >
                                +
                            </span>
                        </button>

                        <div
                            class="faq-body"
                            id="<?= e($panelId) ?>"
                            role="region"
                            aria-labelledby="<?= e($buttonId) ?>"
                        >
                            <p>
                                <?= e((string) ($faq['answer'] ?? '')) ?>
                            </p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <!-- ================================================================
         PEAK-END CONVERSION
         ================================================================ -->

    <section
        class="section section-dark vsl-end-section"
        aria-labelledby="endTitle"
    >
        <div class="section-inner">
            <div class="cta-grid">
                <div class="cta-copy">
                    <span class="eyebrow light">
                        Ready When You Are
                    </span>

                    <h2 id="endTitle">
                        You do not need a perfect specification to start.
                    </h2>

                    <p>
                        Send the product information you have. Our team can help
                        identify the next details required for a professional supply response.
                    </p>
                </div>

                <div class="vsl-end-actions">
                    <a
                        class="btn btn-primary"
                        href="#cta"
                    >
                        Request a Quote

                        <svg class="icon" aria-hidden="true">
                            <use href="#i-arrow-right"></use>
                        </svg>
                    </a>

                    <?php if ($phonePrimaryHref !== ''): ?>
                        <a
                            class="btn btn-secondary"
                            href="tel:<?= e($phonePrimaryHref) ?>"
                        >
                            <svg class="icon" aria-hidden="true">
                                <use href="#i-phone"></use>
                            </svg>

                            Call Varenz
                        </a>
                    <?php endif; ?>

                    <?php if ($contactEmail !== ''): ?>
                        <a
                            class="btn btn-secondary"
                            href="mailto:<?= e($contactEmail) ?>"
                        >
                            <svg class="icon" aria-hidden="true">
                                <use href="#i-mail"></use>
                            </svg>

                            Email
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

</main>


<!-- ====================================================================
     FAST CONTACT / ORDER / FEEDBACK HUB
     ==================================================================== -->

<aside
    class="vsl-action-hub"
    id="vslActionHub"
    aria-label="Fast Varenz contact options"
>
    <div
        class="vsl-action-hub__panel vsl-kit-003"
        id="vslActionHubPanel"
    >
        <div class="vsl-action-hub__head">
            <div>
                <span class="eyebrow">Varenz Support</span>
                <strong>How can we help?</strong>
            </div>

            <button
                id="vslActionHubClose"
                type="button"
                aria-label="Close Varenz support panel"
            >
                Close
            </button>
        </div>

        <a
            href="#cta"
            data-vsl-intent="quotation"
        >
            <svg class="icon" aria-hidden="true">
                <use href="#i-file"></use>
            </svg>

            <span>
                <strong>Request a Quote</strong>
                Send a product, image, list or reference.
            </span>
        </a>

        <a
            href="#cta"
            data-vsl-intent="order"
        >
            <svg class="icon" aria-hidden="true">
                <use href="#i-box"></use>
            </svg>

            <span>
                <strong>Start an Order</strong>
                Begin with the product details you have.
            </span>
        </a>

        <?php if ($whatsAppHref !== ''): ?>
            <a
                href="<?= e($whatsAppHref) ?>"
                target="_blank"
                rel="noopener noreferrer"
            >
                <svg class="icon" aria-hidden="true">
                    <use href="#i-message"></use>
                </svg>

                <span>
                    <strong>WhatsApp Varenz</strong>
                    Open a direct supply conversation.
                </span>
            </a>
        <?php endif; ?>

        <a
            href="#cta"
            data-vsl-intent="feedback"
        >
            <svg class="icon" aria-hidden="true">
                <use href="#i-feedback"></use>
            </svg>

            <span>
                <strong>Give Feedback</strong>
                Share a service concern or suggestion.
            </span>
        </a>
    </div>

    <button
        class="vsl-action-hub__toggle"
        id="vslActionHubToggle"
        type="button"
        aria-controls="vslActionHubPanel"
        aria-expanded="false"
    >
        <svg class="icon" aria-hidden="true">
            <use href="#i-message"></use>
        </svg>

        <span>Talk to Varenz</span>
    </button>
</aside>


<!-- ====================================================================
     FOOTER
     ==================================================================== -->

<footer
    class="vsl-footer"
    id="location"
>
    <div
        class="vsl-footer-bg"
        aria-hidden="true"
    ></div>

    <div class="vsl-footer-grid">
        <div class="vsl-footer-brand">
            <a
                class="vsl-footer-logo vsl-kit-005"
                href="#hero"
                aria-label="Varenz Supplies Ltd home"
            >
                <span
                    class="vsl-kit-005__border"
                    aria-hidden="true"
                ></span>

                <span
                    class="vsl-kit-005__trail"
                    aria-hidden="true"
                ></span>

                <span class="vsl-kit-005__logo-stage">
                    <img
                        class="vsl-kit-005__mark"
                        src="<?= e($imageAsset('images/logo/varenz-icon-logo-clean.png')) ?>"
                        alt=""
                        width="160"
                        height="160"
                        loading="lazy"
                        decoding="async"
                    >

                    <img
                        class="vsl-kit-005__wordmark"
                        src="<?= e($imageAsset('images/logo/varenz-word-logo-clean.png')) ?>"
                        alt=""
                        width="560"
                        height="180"
                        loading="lazy"
                        decoding="async"
                    >
                </span>

                <span class="vsl-kit-005__caption">
                    Reliable Supply. Better Care.
                </span>
            </a>

            <h3>
                Varenz Supplies Ltd
            </h3>

            <p>
                Specialised medical supply support, structured procurement
                communication and professional follow-up.
            </p>

            <div class="vsl-footer-request">
                <span>
                    Have a product name, image, reference or list?
                </span>

                <a href="#cta">
                    Send It to Varenz

                    <svg class="icon" aria-hidden="true">
                        <use href="#i-arrow-right"></use>
                    </svg>
                </a>
            </div>
        </div>

        <div class="vsl-footer-col">
            <h4>
                Explore
            </h4>

            <a href="<?= e(url('/products')) ?>">
                Products
            </a>

            <a href="#procedure">
                How We Supply
            </a>

            <a href="#organizations">
                Organisations
            </a>

            <a href="#partners">
                Partners
            </a>

            <a href="#why">
                Why Varenz
            </a>
        </div>

        <div class="vsl-footer-col">
            <h4>
                Work With Us
            </h4>

            <a href="#cta">
                Request a Quote
            </a>

            <a href="#opportunities">
                Institutional Partnerships
            </a>

            <a href="#opportunities">
                Supplier Collaboration
            </a>

            <a href="#opportunities">
                Professional Opportunities
            </a>

            <a href="#resources">
                Company Resources
            </a>

            <button
                id="footerSearch"
                type="button"
            >
                Search Website
            </button>
        </div>

        <div class="vsl-footer-col vsl-footer-contact">
            <h4>
                Contact
            </h4>

            <?php if ($contactLocation !== ''): ?>
                <span>
                    <?= e($contactLocation) ?>
                </span>
            <?php endif; ?>

            <?php if ($contactHours !== ''): ?>
                <span>
                    <?= e($contactHours) ?>
                </span>
            <?php endif; ?>

            <?php if ($phonePrimary !== '' && $phonePrimaryHref !== ''): ?>
                <a href="tel:<?= e($phonePrimaryHref) ?>">
                    <?= e($phonePrimary) ?>
                </a>
            <?php endif; ?>

            <?php if ($phoneSecondary !== '' && $phoneSecondaryHref !== ''): ?>
                <a href="tel:<?= e($phoneSecondaryHref) ?>">
                    <?= e($phoneSecondary) ?>
                </a>
            <?php endif; ?>

            <?php if ($contactEmail !== ''): ?>
                <a href="mailto:<?= e($contactEmail) ?>">
                    <?= e($contactEmail) ?>
                </a>
            <?php endif; ?>

            <a
                class="footer-support-pill"
                href="#cta"
            >
                Request a Quote
            </a>
        </div>
    </div>

    <div class="vsl-footer-bottom">
        <span>
            © <?= (int) date('Y') ?> Varenz Supplies Ltd. All Rights Reserved.
        </span>
    </div>
</footer>

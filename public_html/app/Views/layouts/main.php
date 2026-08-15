<?php
declare(strict_types=1);

use App\Core\Security;

/*
|--------------------------------------------------------------------------
| Varenz Supplies Ltd — Main Application Layout
|--------------------------------------------------------------------------
|
| Responsibilities:
|
| - Safe metadata and canonical URLs
| - Portable staging / production deployment
| - Social sharing metadata
| - SEO and structured business information
| - Theme bootstrapping
| - Frontend application bootstrap
| - Critical image preloading
| - Versioned CSS and JavaScript assets
| - Accessibility foundation
|
| DEPLOYMENT CONTRACT:
|
| Ordinary website resources must remain same-origin:
|
| - CSS
| - JavaScript
| - images
| - API routes
| - team routes
| - internal navigation
|
| They must NOT contain a hard-coded staging or production hostname.
|
| Absolute URLs are generated only where the web standard requires them:
|
| - canonical URL
| - Open Graph metadata
| - structured data
| - externally shared URLs
|
| This allows the same application files to move from staging to the main
| domain without editing PHP, JavaScript, CSS or image paths.
|
*/


/*
|--------------------------------------------------------------------------
| Basic Page Metadata
|--------------------------------------------------------------------------
*/

$pageTitle = isset($pageTitle)
    && is_string($pageTitle)
    && trim($pageTitle) !== ''
        ? trim($pageTitle)
        : (string) config(
            'name',
            'Varenz Supplies Ltd'
        );

$metaDescription = isset($metaDescription)
    && is_string($metaDescription)
    && trim($metaDescription) !== ''
        ? trim($metaDescription)
        : 'Specialised medical supplies, procurement support, product clarification and responsive healthcare supply coordination from Varenz Supplies Ltd.';

$publicSite = isset($site)
    && is_array($site)
        ? $site
        : [];

$heroCollection = isset($publicSite['hero'])
    && is_array($publicSite['hero'])
        ? $publicSite['hero']
        : [];

$firstHero = isset($heroCollection[0])
    && is_array($heroCollection[0])
        ? $heroCollection[0]
        : null;

$configuredContact = config(
    'contact',
    []
);

$contact = is_array(
    $configuredContact
)
    ? $configuredContact
    : [];

$companyName = trim(
    (string) config(
        'name',
        'Varenz Supplies Ltd'
    )
);

if ($companyName === '') {
    $companyName =
        'Varenz Supplies Ltd';
}

$contactPhone = trim(
    (string) (
        $contact['phone_primary']
        ?? ''
    )
);

$contactPhoneSecondary = trim(
    (string) (
        $contact['phone_secondary']
        ?? ''
    )
);

$contactEmail = trim(
    (string) (
        $contact['email']
        ?? ''
    )
);

$contactLocation = trim(
    (string) (
        $contact['location']
        ?? ''
    )
);

$contactHours = trim(
    (string) (
        $contact['hours']
        ?? ''
    )
);


/*
|--------------------------------------------------------------------------
| Canonical URL
|--------------------------------------------------------------------------
|
| PORTABLE DEPLOYMENT RULE
|
| A controller may provide $pageUrl, but the hostname from an older
| deployment must never become permanently embedded after deployment.
|
| Therefore:
|
| 1. We extract only the application path.
| 2. absolute_url() supplies the correct canonical origin.
|
| APP_URL may intentionally identify the production canonical domain.
|
| It does NOT control CSS, JavaScript, images, API calls or navigation.
|
*/

$requestUri = (string) (
    $_SERVER['REQUEST_URI']
    ?? '/'
);

$requestPath = parse_url(
    $requestUri,
    PHP_URL_PATH
);

if (
    !is_string($requestPath)
    || $requestPath === ''
) {
    $requestPath = '/';
}

if (
    !str_starts_with(
        $requestPath,
        '/'
    )
) {
    $requestPath =
        '/' . $requestPath;
}

$canonicalInput = (
    isset($pageUrl)
    && is_string($pageUrl)
    && trim($pageUrl) !== ''
)
    ? trim($pageUrl)
    : $requestPath;

/*
 * Even if a controller accidentally supplies:
 *
 * https://old-domain.example/team/example
 *
 * only:
 *
 * /team/example
 *
 * survives into the canonical generator.
 */
$canonicalPath = parse_url(
    $canonicalInput,
    PHP_URL_PATH
);

if (
    !is_string($canonicalPath)
    || $canonicalPath === ''
) {
    $canonicalPath =
        $requestPath;
}

if (
    !str_starts_with(
        $canonicalPath,
        '/'
    )
) {
    $canonicalPath =
        '/' . $canonicalPath;
}

$pageUrl = absolute_url(
    $canonicalPath
);


/*
|--------------------------------------------------------------------------
| Search Engine Indexing Policy
|--------------------------------------------------------------------------
|
| Production running on the configured canonical host may be indexed.
|
| A staging/testing deployment automatically becomes NOINDEX when:
|
| - APP_ENV is not production; or
| - APP_URL identifies another canonical hostname.
|
| Example:
|
| Temporary Hostinger domain + APP_URL pointing to production:
|
|     noindex
|
| Same files uploaded to the production domain:
|
|     index
|
| No layout code needs to change.
|
*/

$currentEnvironment = strtolower(
    trim(
        (string) config(
            'environment',
            'production'
        )
    )
);

$currentHost =
    request_host();

$configuredCanonicalBase =
    configured_base_url();

$configuredCanonicalHost =
    $configuredCanonicalBase !== ''
        ? strtolower(
            trim(
                (string) parse_url(
                    $configuredCanonicalBase,
                    PHP_URL_HOST
                )
            )
        )
        : '';

$currentHostWithoutPort = preg_replace(
    '/:\d+$/',
    '',
    strtolower(
        $currentHost
    )
);

if (
    !is_string(
        $currentHostWithoutPort
    )
) {
    $currentHostWithoutPort =
        strtolower(
            $currentHost
        );
}

$isConfiguredCanonicalHost = (
    $configuredCanonicalHost === ''
    || (
        $currentHostWithoutPort !== ''
        && hash_equals(
            $configuredCanonicalHost,
            $currentHostWithoutPort
        )
    )
);

$robotsMeta = (
    $currentEnvironment === 'production'
    && $isConfiguredCanonicalHost
)
    ? 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1'
    : 'noindex,nofollow,noarchive';


/*
|--------------------------------------------------------------------------
| CSRF
|--------------------------------------------------------------------------
|
| Reuse one token throughout the current page.
|
*/

$csrfToken = isset($csrf)
    && is_string($csrf)
    && $csrf !== ''
        ? $csrf
        : Security::csrfToken();


/*
|--------------------------------------------------------------------------
| Asset Versioning
|--------------------------------------------------------------------------
|
| filemtime() provides automatic browser-cache invalidation whenever a
| deployed CSS or JavaScript file changes.
|
| Because versioning uses filesystem timestamps rather than domain names,
| this remains completely portable.
|
*/

$assetVersion = static function (
    string $relativePath
): string {
    $relativePath = ltrim(
        trim(
            $relativePath
        ),
        '/'
    );

    if ($relativePath === '') {
        return '1';
    }

    $absolutePath =
        ROOT_PATH
        . '/'
        . $relativePath;

    if (
        !is_file(
            $absolutePath
        )
    ) {
        return '1';
    }

    $modified = filemtime(
        $absolutePath
    );

    return $modified !== false
        ? (string) $modified
        : '1';
};


/*
|--------------------------------------------------------------------------
| Public Asset Existence
|--------------------------------------------------------------------------
|
| Expects paths relative to:
|
| ROOT_PATH/assets/
|
| Example:
|
| images/hero/final/scene-01-overview.webp
|
*/

$assetExists = static function (
    string $relativePath
): bool {
    $relativePath = ltrim(
        trim(
            $relativePath
        ),
        '/'
    );

    if ($relativePath === '') {
        return false;
    }

    /*
     * Prevent callers from accidentally passing:
     *
     * assets/images/...
     *
     * when this helper specifically expects a path beneath /assets.
     */
    if (
        str_starts_with(
            $relativePath,
            'assets/'
        )
    ) {
        $relativePath = substr(
            $relativePath,
            strlen(
                'assets/'
            )
        );
    }

    if (
        $relativePath === ''
        || str_contains(
            $relativePath,
            "\0"
        )
    ) {
        return false;
    }

    $candidate =
        ROOT_PATH
        . '/assets/'
        . $relativePath;

    if (
        !is_file(
            $candidate
        )
    ) {
        return false;
    }

    /*
     * Verify the real file remains inside the public assets directory.
     */
    $assetsRoot = realpath(
        ROOT_PATH . '/assets'
    );

    $realCandidate = realpath(
        $candidate
    );

    if (
        $assetsRoot === false
        || $realCandidate === false
    ) {
        return false;
    }

    $allowedPrefix =
        rtrim(
            $assetsRoot,
            DIRECTORY_SEPARATOR
        )
        . DIRECTORY_SEPARATOR;

    return str_starts_with(
        $realCandidate,
        $allowedPrefix
    );
};


/*
|--------------------------------------------------------------------------
| Asset Versions
|--------------------------------------------------------------------------
*/

$cssVersion = $assetVersion(
    'assets/css/app.css'
);

$headerCssVersion = $assetVersion(
    'assets/css/header-island.css'
);

$brandCssVersion = $assetVersion(
    'assets/css/brand-color-correction.css'
);

$recoveryCssVersion = $assetVersion(
    'assets/css/recovery-2026.css'
);

$jsVersion = $assetVersion(
    'assets/js/app.js'
);

$headerJsVersion = $assetVersion(
    'assets/js/header-island.js'
);

$recoveryJsVersion = $assetVersion(
    'assets/js/recovery-2026.js'
);

$pageStyles = isset($pageStyles) && is_array($pageStyles)
    ? array_values(array_filter($pageStyles, static fn (mixed $value): bool => is_string($value) && trim($value) !== ''))
    : [];

$pageScripts = isset($pageScripts) && is_array($pageScripts)
    ? array_values(array_filter($pageScripts, static fn (mixed $value): bool => is_string($value) && trim($value) !== ''))
    : [];


/*
|--------------------------------------------------------------------------
| Priority Hero Media
|--------------------------------------------------------------------------
*/

$firstHeroDesktop = is_array(
    $firstHero
)
    ? trim(
        (string) (
            $firstHero['desktop_image']
            ?? ''
        )
    )
    : '';

$firstHeroMobile = is_array(
    $firstHero
)
    ? trim(
        (string) (
            $firstHero['mobile_image']
            ?? ''
        )
    )
    : '';

$firstHeroVisual = is_array(
    $firstHero
)
    ? trim(
        (string) (
            $firstHero['visual_image']
            ?? ''
        )
    )
    : '';


/*
|--------------------------------------------------------------------------
| Social Preview
|--------------------------------------------------------------------------
|
| Open Graph/Twitter images require absolute URLs.
|
| Normal page images continue using asset() and therefore remain same-origin.
|
*/

$defaultOgRelative =
    'images/social/vsl-og.png';

$ogFallbackRelative =
    'images/logo/varenz-icon-logo-clean.png';

$ogImageRelative = $assetExists(
    $defaultOgRelative
)
    ? $defaultOgRelative
    : $ogFallbackRelative;

$ogImage = absolute_url(
    'assets/'
    . ltrim(
        $ogImageRelative,
        '/'
    )
);


/*
|--------------------------------------------------------------------------
| Frontend Application Bootstrap
|--------------------------------------------------------------------------
|
| IMPORTANT:
|
| baseUrl remains same-origin.
|
| It deliberately uses url('/') rather than absolute_url('/').
|
| On staging:
|
| /
|
| belongs to the staging domain.
|
| On production:
|
| /
|
| belongs to the production domain.
|
| This is what keeps AJAX/API calls portable.
|
| JSON_HEX_* flags are important because this JSON is embedded directly
| inside a <script> element.
|
*/

try {
    $frontendConfig = json_encode(
        [
            'baseUrl' =>
                rtrim(
                    url('/'),
                    '/'
                )
                . '/',

            'csrf' =>
                $csrfToken,

            'site' =>
                $publicSite,

            'initialTeamSlug' => (
                isset(
                    $initialTeamSlug
                )
                && is_string(
                    $initialTeamSlug
                )
                && trim(
                    $initialTeamSlug
                ) !== ''
            )
                ? trim(
                    $initialTeamSlug
                )
                : null,
        ],
        JSON_UNESCAPED_SLASHES
        | JSON_UNESCAPED_UNICODE
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
        | JSON_THROW_ON_ERROR
    );
} catch (\JsonException) {
    $frontendConfig = json_encode(
        [
            'baseUrl' =>
                rtrim(
                    url('/'),
                    '/'
                )
                . '/',

            'csrf' =>
                $csrfToken,

            'site' =>
                [],

            'initialTeamSlug' =>
                null,
        ],
        JSON_UNESCAPED_SLASHES
        | JSON_UNESCAPED_UNICODE
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    ) ?: '{}';
}


/*
|--------------------------------------------------------------------------
| Structured Business Data
|--------------------------------------------------------------------------
|
| Search engines require absolute URLs here.
|
| Again, these absolute URLs are metadata only. They do not control where
| the browser loads application CSS, JavaScript or ordinary images.
|
*/

$featuredCollection = isset($publicSite['featured'])
    && is_array($publicSite['featured'])
        ? $publicSite['featured']
        : [];

$catalogItems = [];
$knownTopics = [];

foreach ($featuredCollection as $featuredItem) {
    if (!is_array($featuredItem)) {
        continue;
    }

    $featuredTitle = trim(
        (string) (
            $featuredItem['title']
            ?? ''
        )
    );

    if ($featuredTitle === '') {
        continue;
    }

    $knownTopics[] =
        $featuredTitle;

    $catalogItems[] = [
        '@type' =>
            'Product',

        'name' =>
            $featuredTitle,

        'description' =>
            trim(
                (string) (
                    $featuredItem['text']
                    ?? ''
                )
            ),
    ];
}

$siteRootUrl = absolute_url('/');
$organizationId =
    rtrim($siteRootUrl, '/')
    . '/#organization';
$websiteId =
    rtrim($siteRootUrl, '/')
    . '/#website';
$webPageId =
    $pageUrl
    . '#webpage';

$contactPoint = array_filter(
    [
        '@type' =>
            'ContactPoint',

        'telephone' =>
            $contactPhone !== ''
                ? $contactPhone
                : null,

        'contactType' =>
            'customer support',

        'email' =>
            $contactEmail !== ''
                ? $contactEmail
                : null,

        'areaServed' =>
            'UG',

        'availableLanguage' => [
            'English',
        ],
    ],
    static fn (mixed $value): bool =>
        $value !== null
);

$organizationData = array_filter(
    [
        '@type' =>
            'Organization',

        '@id' =>
            $organizationId,

        'name' =>
            $companyName,

        'url' =>
            $siteRootUrl,

        'logo' => [
            '@type' =>
                'ImageObject',

            'url' =>
                absolute_url(
                    'assets/images/logo/varenz-icon-logo-clean.png'
                ),
        ],

        'description' =>
            $metaDescription,

        'slogan' =>
            'Reliable Supply. Better Care.',

        'email' =>
            $contactEmail !== ''
                ? $contactEmail
                : null,

        'telephone' =>
            $contactPhone !== ''
                ? $contactPhone
                : null,

        'address' =>
            $contactLocation !== ''
                ? [
                    '@type' =>
                        'PostalAddress',

                    'addressLocality' =>
                        'Kampala',

                    'addressCountry' =>
                        'UG',

                    'streetAddress' =>
                        $contactLocation,
                ]
                : null,

        'contactPoint' => [
            $contactPoint,
        ],

        'areaServed' => [
            '@type' =>
                'Country',

            'name' =>
                'Uganda',
        ],

        'knowsAbout' =>
            $knownTopics !== []
                ? array_values(
                    array_unique(
                        $knownTopics
                    )
                )
                : null,

        'hasOfferCatalog' =>
            $catalogItems !== []
                ? [
                    '@type' =>
                        'OfferCatalog',

                    'name' =>
                        'Specialised medical supply solutions',

                    'itemListElement' =>
                        $catalogItems,
                ]
                : null,
    ],
    static fn (mixed $value): bool =>
        $value !== null
);

$structuredData = [
    '@context' =>
        'https://schema.org',

    '@graph' => [
        $organizationData,
        [
            '@type' =>
                'WebSite',

            '@id' =>
                $websiteId,

            'url' =>
                $siteRootUrl,

            'name' =>
                $companyName,

            'publisher' => [
                '@id' =>
                    $organizationId,
            ],

            'inLanguage' =>
                'en-UG',
        ],
        [
            '@type' =>
                'WebPage',

            '@id' =>
                $webPageId,

            'url' =>
                $pageUrl,

            'name' =>
                $pageTitle,

            'description' =>
                $metaDescription,

            'isPartOf' => [
                '@id' =>
                    $websiteId,
            ],

            'about' => [
                '@id' =>
                    $organizationId,
            ],

            'inLanguage' =>
                'en-UG',
        ],
    ],
];

try {
    $structuredDataJson = json_encode(
        $structuredData,
        JSON_UNESCAPED_SLASHES
        | JSON_UNESCAPED_UNICODE
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
        | JSON_THROW_ON_ERROR
    );
} catch (\JsonException) {
    $structuredDataJson =
        '{}';
}


/*
|--------------------------------------------------------------------------
| Application JavaScript
|--------------------------------------------------------------------------
*/

$includeAppJs = (
    $includeAppJs
    ?? true
) === true;

?>
<!doctype html>

<html
    lang="en"
    data-theme="light"
>
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover"
    >

    <meta
        name="theme-color"
        content="#31459A"
    >

    <meta
        name="color-scheme"
        content="light dark"
    >

    <meta
        name="description"
        content="<?= e($metaDescription) ?>"
    >

    <meta
        name="application-name"
        content="<?= e($companyName) ?>"
    >

    <meta
        name="author"
        content="<?= e($companyName) ?>"
    >

    <meta
        name="robots"
        content="<?= e($robotsMeta) ?>"
    >

    <meta
        name="referrer"
        content="strict-origin-when-cross-origin"
    >

    <meta
        http-equiv="x-content-type-options"
        content="nosniff"
    >

    <link
        rel="canonical"
        href="<?= e($pageUrl) ?>"
    >

    <link
        rel="alternate"
        hreflang="en-UG"
        href="<?= e($pageUrl) ?>"
    >

    <link
        rel="alternate"
        hreflang="x-default"
        href="<?= e($pageUrl) ?>"
    >

    <title><?= e($pageTitle) ?></title>


    <!-- ================================================================
         Open Graph
         ================================================================ -->

    <meta
        property="og:type"
        content="website"
    >

    <meta
        property="og:site_name"
        content="<?= e($companyName) ?>"
    >

    <meta
        property="og:locale"
        content="en_UG"
    >

    <meta
        property="og:title"
        content="<?= e($pageTitle) ?>"
    >

    <meta
        property="og:description"
        content="<?= e($metaDescription) ?>"
    >

    <meta
        property="og:url"
        content="<?= e($pageUrl) ?>"
    >

    <meta
        property="og:image"
        content="<?= e($ogImage) ?>"
    >

    <?php if (
        str_starts_with(
            strtolower(
                $ogImage
            ),
            'https://'
        )
    ): ?>
        <meta
            property="og:image:secure_url"
            content="<?= e($ogImage) ?>"
        >
    <?php endif; ?>

    <meta
        property="og:image:alt"
        content="Varenz Supplies Ltd specialised medical supply support in Uganda"
    >

    <meta
        property="og:image:width"
        content="1200"
    >

    <meta
        property="og:image:height"
        content="630"
    >


    <!-- ================================================================
         Twitter / X
         ================================================================ -->

    <meta
        name="twitter:card"
        content="summary_large_image"
    >

    <meta
        name="twitter:title"
        content="<?= e($pageTitle) ?>"
    >

    <meta
        name="twitter:description"
        content="<?= e($metaDescription) ?>"
    >

    <meta
        name="twitter:image"
        content="<?= e($ogImage) ?>"
    >


    <!-- ================================================================
         Application / Security Integration
         ================================================================ -->

    <meta
        name="csrf-token"
        content="<?= e($csrfToken) ?>"
    >


    <!-- ================================================================
         Icons
         ================================================================ -->

    <link
        rel="icon"
        type="image/png"
        href="<?= e(
            asset(
                'images/logo/favicon-vsl.png'
            )
        ) ?>"
    >

    <link
        rel="apple-touch-icon"
        href="<?= e(
            asset(
                'images/logo/favicon-vsl.png'
            )
        ) ?>"
    >


    <!-- ================================================================
         Network Hints
         ================================================================ -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        rel="dns-prefetch"
        href="//fonts.googleapis.com"
    >

    <link
        rel="dns-prefetch"
        href="//fonts.gstatic.com"
    >


    <!-- ================================================================
         Typography
         ================================================================ -->

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    <!-- ================================================================
         Priority Hero Media
         ================================================================ -->

    <?php if (
        $firstHeroDesktop !== ''
        && $assetExists(
            $firstHeroDesktop
        )
    ): ?>
        <link
            rel="preload"
            as="image"
            href="<?= e(
                asset(
                    $firstHeroDesktop
                )
            ) ?>"
            media="(min-width: 761px)"
            fetchpriority="high"
        >
    <?php endif; ?>

    <?php if (
        $firstHeroMobile !== ''
        && $assetExists(
            $firstHeroMobile
        )
    ): ?>
        <link
            rel="preload"
            as="image"
            href="<?= e(
                asset(
                    $firstHeroMobile
                )
            ) ?>"
            media="(max-width: 760px)"
            fetchpriority="high"
        >
    <?php endif; ?>

    <?php if (
        $firstHeroVisual !== ''
        && $assetExists(
            $firstHeroVisual
        )
    ): ?>
        <link
            rel="preload"
            as="image"
            href="<?= e(
                asset(
                    $firstHeroVisual
                )
            ) ?>"
            fetchpriority="high"
        >
    <?php endif; ?>


    <!-- ================================================================
         Core Design System
         ================================================================
         SAME-ORIGIN ASSET.
         No environment hostname is embedded.
         ================================================================ -->

    <link
        rel="stylesheet"
        href="<?= e(
            asset(
                'css/app.css'
            )
        ) ?>?v=<?= e(
            $cssVersion
        ) ?>"
    >

    <!-- ================================================================
         Header / Navigation
         ================================================================
         SAME-ORIGIN ASSET.
         ================================================================ -->

    <link
        rel="stylesheet"
        href="<?= e(
            asset(
                'css/header-island.css'
            )
        ) ?>?v=<?= e(
            $headerCssVersion
        ) ?>"
    >

    <!-- ================================================================
         Final Brand Governance
         ================================================================
         SAME-ORIGIN ASSET.
         ================================================================ -->

    <link
        rel="stylesheet"
        href="<?= e(
            asset(
                'css/brand-color-correction.css'
            )
        ) ?>?v=<?= e(
            $brandCssVersion
        ) ?>"
    >

    <link
        rel="stylesheet"
        href="<?= e(asset('css/recovery-2026.css')) ?>?v=<?= e($recoveryCssVersion) ?>"
    >

    <?php foreach ($pageStyles as $pageStyle): ?>
        <?php if ($assetExists($pageStyle)): ?>
            <link
                rel="stylesheet"
                href="<?= e(asset($pageStyle)) ?>?v=<?= e($assetVersion('assets/' . ltrim($pageStyle, '/'))) ?>"
            >
        <?php endif; ?>
    <?php endforeach; ?>


    <!-- ================================================================
         Theme Restoration
         ================================================================
         Runs before paint to minimise visible theme flashing.
         ================================================================ -->

    <script>
    (() => {
        'use strict';

        try {
            const storedTheme =
                localStorage.getItem(
                    'vsl-theme'
                );

            if (
                storedTheme === 'light'
                || storedTheme === 'dark'
            ) {
                document.documentElement.dataset.theme =
                    storedTheme;
            }
        } catch (_) {
            /*
             * localStorage can be unavailable in privacy-restricted
             * browser contexts.
             *
             * Light mode remains the safe default.
             */
        }
    })();
    </script>


    <!-- ================================================================
         Frontend Configuration
         ================================================================
         baseUrl is SAME ORIGIN.
         ================================================================ -->

    <script>
    window.VARENZ_APP = <?= $frontendConfig ?>;
    </script>


    <!-- ================================================================
         Structured Business Data
         ================================================================ -->

    <script type="application/ld+json">
    <?= $structuredDataJson ?>
    </script>
</head>

<body>
    <a
        class="skip-link"
        href="#mainContent"
    >
        Skip to main content
    </a>

    <noscript>
        <div
            class="vsl-noscript"
            role="status"
        >
            Some interactive features require JavaScript, but the main Varenz
            product, procurement and contact information remains available.
        </div>
    </noscript>


    <!-- ================================================================
         Page Content
         ================================================================ -->

    <?= $content ?>


    <!-- ================================================================
         Toast Announcements
         ================================================================ -->

    <div
        class="toast-stack"
        id="toastStack"
        role="status"
        aria-live="polite"
        aria-atomic="false"
    ></div>


    <?php if ($includeAppJs): ?>

        <!--
        ====================================================================
        Navigation Controller
        ====================================================================
        SAME-ORIGIN SCRIPT.
        ====================================================================
        -->

        <script
            src="<?= e(
                asset(
                    'js/header-island.js'
                )
            ) ?>?v=<?= e(
                $headerJsVersion
            ) ?>"
            defer
        ></script>


        <!--
        ====================================================================
        Main Application Controller
        ====================================================================
        SAME-ORIGIN SCRIPT.
        ====================================================================
        -->

        <script
            src="<?= e(
                asset(
                    'js/app.js'
                )
            ) ?>?v=<?= e(
                $jsVersion
            ) ?>"
            defer
        ></script>

        <script
            src="<?= e(asset('js/recovery-2026.js')) ?>?v=<?= e($recoveryJsVersion) ?>"
            defer
        ></script>

    <?php endif; ?>

    <?php foreach ($pageScripts as $pageScript): ?>
        <?php if ($assetExists($pageScript)): ?>
            <script
                src="<?= e(asset($pageScript)) ?>?v=<?= e($assetVersion('assets/' . ltrim($pageScript, '/'))) ?>"
                defer
            ></script>
        <?php endif; ?>
    <?php endforeach; ?>
</body>
</html>

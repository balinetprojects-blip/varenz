<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Varenz Supplies Ltd — Public Application Entrypoint
|--------------------------------------------------------------------------
|
| Responsibilities:
|
| - Load the hardened application bootstrap.
| - Capture the current HTTP request.
| - Register all existing public routes.
| - Apply safe baseline response headers.
| - Dispatch the request through the existing Router.
| - Prevent internal exception details leaking in production.
|
| Important:
|
| Do not place page markup, frontend assets, database logic or business
| logic in this file. Those responsibilities remain in their existing
| controllers, models, views and services.
|
*/

require __DIR__ . '/config/bootstrap.php';

use App\Controllers\ApiController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use App\Controllers\ProductController;
use App\Controllers\TeamController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;


/*
|--------------------------------------------------------------------------
| Environment
|--------------------------------------------------------------------------
*/

$environment = strtolower(
    trim(
        (string) config(
            'environment',
            'production'
        )
    )
);

$debug = (bool) config(
    'debug',
    false
);

$isProduction = $environment === 'production';


/*
|--------------------------------------------------------------------------
| Baseline Security Headers
|--------------------------------------------------------------------------
|
| These are intentionally conservative.
|
| We are NOT adding a strict Content-Security-Policy here yet because the
| current website legitimately uses:
|
| - inline theme/bootstrap scripts,
| - Google Fonts,
| - data-driven images,
| - frontend AJAX,
| - externally opened WhatsApp links.
|
| A CSP should only be introduced once every dependency has been audited.
|
*/

if (!headers_sent()) {
    header(
        'X-Content-Type-Options: nosniff'
    );

    header(
        'X-Frame-Options: SAMEORIGIN'
    );

    header(
        'Referrer-Policy: strict-origin-when-cross-origin'
    );

    header(
        'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()'
    );

    /*
     * Avoid exposing unnecessary PHP implementation details.
     */
    header_remove(
        'X-Powered-By'
    );

    /*
     * HSTS is safe only when the current deployment is genuinely HTTPS.
     *
     * request_is_https() is supplied by the application bootstrap.
     */
    if (
        function_exists('request_is_https')
        && request_is_https()
        && $isProduction
    ) {
        header(
            'Strict-Transport-Security: max-age=31536000; includeSubDomains'
        );
    }
}


/*
|--------------------------------------------------------------------------
| Request
|--------------------------------------------------------------------------
*/

$request = Request::capture();


/*
|--------------------------------------------------------------------------
| Router
|--------------------------------------------------------------------------
*/

$router = new Router(
    $request
);


/*
|--------------------------------------------------------------------------
| Public Website Routes
|--------------------------------------------------------------------------
*/

$router->get(
    '/',
    [
        HomeController::class,
        'index',
    ]
);

$router->get(
    '/products',
    [
        ProductController::class,
        'index',
    ]
);

$router->get(
    '/products/{slug}',
    [
        ProductController::class,
        'show',
    ]
);

$router->get('/about', [PageController::class, 'about']);
$router->get('/procurement', [PageController::class, 'procurement']);
$router->get('/quality-compliance', [PageController::class, 'qualityCompliance']);
$router->get('/partners', [PageController::class, 'partners']);
$router->get('/resources', [PageController::class, 'resources']);
$router->get('/faq', [PageController::class, 'faq']);
$router->get('/contact', [PageController::class, 'contact']);

$router->get(
    '/team/{slug}',
    [
        TeamController::class,
        'show',
    ]
);


/*
|--------------------------------------------------------------------------
| Public API — Content
|--------------------------------------------------------------------------
*/

$router->get(
    '/api/challenges/{id}',
    [
        ApiController::class,
        'challenge',
    ]
);

$router->get(
    '/api/categories/{id}',
    [
        ApiController::class,
        'category',
    ]
);

$router->get(
    '/api/procurement/{id}',
    [
        ApiController::class,
        'procurement',
    ]
);

$router->get(
    '/api/organizations/{id}',
    [
        ApiController::class,
        'organization',
    ]
);

$router->get(
    '/api/team',
    [
        ApiController::class,
        'team',
    ]
);

$router->get(
    '/api/search',
    [
        ApiController::class,
        'search',
    ]
);

$router->get(
    '/api/products',
    [
        ApiController::class,
        'products',
    ]
);

$router->get(
    '/api/products/{slug}',
    [
        ApiController::class,
        'product',
    ]
);


/*
|--------------------------------------------------------------------------
| Public API — Submissions
|--------------------------------------------------------------------------
*/

$router->post(
    '/api/submissions',
    [
        ApiController::class,
        'submit',
    ]
);


/*
|--------------------------------------------------------------------------
| Dispatch
|--------------------------------------------------------------------------
|
| The Router remains responsible for normal route-level 404 handling.
|
| This final boundary catches unexpected failures so production visitors
| never receive stack traces, filesystem paths or internal PHP details.
|
*/

try {
    $router->dispatch();
} catch (\Throwable $exception) {
    /*
     * Always record unexpected failures through PHP's configured server
     * error log. Do not expose the exception publicly in production.
     */
    error_log(
        sprintf(
            '[VARENZ] Unhandled application exception: %s in %s:%d',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        )
    );

    /*
     * During an explicitly configured non-production debug session,
     * allow a useful development response without affecting production.
     */
    if (
        !$isProduction
        && $debug
    ) {
        if (!headers_sent()) {
            http_response_code(
                500
            );

            header(
                'Content-Type: text/plain; charset=UTF-8'
            );

            header(
                'Cache-Control: no-store, private'
            );
        }

        echo "Varenz development error\n\n";
        echo $exception->getMessage();
        echo "\n\n";
        echo $exception->getFile();
        echo ':';
        echo $exception->getLine();

        exit;
    }

    /*
     * Production-safe response.
     */
    if (!headers_sent()) {
        http_response_code(
            500
        );

        header(
            'Content-Type: text/html; charset=UTF-8'
        );

        header(
            'Cache-Control: no-store, private'
        );
    }

    echo '<!doctype html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<meta name="robots" content="noindex,nofollow">';
    echo '<title>Service temporarily unavailable | Varenz Supplies Ltd</title>';
    echo '</head>';
    echo '<body>';
    echo '<main>';
    echo '<h1>Service temporarily unavailable</h1>';
    echo '<p>Varenz Supplies Ltd could not complete this request. Please try again shortly.</p>';
    echo '</main>';
    echo '</body>';
    echo '</html>';

    exit;
}

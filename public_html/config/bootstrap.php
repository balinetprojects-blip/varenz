<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Varenz Supplies Ltd — Application Bootstrap
|--------------------------------------------------------------------------
|
| PORTABLE DEPLOYMENT CONTRACT
|
| This application must run unchanged on:
|
| - Hostinger temporary domains
| - varenzsupplies.com
| - staging environments
| - local development
| - future approved domains
|
| Application files must NEVER contain a deployment-specific hostname for
| ordinary routes, CSS, JavaScript, images or API requests.
|
| URL responsibilities:
|
| url()          -> same-origin application URL
| asset()        -> same-origin public asset URL
| absolute_url() -> absolute URL only where explicitly required
|
| Therefore transferring the same application files between environments
| does not require rewriting URLs.
|
*/


/*
|--------------------------------------------------------------------------
| Project Root
|--------------------------------------------------------------------------
*/

if (!defined('ROOT_PATH')) {
    define(
        'ROOT_PATH',
        dirname(__DIR__)
    );
}


/*
|--------------------------------------------------------------------------
| Application Configuration
|--------------------------------------------------------------------------
*/

$configFile = __DIR__ . '/app.php';

if (
    !is_file($configFile)
    || !is_readable($configFile)
) {
    throw new RuntimeException(
        'Application configuration could not be loaded.'
    );
}

$config = require $configFile;

if (!is_array($config)) {
    throw new RuntimeException(
        'Application configuration must return an array.'
    );
}

if (!defined('APP_CONFIG')) {
    define(
        'APP_CONFIG',
        $config
    );
}


/*
|--------------------------------------------------------------------------
| Runtime Environment
|--------------------------------------------------------------------------
*/

$environment = strtolower(
    trim(
        (string) (
            APP_CONFIG['environment']
            ?? 'production'
        )
    )
);

$debug = filter_var(
    APP_CONFIG['debug'] ?? false,
    FILTER_VALIDATE_BOOL
);

ini_set(
    'default_charset',
    'UTF-8'
);

ini_set(
    'log_errors',
    '1'
);

if ($environment === 'production') {
    ini_set(
        'display_errors',
        '0'
    );

    ini_set(
        'display_startup_errors',
        '0'
    );
} elseif ($debug) {
    ini_set(
        'display_errors',
        '1'
    );

    ini_set(
        'display_startup_errors',
        '1'
    );
}


/*
|--------------------------------------------------------------------------
| Timezone
|--------------------------------------------------------------------------
*/

$timezone = trim(
    (string) (
        APP_CONFIG['timezone']
        ?? 'Africa/Kampala'
    )
);

if ($timezone === '') {
    $timezone = 'Africa/Kampala';
}

try {
    date_default_timezone_set(
        $timezone
    );
} catch (ValueError) {
    date_default_timezone_set(
        'Africa/Kampala'
    );
}


/*
|--------------------------------------------------------------------------
| Configuration Helper
|--------------------------------------------------------------------------
|
| Examples:
|
| config()
| config('contact.email')
| config('upload.max_bytes', 8388608)
|
*/

function config(
    ?string $key = null,
    mixed $default = null
): mixed {
    $value = APP_CONFIG;

    if (
        $key === null
        || $key === ''
    ) {
        return $value;
    }

    foreach (
        explode('.', $key)
        as $segment
    ) {
        if (
            $segment === ''
            || !is_array($value)
            || !array_key_exists(
                $segment,
                $value
            )
        ) {
            return $default;
        }

        $value =
            $value[$segment];
    }

    return $value;
}


/*
|--------------------------------------------------------------------------
| Configured Absolute Base URL
|--------------------------------------------------------------------------
|
| APP_URL remains optional.
|
| IMPORTANT:
|
| APP_URL is NOT used by url() or asset().
|
| It is only available to absolute_url() for contexts where an absolute
| address is genuinely required.
|
| Therefore a wrong or stale APP_URL can never redirect CSS, JavaScript,
| images or ordinary application links to another deployment.
|
*/

function configured_base_url(): string
{
    $configured = trim(
        (string) config(
            'base_url',
            ''
        )
    );

    if ($configured === '') {
        return '';
    }

    $configured = preg_replace(
        '/[\x00-\x1F\x7F]/',
        '',
        $configured
    ) ?? '';

    if ($configured === '') {
        return '';
    }

    $validated = filter_var(
        $configured,
        FILTER_VALIDATE_URL
    );

    if ($validated === false) {
        return '';
    }

    $scheme = strtolower(
        (string) parse_url(
            $validated,
            PHP_URL_SCHEME
        )
    );

    $host = trim(
        (string) parse_url(
            $validated,
            PHP_URL_HOST
        )
    );

    if (
        !in_array(
            $scheme,
            [
                'http',
                'https',
            ],
            true
        )
        || $host === ''
    ) {
        return '';
    }

    return rtrim(
        $validated,
        '/'
    );
}


/*
|--------------------------------------------------------------------------
| HTTPS Detection
|--------------------------------------------------------------------------
|
| We do not blindly trust X-Forwarded-Proto or other client-controllable
| forwarding headers.
|
| HTTPS is determined from the web-server environment.
|
*/

function request_is_https(): bool
{
    $https = strtolower(
        trim(
            (string) (
                $_SERVER['HTTPS']
                ?? ''
            )
        )
    );

    if (
        $https !== ''
        && $https !== 'off'
        && $https !== '0'
    ) {
        return true;
    }

    $scheme = strtolower(
        trim(
            (string) (
                $_SERVER['REQUEST_SCHEME']
                ?? ''
            )
        )
    );

    if ($scheme === 'https') {
        return true;
    }

    $port = (string) (
        $_SERVER['SERVER_PORT']
        ?? ''
    );

    return $port === '443';
}


/*
|--------------------------------------------------------------------------
| Current Request Host
|--------------------------------------------------------------------------
|
| Used only by absolute_url() when no configured APP_URL is available.
|
| Ordinary assets/routes never depend on this function.
|
*/

function request_host(): string
{
    $host = trim(
        (string) (
            $_SERVER['HTTP_HOST']
            ?? $_SERVER['SERVER_NAME']
            ?? ''
        )
    );

    if ($host === '') {
        return '';
    }

    /*
     * Remove control characters.
     */
    $host = preg_replace(
        '/[\x00-\x1F\x7F]/',
        '',
        $host
    ) ?? '';

    if ($host === '') {
        return '';
    }

    /*
     * Allow:
     *
     * example.com
     * sub.example.com
     * example.com:8080
     *
     * IPv6 literals are not currently required for this deployment.
     */
    if (
        preg_match(
            '/^[A-Za-z0-9.-]+(?::[0-9]{1,5})?$/D',
            $host
        ) !== 1
    ) {
        return '';
    }

    return strtolower(
        $host
    );
}


/*
|--------------------------------------------------------------------------
| Application Base Path
|--------------------------------------------------------------------------
|
| Supports:
|
| https://example.com/
|
| and:
|
| https://example.com/varenz/
|
| WITHOUT embedding the hostname.
|
*/

function base_path_from_request(): string
{
    $scriptName = str_replace(
        '\\',
        '/',
        (string) (
            $_SERVER['SCRIPT_NAME']
            ?? '/index.php'
        )
    );

    $scriptPath = parse_url(
        $scriptName,
        PHP_URL_PATH
    );

    if (
        !is_string($scriptPath)
        || $scriptPath === ''
    ) {
        $scriptPath =
            '/index.php';
    }

    $directory = str_replace(
        '\\',
        '/',
        dirname($scriptPath)
    );

    if (
        $directory === '.'
        || $directory === '/'
        || $directory === ''
    ) {
        return '';
    }

    $directory =
        '/' . trim(
            $directory,
            '/'
        );

    return $directory === '/'
        ? ''
        : rtrim(
            $directory,
            '/'
        );
}


/*
|--------------------------------------------------------------------------
| Same-Origin Application URL
|--------------------------------------------------------------------------
|
| THIS IS THE DEFAULT URL GENERATOR.
|
| It deliberately does not use APP_URL.
|
| Examples on ANY domain:
|
| url()
|      -> /
|
| url('team')
|      -> /team
|
| url('/api/search')
|      -> /api/search
|
| If installed in /varenz:
|
| url('/api/search')
|      -> /varenz/api/search
|
| This makes the codebase portable between temporary, staging and production
| domains without changing application files.
|
*/

function url(
    string $path = ''
): string {
    $base =
        base_path_from_request();

    $path = trim(
        $path
    );

    if (
        $path === ''
        || $path === '/'
    ) {
        return $base === ''
            ? '/'
            : $base . '/';
    }

    /*
     * Application-local URLs only.
     */
    $cleanPath =
        '/' . ltrim(
            $path,
            '/'
        );

    return $base === ''
        ? $cleanPath
        : $base . $cleanPath;
}


/*
|--------------------------------------------------------------------------
| Same-Origin Asset URL
|--------------------------------------------------------------------------
|
| Examples:
|
| asset('css/app.css')
|      -> /assets/css/app.css
|
| asset('images/hero/example.webp')
|      -> /assets/images/hero/example.webp
|
| asset('assets/images/logo.png')
|      -> /assets/images/logo.png
|
| This helper NEVER embeds a domain.
|
*/

function asset(
    string $path
): string {
    $path = trim(
        $path
    );

    $path = preg_replace(
        '/[\x00-\x1F\x7F]/',
        '',
        $path
    ) ?? '';

    $path = ltrim(
        $path,
        '/'
    );

    /*
     * Prevent:
     *
     * /assets/assets/...
     */
    if (
        str_starts_with(
            $path,
            'assets/'
        )
    ) {
        $path = substr(
            $path,
            strlen('assets/')
        );
    }

    return url(
        'assets/' . $path
    );
}


/*
|--------------------------------------------------------------------------
| Absolute URL
|--------------------------------------------------------------------------
|
| Use this ONLY where an absolute address is actually required:
|
| - canonical metadata
| - Open Graph URLs
| - structured data URLs
| - externally shared links
| - email links
|
| Normal CSS, JS, images, API calls and navigation should use url()/asset().
|
*/

function absolute_url(
    string $path = ''
): string {
    $localUrl = url(
        $path
    );

    /*
     * Prefer explicitly configured APP_URL for canonical/external contexts.
     */
    $configured =
        configured_base_url();

    if ($configured !== '') {
        $configuredParts =
            parse_url(
                $configured
            );

        $configuredPath = '';

        if (
            is_array(
                $configuredParts
            )
            && isset(
                $configuredParts['path']
            )
        ) {
            $configuredPath = rtrim(
                (string) $configuredParts['path'],
                '/'
            );
        }

        $localPath = $localUrl;

        if (
            $configuredPath !== ''
            && str_starts_with(
                $localPath,
                $configuredPath . '/'
            )
        ) {
            $localPath = substr(
                $localPath,
                strlen(
                    $configuredPath
                )
            );
        }

        return rtrim(
            $configured,
            '/'
        ) . '/'
        . ltrim(
            $localPath,
            '/'
        );
    }

    /*
     * If APP_URL is intentionally unset, derive the current origin.
     */
    $host =
        request_host();

    if ($host === '') {
        /*
         * A relative URL is safer than inventing a hostname.
         */
        return $localUrl;
    }

    $scheme =
        request_is_https()
            ? 'https'
            : 'http';

    return $scheme
        . '://'
        . $host
        . $localUrl;
}


/*
|--------------------------------------------------------------------------
| PHP Session Hardening
|--------------------------------------------------------------------------
*/

if (
    session_status()
    === PHP_SESSION_NONE
) {
    ini_set(
        'session.use_strict_mode',
        '1'
    );

    ini_set(
        'session.use_only_cookies',
        '1'
    );

    ini_set(
        'session.use_cookies',
        '1'
    );

    ini_set(
        'session.use_trans_sid',
        '0'
    );

    ini_set(
        'session.cookie_httponly',
        '1'
    );

    ini_set(
        'session.cookie_samesite',
        'Lax'
    );

    $secureCookie =
        request_is_https();

    ini_set(
        'session.cookie_secure',
        $secureCookie
            ? '1'
            : '0'
    );

    /*
     * Use the application installation path for the session cookie.
     *
     * Root installation:
     *
     * /
     *
     * Subdirectory installation:
     *
     * /varenz/
     */
    $cookiePath =
        base_path_from_request();

    if ($cookiePath === '') {
        $cookiePath = '/';
    } else {
        $cookiePath =
            rtrim(
                $cookiePath,
                '/'
            ) . '/';
    }

    session_set_cookie_params(
        [
            'lifetime' => 0,

            'path' =>
                $cookiePath,

            'domain' => '',

            'secure' =>
                $secureCookie,

            'httponly' =>
                true,

            'samesite' =>
                'Lax',
        ]
    );

    if (
        headers_sent(
            $headersFile,
            $headersLine
        )
    ) {
        throw new RuntimeException(
            sprintf(
                'The application session could not start because output was already sent from %s:%d.',
                $headersFile,
                $headersLine
            )
        );
    }

    if (!session_start()) {
        throw new RuntimeException(
            'The application session could not be started.'
        );
    }
}


/*
|--------------------------------------------------------------------------
| Application Autoloader
|--------------------------------------------------------------------------
|
| App\Controllers\HomeController
|
| becomes:
|
| ROOT_PATH/app/Controllers/HomeController.php
|
*/

spl_autoload_register(
    static function (
        string $class
    ): void {
        $prefix =
            'App\\';

        if (
            !str_starts_with(
                $class,
                $prefix
            )
        ) {
            return;
        }

        $relative = substr(
            $class,
            strlen(
                $prefix
            )
        );

        if ($relative === '') {
            return;
        }

        if (
            preg_match(
                '/^[A-Za-z_][A-Za-z0-9_]*(?:\\\\[A-Za-z_][A-Za-z0-9_]*)*$/D',
                $relative
            ) !== 1
        ) {
            return;
        }

        $relativeFile =
            str_replace(
                '\\',
                '/',
                $relative
            );

        $file =
            ROOT_PATH
            . '/app/'
            . $relativeFile
            . '.php';

        if (
            !is_file(
                $file
            )
            || !is_readable(
                $file
            )
        ) {
            return;
        }

        /*
         * Ensure the resolved path remains inside /app.
         */
        $appRoot =
            realpath(
                ROOT_PATH . '/app'
            );

        $realFile =
            realpath(
                $file
            );

        if (
            $appRoot === false
            || $realFile === false
        ) {
            return;
        }

        $allowedPrefix =
            rtrim(
                $appRoot,
                DIRECTORY_SEPARATOR
            )
            . DIRECTORY_SEPARATOR;

        if (
            !str_starts_with(
                $realFile,
                $allowedPrefix
            )
        ) {
            return;
        }

        require_once
            $realFile;
    }
);


/*
|--------------------------------------------------------------------------
| HTML Escaping Helper
|--------------------------------------------------------------------------
*/

function e(
    mixed $value
): string {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES
        | ENT_SUBSTITUTE
        | ENT_HTML5,
        'UTF-8',
        false
    );
}
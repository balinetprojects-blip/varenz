<?php
declare(strict_types=1);

namespace App\Core;

final class Request
{
    private const DEFAULT_METHOD = 'GET';
    private const DEFAULT_PATH = '/';
    private const DEFAULT_IP = '0.0.0.0';

    /**
     * Common HTTP methods accepted by the request layer.
     *
     * Unknown methods are still preserved rather than silently converted to
     * GET, because routers or future API integrations may intentionally use
     * extension methods.
     */
    private const STANDARD_METHODS = [
        'GET',
        'HEAD',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
    ];

    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query,
        public readonly array $post,
        public readonly array $files,
        public readonly array $server,
    ) {}

    /**
     * Capture the current PHP request into an immutable request object.
     */
    public static function capture(): self
    {
        $method = self::normalizeMethod(
            $_SERVER['REQUEST_METHOD'] ?? self::DEFAULT_METHOD
        );

        $path = self::normalizePath(
            $_SERVER['REQUEST_URI'] ?? self::DEFAULT_PATH
        );

        return new self(
            $method,
            $path,
            $_GET,
            $_POST,
            $_FILES,
            $_SERVER
        );
    }

    /**
     * Retrieve input while preserving the application's existing precedence:
     *
     * POST value -> query-string value -> supplied default.
     */
    public function input(
        string $key,
        mixed $default = null
    ): mixed {
        if (array_key_exists($key, $this->post)) {
            return $this->post[$key];
        }

        if (array_key_exists($key, $this->query)) {
            return $this->query[$key];
        }

        return $default;
    }

    /**
     * Determine whether an input key exists in either request data source.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->post)
            || array_key_exists($key, $this->query);
    }

    /**
     * Retrieve a string-compatible input value.
     *
     * This is additive and does not replace input(), preserving compatibility
     * with code that legitimately expects arrays or other request values.
     */
    public function string(
        string $key,
        string $default = ''
    ): string {
        $value = $this->input($key, $default);

        if (
            is_string($value)
            || is_int($value)
            || is_float($value)
            || is_bool($value)
        ) {
            return (string) $value;
        }

        return $default;
    }

    /**
     * Return the directly connected client address.
     *
     * Proxy headers are deliberately not trusted here. They should only be
     * enabled after the application's trusted-proxy configuration has been
     * explicitly defined.
     */
    public function ip(): string
    {
        $ip = trim(
            (string) ($this->server['REMOTE_ADDR'] ?? '')
        );

        if ($ip === '') {
            return self::DEFAULT_IP;
        }

        return filter_var($ip, FILTER_VALIDATE_IP)
            ? $ip
            : self::DEFAULT_IP;
    }

    /**
     * Determine whether the caller expects a JSON response.
     *
     * API routes remain JSON-oriented even when the client omits an Accept
     * header, preserving existing behavior.
     */
    public function expectsJson(): bool
    {
        if ($this->isApiPath()) {
            return true;
        }

        $accept = strtolower(
            trim(
                (string) ($this->server['HTTP_ACCEPT'] ?? '')
            )
        );

        if ($accept === '') {
            return false;
        }

        return str_contains($accept, 'application/json')
            || str_contains($accept, '+json');
    }

    /**
     * Convenient request-method comparison for future controllers/middleware.
     */
    public function isMethod(string $method): bool
    {
        return $this->method === strtoupper(trim($method));
    }

    /**
     * Detect API routes without treating unrelated paths such as
     * "/api-example" as API endpoints.
     */
    private function isApiPath(): bool
    {
        return $this->path === '/api'
            || str_starts_with($this->path, '/api/');
    }

    /**
     * Normalize the incoming HTTP method without destroying unfamiliar but
     * potentially valid extension methods.
     */
    private static function normalizeMethod(mixed $method): string
    {
        $method = strtoupper(
            trim(
                is_scalar($method)
                    ? (string) $method
                    : ''
            )
        );

        if ($method === '') {
            return self::DEFAULT_METHOD;
        }

        if (in_array($method, self::STANDARD_METHODS, true)) {
            return $method;
        }

        /*
         * HTTP token validation.
         *
         * Unknown but syntactically valid methods are intentionally retained.
         */
        if (
            preg_match(
                "/^[!#$%&'*+.^_`|~0-9A-Z-]+$/",
                $method
            ) === 1
        ) {
            return $method;
        }

        return self::DEFAULT_METHOD;
    }

    /**
     * Normalize REQUEST_URI into the application-relative route path.
     */
    private static function normalizePath(mixed $requestUri): string
    {
        $requestUri = is_scalar($requestUri)
            ? (string) $requestUri
            : self::DEFAULT_PATH;

        $uriPath = parse_url(
            $requestUri,
            PHP_URL_PATH
        );

        if (!is_string($uriPath) || $uriPath === '') {
            $uriPath = self::DEFAULT_PATH;
        }

        $uriPath = self::safeRawUrlDecode($uriPath);

        $base = self::normalizeBasePath(
            base_path_from_request()
        );

        if (
            $base !== ''
            && self::pathStartsWithBase($uriPath, $base)
        ) {
            $uriPath = substr(
                $uriPath,
                strlen($base)
            );

            if ($uriPath === '' || $uriPath === false) {
                $uriPath = self::DEFAULT_PATH;
            }
        }

        return self::canonicalizePath($uriPath);
    }

    /**
     * Decode a URL path safely.
     *
     * rawurldecode() itself does not throw, but malformed percent sequences are
     * left intact rather than passed through repeatedly.
     */
    private static function safeRawUrlDecode(string $path): string
    {
        return rawurldecode($path);
    }

    /**
     * Normalize an application base path.
     */
    private static function normalizeBasePath(mixed $base): string
    {
        if (!is_scalar($base)) {
            return '';
        }

        $base = trim((string) $base);

        if ($base === '' || $base === '/') {
            return '';
        }

        $base = '/' . trim($base, '/');

        return rtrim($base, '/');
    }

    /**
     * Ensure "/app" matches "/app" and "/app/..." but not "/application".
     */
    private static function pathStartsWithBase(
        string $path,
        string $base
    ): bool {
        return $path === $base
            || str_starts_with($path, $base . '/');
    }

    /**
     * Produce a predictable route path while preserving route segment content.
     */
    private static function canonicalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);

        /*
         * Collapse accidental repeated separators.
         */
        $path = preg_replace('#/+#', '/', $path) ?? $path;

        $segments = explode('/', $path);
        $normalized = [];

        foreach ($segments as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }

            if ($segment === '..') {
                array_pop($normalized);
                continue;
            }

            $normalized[] = $segment;
        }

        if ($normalized === []) {
            return self::DEFAULT_PATH;
        }

        return '/' . implode('/', $normalized);
    }
}
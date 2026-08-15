<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Router
{
    /**
     * @var array<int, array{
     *     method:string,
     *     pattern:string,
     *     handler:array
     * }>
     */
    private array $routes = [];

    public function __construct(
        private readonly Request $request
    ) {}

    /**
     * Register a GET route.
     */
    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    /**
     * Register a route while preserving the application's existing
     * controller/action handler format:
     *
     * [HomeController::class, 'index']
     */
    private function add(
        string $method,
        string $path,
        array $handler
    ): void {
        $method = strtoupper(trim($method));
        $path = $this->normalizeRoutePath($path);

        $this->routes[] = [
            'method' => $method,
            'pattern' => $this->compileRoutePattern($path),
            'handler' => $handler,
        ];
    }

    /**
     * Dispatch the current request to the first matching route.
     */
    public function dispatch(): never
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $this->request->method) {
                continue;
            }

            $matches = [];

            $matched = preg_match(
                $route['pattern'],
                $this->request->path,
                $matches
            );

            if ($matched !== 1) {
                continue;
            }

            $params = $this->extractRouteParameters($matches);

            $this->invokeHandler(
                $route['handler'],
                $params
            );
        }

        $this->notFound();
    }

    /**
     * Normalize route declarations into the same path format used by Request.
     */
    private function normalizeRoutePath(string $path): string
    {
        $path = trim($path);

        if ($path === '' || $path === '/') {
            return '/';
        }

        return '/' . trim($path, '/');
    }

    /**
     * Convert a route such as:
     *
     * /team/{slug}
     *
     * into a safe anchored regular expression.
     *
     * Static text is regex-escaped while dynamic placeholders remain
     * available as named capture groups.
     */
    private function compileRoutePattern(string $path): string
    {
        if ($path === '/') {
            return '#^/$#D';
        }

        $offset = 0;
        $compiled = '';

        $placeholderPattern = '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/';

        while (
            preg_match(
                $placeholderPattern,
                $path,
                $match,
                PREG_OFFSET_CAPTURE,
                $offset
            ) === 1
        ) {
            $placeholder = $match[0][0];
            $placeholderOffset = $match[0][1];
            $parameterName = $match[1][0];

            $staticPart = substr(
                $path,
                $offset,
                $placeholderOffset - $offset
            );

            $compiled .= preg_quote(
                $staticPart,
                '#'
            );

            $compiled .= '(?P<'
                . $parameterName
                . '>[^/]+)';

            $offset = $placeholderOffset
                + strlen($placeholder);
        }

        $compiled .= preg_quote(
            substr($path, $offset),
            '#'
        );

        return '#^' . $compiled . '$#D';
    }

    /**
     * Extract only named route parameters from preg_match() output.
     *
     * @param array<int|string, mixed> $matches
     * @return array<string, string>
     */
    private function extractRouteParameters(array $matches): array
    {
        $params = [];

        foreach ($matches as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            $params[$key] = rawurldecode(
                (string) $value
            );
        }

        return $params;
    }

    /**
     * Instantiate and execute the registered controller handler.
     *
     * Existing handler structure and controller constructor behavior are
     * intentionally preserved.
     *
     * @param array<mixed> $handler
     * @param array<string, string> $params
     */
    private function invokeHandler(
        array $handler,
        array $params
    ): never {
        if (
            count($handler) !== 2
            || !is_string($handler[0])
            || !is_string($handler[1])
        ) {
            throw new RuntimeException(
                'Invalid route handler definition.'
            );
        }

        [$controllerClass, $action] = $handler;

        if (!class_exists($controllerClass)) {
            throw new RuntimeException(
                'Route controller does not exist.'
            );
        }

        $controller = new $controllerClass(
            $this->request
        );

        if (!method_exists($controller, $action)) {
            throw new RuntimeException(
                'Route action does not exist.'
            );
        }

        $controller->{$action}(
            ...array_values($params)
        );

        /*
         * Controllers in this application are expected to terminate through
         * Response::html() or Response::json().
         *
         * Reaching this point indicates a controller contract violation.
         */
        throw new RuntimeException(
            'Route handler completed without sending a response.'
        );
    }

    /**
     * Return the appropriate public 404 representation.
     */
    private function notFound(): never
    {
        if ($this->request->expectsJson()) {
            Response::json([
                'ok' => false,
                'message' => 'Endpoint not found.',
            ], 404);
        }

        Response::html(
            <<<'HTML'
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <title>Page not found — Varenz Supplies Ltd</title>
</head>
<body>
    <main>
        <h1>Page not found</h1>
        <p>The page you requested could not be found.</p>
        <p><a href="/">Return to Varenz Supplies Ltd</a></p>
    </main>
</body>
</html>
HTML,
            404
        );
    }
}
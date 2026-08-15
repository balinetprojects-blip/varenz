<?php
declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = '/' . ltrim(rawurldecode($path), '/');

if (
    str_contains($path, "\0")
    || str_contains($path, '..')
    || preg_match('#^/(?:app|config|data|storage)(?:/|$)#i', $path)
    || preg_match('#/(?:\.|[^/]*\.(?:env|ini|log|sql|bak|sh|md))$#i', $path)
) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Forbidden';
    exit;
}

if (str_starts_with($path, '/assets/')) {
    $assetRoot = realpath(__DIR__ . '/assets');
    $file = realpath(__DIR__ . $path);

    if ($assetRoot !== false && $file !== false && str_starts_with($file, $assetRoot . DIRECTORY_SEPARATOR) && is_file($file)) {
        return false;
    }
}

require __DIR__ . '/index.php';

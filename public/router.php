<?php
/**
 * Router for PHP's built-in development server.
 * Existing assets are served directly; application URLs use the front controller.
 */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

// Uploaded application documents are never public static assets. They must be
// served by RequestController after an authorization check.
if (strpos($path, '/uploads/') === 0) {
    http_response_code(404);
    exit;
}

if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';

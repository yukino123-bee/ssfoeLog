<?php
/**
 * Communifund Assistance System Front Controller
 * Security: Apply security headers before any content output
 */

// Configure the session cookie before starting the session. These settings are
// supported on shared hosting and do not change the application's session API.
$directHttps = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';
$trustedProxyValues = array_values(array_filter(array_map('trim', explode(',', getenv('TRUSTED_PROXIES') ?: ''))));
$fromTrustedProxy = in_array($_SERVER['REMOTE_ADDR'] ?? '', $trustedProxyValues, true);
$isHttps = $directHttps || ($fromTrustedProxy && strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', $isHttps ? '1' : '0');
session_start();

// Start output buffering to prevent headers already sent errors
ob_start();

// Define application paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// ============================================================================
// SECURITY: Apply security headers immediately
// ============================================================================
require_once APP_PATH . '/middleware/security_headers.php';
security_headers_middleware();

// ============================================================================
// Load configuration and helper functions
// ============================================================================
require_once APP_PATH . '/helpers/functions.php';
require_once APP_PATH . '/helpers/sanitizer.php';
require_once APP_PATH . '/helpers/password_validator.php';

// Load configuration and constants
require_once APP_PATH . '/config/database.php';
require_once APP_PATH . '/config/constants.php';

// Simple Router
$routes = require_once ROOT_PATH . '/routes/web.php';

// Resolve the base path dynamically
$script_name = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$base_path = rtrim(dirname($script_name), '/');
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (strpos($request_uri, $base_path) === 0) {
    $path = substr($request_uri, strlen($base_path));
} else {
    // Handled by .htaccess rewrite where REQUEST_URI doesn't include the base_path
    $path = $request_uri;
}

// Ensure path starts with /
if (!$path || $path[0] !== '/') {
    $path = '/' . ltrim($path, '/');
}

if (function_exists('maintenance_should_block_path') && maintenance_should_block_path($path)) {
    http_response_code(503);
    require_once APP_PATH . '/views/maintenance.php';
    exit;
}

if (array_key_exists($path, $routes)) {
    $routeValue = $routes[$path];
    $handler = $routeValue['handler'];
    $middlewares = $routeValue['middleware'];

    // Execute Middlewares
    foreach ($middlewares as $mw) {
        $mwFile = APP_PATH . '/middleware/' . $mw . '.php';
        if (file_exists($mwFile)) {
            require_once $mwFile;
            $mwFunction = $mw . '_middleware';
            if (function_exists($mwFunction)) {
                $mwFunction();
            }
        }
    }

    try {
        list($controllerName, $methodName) = explode('@', $handler);
        
        // Autoload controller
        $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';
        if (!file_exists($controllerFile)) {
            throw new Exception("Controller not found: $controllerName");
        }
        
        require_once $controllerFile;
        
        if (!class_exists($controllerName)) {
            throw new Exception("Controller class not found: $controllerName");
        }
        
        if (!method_exists($controllerName, $methodName)) {
            throw new Exception("Method not found: $controllerName@$methodName");
        }
        
        $controller = new $controllerName();
        $controller->$methodName();
    } catch (\Throwable $e) {
        error_log('Route handling error: ' . $e->getMessage());
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        http_response_code(500);
        echo "<h1>500 Internal Server Error</h1>";
        echo "<p>An unexpected error occurred. Please try again later.</p>";
    }
} else {
    // 404 Not Found
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    echo "<p>The requested page was not found on this server.</p>";
}

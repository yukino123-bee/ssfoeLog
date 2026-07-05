<?php
/**
 * SSFO Front Controller
 * Security: Apply security headers before any content output
 */

@session_start();

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
$path = substr($request_uri, strlen($base_path));

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
    } catch (Exception $e) {
        error_log('Route handling error: ' . $e->getMessage());
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        http_response_code(500);
        echo "<h1>500 Internal Server Error</h1>";
        echo "<p>An unexpected error occurred. Please try again later.</p>";
        echo "<div style='background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-top: 1rem; font-family: sans-serif;'>";
        echo "<strong>Debug info:</strong> " . htmlspecialchars($e->getMessage());
        echo "</div>";
    }
} else {
    // 404 Not Found
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    echo "<p>The requested page was not found on this server.</p>";
}



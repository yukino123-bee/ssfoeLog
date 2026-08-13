<?php
/**
 * Database Configuration
 */

/**
 * Read configuration from the local .env file. Environment variables take
 * precedence, which keeps the same code usable from a shell or container.
 */
function app_env(string $key, ?string $default = null): ?string {
    static $values = null;

    $systemValue = getenv($key);
    if ($systemValue !== false) {
        return $systemValue;
    }

    if ($values === null) {
        $file = ROOT_PATH . '/.env';
        $values = is_file($file) ? (parse_ini_file($file, false, INI_SCANNER_RAW) ?: []) : [];
    }

    return array_key_exists($key, $values) ? (string) $values[$key] : $default;
}

define('DB_HOST', app_env('DB_HOST', '127.0.0.1'));
define('DB_PORT', (int) app_env('DB_PORT', '3306'));
define('DB_USER', app_env('DB_USER', 'ssfo'));
define('DB_PASS', app_env('DB_PASS', 'ssfo_local_password'));
define('DB_NAME', app_env('DB_NAME', 'ssfo'));

function get_db_connection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        error_log('Database connection failed: ' . $conn->connect_error);
        throw new RuntimeException('Unable to connect to the database.');
    }

    $conn->set_charset('utf8mb4');

    return $conn;
}

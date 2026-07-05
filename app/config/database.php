<?php
/**
 * Database Configuration
 */

// Load environment variables from .env file
if (file_exists(ROOT_PATH . '/.env')) {
    $env = parse_ini_file(ROOT_PATH . '/.env');
    define('DB_HOST', $env['DB_HOST'] ?? 'localhost');
    define('DB_USER', $env['DB_USER'] ?? 'root');
    define('DB_PASS', $env['DB_PASS'] ?? '');
    define('DB_NAME', $env['DB_NAME'] ?? 'ssfo');
} else {
    // Fallback to defaults (for development)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', 'manok123');
    define('DB_NAME', 'ssfo');
}

function get_db_connection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

<?php
/**
 * Database Configuration
 */

// InfinityFree MySQL connection details. This deployment does not use .env.
define('DB_HOST', 'sql104.infinityfree.com');
define('DB_PORT', 3306);
define('DB_USER', 'if0_42641407');
define('DB_PASS', 'cagatinmark2005');
define('DB_NAME', 'if0_42641407_ssfo');

function get_db_connection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        error_log('Database connection failed: ' . $conn->connect_error);
        throw new RuntimeException('Unable to connect to the database.');
    }

    $conn->set_charset('utf8mb4');

    return $conn;
}

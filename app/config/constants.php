<?php
/**
 * Application Constants
 */

define('APP_NAME', 'SSFO eLog');
define('APP_VERSION', '1.0.0');

// Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_CLIENT', 'client');

// Request Statuses
define('STATUS_PENDING', 'pending');
define('STATUS_APPROVED', 'approved');
define('STATUS_REJECTED', 'rejected');
define('STATUS_COMPLETED', 'completed');

// SMS Configuration
if (file_exists(ROOT_PATH . '/.env')) {
    $env = parse_ini_file(ROOT_PATH . '/.env');
    define('SMS_API_KEY', $env['SMS_API_KEY'] ?? '');
    define('SMS_SENDER_NAME', $env['SMS_SENDER_NAME'] ?? 'SSFO');
    define('SMS_ENABLED', !empty($env['SMS_ENABLED']) && filter_var($env['SMS_ENABLED'], FILTER_VALIDATE_BOOLEAN));
} else {
    define('SMS_API_KEY', '');
    define('SMS_SENDER_NAME', 'SSFO');
    define('SMS_ENABLED', false);
}

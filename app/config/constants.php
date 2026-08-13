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
define('APP_ENV', app_env('APP_ENV', 'local'));
define('APP_DEBUG', filter_var(app_env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN));
define('SMS_API_KEY', app_env('SMS_API_KEY', ''));
define('SMS_SENDER_NAME', app_env('SMS_SENDER_NAME', 'SSFO'));
define('SMS_ENABLED', filter_var(app_env('SMS_ENABLED', 'false'), FILTER_VALIDATE_BOOLEAN));

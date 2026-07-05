<?php
/**
 * Rate Limiting Middleware
 * Prevents brute force attacks and resource exhaustion
 */

define('RATE_LIMIT_STORAGE_PATH', ROOT_PATH . '/storage/rate_limits/');

function rate_limit_middleware() {
    // Create storage directory if needed
    if (!is_dir(RATE_LIMIT_STORAGE_PATH)) {
        mkdir(RATE_LIMIT_STORAGE_PATH, 0755, true);
    }
    
    $ip = get_client_ip();
    $endpoint = $_SERVER['REQUEST_URI'];
    
    // Different limits for different endpoints
    $limits = get_rate_limit_config();
    $limit_key = get_endpoint_limit_key($endpoint);
    
    if (!isset($limits[$limit_key])) {
        return; // No rate limiting for this endpoint
    }
    
    $config = $limits[$limit_key];
    $max_requests = $config['requests'];
    $time_window = $config['window'];
    
    $file = RATE_LIMIT_STORAGE_PATH . md5($ip . $limit_key) . '.json';
    $data = [];
    
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
    }
    
    $now = time();
    $window_start = $now - $time_window;
    
    // Clean old requests outside the time window
    $data['requests'] = array_filter(
        $data['requests'] ?? [],
        function($timestamp) use ($window_start) {
            return $timestamp > $window_start;
        }
    );
    
    // Check if limit exceeded
    if (count($data['requests']) >= $max_requests) {
        http_response_code(429); // Too Many Requests
        die(json_encode([
            'error' => 'Rate limit exceeded',
            'retry_after' => $time_window,
            'message' => 'Too many requests. Please try again later.'
        ]));
    }
    
    // Add current request
    $data['requests'][] = $now;
    
    // Save updated data
    file_put_contents($file, json_encode($data));
    
    // Clean up old files (older than 24 hours)
    cleanup_old_rate_limit_files();
}

function get_client_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return filter_var($ip, FILTER_VALIDATE_IP) ?: '0.0.0.0';
}

function get_rate_limit_config() {
    return [
        'login' => ['requests' => 5, 'window' => 900],           // 5 per 15 minutes
        'register' => ['requests' => 3, 'window' => 3600],       // 3 per hour
        'unlock-programs' => ['requests' => 10, 'window' => 300], // 10 per 5 minutes
        'api' => ['requests' => 100, 'window' => 3600],          // 100 per hour
        'contact' => ['requests' => 5, 'window' => 3600],        // 5 per hour
    ];
}

function get_endpoint_limit_key($endpoint) {
    if (strpos($endpoint, 'login') !== false) {
        return 'login';
    } elseif (strpos($endpoint, 'register') !== false) {
        return 'register';
    } elseif (strpos($endpoint, 'unlock-programs') !== false) {
        return 'unlock-programs';
    } elseif (strpos($endpoint, '/api/') !== false) {
        return 'api';
    } elseif (strpos($endpoint, 'contact') !== false) {
        return 'contact';
    }
    return null;
}

function cleanup_old_rate_limit_files() {
    $files = glob(RATE_LIMIT_STORAGE_PATH . '*.json');
    $now = time();
    $max_age = 86400; // 24 hours
    
    foreach ($files as $file) {
        if ($now - filemtime($file) > $max_age) {
            unlink($file);
        }
    }
}

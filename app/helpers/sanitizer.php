<?php
/**
 * Input Sanitization & Output Encoding Helper
 * Provides functions for secure input/output handling
 */

/**
 * Sanitize user input to remove potentially dangerous characters
 * 
 * @param string $input The input to sanitize
 * @param string $type Type of input: text, email, url, sql_identifier
 * @return string Sanitized input
 */
function sanitize_input($input, $type = 'text') {
    if ($type === 'email') {
        // Email validation and sanitization
        return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
    }
    
    if ($type === 'url') {
        // URL sanitization
        return filter_var(trim($input), FILTER_SANITIZE_URL);
    }
    
    if ($type === 'sql_identifier') {
        // Safe SQL identifier (table/column names)
        return preg_replace('/[^a-zA-Z0-9_]/', '', $input);
    }
    
    if ($type === 'filename') {
        return sanitize_filename($input);
    }
    
    // Default text sanitization
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Escape output for safe HTML display
 * 
 * @param string $output The output to escape
 * @param string $context Context: html, attr, js, css, url
 * @return string Escaped output
 */
function escape_output($output, $context = 'html') {
    if ($context === 'attr') {
        // Escape for HTML attributes
        return htmlspecialchars($output, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    if ($context === 'js') {
        // Escape for JavaScript context
        return json_encode($output, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
    
    if ($context === 'css') {
        // Escape for CSS context
        return preg_replace('/[^a-zA-Z0-9\-_\/\.]/', '', $output);
    }
    
    if ($context === 'url') {
        // Escape for URL context
        return urlencode($output);
    }
    
    // Default HTML context
    return htmlspecialchars($output, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Sanitize filename to prevent directory traversal and special characters
 * 
 * @param string $filename The filename to sanitize
 * @return string Sanitized filename
 */
function sanitize_filename($filename) {
    // Remove path traversal attempts
    $filename = str_replace(['../', '..\\', '..'], '', $filename);
    
    // Remove null bytes
    $filename = str_replace("\0", '', $filename);
    
    // Keep only alphanumeric, dash, underscore, and dot
    $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);
    
    // Remove leading/trailing dots
    $filename = trim($filename, '.');
    
    // Limit length
    $filename = substr($filename, 0, 255);
    
    return $filename;
}

/**
 * Validate and sanitize SQL identifier (table/column names)
 * 
 * @param string $identifier The identifier to validate
 * @return string|false The identifier if valid, false otherwise
 */
function sanitize_sql_identifier($identifier) {
    if (empty($identifier)) {
        return false;
    }
    
    // SQL identifiers should be alphanumeric or underscore
    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
        return false;
    }
    
    return $identifier;
}

/**
 * Sanitize phone number (Philippine format)
 * 
 * @param string $phone The phone number to sanitize
 * @return string Sanitized phone number
 */
function sanitize_phone($phone) {
    // Remove non-digit characters except +
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    return $phone;
}

/**
 * Sanitize JSON data
 * 
 * @param array $data The data to encode to JSON
 * @return string JSON string
 */
function sanitize_json($data) {
    return json_encode(
        $data,
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
    );
}

/**
 * Validate and sanitize email
 * 
 * @param string $email The email to validate
 * @return string|false Valid email or false
 */
function sanitize_and_validate_email($email) {
    $email = sanitize_input(trim($email), 'email');
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    return $email;
}

/**
 * Create a safe redirect URL
 * 
 * @param string $url The URL to redirect to
 * @param bool $internal Whether to ensure it's an internal URL
 * @return string Safe URL
 */
function sanitize_redirect_url($url, $internal = true) {
    if ($internal) {
        // Ensure URL is internal (starts with /)
        if (!empty($url) && $url[0] !== '/') {
            return base_url();
        }
    }
    
    // Prevent protocol-relative URLs (//example.com)
    if (strpos($url, '//') === 0) {
        return base_url();
    }
    
    return $url;
}

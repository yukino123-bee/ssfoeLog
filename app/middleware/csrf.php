<?php
/**
 * CSRF Middleware - Validates CSRF tokens on POST requests
 */

function csrf_middleware() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Detect if POST payload exceeded post_max_size (which empties $_POST and causes CSRF to falsely fail)
        if (empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $content_length = (int) $_SERVER['CONTENT_LENGTH'];
            $max_size = ini_get('post_max_size');
            $val = trim($max_size);
            $last = strtolower($val[strlen($val)-1]);
            $val = (int)$val;
            switch($last) {
                case 'g': $val *= 1024;
                case 'm': $val *= 1024;
                case 'k': $val *= 1024;
            }
            if ($content_length > $val) {
                if (!headers_sent()) {
                    http_response_code(413); // Payload Too Large
                }
                die("Upload error: The files you are trying to upload (" . round($content_length / 1048576, 2) . " MB) exceed the server's POST payload limit of " . ini_get('post_max_size') . ". Please reduce the file sizes.");
            }
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!validate_csrf_token($token)) {
            if (!headers_sent()) {
                http_response_code(403);
            }
            die("CSRF token validation failed.");
        }
    }
}
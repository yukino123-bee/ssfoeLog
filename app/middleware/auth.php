<?php
/**
 * Auth Middleware - Ensures user is logged in
 */

function auth_middleware() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check session timeout (30 minutes)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        session_unset();
        session_destroy();
        redirect(base_url('login'));
    }
    $_SESSION['last_activity'] = time();
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        redirect(base_url('login'));
    }
}

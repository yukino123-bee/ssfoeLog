<?php
/**
 * Auth Middleware - Ensures user is logged in
 */

function auth_middleware() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        redirect(base_url('login'));
    }
}

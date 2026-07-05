<?php
/**
 * Admin Only Middleware - Ensures user has admin role
 */

require_once __DIR__ . '/auth.php';

function admin_only_middleware() {
    auth_middleware();

    if ($_SESSION['role'] !== ROLE_ADMIN) {
        // Redirect non-admins to client dashboard or unauthorized page
        redirect(base_url('client/dashboard'));
    }
}

<?php
/**
 * Client Only Middleware - Ensures user has client role
 */

require_once __DIR__ . '/auth.php';

function client_only_middleware() {
    auth_middleware();

    if (($_SESSION['role'] ?? null) !== ROLE_CLIENT) {
        // Redirect admins to admin dashboard
        redirect(base_url('admin/dashboard'));
    }
}

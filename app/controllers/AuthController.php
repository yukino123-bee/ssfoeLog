<?php
/**
 * AuthController - Handles Login, Register, and Logout
 */

class AuthController {
    
    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $captcha_token = $_POST['captcha_token'] ?? '';
            $expected = $_SESSION['login_captcha_token'] ?? '';

            if ($expected === '' || $captcha_token === '' || !hash_equals($expected, $captcha_token)) {
                $error_message = "Please complete the verification (I'm not a robot).";
                $_SESSION['login_captcha_token'] = bin2hex(random_bytes(16));
            } else {
                unset($_SESSION['login_captcha_token']);

                require_once APP_PATH . '/models/User.php';
                $userModel = new User();
                $user = $userModel->findByEmail($email);

                if ($user && password_verify($password, $user['password'])) {
                    if ($user['role'] !== 'admin') {
                        $error_message = "Access denied. Only administrators can log in.";
                        $_SESSION['login_captcha_token'] = bin2hex(random_bytes(16));
                    } else {
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_fullname'] = $user['fullname'];
                        $_SESSION['user_image'] = $user['profile_image'];
                        $_SESSION['role'] = $user['role'];

                        redirect(base_url('admin/dashboard'));
                    }
                } else {
                    $error_message = "Invalid credentials. Please try again.";
                    $_SESSION['login_captcha_token'] = bin2hex(random_bytes(16));
                }
            }
        } else {
            $_SESSION['login_captcha_token'] = bin2hex(random_bytes(16));
        }

        $title = "Login - " . APP_NAME;
        require_once APP_PATH . '/views/auth/login.php';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        session_unset();
        session_destroy();
        redirect(base_url('login'));
    }
}

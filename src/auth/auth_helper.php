<?php
require_once __DIR__ . '/../../config/config.php';

class AuthHelper {
    // Check if user is logged in
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Get current user role
    public static function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }

    // Hash password
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    // Verify password
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    // Validate password strength
    public static function validatePassword($password) {
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            return false;
        }
        // Add more password validation rules as needed
        return true;
    }

    // Redirect to login if not authenticated
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . APP_URL . '/login.php');
            exit();
        }
    }

    // Redirect to home if already logged in
    public static function redirectIfLoggedIn() {
        if (self::isLoggedIn()) {
            header('Location: ' . APP_URL . '/index.php');
            exit();
        }
    }
}
?> 
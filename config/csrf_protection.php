<?php
/**
 * CSRF Protection System for Yetuga App
 * Prevents Cross-Site Request Forgery attacks
 */

/**
 * Generate a new CSRF token
 * @return string The generated token
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify a CSRF token
 * @param string $token The token to verify
 * @return bool True if valid, false otherwise
 */
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Regenerate CSRF token (for security)
 * @return string The new token
 */
function regenerateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

/**
 * Get CSRF token for forms
 * @return string The current token
 */
function getCSRFToken() {
    return generateCSRFToken();
}

/**
 * Verify POST request has valid CSRF token
 * @return bool True if valid, false otherwise
 */
function verifyCSRFPost() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return false;
    }
    
    $token = $_POST['csrf_token'] ?? '';
    return verifyCSRFToken($token);
}

/**
 * Display CSRF token as hidden input
 * @return string HTML for hidden input
 */
function csrfTokenField() {
    $token = getCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Check if CSRF validation failed and redirect
 * @param string $redirect_url Where to redirect on failure
 * @return void
 */
function requireCSRF($redirect_url = '../login.php?error=csrf_failed') {
    if (!verifyCSRFPost()) {
        header("Location: $redirect_url");
        exit();
    }
}
?>

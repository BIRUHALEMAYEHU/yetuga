<?php
/**
 * Authentication Middleware
 * This file provides functions to check user authentication and authorization
 */

/**
 * Check if user is logged in
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user has a specific role
 * @param string $role Role to check
 * @return bool True if user has the role, false otherwise
 */
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    return $_SESSION['user_role'] === $role;
}

/**
 * Check if user has any of the specified roles
 * @param array $roles Array of roles to check
 * @return bool True if user has any of the roles, false otherwise
 */
function hasAnyRole($roles) {
    if (!isLoggedIn()) {
        return false;
    }
    
    return in_array($_SESSION['user_role'], $roles);
}

/**
 * Require authentication - redirect to login if not logged in
 * @param string $redirect_url URL to redirect after login
 */
function requireAuth($redirect_url = null) {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $redirect_url ?: $_SERVER['REQUEST_URI'];
        header('Location: ../auth/login.php');
        exit();
    }
}

/**
 * Require specific role - redirect to unauthorized page if role doesn't match
 * @param string $role Required role
 * @param string $redirect_url URL to redirect if unauthorized
 */
function requireRole($role, $redirect_url = null) {
    requireAuth();
    
    if (!hasRole($role)) {
        if ($redirect_url) {
            header("Location: $redirect_url");
        } else {
            // Redirect based on user's actual role
            switch ($_SESSION['user_role']) {
                case 'admin':
                    header('Location: ../admin/dashboard.php');
                    break;
                case 'officer':
                    header('Location: ../officer/dashboard.php');
                    break;
                case 'user':
                default:
                    header('Location: ../user/dashboard.php');
                    break;
            }
        }
        exit();
    }
}

/**
 * Require any of the specified roles
 * @param array $roles Array of allowed roles
 * @param string $redirect_url URL to redirect if unauthorized
 */
function requireAnyRole($roles, $redirect_url = null) {
    requireAuth();
    
    if (!hasAnyRole($roles)) {
        if ($redirect_url) {
            header("Location: $redirect_url");
        } else {
            // Redirect based on user's actual role
            switch ($_SESSION['user_role']) {
                case 'admin':
                    header('Location: ../admin/dashboard.php');
                    break;
                case 'officer':
                    header('Location: ../officer/dashboard.php');
                    break;
                case 'user':
                default:
                    header('Location: ../user/dashboard.php');
                    break;
            }
        }
        exit();
    }
}

/**
 * Get current user's role
 * @return string|null User's role or null if not logged in
 */
function getCurrentUserRole() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Get current user's ID
 * @return int|null User's ID or null if not logged in
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user's full name
 * @return string User's full name or empty string if not logged in
 */
function getCurrentUserName() {
    if (!isLoggedIn()) {
        return '';
    }
    
    $first_name = $_SESSION['first_name'] ?? '';
    $last_name = $_SESSION['last_name'] ?? '';
    
    return trim("$first_name $last_name");
}

/**
 * Check if user can access a specific feature
 * @param string $feature Feature to check
 * @return bool True if user can access, false otherwise
 */
function canAccess($feature) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $role = $_SESSION['user_role'];
    
    switch ($feature) {
        case 'manage_routes':
            return in_array($role, ['officer', 'admin']);
        case 'manage_reports':
            return in_array($role, ['officer', 'admin']);
        case 'manage_users':
            return $role === 'admin';
        case 'submit_reports':
            return in_array($role, ['user', 'officer', 'admin']);
        case 'view_analytics':
            return in_array($role, ['officer', 'admin']);
        case 'emergency_updates':
            return in_array($role, ['officer', 'admin']);
        default:
            return false;
    }
}

/**
 * Redirect user to appropriate dashboard based on their role
 */
function redirectToDashboard() {
    if (!isLoggedIn()) {
        header('Location: ../auth/login.php');
        exit();
    }
    
    switch ($_SESSION['user_role']) {
        case 'admin':
            header('Location: ../admin/dashboard.php');
            break;
        case 'officer':
            header('Location: ../officer/dashboard.php');
            break;
        case 'user':
        default:
            header('Location: ../user/dashboard.php');
            break;
    }
    exit();
}

/**
 * Log unauthorized access attempt
 * @param string $feature Feature that was accessed
 * @param string $required_role Required role for the feature
 */
function logUnauthorizedAccess($feature, $required_role) {
    if (isLoggedIn()) {
        require_once '../config/database.php';
        logUserActivity(
            $_SESSION['user_id'], 
            'unauthorized_access', 
            "Attempted to access '$feature' (requires role: $required_role)"
        );
    }
}
?>



<?php
/**
 * Session Configuration and Management for Yetuga App
 * Provides secure session handling with expiration and security features
 */

// Session security configuration
ini_set('session.cookie_httponly', 1);           // Prevent XSS attacks
ini_set('session.cookie_secure', 0);             // Set to 1 if using HTTPS
ini_set('session.use_strict_mode', 1);           // Use strict mode
ini_set('session.cookie_samesite', 'Lax');       // Changed from Strict to Lax for better compatibility
ini_set('session.gc_maxlifetime', 1800);         // 30 minutes session lifetime (more practical)
ini_set('session.gc_probability', 1);            // Garbage collection probability
ini_set('session.gc_divisor', 100);              // Garbage collection divisor

// Start session with custom settings
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Set session parameters before starting
        ini_set('session.gc_maxlifetime', 1800); // 30 minutes
        ini_set('session.cookie_lifetime', 1800); // 30 minutes
        
        session_start();
    }
    
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) { // Every 5 minutes
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// Check if session is valid
function isSessionValid() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['login_time']) || !isset($_SESSION['last_activity'])) {
        return false;
    }
    
    $current_time = time();
    $session_lifetime = 1800; // 30 minutes
    $time_since_activity = $current_time - $_SESSION['last_activity'];
    
    // Check if session has expired due to inactivity
    if ($time_since_activity > $session_lifetime) {
        return false;
    }
    
    return true;
}

// Validate user role and permissions
function validateUserRole($required_role) {
    if (!isSessionValid()) {
        return false;
    }
    
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $required_role) {
        return false;
    }
    
    return true;
}

// Check if user has any of the specified roles
function validateUserRoles($allowed_roles) {
    if (!isSessionValid()) {
        return false;
    }
    
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowed_roles)) {
        return false;
    }
    
    return true;
}

// Destroy session completely
function destroySession() {
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    // Clear session data from memory
    unset($_SESSION);
}

// Log user activity
function logUserActivity($user_id, $action, $details = '') {
    try {
        // For now, just log to error log since database might not be available
        // This prevents the session system from breaking if database is down
        error_log("User Activity: User {$user_id} - {$action} - {$details}");
        
        // TODO: Re-enable database logging when database is properly set up
        /*
        require_once 'database.php';
        
        $stmt = $pdo->prepare("INSERT INTO user_activity (user_id, action, details, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $user_id,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
        */
    } catch (Exception $e) {
        error_log("Failed to log user activity: " . $e->getMessage());
    }
}

// Check for suspicious activity
function checkSuspiciousActivity($user_id) {
    try {
        // For now, just return true to allow access
        // This prevents the session system from breaking if database is down
        error_log("Suspicious activity check skipped - database not available");
        return true;
        
        // TODO: Re-enable database checks when database is properly set up
        /*
        require_once 'database.php';
        
        // Check for multiple failed login attempts
        $stmt = $pdo->prepare("SELECT COUNT(*) as failed_attempts FROM user_activity WHERE user_id = ? AND action = 'login_failed' AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
        $stmt->execute([$user_id]);
        $failed_attempts = $stmt->fetch()['failed_attempts'];
        
        if ($failed_attempts >= 5) {
            // Lock account temporarily
            $stmt = $pdo->prepare("UPDATE users SET is_active = 0, locked_until = DATE_ADD(NOW(), INTERVAL 30 MINUTE) WHERE id = ?");
            $stmt->execute([$user_id]);
            
            logUserActivity($user_id, 'account_locked', 'Too many failed login attempts');
            return false;
        }
        
        return true;
        */
    } catch (Exception $e) {
        error_log("Failed to check suspicious activity: " . $e->getMessage());
        return true; // Allow access if check fails
    }
}

// Refresh session (extend lifetime)
function refreshSession() {
    if (isSessionValid()) {
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        return true;
    }
    return false;
}

// Get session info for debugging
function getSessionInfo() {
    return [
        'user_id' => $_SESSION['user_id'] ?? null,
        'user_role' => $_SESSION['user_role'] ?? null,
        'login_time' => $_SESSION['login_time'] ?? null,
        'last_activity' => $_SESSION['last_activity'] ?? null,
        'session_age' => isset($_SESSION['login_time']) ? time() - $_SESSION['login_time'] : null,
        'is_valid' => isSessionValid()
    ];
}

// Force logout all user sessions (for security)
function forceLogoutAllSessions($user_id) {
    try {
        // For now, just log the action
        // This prevents the session system from breaking if database is down
        error_log("Force logout all sessions requested for user {$user_id} - database not available");
        return true;
        
        // TODO: Re-enable database operations when database is properly set up
        /*
        require_once 'database.php';
        
        // Update user's session token to invalidate all sessions
        $stmt = $pdo->prepare("UPDATE users SET session_token = UUID() WHERE id = ?");
        $stmt->execute([$user_id]);
        
        logUserActivity($user_id, 'force_logout_all', 'All sessions invalidated');
        return true;
        */
    } catch (Exception $e) {
        error_log("Failed to force logout all sessions: " . $e->getMessage());
        return false;
    }
}
?>

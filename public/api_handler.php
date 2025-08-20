<?php
/**
 * Secure API Handler for Yetuga App
 * This file handles all API requests and ensures they're properly authenticated
 * APIs can only be called from authenticated pages, not directly via URL
 */

// Include session configuration
require_once '../config/session_config.php';
require_once '../config/rate_limiter.php';

// Start secure session
startSecureSession();

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests (prevents direct URL access)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST requests only.',
        'error' => 'invalid_method'
    ]);
    exit();
}

// Check if user is logged in
if (!isSessionValid()) {
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'message' => 'Session expired or invalid',
        'status' => 'expired',
        'error' => 'unauthorized'
    ]);
    exit();
}

// Get the API action from POST data
$action = $_POST['action'] ?? '';

// Rate limit API calls
$rate_limit = checkRateLimit('api_call', 100, 300); // 100 API calls per 5 minutes
if (!$rate_limit['allowed']) {
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'message' => $rate_limit['message'],
        'error' => 'rate_limited'
    ]);
    exit();
}

// Handle different API actions
switch ($action) {
    case 'session_status':
        handleSessionStatus();
        break;
        
    case 'refresh_session':
        handleRefreshSession();
        break;
        
    case 'user_info':
        handleUserInfo();
        break;
        
    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid API action',
            'error' => 'invalid_action'
        ]);
        break;
}

/**
 * Handle session status request
 */
function handleSessionStatus() {
    try {
        // Calculate remaining session time
        $session_lifetime = 1800; // 30 minutes
        $current_time = time();
        $elapsed = $current_time - $_SESSION['login_time'];
        $remaining = $session_lifetime - $elapsed;
        
        // Determine session status
        $status = 'active';
        if ($remaining <= 300) { // 5 minutes or less
            $status = 'critical';
        } elseif ($remaining <= 600) { // 10 minutes or less
            $status = 'warning';
        }
        
        echo json_encode([
            'success' => true,
            'status' => $status,
            'remaining_time' => $remaining,
            'remaining_minutes' => floor($remaining / 60),
            'remaining_seconds' => $remaining % 60,
            'user_id' => $_SESSION['user_id'],
            'user_role' => $_SESSION['user_role'],
            'user_name' => $_SESSION['user_name'],
            'login_time' => $_SESSION['login_time'],
            'last_activity' => $_SESSION['last_activity']
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
}

/**
 * Handle session refresh request
 */
function handleRefreshSession() {
    try {
        // Update last activity time to extend session
        $_SESSION['last_activity'] = time();
        
        // Log activity
        try {
            logUserActivity($_SESSION['user_id'], 'session_refreshed', 'Session extended due to user activity');
        } catch (Exception $e) {
            // Log silently if it fails
            error_log("Failed to log session refresh: " . $e->getMessage());
        }
        
        $response = [
            'success' => true, 
            'message' => 'Session refreshed',
            'last_activity' => $_SESSION['last_activity'],
            'remaining_time' => 1800 - (time() - $_SESSION['last_activity'])
        ];
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
}

/**
 * Handle user info request
 */
function handleUserInfo() {
    try {
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email'],
                'role' => $_SESSION['user_role']
            ]
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
}

// Record successful API call
recordSuccess('api_call');
?>

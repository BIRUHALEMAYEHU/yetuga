<?php
/**
 * DEPRECATED: This API endpoint has been moved to api_handler.php for security
 * Direct access to this file is no longer allowed
 */

http_response_code(410); // Gone
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'This API endpoint has been deprecated for security reasons. Use api_handler.php instead.',
    'error' => 'deprecated_endpoint'
]);
exit();

try {
    // Update last activity time to extend session
    $_SESSION['last_activity'] = time();
    
    // Log activity (optional, can be disabled for performance)
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
        'remaining_time' => 300 - (time() - $_SESSION['last_activity']),
        'debug' => [
            'user_id' => $_SESSION['user_id'],
            'current_time' => time(),
            'session_age' => time() - $_SESSION['login_time']
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()]);
}
?>

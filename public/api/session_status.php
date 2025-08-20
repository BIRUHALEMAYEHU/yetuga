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
    // Calculate remaining session time
    $session_lifetime = 30; // 30 seconds for testing
    $current_time = time();
    $elapsed = $current_time - $_SESSION['login_time'];
    $remaining = $session_lifetime - $elapsed;
    
    // Determine session status
    $status = 'active';
    if ($remaining <= 5) { // 5 seconds or less
        $status = 'critical';
    } elseif ($remaining <= 10) { // 10 seconds or less
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
?>

<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'role' => null,
    'message' => ''
];

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    $response['message'] = 'Not logged in';
    echo json_encode($response);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Verify user exists and is active
    $stmt = $pdo->prepare("SELECT id, role FROM users WHERE id = ? AND role = ?");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_role']]);
    $user = $stmt->fetch();
    
    if ($user) {
        $response['success'] = true;
        $response['role'] = $user['role'];
        $response['message'] = 'Authenticated';
    } else {
        $response['message'] = 'Invalid user';
        // Clear invalid session
        session_destroy();
    }
} catch (Exception $e) {
    error_log("Auth check error: " . $e->getMessage());
    $response['message'] = 'Authentication check failed';
}

echo json_encode($response); 
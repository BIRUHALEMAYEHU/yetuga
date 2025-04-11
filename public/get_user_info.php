<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'username' => '',
    'email' => '',
    'role' => '',
    'message' => ''
];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Not logged in';
    echo json_encode($response);
    exit;
}

try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT username, email, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if ($user) {
        $response['success'] = true;
        $response['username'] = $user['username'];
        $response['email'] = $user['email'];
        $response['role'] = $user['role'];
        $response['message'] = 'User info retrieved successfully';
    } else {
        $response['message'] = 'User not found';
        // Clear invalid session
        session_destroy();
    }
} catch (Exception $e) {
    error_log("Get user info error: " . $e->getMessage());
    $response['message'] = 'Failed to retrieve user information';
}

echo json_encode($response); 
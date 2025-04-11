<?php
require_once '../config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

// Get POST data
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$role = $_POST['role'] ?? 'user';

// Debug: Log the received data
error_log("Received registration request for username: $username, email: $email");

// Validate input
if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    $response['message'] = 'All fields are required';
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit;
}

if (strlen($password) < 8) {
    $response['message'] = 'Password must be at least 8 characters long';
    echo json_encode($response);
    exit;
}

if ($password !== $confirm_password) {
    $response['message'] = 'Passwords do not match';
    echo json_encode($response);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $response['message'] = 'Username can only contain letters, numbers, and underscores';
    echo json_encode($response);
    exit;
}

try {
    // Get database connection
    $pdo = getDBConnection();
    
    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        $response['message'] = 'Username or email already exists';
        echo json_encode($response);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    $result = $stmt->execute([$username, $email, $hashedPassword, $role]);
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Registration successful! Redirecting to login page...';
        error_log("User registered successfully: $username");
    } else {
        $response['message'] = 'Registration failed. Please try again.';
        error_log("Failed to insert new user: $username");
    }
} catch (PDOException $e) {
    error_log("Database error during registration: " . $e->getMessage());
    $response['message'] = 'Database error occurred. Please try again.';
} catch (Exception $e) {
    error_log("General error during registration: " . $e->getMessage());
    $response['message'] = 'An unexpected error occurred. Please try again.';
}

echo json_encode($response);
?> 
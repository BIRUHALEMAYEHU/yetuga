<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/auth/auth_helper.php';

header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);

    if (empty($username) || empty($password)) {
        $response['message'] = 'Please fill in all fields';
    } else {
        try {
            $db = getDBConnection();
            $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && AuthHelper::verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                
                // Handle remember me
                if ($remember_me) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
                    
                    $stmt = $db->prepare("INSERT INTO remember_tokens (user_id, token, expiry) VALUES (?, ?, ?)");
                    $stmt->execute([$user['id'], $token, $expiry]);
                    
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
                }
                
                $response['success'] = true;
                $response['message'] = 'Login successful';
            } else {
                $response['message'] = 'Invalid username or password';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?> 
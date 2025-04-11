<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/auth/auth_helper.php';

try {
    $db = getDBConnection();
    
    // Create test user
    $username = 'testuser';
    $email = 'test@example.com';
    $password = AuthHelper::hashPassword('password123');
    $role = 'user';
    
    $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password, $role]);
    
    echo "Test user created successfully!<br>";
    echo "Username: testuser<br>";
    echo "Password: password123<br>";
    echo "<a href='login.html'>Go to Login Page</a>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 
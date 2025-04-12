<?php
require_once 'includes/config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        die("Could not connect to database");
    }
    
    echo "Connected to database successfully!<br>";
    
    // Check if tables exist
    $tables = ['users', 'reports'];
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "Table '$table' exists<br>";
        } else {
            echo "Table '$table' does NOT exist<br>";
        }
    }
    
    // Check if we have any users
    $stmt = $conn->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "Number of users: $count<br>";
    
    // Check if we have any reports
    $stmt = $conn->query("SELECT COUNT(*) FROM reports");
    $count = $stmt->fetchColumn();
    echo "Number of reports: $count<br>";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?> 
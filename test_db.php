<?php
require_once 'config/database.php';

try {
    // Get database connection
    $pdo = getDBConnection();
    
    // Test the connection with a simple query
    $result = $pdo->query("SELECT 1");
    
    if ($result) {
        echo "Database connection is working!";
        error_log("Database test successful");
    } else {
        throw new Exception("Query execution failed");
    }
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage();
    error_log("Database test failed: " . $e->getMessage());
}
?> 
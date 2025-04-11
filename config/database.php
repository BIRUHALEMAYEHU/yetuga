<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$dbConfig = [
    'host' => 'localhost',
    'dbname' => 'yetuga',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

// Function to get database connection
function getDBConnection() {
    global $dbConfig;
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
            $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
            
            // Set PDO attributes
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
            error_log("Database connection established successfully");
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

// Test the connection
try {
    $pdo = getDBConnection();
} catch (Exception $e) {
    error_log("Failed to initialize database connection: " . $e->getMessage());
}
?> 
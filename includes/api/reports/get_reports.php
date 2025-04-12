<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';

// Check if user is authenticated
session_start();
if (!isAuthenticated() || !hasRole('transport_officer')) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        throw new Exception("Could not connect to database");
    }

    $query = "SELECT r.*, u.username as reporter_name 
              FROM reports r 
              LEFT JOIN users u ON r.reporter_id = u.id 
              ORDER BY r.created_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'reports' => $reports]);

} catch (Exception $e) {
    error_log("Reports Error: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Error loading reports: ' . $e->getMessage()]);
}
?> 
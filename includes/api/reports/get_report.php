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

// Get report ID from query parameter
$reportId = $_GET['id'] ?? null;
if (!$reportId) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Report ID is required']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT r.*, u.username as reporter_name 
              FROM reports r 
              LEFT JOIN users u ON r.reporter_id = u.id 
              WHERE r.id = :id";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([':id' => $reportId]);
    
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$report) {
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Report not found']);
        exit();
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'report' => $report]);

} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 
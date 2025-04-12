<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is logged in and is a transport officer
if (!isLoggedIn() || !isTransportOfficer()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Report ID is required']);
    exit;
}

try {
    $db = getDB();
    $reportId = $_GET['id'];
    
    $query = "SELECT r.*, u.username as reporter_name, u.email as reporter_email
              FROM reports r 
              JOIN users u ON r.user_id = u.id
              WHERE r.id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $reportId);
    $stmt->execute();
    
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$report) {
        http_response_code(404);
        echo json_encode(['error' => 'Report not found']);
        exit;
    }
    
    echo json_encode($report);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 
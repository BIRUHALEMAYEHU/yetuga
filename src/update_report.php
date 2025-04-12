<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is logged in and is a transport officer
if (!isLoggedIn() || !isTransportOfficer()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

if (!isset($_POST['id']) || !isset($_POST['status']) || !isset($_POST['response'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    $db = getDB();
    $reportId = $_POST['id'];
    $status = $_POST['status'];
    $response = $_POST['response'];
    
    $query = "UPDATE reports 
              SET status = :status, 
                  response = :response,
                  updated_at = CURRENT_TIMESTAMP
              WHERE id = :id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $reportId);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':response', $response);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update report']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 
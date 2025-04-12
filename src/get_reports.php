<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

// Check if user is logged in and is a transport officer
if (!isLoggedIn() || !isTransportOfficer()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

try {
    $db = getDB();
    
    $query = "SELECT r.*, u.username as reporter_name 
              FROM reports r 
              JOIN users u ON r.user_id = u.id";
    
    if ($filter !== 'all') {
        $query .= " WHERE r.status = :status";
    }
    
    $query .= " ORDER BY r.created_at DESC";
    
    $stmt = $db->prepare($query);
    
    if ($filter !== 'all') {
        $stmt->bindParam(':status', $filter);
    }
    
    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($reports);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 
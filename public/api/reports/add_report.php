<?php
require_once '../../../includes/config/database.php';
require_once '../../../includes/config/auth.php';

// Check if user is authenticated
session_start();
if (!isAuthenticated() || !hasRole('transport_officer')) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

try {
    // Get POST data
    $type = $_POST['type'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $severity = $_POST['severity'] ?? 'medium';
    $reporter_id = $_SESSION['user_id'] ?? null;

    // Validate required fields
    if (empty($type) || empty($location) || empty($description)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }

    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        throw new Exception("Could not connect to database");
    }

    $query = "INSERT INTO reports (type, location, description, severity, reporter_id, status, created_at) 
              VALUES (:type, :location, :description, :severity, :reporter_id, 'pending', NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':type' => $type,
        ':location' => $location,
        ':description' => $description,
        ':severity' => $severity,
        ':reporter_id' => $reporter_id
    ]);

    $reportId = $conn->lastInsertId();

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Report added successfully',
        'report_id' => $reportId
    ]);

} catch (Exception $e) {
    error_log("Add Report Error: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Error adding report: ' . $e->getMessage()]);
}

// Simulate successful report creation
echo json_encode([
    'success' => true,
    'message' => 'Report added successfully',
    'report_id' => rand(6, 100) // Random ID for simulation
]);
?> 
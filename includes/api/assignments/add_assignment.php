<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../auth/auth_utils.php';

// Check if user is authenticated and has transport officer role
if (!isAuthenticated() || !hasRole('transport_officer')) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['staff_id', 'route_id', 'vehicle_id', 'shift_start', 'shift_end'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing required field: $field"]);
        exit();
    }
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if staff member is already assigned for the given time slot
    $check_query = "
        SELECT COUNT(*) as count 
        FROM staff_assignments 
        WHERE staff_id = :staff_id 
        AND (
            (shift_start BETWEEN :shift_start AND :shift_end)
            OR (shift_end BETWEEN :shift_start AND :shift_end)
        )
    ";
    
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bindParam(':staff_id', $data['staff_id']);
    $check_stmt->bindParam(':shift_start', $data['shift_start']);
    $check_stmt->bindParam(':shift_end', $data['shift_end']);
    $check_stmt->execute();
    
    if ($check_stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Staff member already assigned during this time slot']);
        exit();
    }

    // Insert new assignment
    $query = "
        INSERT INTO staff_assignments 
        (staff_id, route_id, vehicle_id, shift_start, shift_end, status) 
        VALUES 
        (:staff_id, :route_id, :vehicle_id, :shift_start, :shift_end, 'active')
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':staff_id', $data['staff_id']);
    $stmt->bindParam(':route_id', $data['route_id']);
    $stmt->bindParam(':vehicle_id', $data['vehicle_id']);
    $stmt->bindParam(':shift_start', $data['shift_start']);
    $stmt->bindParam(':shift_end', $data['shift_end']);
    
    if ($stmt->execute()) {
        $assignment_id = $conn->lastInsertId();
        echo json_encode([
            'success' => true,
            'message' => 'Assignment created successfully',
            'id' => $assignment_id
        ]);
    } else {
        throw new Exception('Failed to create assignment');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit();
}
?> 
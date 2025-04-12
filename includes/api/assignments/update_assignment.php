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

// Check if request method is PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get PUT data
$data = json_decode(file_get_contents('php://input'), true);

// Validate assignment ID
if (!isset($data['id']) || empty($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Assignment ID is required']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Build update query dynamically based on provided fields
    $updateFields = [];
    $params = [':id' => $data['id']];
    
    $allowedFields = ['staff_id', 'route_id', 'vehicle_id', 'shift_start', 'shift_end', 'status'];
    foreach ($allowedFields as $field) {
        if (isset($data[$field]) && !empty($data[$field])) {
            $updateFields[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }

    if (empty($updateFields)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        exit();
    }

    // If shift times are being updated, check for conflicts
    if (isset($data['shift_start']) && isset($data['shift_end'])) {
        $check_query = "
            SELECT COUNT(*) as count 
            FROM staff_assignments 
            WHERE staff_id = :staff_id 
            AND id != :check_id
            AND (
                (shift_start BETWEEN :shift_start AND :shift_end)
                OR (shift_end BETWEEN :shift_start AND :shift_end)
            )
        ";
        
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bindParam(':staff_id', $data['staff_id']);
        $check_stmt->bindParam(':check_id', $data['id']);
        $check_stmt->bindParam(':shift_start', $data['shift_start']);
        $check_stmt->bindParam(':shift_end', $data['shift_end']);
        $check_stmt->execute();
        
        if ($check_stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Staff member already assigned during this time slot']);
            exit();
        }
    }

    // Update assignment
    $query = "UPDATE staff_assignments SET " . implode(', ', $updateFields) . " WHERE id = :id";
    $stmt = $conn->prepare($query);
    
    if ($stmt->execute($params)) {
        echo json_encode([
            'success' => true,
            'message' => 'Assignment updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update assignment');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit();
}
?> 
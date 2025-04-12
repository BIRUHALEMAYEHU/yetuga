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

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch all staff assignments with related information
    $query = "
        SELECT 
            a.id,
            u.username as staff_name,
            r.route_name,
            v.vehicle_number,
            a.shift_start,
            a.shift_end,
            a.status
        FROM staff_assignments a
        JOIN users u ON a.staff_id = u.id
        JOIN routes r ON a.route_id = r.id
        JOIN vehicles v ON a.vehicle_id = v.id
        ORDER BY a.shift_start ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the response for DataTables
    $response = [
        'data' => array_map(function($assignment) {
            return [
                'id' => $assignment['id'],
                'staffName' => $assignment['staff_name'],
                'route' => $assignment['route_name'],
                'vehicle' => $assignment['vehicle_number'],
                'shiftTime' => date('H:i', strtotime($assignment['shift_start'])) . ' - ' . 
                              date('H:i', strtotime($assignment['shift_end'])),
                'status' => $assignment['status']
            ];
        }, $assignments)
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit();
}
?> 
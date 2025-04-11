<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'activities' => [],
    'message' => ''
];

// Check if user is logged in and is an officer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'officer') {
    $response['message'] = 'Unauthorized access';
    echo json_encode($response);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Get recent activities (route updates, traffic alerts, reports)
    $query = "
        (SELECT 
            'Route Update' as event_type,
            created_at as time,
            CONCAT(start_point, ' - ', end_point) as route,
            status,
            'route' as source
        FROM routes
        WHERE updated_at >= NOW() - INTERVAL 24 HOUR)
        
        UNION ALL
        
        (SELECT 
            'Traffic Alert' as event_type,
            created_at as time,
            route_name as route,
            status,
            'alert' as source
        FROM route_alerts
        WHERE created_at >= NOW() - INTERVAL 24 HOUR)
        
        UNION ALL
        
        (SELECT 
            'New Report' as event_type,
            created_at as time,
            route_name as route,
            status,
            'report' as source
        FROM reports
        WHERE created_at >= NOW() - INTERVAL 24 HOUR)
        
        ORDER BY time DESC
        LIMIT 10";
    
    $stmt = $pdo->query($query);
    $activities = $stmt->fetchAll();
    
    $response['success'] = true;
    $response['activities'] = array_map(function($activity) {
        return [
            'time' => $activity['time'],
            'event' => $activity['event_type'],
            'route' => $activity['route'],
            'status' => ucfirst($activity['status'])
        ];
    }, $activities);
    $response['message'] = 'Activities retrieved successfully';
    
} catch (Exception $e) {
    error_log("Recent activity error: " . $e->getMessage());
    $response['message'] = 'Failed to retrieve recent activities';
}

echo json_encode($response); 
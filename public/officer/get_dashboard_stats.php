<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'stats' => [],
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
    
    // Get active routes count
    $stmt = $pdo->query("SELECT 
        COUNT(*) as current,
        (SELECT COUNT(*) FROM routes WHERE status = 'active' AND DATE(created_at) = DATE(NOW() - INTERVAL 1 DAY)) as previous
        FROM routes WHERE status = 'active'");
    $routeStats = $stmt->fetch();
    
    // Get vehicle statistics
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available,
        SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maintenance
        FROM vehicles");
    $vehicleStats = $stmt->fetch();
    
    // Get report statistics
    $stmt = $pdo->query("SELECT 
        COUNT(*) as pending,
        SUM(CASE WHEN priority = 'urgent' THEN 1 ELSE 0 END) as urgent
        FROM reports WHERE status = 'pending'");
    $reportStats = $stmt->fetch();
    
    // Get route alerts
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN severity = 'critical' THEN 1 ELSE 0 END) as critical
        FROM route_alerts WHERE status = 'active'");
    $alertStats = $stmt->fetch();
    
    $response['success'] = true;
    $response['stats'] = [
        'activeRoutes' => [
            'current' => (int)$routeStats['current'],
            'previous' => (int)$routeStats['previous']
        ],
        'vehicles' => [
            'available' => (int)$vehicleStats['available'],
            'maintenance' => (int)$vehicleStats['maintenance'],
            'total' => (int)$vehicleStats['total']
        ],
        'reports' => [
            'pending' => (int)$reportStats['pending'],
            'urgent' => (int)$reportStats['urgent']
        ],
        'alerts' => [
            'total' => (int)$alertStats['total'],
            'critical' => (int)$alertStats['critical']
        ]
    ];
    $response['message'] = 'Statistics retrieved successfully';
    
} catch (Exception $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    $response['message'] = 'Failed to retrieve dashboard statistics';
}

echo json_encode($response); 
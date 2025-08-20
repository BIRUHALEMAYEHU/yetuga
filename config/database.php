<?php
/**
 * Database Connection and Helper Functions for Yetuga App
 * This file provides database connectivity and utility functions
 */

// Include the database configuration
require_once 'database_config.php';

// Establish database connection
try {
    $pdo = getDatabaseConnection();
} catch (Exception $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

/**
 * Helper Functions for Database Operations
 */

/**
 * Execute a query and return the statement
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return PDOStatement
 */
function executeQuery($sql, $params = []) {
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch a single row from database
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return array|false Single row or false if not found
 */
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

/**
 * Fetch all rows from database
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return array Array of rows
 */
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

/**
 * Insert data and return the last insert ID
 * @param string $sql SQL insert query
 * @param array $params Parameters for prepared statement
 * @return int Last insert ID
 */
function insertAndGetId($sql, $params = []) {
    global $pdo;
    executeQuery($sql, $params);
    return $pdo->lastInsertId();
}

/**
 * Check if a record exists
 * @param string $table Table name
 * @param string $column Column to check
 * @param mixed $value Value to check
 * @return bool True if exists, false otherwise
 */
function recordExists($table, $column, $value) {
    $sql = "SELECT COUNT(*) FROM $table WHERE $column = ?";
    $count = fetchOne($sql, [$value]);
    return $count['COUNT(*)'] > 0;
}

/**
 * Sanitize input data
 * @param string $input Input string
 * @return string Sanitized string
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 * @param string $email Email to validate
 * @return bool True if valid, false otherwise
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (Ethiopian format)
 * @param string $phone Phone number to validate
 * @return bool True if valid, false otherwise
 */
function isValidPhone($phone) {
    // Ethiopian phone number format: +251XXXXXXXXX or 09XXXXXXXX
    return preg_match('/^(\+251|0)9[0-9]{8}$/', $phone);
}

/**
 * Format currency (Ethiopian Birr)
 * @param float $amount Amount to format
 * @return string Formatted currency string
 */
function formatCurrency($amount) {
    return 'ETB ' . number_format($amount, 2);
}

/**
 * Format date for display
 * @param string $date Date string or timestamp
 * @param string $format Output format
 * @return string Formatted date
 */
function formatDate($date, $format = 'Y-m-d H:i:s') {
    if (is_string($date)) {
        $date = strtotime($date);
    }
    return date($format, $date);
}

/**
 * Get time ago from timestamp
 * @param string $timestamp Timestamp to compare
 * @return string Human readable time ago
 */
function getTimeAgo($timestamp) {
    $time = time() - strtotime($timestamp);
    
    if ($time < 60) {
        return 'Just now';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($time < 2592000) {
        $days = floor($time / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        $months = floor($time / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    }
}

/**
 * Get user by ID
 * @param int $user_id User ID
 * @return array|false User data or false if not found
 */
function getUserById($user_id) {
    return fetchOne("SELECT * FROM users WHERE id = ? AND is_active = 1", [$user_id]);
}

/**
 * Get user by username or email
 * @param string $identifier Username or email
 * @return array|false User data or false if not found
 */
function getUserByIdentifier($identifier) {
    return fetchOne("SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1", [$identifier, $identifier]);
}

/**
 * Get user dashboard statistics
 * @param int $user_id User ID
 * @return array User statistics
 */
function getUserDashboardStats($user_id) {
    return fetchOne("SELECT * FROM user_dashboard_stats WHERE id = ?", [$user_id]);
}

/**
 * Get active routes between locations
 * @param string $from Starting location
 * @param string $to Destination location
 * @param string $transport_type Transport type filter
 * @return array Available routes
 */
function getRoutesBetweenLocations($from, $to, $transport_type = 'any') {
    if ($transport_type === 'any') {
        return fetchAll("SELECT * FROM active_routes_with_stops WHERE from_location LIKE ? OR to_location LIKE ?", 
                       ['%' . $from . '%', '%' . $to . '%']);
    } else {
        return fetchAll("SELECT * FROM active_routes_with_stops WHERE (from_location LIKE ? OR to_location LIKE ?) AND transport_type = ?", 
                       ['%' . $from . '%', '%' . $to . '%', $transport_type]);
    }
}

/**
 * Get latest news items
 * @param int $limit Number of news items to fetch
 * @return array News items
 */
function getLatestNews($limit = 5) {
    return fetchAll("SELECT * FROM news WHERE is_published = 1 ORDER BY published_at DESC LIMIT ?", [$limit]);
}

/**
 * Get user's recent reports
 * @param int $user_id User ID
 * @param int $limit Number of reports to fetch
 * @return array User's reports
 */
function getUserRecentReports($user_id, $limit = 5) {
    return fetchAll("SELECT * FROM reports WHERE user_id = ? ORDER BY created_at DESC LIMIT ?", [$user_id, $limit]);
}

/**
 * Get user's recent activity
 * @param int $user_id User ID
 * @param int $limit Number of activities to fetch
 * @return array User's activities
 */
function getUserRecentActivity($user_id, $limit = 10) {
    return fetchAll("SELECT * FROM user_activity WHERE user_id = ? ORDER BY created_at DESC LIMIT ?", [$user_id, $limit]);
}

/**
 * Log user activity to database
 * @param int $user_id User ID
 * @param string $activity_type Type of activity
 * @param string $description Activity description
 * @return bool Success status
 */
function logDatabaseActivity($user_id, $activity_type, $description = '') {
    try {
        $sql = "INSERT INTO user_activity (user_id, activity_type, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)";
        executeQuery($sql, [
            $user_id, 
            $activity_type, 
            $description,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        return true;
    } catch (Exception $e) {
        error_log("Failed to log user activity: " . $e->getMessage());
        return false;
    }
}

/**
 * Log route search
 * @param int|null $user_id User ID (null for anonymous)
 * @param string $from Starting location
 * @param string $to Destination location
 * @param string $transport_type Transport type
 * @param int $results_count Number of results found
 * @return bool Success status
 */
function logRouteSearch($user_id, $from, $to, $transport_type, $results_count) {
    try {
        $sql = "INSERT INTO route_searches (user_id, from_location, to_location, transport_type, results_count, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)";
        executeQuery($sql, [
            $user_id,
            $from,
            $to,
            $transport_type,
            $results_count,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
        // Log user activity if user is logged in
        if ($user_id) {
            logDatabaseActivity($user_id, 'route_search', "Searched for route from $from to $to");
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Failed to log route search: " . $e->getMessage());
        return false;
    }
}

/**
 * Submit a report
 * @param array $report_data Report data
 * @return int|false Report ID on success, false on failure
 */
function submitReport($report_data) {
    try {
        $sql = "INSERT INTO reports (user_id, report_type, from_location, to_location, blocked_location, transport_type, report_date, description, driver_name, vehicle_plate, witnesses_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $report_id = insertAndGetId($sql, [
            $report_data['user_id'],
            $report_data['report_type'],
            $report_data['from_location'] ?? null,
            $report_data['to_location'] ?? null,
            $report_data['blocked_location'] ?? null,
            $report_data['transport_type'] ?? null,
            $report_data['report_date'] ?? date('Y-m-d'),
            $report_data['description'] ?? null,
            $report_data['driver_name'] ?? null,
            $report_data['vehicle_plate'] ?? null,
            $report_data['witnesses_info'] ?? null
        ]);
        
        // Log user activity
        if ($report_id) {
            logDatabaseActivity($report_data['user_id'], 'report_submitted', "Submitted {$report_data['report_type']} report");
        }
        
        return $report_id;
    } catch (Exception $e) {
        error_log("Failed to submit report: " . $e->getMessage());
        return false;
    }
}

/**
 * Update user profile
 * @param int $user_id User ID
 * @param array $profile_data Profile data to update
 * @return bool Success status
 */
function updateUserProfile($user_id, $profile_data) {
    try {
        $sql = "UPDATE users SET first_name = ?, last_name = ?, phone = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        executeQuery($sql, [
            $profile_data['first_name'],
            $profile_data['last_name'],
            $profile_data['phone'] ?? null,
            $user_id
        ]);
        
        // Update preferences if provided
        if (isset($profile_data['preferences'])) {
            $pref_sql = "INSERT INTO user_preferences (user_id, preferred_transport_type, preferred_route_type, notifications_enabled) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE preferred_transport_type = VALUES(preferred_transport_type), preferred_route_type = VALUES(preferred_route_type), notifications_enabled = VALUES(notifications_enabled), updated_at = CURRENT_TIMESTAMP";
            executeQuery($pref_sql, [
                $user_id,
                $profile_data['preferences']['transport_type'] ?? 'any',
                $profile_data['preferences']['route_type'] ?? 'fastest',
                $profile_data['preferences']['notifications'] ?? true
            ]);
        }
        
        logDatabaseActivity($user_id, 'profile_updated', 'Updated profile information');
        return true;
    } catch (Exception $e) {
        error_log("Failed to update user profile: " . $e->getMessage());
        return false;
    }
}

/**
 * Get emergency updates for routes
 * @param int|null $route_id Specific route ID or null for all
 * @return array Emergency updates
 */
function getEmergencyUpdates($route_id = null) {
    if ($route_id) {
        return fetchAll("SELECT * FROM emergency_updates WHERE route_id = ? AND is_active = 1 AND (end_time IS NULL OR end_time > NOW()) ORDER BY start_time DESC", [$route_id]);
    } else {
        return fetchAll("SELECT * FROM emergency_updates WHERE is_active = 1 AND (end_time IS NULL OR end_time > NOW()) ORDER BY start_time DESC");
    }
}

/**
 * Check if user has permission for action
 * @param int $user_id User ID
 * @param string $action Action to check
 * @return bool True if user has permission
 */
function userHasPermission($user_id, $action) {
    $user = getUserById($user_id);
    if (!$user) return false;
    
    switch ($action) {
        case 'manage_routes':
            return in_array($user['role'], ['officer', 'admin']);
        case 'manage_reports':
            return in_array($user['role'], ['officer', 'admin']);
        case 'manage_users':
            return $user['role'] === 'admin';
        case 'submit_reports':
            return in_array($user['role'], ['user', 'officer', 'admin']);
        default:
            return false;
    }
}
?>

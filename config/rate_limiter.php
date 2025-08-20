<?php
/**
 * Simple Rate Limiter for Yetuga App
 * Prevents brute force attacks and API abuse
 */

/**
 * Check if an IP address has exceeded rate limits
 * 
 * @param string $action The action being rate limited (e.g., 'login', 'api_call')
 * @param int $max_attempts Maximum attempts allowed
 * @param int $time_window Time window in seconds
 * @return array Array with 'allowed' boolean and 'remaining_attempts' count
 */
function checkRateLimit($action, $max_attempts = 5, $time_window = 300) {
    // Get IP address and handle IPv6 localhost
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if ($ip === '::1' || $ip === '127.0.0.1') {
        $ip = 'localhost'; // Use a simpler identifier for local development
    }
    
    // Always use our project's temp directory
    $temp_dir = __DIR__ . '/../temp';
    if (!is_dir($temp_dir)) {
        mkdir($temp_dir, 0755, true);
    }
    
    // Ensure the directory is writable
    if (!is_writable($temp_dir)) {
        // If we can't write to temp directory, just allow the request
        error_log("Warning: Temp directory not writable, rate limiting disabled");
        return [
            'allowed' => true,
            'remaining_attempts' => 999,
            'blocked_until' => 0,
            'remaining_block_time' => 0,
            'message' => "Rate limiting temporarily disabled"
        ];
    }
    
    $cache_file = $temp_dir . "/yetuga_rate_limit_{$action}_{$ip}.txt";
    
    // Check if cache file exists and is within time window
    if (file_exists($cache_file)) {
        try {
            $file_content = file_get_contents($cache_file);
            if ($file_content === false) {
                throw new Exception("Could not read rate limit file");
            }
            $data = json_decode($file_content, true);
            if ($data === null) {
                throw new Exception("Invalid rate limit data format");
            }
        } catch (Exception $e) {
            // If there's an error reading the file, start fresh
            error_log("Rate limit file error: " . $e->getMessage());
            $data = [
                'attempts' => 1,
                'timestamp' => time(),
                'blocked_until' => 0
            ];
        }
        
        // If time window has passed, reset the counter
        if (time() - $data['timestamp'] > $time_window) {
            $data = [
                'attempts' => 1,
                'timestamp' => time(),
                'blocked_until' => 0
            ];
        } else {
            // Check if currently blocked
            if (isset($data['blocked_until']) && time() < $data['blocked_until']) {
                $remaining_block = $data['blocked_until'] - time();
                return [
                    'allowed' => false,
                    'remaining_attempts' => 0,
                    'blocked_until' => $data['blocked_until'],
                    'remaining_block_time' => $remaining_block,
                    'message' => "Too many attempts. Try again in " . ceil($remaining_block / 60) . " minutes."
                ];
            }
            
            // Increment attempt counter
            $data['attempts']++;
            
            // If max attempts exceeded, block the IP
            if ($data['attempts'] > $max_attempts) {
                $block_duration = 900; // 15 minutes
                $data['blocked_until'] = time() + $block_duration;
                
                return [
                    'allowed' => false,
                    'remaining_attempts' => 0,
                    'blocked_until' => $data['blocked_until'],
                    'remaining_block_time' => $block_duration,
                    'message' => "Too many failed attempts. Account locked for 15 minutes."
                ];
            }
        }
    } else {
        // First attempt
        $data = [
            'attempts' => 1,
            'timestamp' => time(),
            'blocked_until' => 0
        ];
    }
    
    // Save updated data with error handling
    try {
        $json_data = json_encode($data);
        if ($json_data === false) {
            throw new Exception("Could not encode rate limit data");
        }
        
        if (file_put_contents($cache_file, $json_data) === false) {
            throw new Exception("Could not write to rate limit file");
        }
    } catch (Exception $e) {
        // If there's an error, allow the request but log the error
        error_log("Warning: Rate limit error: " . $e->getMessage());
        // Don't fail the request, just log the error
    }
    
    return [
        'allowed' => true,
        'remaining_attempts' => $max_attempts - $data['attempts'],
        'blocked_until' => 0,
        'remaining_block_time' => 0,
        'message' => "Attempts remaining: " . ($max_attempts - $data['attempts'])
    ];
}

/**
 * Record a successful action (resets rate limiting)
 * 
 * @param string $action The action that was successful
 */
function recordSuccess($action) {
    // Get IP address and handle IPv6 localhost
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if ($ip === '::1' || $ip === '127.0.0.1') {
        $ip = 'localhost';
    }
    
    // Always use our project's temp directory
    $temp_dir = __DIR__ . '/../temp';
    $cache_file = $temp_dir . "/yetuga_rate_limit_{$action}_{$ip}.txt";
    
    // Remove the rate limit file on success
    if (file_exists($cache_file)) {
        try {
            unlink($cache_file);
        } catch (Exception $e) {
            error_log("Warning: Could not remove rate limit file: " . $e->getMessage());
        }
    }
}

/**
 * Get rate limit info for display
 * 
 * @param string $action The action to check
 * @return array Rate limit information
 */
function getRateLimitInfo($action) {
    // Get IP address and handle IPv6 localhost
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if ($ip === '::1' || $ip === '127.0.0.1') {
        $ip = 'localhost';
    }
    
    // Always use our project's temp directory
    $temp_dir = __DIR__ . '/../temp';
    $cache_file = $temp_dir . "/yetuga_rate_limit_{$action}_{$ip}.txt";
    
    if (!file_exists($cache_file)) {
        return [
            'attempts' => 0,
            'remaining' => 5,
            'blocked' => false
        ];
    }
    
    try {
        $file_content = file_get_contents($cache_file);
        if ($file_content === false) {
            return [
                'attempts' => 0,
                'remaining' => 5,
                'blocked' => false
            ];
        }
        
        $data = json_decode($file_content, true);
        if ($data === null || !isset($data['timestamp'])) {
            return [
                'attempts' => 0,
                'remaining' => 5,
                'blocked' => false
            ];
        }
    } catch (Exception $e) {
        error_log("Rate limit info error: " . $e->getMessage());
        return [
            'attempts' => 0,
            'remaining' => 5,
            'blocked' => false
        ];
    }
    
    $time_window = 300; // 5 minutes
    
    // Check if time window has passed
    if (time() - $data['timestamp'] > $time_window) {
        return [
            'attempts' => 0,
            'remaining' => 5,
            'blocked' => false
        ];
    }
    
    $blocked = isset($data['blocked_until']) && time() < $data['blocked_until'];
    
    return [
        'attempts' => $data['attempts'],
        'remaining' => 5 - $data['attempts'],
        'blocked' => $blocked,
        'blocked_until' => $data['blocked_until'] ?? 0
    ];
}
?>

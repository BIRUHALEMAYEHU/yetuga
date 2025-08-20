<?php
/**
 * Input Sanitization System for Yetuga App
 * Prevents XSS attacks and ensures data safety
 */

/**
 * Sanitize input data (remove dangerous content)
 * @param mixed $data The data to sanitize
 * @param string $type The type of sanitization (text, email, url, etc.)
 * @return mixed The sanitized data
 */
function sanitizeInputData($data, $type = 'text') {
    if (is_array($data)) {
        return array_map(function($item) use ($type) {
            return sanitizeInputData($item, $type);
        }, $data);
    }
    
    if (!is_string($data)) {
        return $data;
    }
    
    $data = trim($data);
    
    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
            
        case 'url':
            return filter_var($data, FILTER_SANITIZE_URL);
            
        case 'int':
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
            
        case 'float':
            return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            
        case 'html':
            // Allow some HTML but remove dangerous tags
            $allowed_tags = '<p><br><strong><em><u><ol><ul><li><h1><h2><h3><h4><h5><h6>';
            return strip_tags($data, $allowed_tags);
            
        case 'filename':
            // Remove path traversal and dangerous characters
            $data = preg_replace('/[^a-zA-Z0-9._-]/', '', $data);
            return basename($data);
            
        case 'username':
            // Only allow alphanumeric and underscore
            return preg_replace('/[^a-zA-Z0-9_]/', '', $data);
            
        case 'phone':
            // Only allow numbers, spaces, dashes, and parentheses
            return preg_replace('/[^0-9\s\-\(\)]/', '', $data);
            
        default:
            // Default: remove all HTML tags
            return strip_tags($data);
    }
}

/**
 * Sanitize output data (prevent XSS)
 * @param mixed $data The data to sanitize for output
 * @param string $context The output context (html, attribute, js, css)
 * @return mixed The sanitized data
 */
function sanitizeOutput($data, $context = 'html') {
    if (is_array($data)) {
        return array_map(function($item) use ($context) {
            return sanitizeOutput($item, $context);
        }, $data);
    }
    
    if (!is_string($data)) {
        return $data;
    }
    
    switch ($context) {
        case 'html':
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
        case 'attribute':
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
        case 'js':
            return json_encode($data);
            
        case 'css':
            return preg_replace('/[^a-zA-Z0-9\s\-_#.,%()]/', '', $data);
            
        case 'url':
            return filter_var($data, FILTER_SANITIZE_URL);
            
        default:
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

/**
 * Validate email address
 * @param string $email The email to validate
 * @return bool True if valid, false otherwise
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate URL
 * @param string $url The URL to validate
 * @return bool True if valid, false otherwise
 */
function validateURL($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Validate integer
 * @param mixed $value The value to validate
 * @param int $min Minimum value (optional)
 * @param int $max Maximum value (optional)
 * @return bool True if valid, false otherwise
 */
function validateInteger($value, $min = null, $max = null) {
    if (!is_numeric($value) || (int)$value != $value) {
        return false;
    }
    
    $value = (int)$value;
    
    if ($min !== null && $value < $min) {
        return false;
    }
    
    if ($max !== null && $value > $max) {
        return false;
    }
    
    return true;
}

/**
 * Validate string length
 * @param string $string The string to validate
 * @param int $min Minimum length
 * @param int $max Maximum length
 * @return bool True if valid, false otherwise
 */
function validateStringLength($string, $min, $max) {
    $length = mb_strlen($string, 'UTF-8');
    return $length >= $min && $length <= $max;
}

/**
 * Clean and validate file upload
 * @param array $file The $_FILES array element
 * @param array $allowed_types Allowed MIME types
 * @param int $max_size Maximum file size in bytes
 * @return array|false Cleaned file data or false if invalid
 */
function validateFileUpload($file, $allowed_types = [], $max_size = 5242880) {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        return false;
    }
    
    // Check file type if specified
    if (!empty($allowed_types) && !in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    // Sanitize filename
    $filename = sanitizeInputData($file['name'], 'filename');
    
    return [
        'name' => $filename,
        'type' => $file['type'],
        'size' => $file['size'],
        'tmp_name' => $file['tmp_name'],
        'error' => $file['error']
    ];
}

/**
 * Escape HTML entities for safe output
 * @param string $string The string to escape
 * @return string The escaped string
 */
function escapeHTML($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Remove potentially dangerous content
 * @param string $string The string to clean
 * @return string The cleaned string
 */
function removeDangerousContent($string) {
    // Remove JavaScript events
    $string = preg_replace('/on\w+\s*=/i', '', $string);
    
    // Remove script tags
    $string = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $string);
    
    // Remove iframe tags
    $string = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi', '', $string);
    
    // Remove object tags
    $string = preg_replace('/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/mi', '', $string);
    
    // Remove embed tags
    $string = preg_replace('/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/mi', '', $string);
    
    return $string;
}
?>

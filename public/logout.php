<?php
// Include session configuration
require_once '../config/session_config.php';

// Start secure session
startSecureSession();

// Log logout activity if user was logged in
if (isset($_SESSION['user_id'])) {
    logUserActivity($_SESSION['user_id'], 'logout', 'User logged out');
}

// Get logout reason
$logout_reason = $_GET['reason'] ?? 'user_logout';

// Destroy session completely
destroySession();

// Redirect to login page with appropriate message
$redirect_url = 'login.php';
if ($logout_reason === 'session_expired') {
    $redirect_url .= '?message=session_expired';
} elseif ($logout_reason === 'unauthorized') {
    $redirect_url .= '?message=unauthorized';
}

header('Location: ' . $redirect_url);
exit();
?>

<?php
require_once __DIR__ . '/../config/config.php';

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: ' . APP_URL . '/login.php');
exit();
?> 
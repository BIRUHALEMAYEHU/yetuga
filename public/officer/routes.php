<?php
// Include centralized session handler
require_once '../../config/session_handler.php';

// Handle session management with one line
$user_data = officerSessionCheck('Officer Routes Page');

// Extract user data
$user_name = $user_data['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Routes - Yetuga</title>
    <link rel="stylesheet" href="../assets/css/officer/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-route"></i>
                <h1>Yetuga</h1>
            </div>
            <nav class="nav">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="routes.php" class="nav-link active">
                    <i class="fas fa-route"></i>
                    Routes
                </a>
                <a href="reports.php" class="nav-link">
                    <i class="fas fa-flag"></i>
                    Reports
                </a>
                <a href="fares.html" class="nav-link">
                    <i class="fas fa-dollar-sign"></i>
                    Fares
                </a>
                <a href="emergency.html" class="nav-link">
                    <i class="fas fa-exclamation-triangle"></i>
                    Emergency
                </a>
            </nav>
            <div class="user-menu">
                <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="profile.html">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>
                    <a href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Manage Transport Routes</h1>
                <p>Add, edit, or remove transport routes in your area.</p>
                <?php echo getSessionTimer('../logout.php'); ?>
            </div>

            <!-- Route Management Content -->
            <div class="route-management">
                <h2>Route Management</h2>
                <p>This page will contain route management functionality.</p>
                <!-- Add your route management content here -->
            </div>
        </div>
    </main>

    <script src="../assets/js/officer/dashboard.js"></script>
</body>
</html> 
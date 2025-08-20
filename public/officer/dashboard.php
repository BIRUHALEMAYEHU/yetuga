<?php
// Include centralized session handler
require_once '../../config/session_handler.php';

// Handle session management with one line
$user_data = officerSessionCheck('Officer Dashboard');

// Extract user data
$user_name = $user_data['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Dashboard - Yetuga</title>
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
                <a href="dashboard.php" class="nav-link active">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="routes.php" class="nav-link">
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
                <h1>Transport Officer Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($user_name); ?>! Here's what's happening in your area.</p>
                <?php echo getSessionTimer('../logout.php'); ?>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <div class="stat-content">
                        <h3>15</h3>
                        <p>Active Routes</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-flag"></i>
                    </div>
                    <div class="stat-content">
                        <h3>8</h3>
                        <p>Pending Reports</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3>42</h3>
                            <p>Daily Commuters</p>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>2</h3>
                        <p>Active Alerts</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="actions-grid">
                    <a href="routes.php" class="action-card">
                        <i class="fas fa-route"></i>
                        <h3>Manage Routes</h3>
                        <p>Add, edit, or remove transport routes</p>
                    </a>
                    <a href="reports.php" class="action-card">
                        <i class="fas fa-flag"></i>
                        <h3>View Reports</h3>
                        <p>Review and respond to user reports</p>
                    </a>
                    <a href="fares.html" class="action-card">
                        <i class="fas fa-dollar-sign"></i>
                        <h3>Update Fares</h3>
                        <p>Modify fare information for routes</p>
                    </a>
                    <a href="emergency.html" class="action-card">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>Emergency Updates</h3>
                        <p>Post urgent route changes or alerts</p>
                    </a>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="recent-reports">
                <h2>Recent Reports</h2>
                <div class="reports-list">
                    <div class="report-item">
                        <div class="report-icon">
                            <i class="fas fa-flag"></i>
                        </div>
                        <div class="report-content">
                            <h4>Fare Dispute</h4>
                            <p>User reported overcharging on Route 15</p>
                            <span class="report-time">2 minutes ago</span>
                            <span class="report-status pending">Pending</span>
                        </div>
                    </div>
                    <div class="report-item">
                        <div class="report-icon">
                            <i class="fas fa-road"></i>
                        </div>
                        <div class="report-content">
                            <h4>Roadblock</h4>
                            <p>Construction work blocking Route 8</p>
                            <span class="report-time">15 minutes ago</span>
                            <span class="report-status in-progress">In Progress</span>
                        </div>
                    </div>
                    <div class="report-item">
                        <div class="report-icon">
                            <i class="fas fa-bus"></i>
                        </div>
                        <div class="report-content">
                            <h4>Service Quality</h4>
                            <p>Bus driver behavior complaint</p>
                            <span class="report-time">1 hour ago</span>
                            <span class="report-status resolved">Resolved</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/officer/dashboard.js"></script>
</body>
</html>

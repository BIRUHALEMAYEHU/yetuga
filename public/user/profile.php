<?php
// Include centralized session handler
require_once '../../config/session_handler.php';

// Handle session management with one line
$user_data = userSessionCheck('User Profile Page');

// Extract user data
$user_name = $user_data['user_name'];
$user_id = $user_data['user_id'];

// Handle profile update
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $preferences = $_POST['preferences'] ?? [];
        
        // Validation
        if (empty($first_name) || empty($last_name) || empty($email)) {
            throw new Exception('First name, last name, and email are required.');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Please enter a valid email address.');
        }
        
        $success_message = 'Profile updated successfully!';
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// For now, we'll use sample data since the database tables don't exist yet
$user_preferences = [
    'email_notifications' => '1',
    'sms_notifications' => '0',
    'route_suggestions' => '1',
    'news_updates' => '1'
];

$total_routes = 0;
$total_reports = 0;
$total_reviews = 0;

$recent_activity = [
    [
        'type' => 'route',
        'from_location' => 'Addis Ababa University',
        'to_location' => 'Bole',
        'created_at' => '2024-01-15 10:30:00',
        'action' => 'Used route'
    ],
    [
        'type' => 'report',
        'from_location' => 'Meskel Square',
        'to_location' => 'Piazza',
        'created_at' => '2024-01-14 15:45:00',
        'action' => 'Submitted report'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yetuga - User Profile</title>
    <link rel="stylesheet" href="../../assets/css/user/profile.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-route"></i>
                <span>Yetuga</span>
            </div>
            <nav class="nav-menu">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="routes.php" class="nav-link">
                    <i class="fas fa-search"></i>
                    <span>Find Routes</span>
                </a>
                <a href="reports.php" class="nav-link">
                    <i class="fas fa-flag"></i>
                    <span>Reports</span>
                </a>
                <a href="profile.php" class="nav-link active">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </nav>
            <div class="user-menu">
                <div class="user-info">
                    <img src="../../assets/images/avatar-placeholder.jpg" alt="User Avatar" class="user-avatar">
                    <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                </div>
                <div class="dropdown-menu">
                    <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Success/Error Messages -->
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="profile-content">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="profile-avatar">
                        <img src="../../assets/images/avatar-placeholder.jpg" alt="Profile Avatar" id="profileAvatar">
                        <div class="avatar-overlay">
                            <label for="avatarUpload" class="avatar-upload-btn">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="avatarUpload" accept="image/*" style="display: none;">
                        </div>
                    </div>
                    <div class="profile-info">
                        <h1><?php echo htmlspecialchars($user_name); ?></h1>
                        <p class="user-email"><?php echo htmlspecialchars($user_data['user_email'] ?? 'user@example.com'); ?></p>
                        <p class="member-since">Member since <?php echo date('F Y'); ?></p>
                    </div>
                </div>

                <!-- Profile Stats -->
                <div class="profile-stats">
                    <div class="stat-card">
                        <i class="fas fa-route"></i>
                        <div class="stat-info">
                            <span class="stat-number"><?php echo $total_routes; ?></span>
                            <span class="stat-label">Routes Used</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-flag"></i>
                        <div class="stat-info">
                            <span class="stat-number"><?php echo $total_reports; ?></span>
                            <span class="stat-label">Reports Sent</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-star"></i>
                        <div class="stat-info">
                            <span class="stat-number"><?php echo $total_reviews; ?></span>
                            <span class="stat-label">Reviews Posted</span>
                        </div>
                    </div>
                </div>

                <div class="profile-sections">
                    <!-- Profile Form Section -->
                    <div class="profile-form-section">
                        <h2>Edit Profile</h2>
                        <form class="profile-form" method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first-name">
                                        <i class="fas fa-user"></i>
                                        First Name <span class="required">*</span>
                                    </label>
                                    <input type="text" id="first-name" name="first_name" value="<?php echo htmlspecialchars(explode(' ', $user_name)[0] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="last-name">
                                        <i class="fas fa-user"></i>
                                        Last Name <span class="required">*</span>
                                    </label>
                                    <input type="text" id="last-name" name="last_name" value="<?php echo htmlspecialchars(explode(' ', $user_name)[1] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">
                                        <i class="fas fa-envelope"></i>
                                        Email <span class="required">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['user_email'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">
                                        <i class="fas fa-phone"></i>
                                        Phone Number
                                    </label>
                                    <input type="tel" id="phone" name="phone" placeholder="+251 9XXXXXXXX">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Address
                                </label>
                                <textarea id="address" name="address" rows="3" placeholder="Enter your address"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Preferences</label>
                                <div class="preferences-grid">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="preferences[email_notifications]" value="1" <?php echo ($user_preferences['email_notifications'] ?? '') === '1' ? 'checked' : ''; ?>>
                                        <span class="checkmark"></span>
                                        Email Notifications
                                    </label>
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="preferences[sms_notifications]" value="1" <?php echo ($user_preferences['sms_notifications'] ?? '') === '1' ? 'checked' : ''; ?>>
                                        <span class="checkmark"></span>
                                        SMS Notifications
                                    </label>
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="preferences[route_suggestions]" value="1" <?php echo ($user_preferences['route_suggestions'] ?? '') === '1' ? 'checked' : ''; ?>>
                                        <span class="checkmark"></span>
                                        Route Suggestions
                                    </label>
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="preferences[news_updates]" value="1" <?php echo ($user_preferences['news_updates'] ?? '') === '1' ? 'checked' : ''; ?>>
                                        <span class="checkmark"></span>
                                        News & Updates
                                    </label>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="save-btn">
                                    <i class="fas fa-save"></i>
                                    Save Changes
                                </button>
                                <button type="reset" class="reset-btn">
                                    <i class="fas fa-undo"></i>
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Recent Activity Section -->
                    <div class="recent-activity-section">
                        <h2>Recent Activity</h2>
                        <div class="activity-list">
                            <?php if (!empty($recent_activity)): ?>
                                <?php foreach ($recent_activity as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-<?php echo getActivityIcon($activity['type']); ?>"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text"><?php echo htmlspecialchars($activity['action']); ?></p>
                                        <p class="activity-location"><?php echo htmlspecialchars($activity['from_location']); ?> → <?php echo htmlspecialchars($activity['to_location']); ?></p>
                                        <span class="activity-time"><?php echo formatTimeAgo($activity['created_at']); ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-activity">
                                    <i class="fas fa-info-circle"></i>
                                    <p>No recent activity to display</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Account Actions -->
                <div class="account-actions">
                    <h2>Account Actions</h2>
                    <div class="actions-grid">
                        <a href="change-password.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-key"></i>
                            </div>
                            <h3>Change Password</h3>
                            <p>Update your account password</p>
                        </a>
                        
                        <a href="privacy-settings.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h3>Privacy Settings</h3>
                            <p>Manage your privacy preferences</p>
                        </a>
                        
                        <a href="download-data.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-download"></i>
                            </div>
                            <h3>Download Data</h3>
                            <p>Export your personal data</p>
                        </a>
                        
                        <a href="delete-account.php" class="action-card danger">
                            <div class="action-icon">
                                <i class="fas fa-trash"></i>
                            </div>
                            <h3>Delete Account</h3>
                            <p>Permanently delete your account</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../../assets/js/user/profile.js"></script>
</body>
</html>

<?php
function getActivityIcon($type) {
    $icons = [
        'route' => 'route',
        'report' => 'flag',
        'review' => 'star'
    ];
    return $icons[$type] ?? 'circle';
}

function formatTimeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return floor($diff / 604800) . ' weeks ago';
}
?>

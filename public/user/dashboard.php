<?php
// Include centralized session handler
require_once '../../config/session_handler.php';

// Handle session management with one line
$user_data = userSessionCheck('User Dashboard');

// Extract user data
$user_name = $user_data['user_name'];
$user_id = $user_data['user_id'];

// For now, we'll use sample data since the database tables don't exist yet
$routes_used = 0;
$reports_sent = 0;
$reviews_posted = 0;

// Sample recent routes data
$recent_routes = [
    [
        'from_location' => 'Addis Ababa University',
        'to_location' => 'Bole',
        'transport_type' => 'Minibus',
        'travel_time' => '45 min',
        'fare' => '25 ETB'
    ],
    [
        'from_location' => 'Meskel Square',
        'to_location' => 'Piazza',
        'transport_type' => 'Bus',
        'travel_time' => '30 min',
        'fare' => '15 ETB'
    ]
];

// Sample news data
$news_items = [
    [
        'type' => 'announcement',
        'title' => 'New Route Added',
        'content' => 'Route 25 now connects Addis Ababa University to Bole International Airport'
    ],
    [
        'type' => 'update',
        'title' => 'Fare Update',
        'content' => 'Minibus fares have been updated for routes in the city center'
    ],
    [
        'type' => 'maintenance',
        'title' => 'Service Notice',
        'content' => 'Route 8 will have reduced service due to road construction'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yetuga - User Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/user/dashboard.css">
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
                <a href="dashboard.php" class="nav-link active">
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
                <a href="profile.php" class="nav-link">
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
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-content">
                    <h1>Welcome back, <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?>! 👋</h1>
                    <p class="lead">Navigate Your City with Confidence - Find the most efficient transport routes and discover urban services.</p>
                    <?php echo getSessionTimer('../logout.php'); ?>
                    
                    <!-- Route Search Box -->
                    <div class="search-box">
                        <h2>Where do you want to go?</h2>
                        <form class="route-search-form" action="routes.php" method="GET">
                            <div class="search-row">
                                <div class="form-group">
                                    <label for="from-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        From
                                    </label>
                                    <input type="text" id="from-location" name="from" placeholder="Start location" required>
                                </div>
                                <div class="form-group">
                                    <label for="to-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        To
                                    </label>
                                    <input type="text" id="to-location" name="to" placeholder="End location" required>
                                </div>
                            </div>
                            
                            <div class="search-row">
                                <div class="form-group">
                                    <label for="transport-type">
                                        <i class="fas fa-bus"></i>
                                        Transport Type
                                    </label>
                                    <select id="transport-type" name="transport_type">
                                        <option value="">Any Transport</option>
                                        <option value="bus">Public Bus</option>
                                        <option value="taxi">Taxi</option>
                                        <option value="train">Light Rail</option>
                                        <option value="minibus">Minibus</option>
                                    </select>
                                </div>
                            </div>
                            
                            <button type="submit" class="search-btn">
                                <i class="fas fa-search"></i>
                                Find Best Route
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Stats Section -->
        <section class="quick-stats-section">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-route"></i>
                        <div class="stat-info">
                            <span class="stat-number"><?php echo $routes_used; ?></span>
                            <span class="stat-label">Routes Used</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-flag"></i>
                        <div class="stat-info">
                            <span class="stat-number"><?php echo $reports_sent; ?></span>
                            <span class="stat-label">Reports Sent</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-star"></i>
                        <div class="stat-info">
                            <span class="stat-number"><?php echo $reviews_posted; ?></span>
                            <span class="stat-label">Reviews Posted</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-clock"></i>
                        <div class="stat-info">
                            <span class="stat-number">0</span>
                            <span class="stat-label">Hours Saved</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recent Routes Section -->
        <?php if (!empty($recent_routes)): ?>
        <section class="recent-routes-section">
            <div class="container">
                <h2>Recent Routes</h2>
                <div class="routes-grid">
                    <?php foreach ($recent_routes as $route): ?>
                    <div class="route-card">
                        <div class="route-info">
                            <h3><?php echo htmlspecialchars($route['from_location']); ?> → <?php echo htmlspecialchars($route['to_location']); ?></h3>
                            <p><i class="fas fa-bus"></i> <?php echo htmlspecialchars($route['transport_type']); ?></p>
                            <p><i class="fas fa-clock"></i> <?php echo htmlspecialchars($route['travel_time']); ?></p>
                            <p><i class="fas fa-coins"></i> <?php echo htmlspecialchars($route['fare']); ?></p>
                        </div>
                        <div class="route-actions">
                            <a href="routes.php?from=<?php echo urlencode($route['from_location']); ?>&to=<?php echo urlencode($route['to_location']); ?>" class="btn btn-primary">Use Again</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Features Section -->
        <section class="features-section">
            <div class="container">
                <h2>Why Choose Yetuga?</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-route"></i>
                        </div>
                        <h3>Smart Route Planning</h3>
                        <p>Find optimal transport routes using public transport, minibuses, and light rail with real-time updates.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <h3>Fair Fare System</h3>
                        <p>Access transparent fare information and avoid overcharging with our verified pricing system.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3>Urban Services</h3>
                        <p>Discover nearby businesses, services, and points of interest along your route.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Actions Section -->
        <section class="quick-actions-section">
            <div class="container">
                <h2>Quick Actions</h2>
                <div class="actions-grid">
                    <a href="reports.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-flag"></i>
                        </div>
                        <h3>Report Issue</h3>
                        <p>Report roadblocks, fare disputes, or driver issues</p>
                    </a>
                    
                    <a href="routes.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>Find Routes</h3>
                        <p>Search for optimal transport routes</p>
                    </a>
                    
                    <a href="profile.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3>View Profile</h3>
                        <p>Manage your account and preferences</p>
                    </a>
                    
                    <a href="favorites.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3>Favorite Routes</h3>
                        <p>Quick access to your frequently used routes</p>
                    </a>
                </div>
            </div>
        </section>

        <!-- News Section -->
        <section class="news-section">
            <div class="container">
                <h2>Latest Updates & News</h2>
                <div class="news-grid">
                    <?php foreach ($news_items as $news): ?>
                    <div class="news-card">
                        <div class="news-icon">
                            <i class="fas fa-<?php echo getNewsIcon($news['type']); ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                        <p><?php echo htmlspecialchars($news['content']); ?></p>
                        <span class="news-date"><?php echo formatTimeAgo($news['created_at'] ?? time()); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <script src="../../assets/js/user/dashboard.js"></script>
</body>
</html>

<?php
// Helper functions
function getNewsIcon($type) {
    $icons = [
        'route' => 'bus',
        'fare' => 'coins',
        'maintenance' => 'tools',
        'announcement' => 'bullhorn',
        'update' => 'sync'
    ];
    return $icons[$type] ?? 'newspaper';
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

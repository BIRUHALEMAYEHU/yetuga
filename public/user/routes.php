<?php
// Include centralized session handler
require_once '../../config/session_handler.php';

// Handle session management with one line
$user_data = userSessionCheck('User Routes Page');

// Extract user data
$user_name = $user_data['user_name'];
$user_id = $user_data['user_id'];

// Get search parameters
$from_location = $_GET['from'] ?? '';
$to_location = $_GET['to'] ?? '';
$transport_type = $_GET['transport_type'] ?? '';

// For now, we'll use sample data since the database tables don't exist yet
$routes = [];
$search_performed = false;

if (!empty($from_location) && !empty($to_location)) {
    $search_performed = true;
    
    // Sample route data for testing
    $routes = [
        [
            'id' => 1,
            'route_name' => 'Route 15',
            'transport_type' => 'Minibus',
            'fare' => '25 ETB',
            'estimated_time' => '45 min',
            'distance' => '8.5 km',
            'from_location' => $from_location,
            'to_location' => $to_location
        ],
        [
            'id' => 2,
            'route_name' => 'Route 8',
            'transport_type' => 'Bus',
            'fare' => '15 ETB',
            'estimated_time' => '60 min',
            'distance' => '12.2 km',
            'from_location' => $from_location,
            'to_location' => $to_location
        ],
        [
            'id' => 3,
            'route_name' => 'Route 25',
            'transport_type' => 'Light Rail',
            'fare' => '20 ETB',
            'estimated_time' => '35 min',
            'distance' => '7.8 km',
            'from_location' => $from_location,
            'to_location' => $to_location
        ]
    ];
}

// Sample popular routes data
$popular_routes = [
    [
        'id' => 4,
        'from_location' => 'Addis Ababa University',
        'to_location' => 'Bole International Airport',
        'route_name' => 'Route 25',
        'transport_type' => 'Light Rail',
        'fare' => '20 ETB',
        'estimated_time' => '35 min',
        'search_count' => 156
    ],
    [
        'id' => 5,
        'from_location' => 'Meskel Square',
        'to_location' => 'Piazza',
        'route_name' => 'Route 8',
        'transport_type' => 'Bus',
        'fare' => '15 ETB',
        'estimated_time' => '30 min',
        'search_count' => 142
    ],
    [
        'id' => 6,
        'from_location' => 'Bole',
        'to_location' => 'Kazanchis',
        'route_name' => 'Route 15',
        'transport_type' => 'Minibus',
        'fare' => '25 ETB',
        'estimated_time' => '45 min',
        'search_count' => 98
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yetuga - Find Routes</title>
    <link rel="stylesheet" href="../../assets/css/user/routes.css">
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
                <a href="routes.php" class="nav-link active">
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
        <!-- Search Section -->
        <section class="search-section">
            <div class="search-container">
                <h1>Find Your Perfect Route</h1>
                <p>Discover the best transport options with real-time information</p>
                <?php echo getSessionTimer('../logout.php'); ?>
                
                <form class="route-search-form" method="GET" action="routes.php">
                    <div class="search-row">
                        <div class="form-group">
                            <label for="from-location">
                                <i class="fas fa-map-marker-alt"></i>
                                From
                            </label>
                            <input type="text" id="from-location" name="from" placeholder="Enter starting point" value="<?php echo htmlspecialchars($from_location); ?>" required>
                            <div class="suggestions" id="from-suggestions"></div>
                        </div>
                        
                        <div class="swap-btn" id="swapBtn">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        
                        <div class="form-group">
                            <label for="to-location">
                                <i class="fas fa-map-marker-alt"></i>
                                To
                            </label>
                            <input type="text" id="to-location" name="to" placeholder="Enter destination" value="<?php echo htmlspecialchars($to_location); ?>" required>
                            <div class="suggestions" id="to-suggestions"></div>
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
                                <option value="bus" <?php echo $transport_type === 'bus' ? 'selected' : ''; ?>>Public Bus</option>
                                <option value="taxi" <?php echo $transport_type === 'taxi' ? 'selected' : ''; ?>>Taxi</option>
                                <option value="train" <?php echo $transport_type === 'train' ? 'selected' : ''; ?>>Light Rail</option>
                                <option value="minibus" <?php echo $transport_type === 'minibus' ? 'selected' : ''; ?>>Minibus</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                        Find Routes
                    </button>
                </form>
            </div>
        </section>

        <!-- Results Section -->
        <?php if ($search_performed): ?>
            <?php if (!empty($routes)): ?>
                <!-- Results Section -->
                <section class="results-section">
                    <div class="results-header">
                        <h2>Available Routes</h2>
                        <div class="results-info">
                            <span><?php echo count($routes); ?> routes found</span>
                            <span>From: <?php echo htmlspecialchars($from_location); ?> → To: <?php echo htmlspecialchars($to_location); ?></span>
                        </div>
                    </div>
                    
                    <div class="routes-list">
                        <?php foreach ($routes as $route): ?>
                        <div class="route-item" data-route-id="<?php echo $route['id']; ?>">
                            <div class="route-header">
                                <div class="route-type">
                                    <i class="fas fa-<?php echo getTransportIcon($route['transport_type']); ?>"></i>
                                    <span><?php echo htmlspecialchars(ucfirst($route['transport_type'])); ?></span>
                                </div>
                                <div class="route-number"><?php echo htmlspecialchars($route['route_name']); ?></div>
                            </div>
                            
                            <div class="route-details">
                                <div class="route-info">
                                    <div class="route-location">
                                        <span class="from"><?php echo htmlspecialchars($route['from_location']); ?></span>
                                        <i class="fas fa-arrow-right"></i>
                                        <span class="to"><?php echo htmlspecialchars($route['to_location']); ?></span>
                                    </div>
                                    <div class="route-meta">
                                        <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($route['estimated_time']); ?></span>
                                        <span><i class="fas fa-coins"></i> <?php echo htmlspecialchars($route['fare']); ?></span>
                                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($route['distance']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="route-actions">
                                    <button class="btn btn-primary view-route-btn" onclick="viewRouteDetails(<?php echo $route['id']; ?>)">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                    <button class="btn btn-secondary save-route-btn" onclick="saveRoute(<?php echo $route['id']; ?>)">
                                        <i class="fas fa-heart"></i> Save
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php else: ?>
                <!-- No Results Section -->
                <section class="no-results">
                    <div class="no-results-content">
                        <i class="fas fa-search"></i>
                        <h3>No routes found</h3>
                        <p>Try adjusting your search criteria or check if there are any service disruptions</p>
                        <a href="routes.php" class="retry-btn">
                            <i class="fas fa-redo"></i>
                            Try Again
                        </a>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Popular Routes Section -->
        <section class="popular-routes-section">
            <div class="container">
                <h2>Popular Routes</h2>
                <div class="popular-routes-grid">
                    <?php foreach ($popular_routes as $route): ?>
                    <div class="popular-route-card">
                        <div class="route-icon">
                            <i class="fas fa-<?php echo getTransportIcon($route['transport_type']); ?>"></i>
                        </div>
                        <div class="route-info">
                            <h3><?php echo htmlspecialchars($route['from_location']); ?> → <?php echo htmlspecialchars($route['to_location']); ?></h3>
                            <p><?php echo htmlspecialchars($route['route_name']); ?></p>
                            <div class="route-meta">
                                <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($route['estimated_time']); ?></span>
                                <span><i class="fas fa-coins"></i> <?php echo htmlspecialchars($route['fare']); ?></span>
                            </div>
                        </div>
                        <a href="routes.php?from=<?php echo urlencode($route['from_location']); ?>&to=<?php echo urlencode($route['to_location']); ?>" class="use-route-btn">
                            <i class="fas fa-search"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Route Details Modal -->
    <div class="modal" id="routeModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Route Details</h3>
                <button class="close-btn" id="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Route details will be populated here -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="closeModalBtn">Close</button>
                <button class="btn btn-primary" id="saveRouteBtn">
                    <i class="fas fa-heart"></i>
                    Save Route
                </button>
            </div>
        </div>
    </div>

    <script src="../../assets/js/user/routes.js"></script>
</body>
</html>

<?php
function getTransportIcon($type) {
    $icons = [
        'bus' => 'bus',
        'taxi' => 'taxi',
        'train' => 'train',
        'minibus' => 'bus'
    ];
    return $icons[$type] ?? 'bus';
}
?>

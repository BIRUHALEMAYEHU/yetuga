<?php
// Include centralized session handler
require_once '../../config/session_handler.php';
require_once '../../config/csrf_protection.php';
require_once '../../config/input_sanitization.php';

// Handle session management with one line
$user_data = userSessionCheck('User Reports Page');

// Extract user data
$user_name = $user_data['user_name'];
$user_id = $user_data['user_id'];

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $report_type = $_POST['report_type'] ?? '';
        $from_location = $_POST['from_location'] ?? '';
        $to_location = $_POST['to_location'] ?? '';
        $description = $_POST['description'] ?? '';
        $date = $_POST['date'] ?? date('Y-m-d');
        $time = $_POST['time'] ?? '';
        
        // Validate required fields
        if (empty($report_type) || empty($from_location) || empty($to_location)) {
            throw new Exception('Please fill in all required fields.');
        }
        
        // Validate type-specific required fields
        if ($report_type === 'fareDispute') {
            $transport_type = $_POST['transport_type'] ?? '';
            $vehicle_plate = $_POST['vehicle_plate'] ?? '';
            
            if (empty($transport_type) || empty($vehicle_plate)) {
                throw new Exception('Transport type and vehicle plate number are required for fare disputes.');
            }
        } elseif ($report_type === 'roadblock') {
            $blocked_location = $_POST['blocked_location'] ?? '';
            
            if (empty($blocked_location)) {
                throw new Exception('Blocked location is required for roadblock reports.');
            }
        }
        
        $success_message = 'Report submitted successfully! Our team will review it shortly.';
        
        // Clear form data after successful submission
        $_POST = [];
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// For now, we'll use sample data since the database tables don't exist yet
$recent_reports = [
    [
        'report_type' => 'Fare Dispute',
        'from_location' => 'Addis Ababa University',
        'to_location' => 'Bole',
        'status' => 'Pending',
        'created_at' => '2024-01-15 10:30:00'
    ],
    [
        'report_type' => 'Roadblock',
        'from_location' => 'Meskel Square',
        'to_location' => 'Piazza',
        'status' => 'In Progress',
        'created_at' => '2024-01-14 15:45:00'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yetuga - Submit Reports & Complaints</title>
    <link rel="stylesheet" href="../../assets/css/user/reports.css">
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
                <a href="reports.php" class="nav-link active">
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
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Submit Reports & Complaints</h1>
                <p>Help us improve the transport system by reporting issues and concerns</p>
                <?php echo getSessionTimer('../logout.php'); ?>
            </div>

            <!-- Messages -->
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

            <!-- Report Form -->
            <div class="report-form-container">
                <form class="report-form" method="POST" enctype="multipart/form-data">
                    <?php echo csrfTokenField(); ?>
                    <div class="form-section">
                        <h3>Report Type</h3>
                        <div class="form-group">
                            <label for="report_type">Select Report Type *</label>
                            <select id="report_type" name="report_type" required>
                                <option value="">Choose report type...</option>
                                <option value="fareDispute" <?php echo sanitizeOutput($_POST['report_type'] ?? '') === 'fareDispute' ? 'selected' : ''; ?>>Fare Dispute</option>
                                <option value="roadblock" <?php echo sanitizeOutput($_POST['report_type'] ?? '') === 'roadblock' ? 'selected' : ''; ?>>Roadblock</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Location Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="from_location">From Location *</label>
                                <input type="text" id="from_location" name="from_location" value="<?php echo sanitizeOutput($_POST['from_location'] ?? ''); ?>" placeholder="Starting point" required>
                            </div>
                            <div class="form-group">
                                <label for="to_location">To Location *</label>
                                <input type="text" id="to_location" name="to_location" value="<?php echo sanitizeOutput($_POST['to_location'] ?? ''); ?>" placeholder="Destination" required>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Fields Based on Report Type -->
                    <div id="fareDispute-fields" class="form-section" style="display: none;">
                        <h3>Fare Dispute Details</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="transport_type">Transport Type *</label>
                                <select id="transport_type" name="transport_type">
                                    <option value="">Select transport type...</option>
                                    <option value="bus">Public Bus</option>
                                    <option value="taxi">Taxi</option>
                                    <option value="minibus">Minibus</option>
                                    <option value="train">Light Rail</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="vehicle_plate">Vehicle Plate Number *</label>
                                <input type="text" id="vehicle_plate" name="vehicle_plate" placeholder="e.g., AA-12345" value="<?php echo sanitizeOutput($_POST['vehicle_plate'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="driver_name">Driver Name/ID</label>
                                <input type="text" id="driver_name" name="driver_name" placeholder="Driver's name or ID" value="<?php echo sanitizeOutput($_POST['driver_name'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="witnesses">Witnesses Information</label>
                                <input type="text" id="witnesses" name="witnesses" placeholder="Any witnesses or additional info" value="<?php echo sanitizeOutput($_POST['witnesses'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <div id="roadblock-fields" class="form-section" style="display: none;">
                        <h3>Roadblock Details</h3>
                        <div class="form-group">
                            <label for="blocked_location">Blocked Location *</label>
                            <input type="text" id="blocked_location" name="blocked_location" placeholder="Specific location of the roadblock" value="<?php echo sanitizeOutput($_POST['blocked_location'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="reason">Reason for Blockage</label>
                            <input type="text" id="reason" name="reason" placeholder="Construction, accident, etc." value="<?php echo sanitizeOutput($_POST['reason'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Additional Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="date">Date *</label>
                                <input type="date" id="date" name="date" value="<?php echo sanitizeOutput($_POST['date'] ?? date('Y-m-d')); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="time">Time</label>
                                <input type="time" id="time" name="time" value="<?php echo sanitizeOutput($_POST['time'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4" placeholder="Provide additional details about the issue..." value="<?php echo htmlspecialchars($_POST['description'] ?? ''); ?>"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="attachments">Attachments</label>
                            <input type="file" id="attachments" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx">
                            <small>You can upload images, PDFs, or documents (max 5 files)</small>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="agree_terms" required>
                                <span class="checkmark"></span>
                                I agree to the terms and conditions
                            </label>
                        </div>
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="confirm_accuracy" required>
                                <span class="checkmark"></span>
                                I confirm that the information provided is accurate to the best of my knowledge
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            Submit Report
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i>
                            Reset Form
                        </button>
                    </div>
                </form>
            </div>

            <!-- Recent Reports -->
            <div class="recent-reports">
                <h2>Your Recent Reports</h2>
                <div class="reports-list">
                    <?php foreach ($recent_reports as $report): ?>
                    <div class="report-item">
                        <div class="report-icon">
                            <i class="fas fa-<?php echo $report['report_type'] === 'Fare Dispute' ? 'flag' : 'road'; ?>"></i>
                        </div>
                        <div class="report-content">
                            <h4><?php echo htmlspecialchars($report['report_type']); ?></h4>
                            <p><?php echo htmlspecialchars($report['from_location']); ?> → <?php echo htmlspecialchars($report['to_location']); ?></p>
                            <span class="report-time"><?php echo date('M j, Y', strtotime($report['created_at'])); ?></span>
                            <span class="report-status <?php echo strtolower(str_replace(' ', '-', $report['status'])); ?>"><?php echo htmlspecialchars($report['status']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="../../assets/js/user/reports.js"></script>
</body>
</html>

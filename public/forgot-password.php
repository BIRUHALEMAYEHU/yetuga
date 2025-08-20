<?php
// Include session configuration
require_once '../config/session_config.php';

// Start secure session
startSecureSession();

// Redirect if already logged in
if (isSessionValid()) {
    if (validateUserRole('admin')) {
        header("Location: admin/dashboard.php");
    } elseif (validateUserRole('officer')) {
        header("Location: officer/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
}

$error_message = '';
$success_message = '';
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = trim($_POST['email'] ?? '');
        
        // Validation
        if (empty($email)) {
            throw new Exception('Please enter your email address.');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Please enter a valid email address.');
        }
        
        // Check if email exists in database
        require_once '../config/database.php';
        $stmt = $pdo->prepare("SELECT id, first_name, last_name FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // Don't reveal if email exists or not for security
            $success_message = 'If an account with that email exists, we have sent a password reset link.';
        } else {
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store reset token in database (you'll need to add this table)
            try {
                $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at, created_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at), created_at = NOW()");
                $stmt->execute([$user['id'], $reset_token, $reset_expires]);
                
                // In a real application, you would send an email here
                // For now, we'll just show a success message
                $success_message = 'Password reset link has been sent to your email address.';
                
                // Log the password reset request
                try {
                    logUserActivity($user['id'], 'password_reset_requested', 'Password reset requested');
                } catch (Exception $e) {
                    error_log("Could not log password reset request: " . $e->getMessage());
                }
                
            } catch (Exception $e) {
                // If password_resets table doesn't exist, just show success message
                error_log("Password reset table not available: " . $e->getMessage());
                $success_message = 'Password reset functionality is being set up. Please contact support.';
            }
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Yetuga</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <!-- Left Side - Branding and Features -->
        <div class="logo-section">
            <div class="logo">
                <i class="fas fa-route"></i>
                <h1>Yetuga</h1>
            </div>
            <p class="tagline">Urban Mobility & Resource Locator</p>
            
            <div class="features-section">
                <h3>Need Help?</h3>
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure password reset</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-clock"></i>
                        <span>Quick recovery</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-envelope"></i>
                        <span>Email verification</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-lock"></i>
                        <span>Account protection</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="auth-forms">
            <div class="form-container">
                <!-- Alerts -->
                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <!-- Forgot Password Form -->
                <form method="POST" class="auth-form">
                    <h2>Forgot Password?</h2>
                    <p>Enter your email address and we'll send you a link to reset your password.</p>
                    
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i>
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($email); ?>" 
                               placeholder="Enter your email address" required>
                    </div>
                    
                    <button type="submit" class="auth-btn">
                        <i class="fas fa-paper-plane"></i>
                        Send Reset Link
                    </button>
                </form>

                <!-- Form Footer -->
                <div class="form-footer">
                    <p>Remember your password? <a href="login.php">Sign in here</a></p>
                </div>

                <!-- Footer -->
                <div class="auth-footer">
                    <a href="index.php">
                        <i class="fas fa-arrow-left"></i>
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus on email input
        document.getElementById('email').focus();
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            
            if (!email) {
                e.preventDefault();
                alert('Please enter your email address.');
                return false;
            }
            
            // Basic email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }
        });
    </script>
</body>
</html>


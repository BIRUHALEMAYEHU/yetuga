<?php
// Include session configuration and rate limiter
require_once '../config/session_config.php';
require_once '../config/rate_limiter.php';
require_once '../config/csrf_protection.php';
require_once '../config/input_sanitization.php';

// Start secure session
startSecureSession();

// Redirect if already logged in and session is valid
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
$form_data = []; // Store form data for repopulation

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['register'])) {
    try {
        require_once '../config/database.php';
        
        // Login logic
        if (isset($_POST['login'])) {
            // Verify CSRF token
            if (!verifyCSRFPost()) {
                throw new Exception('Security validation failed. Please try again.');
            }
            
            try {
                $identifier = sanitizeInputData(trim($_POST['login_identifier'] ?? ''), 'username');
                $password = $_POST['login_password'] ?? '';
                
                // Store form data for repopulation
                $form_data['login_identifier'] = $identifier;
                
                // Check rate limiting first
                $rate_limit = checkRateLimit('login', 5, 300); // 5 attempts per 5 minutes
                if (!$rate_limit['allowed']) {
                    throw new Exception($rate_limit['message']);
                }
                
                // Validation
                if (empty($identifier) || empty($password)) {
                    throw new Exception('Please enter both username/email and password.');
                }
                
                // Check if user exists by username or email
                $stmt = $pdo->prepare("SELECT id, username, email, first_name, last_name, password_hash, role, is_active FROM users WHERE (username = ? OR email = ?) AND is_active = 1");
                $stmt->execute([$identifier, $identifier]);
                $user = $stmt->fetch();
                
                if (!$user) {
                    // Don't reveal if user exists or not - use same message for both cases
                    throw new Exception('Invalid username/email or password.');
                }
                
                // Verify password
                if (!password_verify($password, $user['password_hash'])) {
                    // Don't reveal if user exists or not - use same message for both cases
                    throw new Exception('Invalid username/email or password.');
                }
                
                // Start session and set user data
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['login_time'] = time();
                $_SESSION['last_activity'] = time();
                
                // Update last login time
                try {
                    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $stmt->execute([$user['id']]);
                } catch (Exception $e) {
                    error_log("Could not update last_login: " . $e->getMessage());
                }
                
                // Record successful login (resets rate limiting)
                recordSuccess('login');
                
                // Log successful login
                try {
                    logUserActivity($user['id'], 'login', 'User logged in successfully');
                } catch (Exception $e) {
                    error_log("Could not log login: " . $e->getMessage());
                }
                

                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } elseif ($user['role'] === 'officer') {
                    header("Location: officer/dashboard.php");
                } else {
                    header("Location: user/dashboard.php");
                }
                exit();
                
            } catch (Exception $e) {
                $error_message = $e->getMessage();
            }
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

    // Handle registration form submission
    if (isset($_POST['register'])) {
        // Verify CSRF token
        if (!verifyCSRFPost()) {
            throw new Exception('Security validation failed. Please try again.');
        }
        
        try {
        require_once '../config/database.php';
        
        $first_name = sanitizeInputData(trim($_POST['reg_first_name'] ?? ''), 'text');
        $last_name = sanitizeInputData(trim($_POST['reg_last_name'] ?? ''), 'text');
        $username = sanitizeInputData(trim($_POST['reg_username'] ?? ''), 'username');
        $email = sanitizeInputData(trim($_POST['reg_email'] ?? ''), 'email');
        $phone = sanitizeInputData(trim($_POST['reg_phone'] ?? ''), 'phone');
        $password = $_POST['reg_password'] ?? '';
        $confirm_password = $_POST['reg_confirm_password'] ?? '';
        
        // Store form data for repopulation
        $form_data['reg_first_name'] = $first_name;
        $form_data['reg_last_name'] = $last_name;
        $form_data['reg_username'] = $username;
        $form_data['reg_email'] = $email;
        $form_data['reg_phone'] = $phone;
        
        // Validation
        if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($phone) || empty($password)) {
            throw new Exception('All fields are required.');
        }
        
        if (strlen($username) < 3) {
            throw new Exception('Username must be at least 3 characters long.');
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw new Exception('Username can only contain letters, numbers, and underscores.');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Please enter a valid email address.');
        }
        
        if (strlen($password) < 6) {
            throw new Exception('Password must be at least 6 characters long.');
        }
        
        if ($password !== $confirm_password) {
            throw new Exception('Passwords do not match.');
        }
        
        // Check if username already exists
        if (recordExists('users', 'username', $username)) {
            throw new Exception('Username already exists. Please choose a different one.');
        }
        
        // Check if email already exists
        if (recordExists('users', 'email', $email)) {
            throw new Exception('Email already exists. Please use a different email or login.');
        }
        
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user (only as regular user, not admin/officer)
        // Check if username column exists in users table
        try {
            $stmt = $pdo->prepare("DESCRIBE users");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $has_username = in_array('username', $columns);
            
            if ($has_username) {
                // Username column exists, include it
                $stmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, email, phone, password_hash, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, 'user', 1, NOW())");
                $stmt->execute([$username, $first_name, $last_name, $email, $phone, $password_hash]);
            } else {
                // No username column, use the original query
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, 'user', 1, NOW())");
                $stmt->execute([$first_name, $last_name, $email, $phone, $password_hash]);
            }
        } catch (Exception $e) {
            // Fallback to original query if column check fails
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, 'user', 1, NOW())");
            $stmt->execute([$first_name, $last_name, $email, $phone, $password_hash]);
        }
        
        $user_id = $pdo->lastInsertId();
        
        // Log user registration
        try {
            logUserActivity($user_id, 'user_registered', 'User registered via login page');
        } catch (Exception $e) {
            error_log("Could not log user registration: " . $e->getMessage());
        }
        
        $success_message = 'Registration successful! You can now login with your username or email.';
        
        // Clear form data after successful registration
        $form_data = [];
        
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
    <title>Login/Register - Yetuga</title>
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
                <h3>Why Choose Yetuga?</h3>
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-search"></i>
                        <span>Find optimal routes quickly</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Report issues safely</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-clock"></i>
                        <span>Real-time updates</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-users"></i>
                        <span>Community driven</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Forms -->
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
                
                <?php
                // Show rate limit information
                $rate_info = getRateLimitInfo('login');
                if ($rate_info['attempts'] > 0): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-shield-alt"></i>
                        <strong>Security Notice:</strong> 
                        <?php if ($rate_info['blocked']): ?>
                            Account temporarily locked due to too many failed attempts. 
                            Try again in <?php echo ceil(($rate_info['blocked_until'] - time()) / 60); ?> minutes.
                        <?php else: ?>
                            Failed login attempts: <?php echo $rate_info['attempts']; ?>. 
                            Remaining attempts: <?php echo $rate_info['remaining']; ?>.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" class="auth-form" id="loginForm">
                    <?php echo csrfTokenField(); ?>
                    <h2>Welcome Back</h2>
                    <p>Sign in to your Yetuga account</p>
                    
                    <div class="form-group">
                        <label for="login_identifier">
                            <i class="fas fa-user"></i>
                            Username or Email
                        </label>
                        <input type="text" id="login_identifier" name="login_identifier" 
                               value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" 
                               placeholder="Enter username or email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_password">
                            <i class="fas fa-lock"></i>
                            Password
                        </label>
                        <div class="password-input">
                            <input type="password" id="login_password" name="login_password" 
                                   placeholder="Enter your password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('login_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember_me">
                            Remember me
                        </label>
                        <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" name="login" class="auth-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </button>
                </form>

                <!-- Registration Form -->
                <form method="POST" class="auth-form" id="registerForm" style="display: none;">
                    <?php echo csrfTokenField(); ?>
                    <h2>Create Account</h2>
                    <p>Join Yetuga for better urban mobility experience</p>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="reg_first_name">
                                <i class="fas fa-user"></i>
                                First Name
                            </label>
                            <input type="text" id="reg_first_name" name="reg_first_name" 
                                   value="<?php echo htmlspecialchars($form_data['reg_first_name'] ?? ''); ?>" 
                                   placeholder="Enter your first name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="reg_last_name">
                                <i class="fas fa-user"></i>
                                Last Name
                            </label>
                            <input type="text" id="reg_last_name" name="reg_last_name" 
                                   value="<?php echo htmlspecialchars($form_data['reg_last_name'] ?? ''); ?>" 
                                   placeholder="Enter your last name" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_username">
                            <i class="fas fa-at"></i>
                            Username
                        </label>
                        <input type="text" id="reg_username" name="reg_username" 
                               value="<?php echo htmlspecialchars($form_data['reg_username'] ?? ''); ?>" 
                               placeholder="Choose a username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_email">
                            <i class="fas fa-envelope"></i>
                            Email Address
                        </label>
                        <input type="email" id="reg_email" name="reg_email" 
                               value="<?php echo htmlspecialchars($form_data['reg_email'] ?? ''); ?>" 
                               placeholder="Enter your email address" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_phone">
                            <i class="fas fa-phone"></i>
                            Phone Number
                        </label>
                        <input type="tel" id="reg_phone" name="reg_phone" 
                               value="<?php echo htmlspecialchars($form_data['reg_phone'] ?? ''); ?>" 
                               placeholder="+251911234567" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_password">
                            <i class="fas fa-lock"></i>
                            Password
                        </label>
                        <div class="password-input">
                            <input type="password" id="reg_password" name="reg_password" 
                                   placeholder="Create a strong password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('reg_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="regPasswordStrength"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="reg_confirm_password">
                            <i class="fas fa-lock"></i>
                            Confirm Password
                        </label>
                        <div class="password-input">
                            <input type="password" id="reg_confirm_password" name="reg_confirm_password" 
                                   placeholder="Confirm your password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('reg_confirm_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="agree_terms" required>
                            I agree to the <a href="#" class="terms-link">Terms & Conditions</a>
                        </label>
                    </div>
                    
                    <button type="submit" name="register" class="auth-btn">
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    </button>
                </form>

                <!-- Form Switcher -->
                <div class="auth-switch">
                    <button type="button" id="showLogin" class="switch-btn active">Sign In</button>
                    <button type="button" id="showRegister" class="switch-btn">Sign Up</button>
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
        // Password strength indicator
        function checkPasswordStrength(password, strengthElement) {
            let strength = 0;
            let feedback = '';
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            strengthElement.className = 'password-strength';
            
            if (strength <= 2) {
                strengthElement.classList.add('weak');
                feedback = 'Weak password';
            } else if (strength <= 3) {
                strengthElement.classList.add('medium');
                feedback = 'Medium strength password';
            } else {
                strengthElement.classList.add('strong');
                feedback = 'Strong password';
            }
            
            strengthElement.title = feedback;
        }
        
        // Password visibility toggle
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const toggle = field.nextElementSibling;
            const icon = toggle.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }
        
        // Form switching
        const showLogin = document.getElementById('showLogin');
        const showRegister = document.getElementById('showRegister');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        
        showLogin.addEventListener('click', function() {
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
            showLogin.classList.add('active');
            showRegister.classList.remove('active');
        });
        
        showRegister.addEventListener('click', function() {
            registerForm.style.display = 'block';
            loginForm.style.display = 'none';
            showRegister.classList.add('active');
            showLogin.classList.remove('active');
        });
        
        // Password strength checking for registration
        const regPassword = document.getElementById('reg_password');
        const regPasswordStrength = document.getElementById('regPasswordStrength');
        
        if (regPassword) {
            regPassword.addEventListener('input', function() {
                checkPasswordStrength(this.value, regPasswordStrength);
            });
        }
        
        // Auto-switch to registration form if there was a registration error
        <?php if (isset($_POST['register']) && $error_message): ?>
        showRegister.click();
        <?php endif; ?>
        
        // Auto-switch to login form if there was a login error
        <?php if (isset($_POST['login']) && $error_message): ?>
        showLogin.click();
        <?php endif; ?>
    </script>
</body>
</html>

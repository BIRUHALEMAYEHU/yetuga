<?php
/**
 * Centralized Session Handler for Yetuga App
 * Single method to handle all session management needs
 */

require_once 'session_config.php';

/**
 * Handle session management for any page
 * This single method does everything: validation, role checking, logging, etc.
 * 
 * @param string $required_role The role required to access this page ('user', 'officer', 'admin', or 'any')
 * @param string $page_name Name of the page being accessed (for logging)
 * @return array Array containing user data and session info
 */
function handleSession($required_role = 'any', $page_name = 'Unknown Page') {
    // Start secure session
    startSecureSession();
    
    // Check if user is logged in and session is valid
    if (!isSessionValid()) {
        // Log the expired session attempt
        if (isset($_SESSION['user_id'])) {
            logUserActivity($_SESSION['user_id'], 'session_expired', "Session expired on {$page_name}");
        }
        
        // Redirect to login with appropriate message
        header('Location: ../login.php?error=session_expired');
        exit();
    }
    
    // If role is specified and not 'any', validate the role
    if ($required_role !== 'any' && !validateUserRole($required_role)) {
        // Log unauthorized access attempt
        logUserActivity($_SESSION['user_id'], 'unauthorized_access', "Attempted to access {$page_name} with role {$_SESSION['user_role']}");
        
        // Redirect to login with unauthorized message
        header('Location: ../login.php?error=unauthorized');
        exit();
    }
    
    // Log successful page access
    logUserActivity($_SESSION['user_id'], 'page_access', "Accessed {$page_name}");
    
    // Update last activity time when page is accessed
    $_SESSION['last_activity'] = time();
    
    // Return user data and session info
    return [
        'user_id' => $_SESSION['user_id'],
        'user_name' => $_SESSION['user_name'],
        'user_role' => $_SESSION['user_role'],
        'user_email' => $_SESSION['user_email'],
        'login_time' => $_SESSION['login_time'],
        'last_activity' => $_SESSION['last_activity'],
        'session_lifetime' => 1800, // 30 minutes
        'remaining_time' => 1800 - (time() - $_SESSION['last_activity'])
    ];
}

/**
 * Get session timer HTML and JavaScript for any page
 * This creates the complete session timer display
 * 
 * @param string $logout_url URL for logout (relative to current page)
 * @return string HTML and JavaScript for session timer
 */
function getSessionTimer($logout_url = '../logout.php') {
    return '
    <div class="session-info">
        <small>Session expires in: <span id="sessionTimer"></span></small>
        <small style="margin-left: 10px; color: #666;">(Activity detected: <span id="lastActivity">Just now</span>)</small>
    </div>
    <script>
        const sessionLifetime = 1800; // 30 minutes
        let lastActivityTime = Date.now();
        let sessionTimer;
        let isPageActive = true;
        
        function updateSessionTimer() {
            if (!isPageActive) return; // Don\'t update if page is not active
            
            const currentTime = Math.floor(Date.now() / 1000);
            const lastActivity = Math.floor(lastActivityTime / 1000);
            const timeSinceActivity = currentTime - lastActivity;
            const remaining = sessionLifetime - timeSinceActivity;
            
            if (remaining <= 0) {
                // Session expired due to inactivity, redirect to login
                window.location.href = "' . $logout_url . '?reason=session_expired";
                return;
            }
            
            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            document.getElementById("sessionTimer").textContent =
                `${minutes}:${seconds.toString().padStart(2, "0")}`;
            
            // Update last activity display
            const timeAgo = Math.floor((Date.now() - lastActivityTime) / 1000);
            if (timeAgo < 5) {
                document.getElementById("lastActivity").textContent = "Just now";
            } else if (timeAgo < 60) {
                document.getElementById("lastActivity").textContent = timeAgo + "s ago";
            } else {
                document.getElementById("lastActivity").textContent = Math.floor(timeAgo / 60) + "m ago";
            }
            
            // Warning when session is about to expire (5 seconds)
            if (remaining <= 5) {
                document.getElementById("sessionTimer").style.color = "red";
            } else if (remaining <= 10) {
                document.getElementById("sessionTimer").style.color = "orange";
            }
        }
        
        // Update timer every second
        setInterval(updateSessionTimer, 1000);
        updateSessionTimer(); // Initial call
        
        // Reset activity timer on user activity
        function resetActivityTimer() {
            if (!isPageActive) return; // Don\'t reset if page is not active
            
            lastActivityTime = Date.now();
                    // Also refresh the session on the server side using secure API
        const formData = new FormData();
        formData.append(\'action\', \'refresh_session\');
        
        fetch("../api_handler.php", {
            method: "POST",
            body: formData,
            credentials: "same-origin"
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("Session refreshed successfully");
            }
        })
        .catch(error => {
            console.log("Session refresh failed:", error);
        });
        }
        
        // Listen for user activity events
        ["mousedown", "mousemove", "keypress", "scroll", "touchstart", "click"].forEach(event => {
            document.addEventListener(event, resetActivityTimer, { passive: true });
        });
        
        // Handle page visibility changes
        document.addEventListener("visibilitychange", function() {
            if (document.hidden) {
                isPageActive = false;
                console.log("Page hidden - pausing session timer");
            } else {
                isPageActive = true;
                console.log("Page visible - resuming session timer");
                resetActivityTimer(); // Reset timer when page becomes visible
            }
        });
        
        // Handle page focus/blur
        document.addEventListener("focus", function() {
            isPageActive = true;
            resetActivityTimer();
        });
        
        document.addEventListener("blur", function() {
            isPageActive = false;
        });
        
        // Handle page unload (user navigating away)
        window.addEventListener("beforeunload", function() {
            // Send a final activity update before leaving
            fetch("../api/refresh_session.php", {
                method: "POST",
                credentials: "same-origin"
            }).catch(console.error);
        });
        
        // Start initial timer
        resetActivityTimer();
        
        // Refresh session immediately when page loads to prevent navigation issues
        setTimeout(() => {
            fetch("../api/refresh_session.php", {
                method: "POST",
                credentials: "same-origin"
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Initial session refresh successful");
                    lastActivityTime = Date.now(); // Reset the timer
                }
            })
            .catch(error => {
                console.log("Initial session refresh failed:", error);
            });
        }, 1000); // Wait 1 second after page load
        
        // Debug: Log when activity is detected
        console.log("Session timer initialized - listening for user activity");
        
        // Log page load
        console.log("Page loaded - session timer active");
    </script>';
}

/**
 * Quick session check for simple pages
 * Just validates session without role checking
 * 
 * @param string $page_name Name of the page
 * @return array User data
 */
function quickSessionCheck($page_name = 'Unknown Page') {
    return handleSession('any', $page_name);
}

/**
 * Admin session check
 * 
 * @param string $page_name Name of the page
 * @return array User data
 */
function adminSessionCheck($page_name = 'Admin Page') {
    return handleSession('admin', $page_name);
}

/**
 * Officer session check
 * 
 * @param string $page_name Name of the page
 * @return array User data
 */
function officerSessionCheck($page_name = 'Officer Page') {
    return handleSession('officer', $page_name);
}

/**
 * User session check
 * 
 * @param string $page_name Name of the page
 * @return array User data
 */
function userSessionCheck($page_name = 'User Page') {
    return handleSession('user', $page_name);
}

/**
 * Check if user has any of the specified roles
 * 
 * @param array $allowed_roles Array of allowed roles
 * @param string $page_name Name of the page
 * @return array User data
 */
function multiRoleSessionCheck($allowed_roles, $page_name = 'Protected Page') {
    // Start secure session
    startSecureSession();
    
    // Check if user is logged in and session is valid
    if (!isSessionValid()) {
        if (isset($_SESSION['user_id'])) {
            logUserActivity($_SESSION['user_id'], 'session_expired', "Session expired on {$page_name}");
        }
        header('Location: ../login.php?error=session_expired');
        exit();
    }
    
    // Check if user has any of the allowed roles
    if (!validateUserRoles($allowed_roles)) {
        logUserActivity($_SESSION['user_id'], 'unauthorized_access', "Attempted to access {$page_name} with role {$_SESSION['user_role']}");
        header('Location: ../login.php?error=unauthorized');
        exit();
    }
    
    // Log successful page access
    logUserActivity($_SESSION['user_id'], 'page_access', "Accessed {$page_name}");
    
    // Update last activity time when page is accessed
    $_SESSION['last_activity'] = time();
    
    // Return user data
    return [
        'user_id' => $_SESSION['user_id'],
        'user_name' => $_SESSION['user_name'],
        'user_role' => $_SESSION['user_role'],
        'user_email' => $_SESSION['user_email'],
        'login_time' => $_SESSION['login_time'],
        'last_activity' => $_SESSION['last_activity'],
        'session_lifetime' => 300,
        'remaining_time' => 300 - (time() - $_SESSION['last_activity'])
    ];
}
?>

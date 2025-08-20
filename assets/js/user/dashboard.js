// User Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard functionality
    initializeDashboard();
});

function initializeDashboard() {
    // Initialize route search form
    initializeRouteSearch();
    
    // Initialize user menu interactions
    initializeUserMenu();
    
    // Initialize quick stats animation
    initializeStatsAnimation();
    
    // Initialize activity items
    initializeActivityItems();
}

// Route Search Functionality
function initializeRouteSearch() {
    const searchForm = document.querySelector('.route-search-form');
    const fromInput = document.getElementById('from-location');
    const toInput = document.getElementById('to-location');
    const transportSelect = document.getElementById('transport-type');
    const timeInput = document.getElementById('departure-time');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleRouteSearch();
        });
    }
    
    // Auto-fill current time if not set
    if (timeInput && !timeInput.value) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        timeInput.value = `${hours}:${minutes}`;
    }
    
    // Add input validation and suggestions
    if (fromInput) {
        fromInput.addEventListener('input', function() {
            validateLocationInput(this);
        });
    }
    
    if (toInput) {
        toInput.addEventListener('input', function() {
            validateLocationInput(this);
        });
    }
}

function handleRouteSearch() {
    const fromLocation = document.getElementById('from-location').value;
    const toLocation = document.getElementById('to-location').value;
    const transportType = document.getElementById('transport-type').value;
    const departureTime = document.getElementById('departure-time').value;
    
    // Basic validation
    if (!fromLocation.trim() || !toLocation.trim()) {
        showNotification('Please enter both starting point and destination', 'error');
        return;
    }
    
    // Show loading state
    const searchBtn = document.querySelector('.search-btn');
    const originalText = searchBtn.innerHTML;
    searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
    searchBtn.disabled = true;
    
    // Simulate API call (replace with actual API call)
    setTimeout(() => {
        // Redirect to routes page with search parameters
        const params = new URLSearchParams({
            from: fromLocation,
            to: toLocation,
            type: transportType,
            time: departureTime
        });
        
        window.location.href = `routes.html?${params.toString()}`;
    }, 1500);
}

function validateLocationInput(input) {
    const value = input.value.trim();
    
    if (value.length > 0) {
        input.style.borderColor = '#10b981';
        input.style.backgroundColor = '#f0fdf4';
    } else {
        input.style.borderColor = '#e5e7eb';
        input.style.backgroundColor = 'white';
    }
}

// User Menu Functionality
function initializeUserMenu() {
    const userInfo = document.querySelector('.user-info');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (userInfo && dropdownMenu) {
        // Toggle dropdown on click (mobile friendly)
        userInfo.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            dropdownMenu.classList.remove('show');
        });
        
        // Prevent dropdown from closing when clicking inside
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

// Stats Animation
function initializeStatsAnimation() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateNumber(entry.target);
                observer.unobserve(entry.target);
            }
        });
    });
    
    statNumbers.forEach(stat => {
        observer.observe(stat);
    });
}

function animateNumber(element) {
    const target = parseInt(element.textContent);
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 20);
}

// Activity Items Functionality
function initializeActivityItems() {
    const activityItems = document.querySelectorAll('.activity-item');
    
    activityItems.forEach(item => {
        item.addEventListener('click', function() {
            // Add click effect
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
            
            // Handle different activity types
            const icon = this.querySelector('.activity-icon i');
            if (icon.classList.contains('fa-route')) {
                // Route activity - could show route details
                showNotification('Route details clicked', 'info');
            } else if (icon.classList.contains('fa-flag')) {
                // Report activity - could show report details
                showNotification('Report details clicked', 'info');
            } else if (icon.classList.contains('fa-star')) {
                // Review activity - could show review details
                showNotification('Review details clicked', 'info');
            }
        });
    });
}

// Notification System
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${getNotificationColor(type)};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        max-width: 400px;
        animation: slideInRight 0.3s ease;
    `;
    
    // Add close functionality
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        notification.remove();
    });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
    
    // Add to page
    document.body.appendChild(notification);
    
    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
}

function getNotificationIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

function getNotificationColor(type) {
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };
    return colors[type] || '#3b82f6';
}

// Utility Functions
function formatTimeAgo(timestamp) {
    const now = new Date();
    const past = new Date(timestamp);
    const diffInSeconds = Math.floor((now - past) / 1000);
    
    if (diffInSeconds < 60) {
        return 'Just now';
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} day${days > 1 ? 's' : ''} ago`;
    }
}

// Initialize dashboard when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Add loading animation
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.3s ease';
    
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
    
    // Initialize all functionality
    initializeDashboard();
});



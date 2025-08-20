/**
 * Admin Dashboard JavaScript
 * Handles interactive functionality for the admin dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin Dashboard loaded successfully');
    
    // Initialize dashboard functionality
    initializeDashboard();
});

function initializeDashboard() {
    // Add click event listeners for quick action cards
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Add click animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });
    
    // Initialize user menu dropdown
    initializeUserMenu();
    
    // Add hover effects for stat cards
    initializeStatCards();
}

function initializeUserMenu() {
    const userAvatar = document.querySelector('.user-avatar');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (userAvatar && dropdownMenu) {
        // Toggle dropdown on click (mobile-friendly)
        userAvatar.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userAvatar.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }
}

function initializeStatCards() {
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

// Function to refresh dashboard data (can be called periodically)
function refreshDashboardData() {
    // This function can be used to refresh statistics and activity data
    console.log('Refreshing dashboard data...');
    
    // Example: Fetch updated statistics via AJAX
    // fetch('/api/admin/dashboard-stats')
    //     .then(response => response.json())
    //     .then(data => updateDashboardStats(data))
    //     .catch(error => console.error('Error refreshing data:', error));
}

// Function to update dashboard statistics
function updateDashboardStats(data) {
    // Update statistics display
    if (data.total_users !== undefined) {
        const userStat = document.querySelector('.stat-card:nth-child(1) .stat-content h3');
        if (userStat) userStat.textContent = data.total_users;
    }
    
    if (data.total_officers !== undefined) {
        const officerStat = document.querySelector('.stat-card:nth-child(2) .stat-content h3');
        if (officerStat) officerStat.textContent = data.total_officers;
    }
    
    if (data.total_reports !== undefined) {
        const reportStat = document.querySelector('.stat-card:nth-child(3) .stat-content h3');
        if (reportStat) reportStat.textContent = data.total_reports;
    }
    
    if (data.total_routes !== undefined) {
        const routeStat = document.querySelector('.stat-card:nth-child(4) .stat-content h3');
        if (routeStat) routeStat.textContent = data.total_routes;
    }
}

// Function to show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="notification-close">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add notification styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Add CSS animation for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .notification-close {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 0.25rem;
        transition: background 0.3s ease;
    }
    
    .notification-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }
`;
document.head.appendChild(style);

// Export functions for global access
window.adminDashboard = {
    refreshData: refreshDashboardData,
    showNotification: showNotification,
    updateStats: updateDashboardStats
};



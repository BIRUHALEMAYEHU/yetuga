document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in and is a transport officer
    checkAuthStatus();
    
    // Load officer's name
    loadOfficerName();
    
    // Load dashboard statistics
    loadDashboardStats();
    
    // Load recent activity
    loadRecentActivity();
});

function checkAuthStatus() {
    fetch('../check_auth.php')
        .then(response => response.json())
        .then(data => {
            if (!data.success || data.role !== 'officer') {
                window.location.href = '../login.html';
            }
        })
        .catch(error => {
            console.error('Auth check failed:', error);
            window.location.href = '../login.html';
        });
}

function loadOfficerName() {
    fetch('../get_user_info.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('officerName').textContent = data.username;
            }
        })
        .catch(error => console.error('Failed to load officer name:', error));
}

function loadDashboardStats() {
    fetch('get_dashboard_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatCards(data.stats);
            }
        })
        .catch(error => console.error('Failed to load dashboard stats:', error));
}

function updateStatCards(stats) {
    // Update active routes
    if (stats.activeRoutes) {
        const activeRoutesCard = document.querySelector('.card.bg-primary .card-text');
        if (activeRoutesCard) {
            activeRoutesCard.textContent = stats.activeRoutes.current;
            const diff = stats.activeRoutes.current - stats.activeRoutes.previous;
            const small = activeRoutesCard.nextElementSibling;
            if (small) {
                small.textContent = `${diff >= 0 ? '+' : ''}${diff} from yesterday`;
            }
        }
    }

    // Update available vehicles
    if (stats.vehicles) {
        const vehiclesCard = document.querySelector('.card.bg-success .card-text');
        if (vehiclesCard) {
            vehiclesCard.textContent = stats.vehicles.available;
            const small = vehiclesCard.nextElementSibling;
            if (small) {
                small.textContent = `${stats.vehicles.maintenance} maintenance`;
            }
        }
    }

    // Update pending reports
    if (stats.reports) {
        const reportsCard = document.querySelector('.card.bg-warning .card-text');
        if (reportsCard) {
            reportsCard.textContent = stats.reports.pending;
            const small = reportsCard.nextElementSibling;
            if (small) {
                small.textContent = `${stats.reports.urgent} urgent`;
            }
        }
    }

    // Update route alerts
    if (stats.alerts) {
        const alertsCard = document.querySelector('.card.bg-danger .card-text');
        if (alertsCard) {
            alertsCard.textContent = stats.alerts.total;
            const small = alertsCard.nextElementSibling;
            if (small) {
                small.textContent = `${stats.alerts.critical} critical`;
            }
        }
    }
}

function loadRecentActivity() {
    fetch('get_recent_activity.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateActivityTable(data.activities);
            }
        })
        .catch(error => console.error('Failed to load recent activity:', error));
}

function updateActivityTable(activities) {
    const tbody = document.querySelector('.table tbody');
    if (!tbody) return;

    tbody.innerHTML = '';
    activities.forEach(activity => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${formatTime(activity.time)}</td>
            <td>${activity.event}</td>
            <td>${activity.route}</td>
            <td><span class="badge bg-${getStatusColor(activity.status)}">${activity.status}</span></td>
        `;
        tbody.appendChild(tr);
    });
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit', 
        hour12: true 
    });
}

function getStatusColor(status) {
    const statusColors = {
        'Completed': 'success',
        'Pending': 'warning',
        'Urgent': 'danger',
        'In Progress': 'info'
    };
    return statusColors[status] || 'secondary';
}

// Event Listeners for Quick Actions
document.querySelectorAll('.list-group-item').forEach(item => {
    item.addEventListener('click', function(e) {
        const action = this.getAttribute('href').replace('.html', '');
        console.log(`Quick action clicked: ${action}`);
    });
}); 
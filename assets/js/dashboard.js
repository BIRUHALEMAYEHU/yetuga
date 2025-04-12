// Handle subcity stats clicks
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers for subcity stats
    document.querySelectorAll('.subcity-stat').forEach(item => {
        item.addEventListener('click', () => {
            const subcity = item.querySelector('span').textContent;
            window.location.href = 'routes.html?subcity=' + encodeURIComponent(subcity);
        });
    });

    // Handle vehicle type clicks
    document.querySelectorAll('.vehicle-type-item').forEach(item => {
        item.addEventListener('click', () => {
            const type = item.querySelector('span').textContent.toLowerCase().replace(' ', '-');
            window.location.href = 'vehicles.html?type=' + type;
        });
    });

    // Initialize any tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle quick action buttons
    document.querySelectorAll('.quick-action-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            switch(action) {
                case 'new-route':
                    window.location.href = 'routes.html?action=new';
                    break;
                case 'review-reports':
                    window.location.href = 'reports.html';
                    break;
                case 'update-traffic':
                    window.location.href = 'traffic.html';
                    break;
            }
        });
    });
}); 
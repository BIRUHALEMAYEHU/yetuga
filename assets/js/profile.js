$(document).ready(function() {
    // Handle profile form submission
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            fullName: $('#fullName').val(),
            email: $('#email').val(),
            phone: $('#phone').val()
        };

        // TODO: Send to backend API
        console.log('Updating profile:', formData);
        
        // Show success message
        alert('Profile updated successfully!');
    });

    // Handle photo change
    $('#changePhoto').click(function() {
        // Create a hidden file input
        const fileInput = $('<input type="file" accept="image/*" style="display: none">');
        
        fileInput.on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Update profile picture
                    $('img.rounded-circle').attr('src', e.target.result);
                    
                    // TODO: Upload to server
                    console.log('Uploading new photo:', file);
                };
                reader.readAsDataURL(file);
            }
        });

        fileInput.click();
    });

    // Load recent activity
    function loadRecentActivity() {
        // TODO: Fetch from backend API
        const activities = [
            {
                action: 'Updated traffic status',
                time: '3 hours ago',
                details: 'Added traffic update for Bole Road'
            },
            {
                action: 'Modified route',
                time: '5 hours ago',
                details: 'Updated Megenagna-Bole route schedule'
            },
            {
                action: 'Responded to report',
                time: '1 day ago',
                details: 'Addressed traffic congestion complaint'
            }
        ];

        const activityList = $('.list-group');
        activityList.empty();

        activities.forEach(activity => {
            activityList.append(`
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">${activity.action}</h6>
                        <small>${activity.time}</small>
                    </div>
                    <p class="mb-1">${activity.details}</p>
                </a>
            `);
        });
    }

    // Initial load of recent activity
    loadRecentActivity();

    // Update officer name in navigation
    $('#officerName, #officerFullName').text('Dr. Biruh Alemayehu');
}); 
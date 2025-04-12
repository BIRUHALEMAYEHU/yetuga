$(document).ready(function() {
    // Handle password change
    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();
        
        const currentPassword = $('#currentPassword').val();
        const newPassword = $('#newPassword').val();
        const confirmPassword = $('#confirmPassword').val();

        if (newPassword !== confirmPassword) {
            alert('New passwords do not match!');
            return;
        }

        // TODO: Send to backend API
        console.log('Changing password');
        
        // Clear form and show success message
        this.reset();
        alert('Password changed successfully!');
    });

    // Handle notification settings
    $('#notificationForm').on('submit', function(e) {
        e.preventDefault();
        
        const settings = {
            emailNotifications: $('#emailNotifications').is(':checked'),
            smsNotifications: $('#smsNotifications').is(':checked'),
            urgentAlerts: $('#urgentAlerts').is(':checked')
        };

        // TODO: Send to backend API
        console.log('Updating notification settings:', settings);
        
        // Show success message
        alert('Notification settings updated successfully!');
    });

    // Handle display settings
    $('#displayForm').on('submit', function(e) {
        e.preventDefault();
        
        const settings = {
            theme: $('#theme').val(),
            language: $('#language').val(),
            timezone: $('#timezone').val()
        };

        // TODO: Send to backend API
        console.log('Updating display settings:', settings);
        
        // Apply theme immediately
        applyTheme(settings.theme);
        
        // Show success message
        alert('Display settings updated successfully!');
    });

    // Handle privacy settings
    $('#privacyForm').on('submit', function(e) {
        e.preventDefault();
        
        const settings = {
            activityTracking: $('#activityTracking').is(':checked'),
            locationSharing: $('#locationSharing').is(':checked'),
            dataAnalytics: $('#dataAnalytics').is(':checked')
        };

        // TODO: Send to backend API
        console.log('Updating privacy settings:', settings);
        
        // Show success message
        alert('Privacy settings updated successfully!');
    });

    // Function to apply theme
    function applyTheme(theme) {
        const body = $('body');
        body.removeClass('theme-light theme-dark');
        
        if (theme === 'system') {
            // Check system preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                body.addClass('theme-dark');
            } else {
                body.addClass('theme-light');
            }
        } else {
            body.addClass(`theme-${theme}`);
        }
    }

    // Load saved settings
    function loadSavedSettings() {
        // TODO: Fetch from backend API
        const savedSettings = {
            theme: 'light',
            language: 'en',
            timezone: 'EAT',
            notifications: {
                email: true,
                sms: true,
                urgent: true
            },
            privacy: {
                activityTracking: true,
                locationSharing: true,
                dataAnalytics: true
            }
        };

        // Apply saved settings
        $('#theme').val(savedSettings.theme);
        $('#language').val(savedSettings.language);
        $('#timezone').val(savedSettings.timezone);
        
        $('#emailNotifications').prop('checked', savedSettings.notifications.email);
        $('#smsNotifications').prop('checked', savedSettings.notifications.sms);
        $('#urgentAlerts').prop('checked', savedSettings.notifications.urgent);
        
        $('#activityTracking').prop('checked', savedSettings.privacy.activityTracking);
        $('#locationSharing').prop('checked', savedSettings.privacy.locationSharing);
        $('#dataAnalytics').prop('checked', savedSettings.privacy.dataAnalytics);

        // Apply theme
        applyTheme(savedSettings.theme);
    }

    // Initial load of saved settings
    loadSavedSettings();

    // Update officer name in navigation
    $('#officerName').text('Dr. Biruh Alemayehu');
}); 
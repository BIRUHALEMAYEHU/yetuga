// Profile Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeProfile();
});

function initializeProfile() {
    initializeAvatarUpload();
    initializeFormHandling();
    loadUserData();
    animateStats();
    initializeActivityChart();
}

// Sample user data - in real app, this would come from API
const userData = {
    id: 1,
    fullName: 'Abebe Kebede',
    email: 'abebe.kebede@email.com',
    phone: '+251 911 123 456',
    dob: '1990-03-15',
    gender: 'Male',
    location: 'Addis Ababa, Ethiopia',
    memberSince: '2022-01-15',
    totalTrips: 47,
    reviewsCount: 12,
    preferences: {
        transport: 'Public Bus',
        language: 'English',
        currency: 'ETB (Ethiopian Birr)',
        notifications: 'Enabled',
        theme: 'Light',
        privacy: 'Public'
    }
};

function initializeAvatarUpload() {
    const avatarInput = document.getElementById('avatarInput');
    const profileAvatar = document.getElementById('profileAvatar');
    const avatarOverlay = document.querySelector('.avatar-overlay');
    
    if (avatarInput && profileAvatar) {
        // Click on avatar to trigger file input
        profileAvatar.addEventListener('click', () => {
            avatarInput.click();
        });
        
        // Handle file selection
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profileAvatar.src = e.target.result;
                        showNotification('Profile picture updated successfully!', 'success');
                        
                        // In real app, this would upload to server
                        uploadAvatarToServer(file);
                    };
                    reader.readAsDataURL(file);
                } else {
                    showNotification('Please select a valid image file.', 'error');
                }
            }
        });
    }
}

function uploadAvatarToServer(file) {
    // Simulate upload to server
    const formData = new FormData();
    formData.append('avatar', file);
    
    // In real app, this would be an actual API call
    console.log('Uploading avatar to server...', file.name);
}

function loadUserData() {
    // Populate profile information
    document.getElementById('userName').textContent = userData.fullName;
    document.getElementById('userEmail').textContent = userData.email;
    document.getElementById('userLocation').textContent = userData.location;
    
    // Populate personal information
    document.getElementById('fullName').textContent = userData.fullName;
    document.getElementById('email').textContent = userData.email;
    document.getElementById('phone').textContent = userData.phone;
    document.getElementById('dob').textContent = formatDate(userData.dob);
    document.getElementById('gender').textContent = userData.gender;
    document.getElementById('location').textContent = userData.location;
    
    // Populate preferences
    document.getElementById('prefTransport').textContent = userData.preferences.transport;
    document.getElementById('prefLanguage').textContent = userData.preferences.language;
    document.getElementById('prefCurrency').textContent = userData.preferences.currency;
    document.getElementById('prefNotifications').textContent = userData.preferences.notifications;
    document.getElementById('prefTheme').textContent = userData.preferences.theme;
    document.getElementById('prefPrivacy').textContent = userData.preferences.privacy;
    
    // Calculate member since years
    const memberSince = new Date(userData.memberSince);
    const currentYear = new Date().getFullYear();
    const yearsMember = currentYear - memberSince.getFullYear();
    document.getElementById('memberSince').textContent = yearsMember;
    
    // Populate stats
    document.getElementById('totalTrips').textContent = userData.totalTrips;
    document.getElementById('reviewsCount').textContent = userData.reviewsCount;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function animateStats() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    statNumbers.forEach(stat => {
        const finalValue = parseInt(stat.textContent);
        let currentValue = 0;
        const increment = finalValue / 50;
        
        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
                currentValue = finalValue;
                clearInterval(timer);
            }
            stat.textContent = Math.floor(currentValue);
        }, 30);
    });
}

function initializeActivityChart() {
    const canvas = document.getElementById('activityChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    // Sample activity data - in real app, this would come from API
    const activityData = {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Trips This Week',
            data: [3, 2, 4, 1, 5, 2, 1],
            backgroundColor: 'rgba(102, 126, 234, 0.2)',
            borderColor: 'rgba(102, 126, 234, 1)',
            borderWidth: 2,
            tension: 0.4
        }]
    };
    
    // Create a simple chart using canvas
    drawSimpleChart(ctx, activityData);
}

function drawSimpleChart(ctx, data) {
    const width = ctx.canvas.width;
    const height = ctx.canvas.height;
    const padding = 40;
    const chartWidth = width - 2 * padding;
    const chartHeight = height - 2 * padding;
    
    // Clear canvas
    ctx.clearRect(0, 0, width, height);
    
    // Find max value for scaling
    const maxValue = Math.max(...data.datasets[0].data);
    
    // Draw chart
    ctx.strokeStyle = data.datasets[0].borderColor;
    ctx.lineWidth = data.datasets[0].borderWidth;
    ctx.beginPath();
    
    data.datasets[0].data.forEach((value, index) => {
        const x = padding + (index / (data.labels.length - 1)) * chartWidth;
        const y = height - padding - (value / maxValue) * chartHeight;
        
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    
    ctx.stroke();
    
    // Draw data points
    ctx.fillStyle = data.datasets[0].backgroundColor;
    data.datasets[0].data.forEach((value, index) => {
        const x = padding + (index / (data.labels.length - 1)) * chartWidth;
        const y = height - padding - (value / maxValue) * chartHeight;
        
        ctx.beginPath();
        ctx.arc(x, y, 4, 0, 2 * Math.PI);
        ctx.fill();
    });
    
    // Draw labels
    ctx.fillStyle = '#718096';
    ctx.font = '12px Inter';
    ctx.textAlign = 'center';
    
    data.labels.forEach((label, index) => {
        const x = padding + (index / (data.labels.length - 1)) * chartWidth;
        const y = height - padding + 20;
        ctx.fillText(label, x, y);
    });
}

function initializeFormHandling() {
    // Edit Profile Form
    const editProfileForm = document.getElementById('editProfileForm');
    if (editProfileForm) {
        editProfileForm.addEventListener('submit', handleProfileUpdate);
    }
    
    // Change Password Form
    const changePasswordForm = document.getElementById('changePasswordForm');
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', handlePasswordChange);
    }
    
    // Preferences Form
    const preferencesForm = document.getElementById('preferencesForm');
    if (preferencesForm) {
        preferencesForm.addEventListener('submit', handlePreferencesUpdate);
    }
    
    // Password strength indicator
    const newPasswordInput = document.getElementById('newPassword');
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', checkPasswordStrength);
    }
}

// Profile editing functions
function editProfile() {
    const modal = document.getElementById('editProfileModal');
    if (modal) {
        // Pre-fill form with current data
        document.getElementById('editFullName').value = userData.fullName;
        document.getElementById('editEmail').value = userData.email;
        document.getElementById('editPhone').value = userData.phone;
        document.getElementById('editDob').value = userData.dob;
        document.getElementById('editGender').value = userData.gender.toLowerCase();
        document.getElementById('editLocation').value = userData.location;
        
        modal.classList.add('show');
    }
}

function closeEditModal() {
    const modal = document.getElementById('editProfileModal');
    if (modal) {
        modal.classList.remove('show');
    }
}

function handleProfileUpdate(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const updatedData = {
        fullName: formData.get('fullName'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        dob: formData.get('dob'),
        gender: formData.get('gender'),
        location: formData.get('location')
    };
    
    // Update user data
    Object.assign(userData, updatedData);
    
    // Update display
    loadUserData();
    
    // Close modal
    closeEditModal();
    
    // Show success message
    showNotification('Profile updated successfully!', 'success');
    
    // In real app, this would send data to server
    console.log('Updating profile:', updatedData);
}

function editPersonalInfo() {
    editProfile();
}

function editPreferences() {
    const modal = document.getElementById('preferencesModal');
    if (modal) {
        // Pre-fill form with current preferences
        document.getElementById('prefTransport').value = userData.preferences.transport.toLowerCase().replace(' ', '-');
        document.getElementById('prefLanguage').value = userData.preferences.language.toLowerCase();
        document.getElementById('prefCurrency').value = userData.preferences.currency.split(' ')[0];
        document.getElementById('prefNotifications').value = userData.preferences.notifications.toLowerCase().replace(' ', '-');
        document.getElementById('prefTheme').value = userData.preferences.theme.toLowerCase();
        document.getElementById('prefPrivacy').value = userData.preferences.privacy.toLowerCase().replace(' ', '-');
        
        modal.classList.add('show');
    }
}

function closePreferencesModal() {
    const modal = document.getElementById('preferencesModal');
    if (modal) {
        modal.classList.remove('show');
    }
}

function handlePreferencesUpdate(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const updatedPreferences = {
        transport: getTransportLabel(formData.get('transport')),
        language: getLanguageLabel(formData.get('language')),
        currency: getCurrencyLabel(formData.get('currency')),
        notifications: getNotificationsLabel(formData.get('notifications')),
        theme: getThemeLabel(formData.get('theme')),
        privacy: getPrivacyLabel(formData.get('privacy'))
    };
    
    // Update user preferences
    Object.assign(userData.preferences, updatedPreferences);
    
    // Update display
    loadUserData();
    
    // Close modal
    closePreferencesModal();
    
    // Show success message
    showNotification('Preferences updated successfully!', 'success');
    
    // In real app, this would send data to server
    console.log('Updating preferences:', updatedPreferences);
}

// Helper functions for preference labels
function getTransportLabel(value) {
    const labels = {
        'public-bus': 'Public Bus',
        'taxi': 'Taxi',
        'minibus': 'Minibus',
        'light-rail': 'Light Rail',
        'any': 'Any Available'
    };
    return labels[value] || value;
}

function getLanguageLabel(value) {
    const labels = {
        'english': 'English',
        'amharic': 'Amharic',
        'oromiffa': 'Oromiffa',
        'tigrinya': 'Tigrinya'
    };
    return labels[value] || value;
}

function getCurrencyLabel(value) {
    const labels = {
        'ETB': 'ETB (Ethiopian Birr)',
        'USD': 'USD (US Dollar)',
        'EUR': 'EUR (Euro)'
    };
    return labels[value] || value;
}

function getNotificationsLabel(value) {
    const labels = {
        'enabled': 'Enabled',
        'disabled': 'Disabled',
        'important-only': 'Important Only'
    };
    return labels[value] || value;
}

function getThemeLabel(value) {
    const labels = {
        'light': 'Light',
        'dark': 'Dark',
        'auto': 'Auto (System)'
    };
    return labels[value] || value;
}

function getPrivacyLabel(value) {
    const labels = {
        'public': 'Public',
        'friends-only': 'Friends Only',
        'private': 'Private'
    };
    return labels[value] || value;
}

// Security functions
function changePassword() {
    const modal = document.getElementById('changePasswordModal');
    if (modal) {
        modal.classList.add('show');
    }
}

function closePasswordModal() {
    const modal = document.getElementById('changePasswordModal');
    if (modal) {
        modal.classList.remove('show');
    }
}

function handlePasswordChange(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const currentPassword = formData.get('currentPassword');
    const newPassword = formData.get('newPassword');
    const confirmPassword = formData.get('confirmPassword');
    
    // Validate passwords
    if (newPassword !== confirmPassword) {
        showNotification('New passwords do not match!', 'error');
        return;
    }
    
    if (newPassword.length < 8) {
        showNotification('Password must be at least 8 characters long!', 'error');
        return;
    }
    
    // In real app, this would validate current password and update
    console.log('Changing password...');
    
    // Close modal
    closePasswordModal();
    
    // Show success message
    showNotification('Password changed successfully!', 'success');
    
    // Reset form
    e.target.reset();
}

function checkPasswordStrength(e) {
    const password = e.target.value;
    const strengthIndicator = document.getElementById('passwordStrength');
    
    if (!strengthIndicator) return;
    
    let strength = 0;
    let feedback = '';
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    strengthIndicator.className = 'password-strength';
    
    if (strength <= 2) {
        strengthIndicator.classList.add('weak');
        feedback = 'Weak password';
    } else if (strength <= 3) {
        strengthIndicator.classList.add('medium');
        feedback = 'Medium strength password';
    } else {
        strengthIndicator.classList.add('strong');
        feedback = 'Strong password';
    }
    
    strengthIndicator.title = feedback;
}

function setup2FA() {
    showNotification('Two-factor authentication setup coming soon!', 'info');
}

function manageSessions() {
    showNotification('Session management coming soon!', 'info');
}

// Connected accounts functions
function connectAccount(provider) {
    showNotification(`Connecting ${provider} account...`, 'info');
    // In real app, this would initiate OAuth flow
}

function disconnectAccount(provider) {
    if (confirm(`Are you sure you want to disconnect your ${provider} account?`)) {
        showNotification(`${provider} account disconnected successfully!`, 'success');
        // In real app, this would disconnect the account
    }
}

// Profile sharing
function shareProfile() {
    if (navigator.share) {
        navigator.share({
            title: `${userData.fullName}'s Profile`,
            text: `Check out ${userData.fullName}'s profile on Yetuga!`,
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        const profileUrl = window.location.href;
        navigator.clipboard.writeText(profileUrl).then(() => {
            showNotification('Profile URL copied to clipboard!', 'success');
        });
    }
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#48bb78' : type === 'error' ? '#f56565' : '#4299e1'};
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 1001;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        transform: translateX(400px);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    const editModal = document.getElementById('editProfileModal');
    const passwordModal = document.getElementById('changePasswordModal');
    const preferencesModal = document.getElementById('preferencesModal');
    
    if (editModal && e.target === editModal) {
        closeEditModal();
    }
    
    if (passwordModal && e.target === passwordModal) {
        closePasswordModal();
    }
    
    if (preferencesModal && e.target === preferencesModal) {
        closePreferencesModal();
    }
});

// Keyboard navigation for modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
        closePasswordModal();
        closePreferencesModal();
    }
});



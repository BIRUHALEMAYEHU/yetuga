document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const rememberMe = document.getElementById('remember-me');

    // Check for saved credentials
    const savedUsername = localStorage.getItem('rememberedUsername');
    const savedPassword = localStorage.getItem('rememberedPassword');
    if (savedUsername && savedPassword) {
        document.getElementById('username').value = savedUsername;
        passwordInput.value = savedPassword;
        rememberMe.checked = true;
    }

    // Password visibility toggle
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    });

    // Form validation
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = passwordInput.value;
        const remember = rememberMe.checked;

        // Basic client-side validation
        if (!username || !password) {
            showMessage(errorMessage, 'Please fill in all fields');
            return;
        }

        // Handle remember me
        if (remember) {
            localStorage.setItem('rememberedUsername', username);
            localStorage.setItem('rememberedPassword', password);
        } else {
            localStorage.removeItem('rememberedUsername');
            localStorage.removeItem('rememberedPassword');
        }

        // Submit form data to PHP
        const formData = new FormData(loginForm);
        
        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(successMessage, 'Login successful! Redirecting...');
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 1500);
            } else {
                showMessage(errorMessage, data.message || 'Login failed');
            }
        })
        .catch(error => {
            showMessage(errorMessage, 'An error occurred. Please try again.');
            console.error('Error:', error);
        });
    });

    // Helper function to show messages
    function showMessage(element, message) {
        element.textContent = message;
        element.classList.remove('d-none');
        element.classList.add('show');
        
        // Hide message after 5 seconds
        setTimeout(() => {
            element.classList.remove('show');
            element.classList.add('d-none');
        }, 5000);
    }

    // Add password strength indicator
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        updatePasswordStrengthIndicator(strength);
    });

    function calculatePasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        return strength;
    }

    function updatePasswordStrengthIndicator(strength) {
        const indicator = document.getElementById('password-strength') || createPasswordStrengthIndicator();
        const strengthText = ['Very Weak', 'Weak', 'Medium', 'Strong', 'Very Strong'][strength - 1] || '';
        const strengthClass = ['danger', 'warning', 'info', 'success', 'success'][strength - 1] || '';
        
        indicator.textContent = strengthText;
        indicator.className = `badge bg-${strengthClass}`;
    }

    function createPasswordStrengthIndicator() {
        const indicator = document.createElement('div');
        indicator.id = 'password-strength';
        indicator.className = 'badge mt-2';
        passwordInput.parentElement.appendChild(indicator);
        return indicator;
    }
}); 
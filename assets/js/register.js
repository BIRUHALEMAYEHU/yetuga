document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('register-form');
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const togglePassword = document.getElementById('toggle-password');
    const toggleConfirmPassword = document.getElementById('toggle-confirm-password');

    // Password visibility toggles
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    toggleConfirmPassword.addEventListener('click', function() {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    // Real-time validation
    const inputs = {
        username: document.getElementById('username'),
        email: document.getElementById('email'),
        password: passwordInput,
        confirmPassword: confirmPasswordInput
    };

    // Add input event listeners for real-time validation
    Object.entries(inputs).forEach(([key, input]) => {
        if (input) {  // Only add listener if element exists
            input.addEventListener('input', function() {
                validateInput(key, this.value);
            });
        }
    });

    // Form submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        console.log('Form submission started');
        
        // Reset messages
        errorMessage.classList.add('d-none');
        successMessage.classList.add('d-none');
        
        // Get form values directly
        const formData = new FormData(form);
        const username = formData.get('username');
        const email = formData.get('email');
        const password = formData.get('password');
        const confirmPassword = formData.get('confirm_password');
        
        // Validate inputs
        let isValid = true;
        if (!username || username.length < 3 || !/^[a-zA-Z0-9_]+$/.test(username)) {
            showMessage('Invalid username format', 'error');
            isValid = false;
        }
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showMessage('Invalid email format', 'error');
            isValid = false;
        }
        if (!password || password.length < 8) {
            showMessage('Password must be at least 8 characters', 'error');
            isValid = false;
        }
        if (password !== confirmPassword) {
            showMessage('Passwords do not match', 'error');
            isValid = false;
        }
        
        if (!isValid) return;
        
        try {
            const response = await fetch('register.php', {
                method: 'POST',
                body: formData
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Server response:', data);
            
            if (data.success) {
                showMessage(data.message, 'success');
                // Redirect to login page after successful registration
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 2000);
            } else {
                showMessage(data.message, 'error');
            }
        } catch (error) {
            console.error('Error during form submission:', error);
            showMessage('An error occurred. Please try again.', 'error');
        }
    });

    function showMessage(message, type) {
        const messageDiv = type === 'error' ? errorMessage : successMessage;
        if (messageDiv) {
            messageDiv.textContent = message;
            messageDiv.classList.remove('d-none');
            
            if (type === 'success' && errorMessage) {
                errorMessage.classList.add('d-none');
            }
        }
    }

    function updatePasswordStrength(password) {
        const strengthDiv = document.getElementById('password-strength');
        if (!strengthDiv) return;
        
        const strength = calculatePasswordStrength(password);
        
        strengthDiv.className = 'mt-2';
        strengthDiv.textContent = `Password strength: ${strength}`;
        
        switch (strength) {
            case 'Weak':
                strengthDiv.classList.add('text-danger');
                break;
            case 'Medium':
                strengthDiv.classList.add('text-warning');
                break;
            case 'Strong':
                strengthDiv.classList.add('text-success');
                break;
        }
    }

    function calculatePasswordStrength(password) {
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        if (strength <= 2) return 'Weak';
        if (strength <= 4) return 'Medium';
        return 'Strong';
    }
}); 
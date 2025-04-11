// Form validation
document.addEventListener('DOMContentLoaded', function() {
    // Password strength validation
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const minLength = 8;
            
            if (password.length < minLength) {
                this.setCustomValidity(`Password must be at least ${minLength} characters long`);
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Confirm password validation
    const confirmPasswordInput = document.getElementById('confirm_password');
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // City selector functionality
    const switchCityLink = document.querySelector('.switch-city-link');
    const cityDropdown = document.querySelector('.city-dropdown .dropdown-menu');
    const currentCity = document.querySelector('.current-city');
    const cityItems = document.querySelectorAll('.dropdown-item');

    // Toggle dropdown
    switchCityLink.addEventListener('click', function(e) {
        e.preventDefault();
        cityDropdown.classList.toggle('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.city-selector')) {
            cityDropdown.classList.remove('show');
        }
    });

    // Handle city selection
    cityItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const selectedCity = this.textContent;
            currentCity.textContent = selectedCity.toUpperCase();
            cityDropdown.classList.remove('show');
            
            // You can add additional logic here to handle city change
            // For example, saving to localStorage or making an API call
        });
    });

    // Route search form handling
    const searchForm = document.querySelector('.search-box form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const startLocation = this.querySelector('input[placeholder="Start location"]').value;
            const endLocation = this.querySelector('input[placeholder="End location"]').value;
            const selectedCity = document.querySelector('.city-selector span').textContent;

            if (!startLocation || !endLocation) {
                alert('Please enter both start and end locations');
                return;
            }

            // Here you would typically make an API call to your backend
            console.log('Searching route in', selectedCity, 'from', startLocation, 'to', endLocation);
            // TODO: Implement actual route search functionality
        });
    }

    // Initialize any tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add smooth scrolling to all links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Mouse light effect
    const heroSection = document.querySelector('.hero-section');
    const mouseLight = document.querySelector('.mouse-light');

    if (heroSection && mouseLight) {
        heroSection.addEventListener('mousemove', (e) => {
            const rect = heroSection.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            mouseLight.style.left = `${x}px`;
            mouseLight.style.top = `${y}px`;
        });

        heroSection.addEventListener('mouseleave', () => {
            mouseLight.style.opacity = '0';
        });

        heroSection.addEventListener('mouseenter', () => {
            mouseLight.style.opacity = '1';
        });
    }
}); 
// Routes Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeRoutesPage();
});

function initializeRoutesPage() {
    // Initialize route search functionality
    initializeRouteSearch();
    
    // Initialize filters
    initializeFilters();
    
    // Initialize swap button
    initializeSwapButton();
    
    // Initialize location suggestions
    initializeLocationSuggestions();
    
    // Check for URL parameters (if coming from dashboard)
    checkUrlParameters();
    
    // Initialize modal functionality
    initializeModal();
}

// Route Search Functionality
function initializeRouteSearch() {
    const searchForm = document.getElementById('routeSearchForm');
    const fromInput = document.getElementById('from-location');
    const toInput = document.getElementById('to-location');
    const transportSelect = document.getElementById('transport-type');
    const timeInput = document.getElementById('departure-time');
    const preferencesSelect = document.getElementById('preferences');
    
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
    
    // Add input validation
    if (fromInput) {
        fromInput.addEventListener('input', function() {
            validateLocationInput(this);
            showLocationSuggestions(this, 'from');
        });
    }
    
    if (toInput) {
        toInput.addEventListener('input', function() {
            validateLocationInput(this);
            showLocationSuggestions(this, 'to');
        });
    }
}

function handleRouteSearch() {
    const fromLocation = document.getElementById('from-location').value;
    const toLocation = document.getElementById('to-location').value;
    const transportType = document.getElementById('transport-type').value;
    const departureTime = document.getElementById('departure-time').value;
    const preferences = document.getElementById('preferences').value;
    
    // Basic validation
    if (!fromLocation.trim() || !toLocation.trim()) {
        showNotification('Please enter both starting point and destination', 'error');
        return;
    }
    
    // Show loading state
    showLoadingSection();
    
    // Simulate API call (replace with actual API call)
    setTimeout(() => {
        // Generate mock routes based on search criteria
        const routes = generateMockRoutes(fromLocation, toLocation, transportType, preferences);
        
        if (routes.length > 0) {
            displayRoutes(routes);
            showFiltersSection();
            showMapSection(fromLocation, toLocation);
        } else {
            showNoResults();
        }
        
        hideLoadingSection();
    }, 2000);
}

function generateMockRoutes(from, to, transportType, preferences) {
    // Mock data - replace with actual API response
    const mockRoutes = [
        {
            id: 1,
            from: from,
            to: to,
            transportType: 'bus',
            routeNumber: 'B-12',
            fare: 15,
            travelTime: 25,
            distance: 8.5,
            departureTime: '10:00',
            arrivalTime: '10:25',
            stops: 12,
            status: 'active',
            comfort: 'high',
            safety: 'high'
        },
        {
            id: 2,
            from: from,
            to: to,
            transportType: 'taxi',
            routeNumber: 'T-45',
            fare: 45,
            travelTime: 15,
            distance: 8.5,
            departureTime: '10:05',
            arrivalTime: '10:20',
            stops: 0,
            status: 'active',
            comfort: 'very-high',
            safety: 'high'
        },
        {
            id: 3,
            from: from,
            to: to,
            transportType: 'minibus',
            routeNumber: 'M-23',
            fare: 20,
            travelTime: 30,
            distance: 9.2,
            departureTime: '10:10',
            arrivalTime: '10:40',
            stops: 8,
            status: 'active',
            comfort: 'medium',
            safety: 'medium'
        },
        {
            id: 4,
            from: from,
            to: to,
            transportType: 'train',
            routeNumber: 'L-7',
            fare: 12,
            travelTime: 20,
            distance: 7.8,
            departureTime: '10:15',
            arrivalTime: '10:35',
            stops: 5,
            status: 'active',
            comfort: 'high',
            safety: 'very-high'
        }
    ];
    
    // Filter by transport type if specified
    if (transportType) {
        return mockRoutes.filter(route => route.transportType === transportType);
    }
    
    // Sort by preferences
    switch (preferences) {
        case 'fastest':
            return mockRoutes.sort((a, b) => a.travelTime - b.travelTime);
        case 'cheapest':
            return mockRoutes.sort((a, b) => a.fare - b.fare);
        case 'safest':
            return mockRoutes.sort((a, b) => getSafetyScore(b.safety) - getSafetyScore(a.safety));
        case 'comfortable':
            return mockRoutes.sort((a, b) => getComfortScore(b.comfort) - getComfortScore(a.comfort));
        default:
            return mockRoutes;
    }
}

function getSafetyScore(safety) {
    const scores = { 'low': 1, 'medium': 2, 'high': 3, 'very-high': 4 };
    return scores[safety] || 2;
}

function getComfortScore(comfort) {
    const scores = { 'low': 1, 'medium': 2, 'high': 3, 'very-high': 4 };
    return scores[comfort] || 2;
}

// Display Functions
function displayRoutes(routes) {
    const resultsSection = document.getElementById('resultsSection');
    const routesList = document.getElementById('routesList');
    const routeCount = document.getElementById('routeCount');
    const travelTime = document.getElementById('travelTime');
    
    if (!resultsSection || !routesList) return;
    
    // Update route count
    routeCount.textContent = `${routes.length} route${routes.length !== 1 ? 's' : ''} found`;
    
    // Calculate average travel time
    const avgTime = Math.round(routes.reduce((sum, route) => sum + route.travelTime, 0) / routes.length);
    travelTime.textContent = `Estimated time: ${avgTime} min`;
    
    // Clear existing routes
    routesList.innerHTML = '';
    
    // Display each route
    routes.forEach(route => {
        const routeCard = createRouteCard(route);
        routesList.appendChild(routeCard);
    });
    
    // Show results section
    resultsSection.style.display = 'block';
}

function createRouteCard(route) {
    const card = document.createElement('div');
    card.className = 'route-card';
    card.onclick = () => showRouteDetails(route);
    
    const transportIcon = getTransportIcon(route.transportType);
    const statusColor = route.status === 'active' ? '#10b981' : '#ef4444';
    
    card.innerHTML = `
        <div class="route-header">
            <div class="route-info">
                <h3>${route.from} → ${route.to}</h3>
                <p>${transportIcon} ${route.transportType.toUpperCase()} • Route ${route.routeNumber}</p>
            </div>
            <div class="route-stats">
                <div class="route-stat">
                    <span class="value">${route.fare} ETB</span>
                    <span class="label">Fare</span>
                </div>
                <div class="route-stat">
                    <span class="value">${route.travelTime} min</span>
                    <span class="label">Time</span>
                </div>
                <div class="route-stat">
                    <span class="value">${route.distance} km</span>
                    <span class="label">Distance</span>
                </div>
            </div>
        </div>
        
        <div class="route-details">
            <div class="route-detail">
                <i class="fas fa-clock"></i>
                <span>${route.departureTime} - ${route.arrivalTime}</span>
            </div>
            <div class="route-detail">
                <i class="fas fa-map-marker-alt"></i>
                <span>${route.stops} stops</span>
            </div>
            <div class="route-detail">
                <i class="fas fa-shield-alt"></i>
                <span>${route.safety}</span>
            </div>
            <div class="route-detail">
                <i class="fas fa-star"></i>
                <span>${route.comfort}</span>
            </div>
        </div>
        
        <div class="route-actions">
            <button class="route-btn primary" onclick="event.stopPropagation(); saveRoute(${route.id})">
                <i class="fas fa-heart"></i>
                Save Route
            </button>
            <button class="route-btn secondary" onclick="event.stopPropagation(); shareRoute(${route.id})">
                <i class="fas fa-share"></i>
                Share
            </button>
        </div>
    `;
    
    return card;
}

function getTransportIcon(transportType) {
    const icons = {
        'bus': '🚌',
        'taxi': '🚕',
        'train': '🚆',
        'minibus': '🚐'
    };
    return icons[transportType] || '🚌';
}

// Section Visibility Functions
function showLoadingSection() {
    hideAllSections();
    const loadingSection = document.getElementById('loadingSection');
    if (loadingSection) loadingSection.style.display = 'block';
}

function hideLoadingSection() {
    const loadingSection = document.getElementById('loadingSection');
    if (loadingSection) loadingSection.style.display = 'none';
}

function showFiltersSection() {
    const filtersSection = document.getElementById('filtersSection');
    if (filtersSection) filtersSection.style.display = 'block';
}

function showMapSection(from, to) {
    const mapSection = document.getElementById('mapSection');
    const mapFrom = document.getElementById('mapFrom');
    const mapTo = document.getElementById('mapTo');
    
    if (mapSection && mapFrom && mapTo) {
        mapFrom.textContent = from;
        mapTo.textContent = to;
        mapSection.style.display = 'block';
    }
}

function showNoResults() {
    hideAllSections();
    const noResults = document.getElementById('noResults');
    if (noResults) noResults.style.display = 'block';
}

function hideAllSections() {
    const sections = ['resultsSection', 'noResults', 'loadingSection', 'mapSection'];
    sections.forEach(sectionId => {
        const section = document.getElementById(sectionId);
        if (section) section.style.display = 'none';
    });
}

// Filter Functionality
function initializeFilters() {
    const priceRange = document.getElementById('priceRange');
    const priceValue = document.getElementById('priceValue');
    
    if (priceRange && priceValue) {
        priceRange.addEventListener('input', function() {
            priceValue.textContent = `${this.value} ETB`;
            applyFilters();
        });
    }
    
    // Add event listeners to checkboxes
    const checkboxes = document.querySelectorAll('.filter-options input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', applyFilters);
    });
}

function applyFilters() {
    // Get current filter values
    const maxPrice = document.getElementById('priceRange').value;
    const timeFilters = Array.from(document.querySelectorAll('.time-filters input:checked')).map(cb => cb.value);
    const transportFilters = Array.from(document.querySelectorAll('.transport-filters input:checked')).map(cb => cb.value);
    
    // Apply filters to displayed routes
    const routeCards = document.querySelectorAll('.route-card');
    routeCards.forEach(card => {
        let show = true;
        
        // Price filter
        const fare = parseInt(card.querySelector('.route-stat .value').textContent);
        if (fare > maxPrice) show = false;
        
        // Time filter
        const time = parseInt(card.querySelector('.route-stat .value').textContent);
        if (timeFilters.length > 0) {
            const timeInRange = timeFilters.some(range => {
                if (range === '0-30') return time <= 30;
                if (range === '30-60') return time > 30 && time <= 60;
                if (range === '60+') return time > 60;
                return true;
            });
            if (!timeInRange) show = false;
        }
        
        // Transport filter
        const transport = card.querySelector('.route-info p').textContent.toLowerCase();
        if (transportFilters.length > 0) {
            const transportMatch = transportFilters.some(type => transport.includes(type));
            if (!transportMatch) show = false;
        }
        
        card.style.display = show ? 'block' : 'none';
    });
}

// Swap Button Functionality
function initializeSwapButton() {
    const swapBtn = document.getElementById('swapBtn');
    if (swapBtn) {
        swapBtn.addEventListener('click', function() {
            const fromInput = document.getElementById('from-location');
            const toInput = document.getElementById('to-location');
            
            if (fromInput && toInput) {
                const temp = fromInput.value;
                fromInput.value = toInput.value;
                toInput.value = temp;
                
                // Add swap animation
                this.style.transform = 'rotate(180deg)';
                setTimeout(() => {
                    this.style.transform = 'rotate(0deg)';
                }, 300);
            }
        });
    }
}

// Location Suggestions
function initializeLocationSuggestions() {
    // Mock location suggestions
    const mockLocations = [
        'Bole International Airport',
        'Meskel Square',
        'Addis Ababa University',
        'National Museum',
        'Unity Park',
        'Entoto Park',
        'Lion of Judah',
        'Holy Trinity Cathedral',
        'Mercato Market',
        'Bole Road',
        'Kazanchis',
        'Piazza',
        'Arat Kilo',
        'Sidist Kilo',
        'Shiro Meda'
    ];
    
    // Store for later use
    window.mockLocations = mockLocations;
}

function showLocationSuggestions(input, type) {
    const suggestionsDiv = document.getElementById(`${type}-suggestions`);
    if (!suggestionsDiv || !window.mockLocations) return;
    
    const value = input.value.toLowerCase();
    const filteredLocations = window.mockLocations.filter(location => 
        location.toLowerCase().includes(value)
    ).slice(0, 5);
    
    if (filteredLocations.length > 0 && value.length > 0) {
        suggestionsDiv.innerHTML = filteredLocations.map(location => 
            `<div class="suggestion-item" onclick="selectLocation('${type}', '${location}')">${location}</div>`
        ).join('');
        suggestionsDiv.classList.add('show');
    } else {
        suggestionsDiv.classList.remove('show');
    }
}

function selectLocation(type, location) {
    const input = document.getElementById(`${type}-location`);
    const suggestions = document.getElementById(`${type}-suggestions`);
    
    if (input) input.value = location;
    if (suggestions) suggestions.classList.remove('show');
}

// Modal Functionality
function initializeModal() {
    const modal = document.getElementById('routeModal');
    const closeBtn = document.getElementById('closeModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', () => hideModal());
    }
    
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', () => hideModal());
    }
    
    // Close modal when clicking outside
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) hideModal();
        });
    }
}

function showRouteDetails(route) {
    const modal = document.getElementById('routeModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    
    if (!modal || !modalTitle || !modalBody) return;
    
    modalTitle.textContent = `Route ${route.routeNumber} Details`;
    
    modalBody.innerHTML = `
        <div class="route-detail-modal">
            <div class="route-summary">
                <h4>${route.from} → ${route.to}</h4>
                <p><strong>Transport:</strong> ${route.transportType.toUpperCase()}</p>
                <p><strong>Route Number:</strong> ${route.routeNumber}</p>
            </div>
            
            <div class="route-stats-grid">
                <div class="stat-item">
                    <span class="stat-label">Fare</span>
                    <span class="stat-value">${route.fare} ETB</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Travel Time</span>
                    <span class="stat-value">${route.travelTime} min</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Distance</span>
                    <span class="stat-value">${route.distance} km</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Stops</span>
                    <span class="stat-value">${route.stops}</span>
                </div>
            </div>
            
            <div class="route-schedule">
                <h5>Schedule</h5>
                <p><strong>Departure:</strong> ${route.departureTime}</p>
                <p><strong>Arrival:</strong> ${route.arrivalTime}</p>
            </div>
            
            <div class="route-quality">
                <h5>Quality Indicators</h5>
                <p><strong>Comfort:</strong> ${route.comfort}</p>
                <p><strong>Safety:</strong> ${route.safety}</p>
                <p><strong>Status:</strong> <span style="color: ${route.status === 'active' ? '#10b981' : '#ef4444'}">${route.status}</span></p>
            </div>
        </div>
    `;
    
    modal.classList.add('show');
}

function hideModal() {
    const modal = document.getElementById('routeModal');
    if (modal) modal.classList.remove('show');
}

// Utility Functions
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

function checkUrlParameters() {
    const urlParams = new URLSearchParams(window.location.search);
    const from = urlParams.get('from');
    const to = urlParams.get('to');
    const type = urlParams.get('type');
    const time = urlParams.get('time');
    
    if (from || to) {
        if (from) document.getElementById('from-location').value = from;
        if (to) document.getElementById('to-location').value = to;
        if (type) document.getElementById('transport-type').value = type;
        if (time) document.getElementById('departure-time').value = time;
        
        // Auto-trigger search
        setTimeout(() => {
            handleRouteSearch();
        }, 500);
    }
}

// Action Functions
function saveRoute(routeId) {
    showNotification('Route saved to favorites!', 'success');
    // Add actual save functionality here
}

function shareRoute(routeId) {
    if (navigator.share) {
        navigator.share({
            title: 'Check out this route on Yetuga',
            text: 'I found a great route using Yetuga!',
            url: window.location.href
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('Route link copied to clipboard!', 'success');
        });
    }
}

function retrySearch() {
    const noResults = document.getElementById('noResults');
    if (noResults) noResults.style.display = 'none';
    
    // Clear form and show search section
    document.getElementById('routeSearchForm').reset();
    document.getElementById('from-location').focus();
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



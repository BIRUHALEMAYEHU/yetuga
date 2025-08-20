/**
 * Secure API Helper for Yetuga App
 * Provides secure methods to call APIs from authenticated pages
 */

class YetugaAPI {
    constructor() {
        this.baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '');
        this.apiEndpoint = this.baseUrl + '/api_handler.php';
    }

    /**
     * Make a secure API call
     * @param {string} action - The API action to perform
     * @param {Object} data - Additional data to send
     * @returns {Promise} - Promise that resolves with the API response
     */
    async call(action, data = {}) {
        try {
            // Add the action to the data
            const requestData = {
                action: action,
                ...data
            };

            // Create form data for POST request
            const formData = new FormData();
            Object.keys(requestData).forEach(key => {
                formData.append(key, requestData[key]);
            });

            // Make the API call
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin' // Include cookies for session
            });

            // Parse the response
            const result = await response.json();

            // Check if the response indicates an error
            if (!response.ok) {
                throw new Error(result.message || `HTTP ${response.status}`);
            }

            return result;

        } catch (error) {
            console.error('API call failed:', error);
            throw error;
        }
    }

    /**
     * Get current session status
     * @returns {Promise} - Promise with session status
     */
    async getSessionStatus() {
        return this.call('session_status');
    }

    /**
     * Refresh the current session
     * @returns {Promise} - Promise with refresh result
     */
    async refreshSession() {
        return this.call('refresh_session');
    }

    /**
     * Get current user information
     * @returns {Promise} - Promise with user info
     */
    async getUserInfo() {
        return this.call('user_info');
    }

    /**
     * Check if session is about to expire and refresh if needed
     * @param {number} warningThreshold - Seconds before expiry to show warning (default: 300)
     * @returns {Promise} - Promise indicating if session was refreshed
     */
    async checkAndRefreshSession(warningThreshold = 300) {
        try {
            const status = await this.getSessionStatus();
            
            if (status.success && status.remaining_time <= warningThreshold) {
                // Session is getting close to expiry, refresh it
                await this.refreshSession();
                return true;
            }
            
            return false;
        } catch (error) {
            console.error('Session check failed:', error);
            return false;
        }
    }

    /**
     * Set up automatic session monitoring
     * @param {number} checkInterval - How often to check session (default: 60000ms = 1 minute)
     * @param {number} warningThreshold - Seconds before expiry to show warning (default: 300)
     */
    setupSessionMonitoring(checkInterval = 60000, warningThreshold = 300) {
        setInterval(async () => {
            try {
                const wasRefreshed = await this.checkAndRefreshSession(warningThreshold);
                if (wasRefreshed) {
                    console.log('Session automatically refreshed');
                }
            } catch (error) {
                console.error('Automatic session refresh failed:', error);
            }
        }, checkInterval);
    }
}

// Create a global instance
window.yetugaAPI = new YetugaAPI();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = YetugaAPI;
}

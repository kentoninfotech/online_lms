/**
 * Detect user's timezone and send it to the server
 * This runs on every page load to ensure the timezone is always available
 */
(function() {
    // Get the user's timezone using the Intl API
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    
    if (userTimezone) {
        // Store in localStorage for client-side use
        localStorage.setItem('userTimezone', userTimezone);

        // Also set a cookie so the server receives timezone on the next full-page request
        // Use name 'user_timezone' (matches middleware expectation)
        const cookieName = 'user_timezone=';
        if (!document.cookie.includes(cookieName)) {
            document.cookie = 'user_timezone=' + encodeURIComponent(userTimezone) + '; path=/; max-age=' + (60*60*24*365) + '; SameSite=Lax';
            // Reload once so the cookie is sent to the server and session can be populated
            // Avoid infinite reload by checking a small flag on window
            if (!window.__tzReloadDone) {
                window.__tzReloadDone = true;
                window.location.reload();
            }
        }
        
        // For fetch API
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            if (!args[1]) {
                args[1] = {};
            }
            if (!args[1].headers) {
                args[1].headers = {};
            }
            args[1].headers['X-User-Timezone'] = userTimezone;
            return originalFetch.apply(this, args);
        };
        
        // For XMLHttpRequest (used by jQuery and others)
        const originalOpen = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function(...args) {
            this._userTimezone = userTimezone;
            return originalOpen.apply(this, args);
        };
        
        const originalSend = XMLHttpRequest.prototype.send;
        XMLHttpRequest.prototype.send = function(...args) {
            if (this._userTimezone) {
                this.setRequestHeader('X-User-Timezone', this._userTimezone);
            }
            return originalSend.apply(this, args);
        };
    }
})();

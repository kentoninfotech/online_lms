/**
 * Detect user's timezone and send it to the server
 * This runs on every page load to ensure the timezone is always available
 */
(function() {
    // Get the user's timezone using the Intl API
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    
    if (userTimezone) {
        // Send the timezone as a header in all AJAX requests
        // This will be picked up by the DetectTimezone middleware
        
        // For fetch API
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            if (args[1]) {
                if (!args[1].headers) {
                    args[1].headers = {};
                }
            } else {
                args[1] = {
                    headers: {}
                };
            }
            args[1].headers['X-User-Timezone'] = userTimezone;
            return originalFetch.apply(this, args);
        };
        
        // For XMLHttpRequest (used by jQuery and others)
        const originalOpen = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function(...args) {
            const originalSetRequestHeader = this.setRequestHeader;
            this.setRequestHeader = function(header, value) {
                if (header.toLowerCase() === 'x-user-timezone') {
                    originalSetRequestHeader.call(this, header, value);
                } else {
                    originalSetRequestHeader.call(this, header, value);
                }
            };
            
            // Override send to add the header
            const originalSend = this.send;
            this.send = function(...sendArgs) {
                originalSetRequestHeader.call(this, 'X-User-Timezone', userTimezone);
                return originalSend.apply(this, sendArgs);
            };
            
            return originalOpen.apply(this, args);
        };
        
        // Also set it on page load via meta tag for server-side use
        const meta = document.createElement('meta');
        meta.name = 'user-timezone';
        meta.content = userTimezone;
        document.head.appendChild(meta);
    }
})();

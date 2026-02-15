// Detect user's browser timezone on page load
(function() {
    // Get the user's timezone from Intl API
    const browserTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    
    // Store in sessionStorage for use in forms
    sessionStorage.setItem('user_timezone', browserTimezone);
    
    // Send to server via header for subsequent requests
    // This will be picked up by the DetectTimezone middleware
})();

// Add timezone to AJAX requests
document.addEventListener('DOMContentLoaded', function() {
    const timezone = sessionStorage.getItem('user_timezone') || Intl.DateTimeFormat().resolvedOptions().timeZone;
    
    // Add timezone to all forms and AJAX requests
    const xhr = window.XMLHttpRequest;
    const originalOpen = xhr.prototype.open;
    
    xhr.prototype.open = function(method, url, async, user, pass) {
        originalOpen.apply(this, arguments);
        this.setRequestHeader('X-User-Timezone', timezone);
    };
});

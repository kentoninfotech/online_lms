// Detect user's browser timezone on page load
(function() {
    // Get the user's timezone from Intl API
    const browserTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    
    // Store in sessionStorage for use in forms
    sessionStorage.setItem('user_timezone', browserTimezone);
    localStorage.setItem('user_timezone', browserTimezone);
    
    // Function to add timezone to XMLHttpRequest
    function addTimezoneToXHR() {
        const xhr = window.XMLHttpRequest;
        const originalOpen = xhr.prototype.open;
        
        xhr.prototype.open = function(method, url, async, user, pass) {
            originalOpen.apply(this, arguments);
            this.setRequestHeader('X-User-Timezone', browserTimezone);
        };
    }
    
    // Function to add timezone to all forms
    function addTimezoneToForms() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeForms);
        } else {
            initializeForms();
        }
    }
    
    function initializeForms() {
        // Get all forms on the page
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            // Check if timezone input already exists to avoid duplicates
            const existingInput = form.querySelector('input[name="timezone"]');
            if (!existingInput) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'timezone';
                input.value = browserTimezone;
                form.appendChild(input);
            }
            
            // Also intercept form submissions to ensure header is sent
            form.addEventListener('submit', function(e) {
                // Update the hidden input in case timezone changed
                const tzInput = form.querySelector('input[name="timezone"]');
                if (tzInput) {
                    tzInput.value = browserTimezone;
                }
            });
        });
    }
    
    // Initialize on page load
    addTimezoneToXHR();
    addTimezoneToForms();
    
    // Re-apply to dynamically added forms
    const observer = new MutationObserver(function() {
        addTimezoneToForms();
    });
    
    observer.observe(document.body, {
        subtree: true,
        childList: true
    });
})();

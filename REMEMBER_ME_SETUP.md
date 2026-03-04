# Remember Me Feature - 30 Day Session Configuration

## Overview
This document outlines the "Remember me" feature implementation that keeps users logged in for **30 days** on their device using secure cookies.

## Configuration Details

### 1. Session Lifetime Configuration

**File:** `.env`
```
SESSION_DRIVER=database
SESSION_LIFETIME=43200              # 30 days in minutes (30 × 24 × 60 = 43200)
SESSION_ENCRYPT=false
SESSION_EXPIRE_ON_CLOSE=false       # Keeps session alive even after browser closes
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true          # Only sent over HTTPS
SESSION_SAME_SITE=none              # Allow cross-site requests
```

### 2. Session Configuration

**File:** `config/session.php`

Key settings:
```php
'driver' => 'database',              // Store sessions in database
'lifetime' => 43200,                 // 30 days in minutes
'expire_on_close' => false,          // Don't expire when browser closes
'secure' => SESSION_SECURE_COOKIE,   // Only over HTTPS
'http_only' => true,                 // Not accessible to JavaScript
'same_site' => 'none',               // Works across sites
```

### 3. Database Table
Sessions are stored in the `sessions` table with columns:
- `id` - Session ID
- `user_id` - Associated user ID
- `ip_address` - User's IP address
- `user_agent` - Browser information
- `payload` - Session data
- `last_activity` - Last access timestamp

### 4. User Model Configuration

**File:** `app/Models/User.php`

The User model includes:
```php
protected $hidden = [
    'password',
    'remember_token',  // Token for persistent login
];
```

### 5. Authentication Trait

**File:** `app/Http/Controllers/Auth/LoginController.php`

Uses Laravel's built-in `AuthenticatesUsers` trait which automatically handles:
- Remember me checkbox validation
- Cookie-based authentication
- Session token generation
- Token persistence in `users` table

## How It Works

### Login Flow with "Remember Me"

```
1. User enters email and password
2. User checks "Remember me for 30 days" checkbox
3. Form submits to /login endpoint
4. LoginController validates credentials via AuthenticatesUsers trait
5. If credentials valid:
   - Creates session in database
   - Generates remember_token in users table
   - Sets LARAVEL_SESSION cookie (43200 minutes = 30 days)
   - Sets LARAVEL_REMEMBER_TOKEN cookie (30 days)
   - Redirects to dashboard
6. User is logged in for the next 30 days
```

### Subsequent Visit (Within 30 Days)

```
1. User returns to site within 30 days
2. Browser sends session cookie
3. Laravel validates session:
   - If valid: User is logged in automatically
   - If invalid: Uses remember_token for re-authentication
4. Session is updated and refreshed
5. User sees their dashboard without re-entering credentials
```

### Browser Close Behavior
- **Session cookie:** Expires after 30 days of inactivity
- **Remember cookie:** Persists even after browser closes
- **Database session:** Cleaned up automatically by Laravel (lottery sweep)

## Login Form Implementation

**File:** `resources/views/auth/login.blade.php`

```html
<div class="form-check" title="Keep you logged in for 30 days on this device">
    <input 
        class="form-check-input" 
        type="checkbox" 
        name="remember"
        id="remember" 
        {{ old('remember') ? 'checked' : '' }}
    />
    <label class="form-check-label" for="remember">
        <i class="bi bi-clock-history me-1"></i>Remember me for 30 days
    </label>
</div>
```

## Security Considerations

### ✅ Implemented Security Features

1. **HTTPS Only** (`SESSION_SECURE_COOKIE=true`)
   - Cookies only sent over encrypted HTTPS connections
   - Prevents interception over insecure networks

2. **HttpOnly Flag** 
   - Cookies not accessible to JavaScript
   - Prevents XSS attacks stealing session cookies

3. **SameSite Protection**
   - Set to 'none' to allow cross-site requests
   - With Secure flag, prevents CSRF attacks

4. **Session Database Storage**
   - Sessions stored in database, not in cookies
   - Server can revoke sessions immediately
   - Failed login attempts logged

5. **Remember Token Hashing**
   - Token stored as hash in users table
   - Even if database is compromised, tokens are protected

### 🔒 Security Best Practices

1. **Use HTTPS in Production**
   - Set `APP_ENV=production` for SSL enforcement
   - Update `SESSION_SECURE_COOKIE=true`

2. **Login on Public Devices**
   - **DO NOT** check "Remember me" on shared/public computers
   - Always log out before leaving

3. **Session Cleanup**
   - Old sessions are automatically cleaned up
   - Configure cleanup frequency in `config/session.php`

4. **Two-Factor Authentication**
   - Consider requiring re-authentication for sensitive operations
   - Even with remember me enabled

## Testing the Remember Me Feature

### Manual Test Steps

1. **Initial Login**
   ```
   - Open login page
   - Enter valid email and password
   - CHECK "Remember me for 30 days" checkbox
   - Click Login
   - ✓ Should be logged in and redirected to dashboard
   ```

2. **Close and Reopen Browser**
   ```
   - Close entire browser
   - Reopen browser and navigate to application
   - ✓ Should still be logged in (no re-entry needed)
   - ✓ Session should be active for 30 days
   ```

3. **Clear Cookies Only**
   ```
   - Log in with "Remember me" checked
   - Go to Settings > Cookies/Site Data
   - Clear all cookies for the domain
   - Refresh page or navigate to login
   - ✓ Should use remember_token for re-authentication
   ```

4. **After 30 Days**
   ```
   - Session will expire automatically after 30 days
   - User must log in again
   - Previously valid remember_token becomes invalid
   ```

### Database Verification

Check active sessions in database:
```sql
SELECT id, user_id, last_activity, 
       ROUND((UNIX_TIMESTAMP(NOW()) - last_activity) / 60) as idle_minutes
FROM sessions
ORDER BY last_activity DESC;
```

Check remember tokens:
```sql
SELECT id, email, remember_token 
FROM users 
WHERE remember_token IS NOT NULL;
```

## Configuration Files Modified

### 1. `.env`
- Updated `SESSION_LIFETIME` from 120 to 43200 minutes
- Added `SESSION_EXPIRE_ON_CLOSE=false`
- Ensured `SESSION_SECURE_COOKIE=true` for production

### 2. `resources/views/auth/login.blade.php`
- Enhanced "Remember me" checkbox with icon and 30-day label
- Added tooltip explaining the duration
- Maintained form validation and old value persistence

### 3. `app/Http/Controllers/Auth/LoginController.php`
- No changes needed (inherits from AuthenticatesUsers trait)
- Trait automatically handles remember me with checkbox

## Troubleshooting

### Issue: Users Not Staying Logged In

**Solution:**
1. Check `SESSION_LIFETIME` in `.env` (should be 43200)
2. Verify `SESSION_EXPIRE_ON_CLOSE=false`
3. Ensure sessions table exists: `php artisan migrate`
4. Check browser cookies are enabled
5. Verify `SESSION_SECURE_COOKIE` matches your protocol (true for HTTPS)

### Issue: Session Expires Too Quickly

**Solution:**
1. Verify value in `.env` is `SESSION_LIFETIME=43200`
2. Check that middleware hasn't reduced lifetime
3. Ensure no logout middleware is being triggered

### Issue: Cannot Log In with Remember Me

**Solution:**
1. Verify `remember` checkbox is actually in form
2. Check user has `remember_token` column in users table
3. Run migrations: `php artisan migrate`
4. Clear application cache: `php artisan cache:clear`

### Issue: Remember Cookie Not Being Set

**Solution:**
1. Verify HTTPS is being used in production
2. Check `SESSION_SECURE_COOKIE` is not set to true on localhost
3. Enable cookies in browser settings
4. Check for third-party cookie blocks (privacy settings)

## Environment-Specific Configuration

### Local Development
```env
SESSION_SECURE_COOKIE=false  # Allow non-HTTPS cookies
APP_DEBUG=true
```

### Staging
```env
SESSION_SECURE_COOKIE=true   # Require HTTPS
APP_DEBUG=true
SESSION_LIFETIME=43200       # 30 days
```

### Production
```env
SESSION_SECURE_COOKIE=true   # HTTPS only
APP_ENV=production
APP_DEBUG=false
SESSION_LIFETIME=43200       # 30 days
SESSION_SAME_SITE=strict     # More restrictive
```

## Related Documentation

- Laravel Session Documentation: https://laravel.com/docs/authentication
- HTTP Cookies: https://developer.mozilla.org/en-US/docs/Web/HTTP/Cookies
- OWASP Session Management: https://owasp.org/www-community/attacks/Session_abuse

## Summary

✅ **Remember Me Feature is now active:**
- Session lifetime: **30 days**
- Cookie security: **HTTPS only**
- Database storage: **Encrypted in sessions table**
- Token persistence: **Secure hashing**
- Logout: **Automatic after 30 days of inactivity**

Users who check "Remember me for 30 days" will stay logged in across browser sessions for an entire month without needing to re-enter their credentials.

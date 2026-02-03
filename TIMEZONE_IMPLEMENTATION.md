# Timezone Implementation Guide

## Overview
This document outlines the timezone implementation for the Online LMS application to handle users in different timezones while maintaining UTC as the server baseline.

## Changes Made

### 1. Configuration (`config/app.php`)
- Changed timezone from `'Africa/Lagos'` to `'UTC'`
- All times are now stored and processed in UTC on the server
- User-specific timezone conversion happens only during display

### 2. Database Migration
**File**: `database/migrations/2025_02_02_000000_add_timezone_to_users_table.php`

Creates a new `timezone` column on the users table:
```php
$table->string('timezone')->default('UTC')->after('password');
```

**To run migration**:
```bash
php artisan migrate
```

### 3. User Model (`app/Models/User.php`)
- Added `FormatsTimeForUser` trait
- Added `'timezone'` to the `$fillable` array
- Users can now have their own timezone preference

### 4. FormatsTimeForUser Trait (`app/Traits/FormatsTimeForUser.php`)
Provides helper methods for time formatting:

- `formatForUser(Carbon $date, $format)` - Full customizable formatting
- `formatTimeForUser(Carbon $date)` - Time only (h:i A)
- `formatDateTimeForUser(Carbon $date)` - Full date-time (d M Y h:i A)
- `formatDateForUser(Carbon $date)` - Date only (d M Y)

### 5. Blade Component (`resources/views/components/format-time.blade.php`)
Simple component for use in views:
```blade
<x-format-time :date="$variable" format="d M Y h:i A" />
```

Supports any format string. Defaults to `d M Y h:i A` if format not specified.

### 6. Updated Views
The following views have been updated to use timezone-aware formatting:

**Instructor Dashboard**:
- `resources/views/dashboard/instructor/index.blade.php`
  - Today's classes schedule
  - Reschedule requests
  - Upcoming classes table
  - Attendance history
  - Ongoing class details
  - Next event countdown

**Admin Dashboard**:
- `resources/views/dashboard/admin/lessons.blade.php`
  - Today's classes
  - Lessons table with next class time
  - Attendance records

**Student Dashboard**:
- `resources/views/dashboard/student.blade.php`
  - Upcoming classes schedule
  - Attendance records
- `resources/views/dashboard/student/attendance.blade.php`
  - Attendance history with times

**Other**:
- `resources/views/dashboard/waiting.blade.php` - Waiting room schedule
- `resources/views/dashboard/show-payment.blade.php` - Payment creation date

## How It Works

### Display Flow
1. Time is stored in **UTC** in database
2. When displaying, the component retrieves the authenticated user's timezone
3. If no timezone is set, falls back to application timezone (UTC)
4. Time is converted to user's timezone for display only
5. Database remains unchanged (always UTC)

### Example Usage in Blade

```blade
<!-- Default format (d M Y h:i A) -->
<x-format-time :date="$occurrence->scheduled_start" />

<!-- Custom format -->
<x-format-time :date="$lesson->start_time" format="h:i A" />

<!-- Using trait method in controller -->
$this->formatTimeForUser($date) // Returns "2:30 PM"
$this->formatDateTimeForUser($date) // Returns "02 Feb 2026 2:30 PM"
```

## User Timezone Preferences

### Setting User Timezone
```php
// In controller or seeder
$user->update(['timezone' => 'America/New_York']);
$user->update(['timezone' => 'Europe/London']);
$user->update(['timezone' => 'Asia/Tokyo']);
```

### Available Timezones
Use standard PHP timezone identifiers from the [IANA timezone database](https://www.php.net/manual/en/datetimezone.listidentifiers.php):
- `UTC`
- `America/New_York`
- `Europe/London`
- `Africa/Lagos`
- `Asia/Tokyo`
- `Australia/Sydney`
- etc.

## Testing Timezone Conversion

You can test timezone functionality:

```blade
<!-- Display same time in different formats -->
<p>Server time (UTC): {{ $date }}</p>
<p>User's time: <x-format-time :date="$date" /></p>
<p>Custom format: <x-format-time :date="$date" format="l, F j, Y g:i A" /></p>
```

## JavaScript Countdown Timers

Countdown timers in JavaScript automatically use the browser's local timezone:
```javascript
const targetDate = new Date("{{ $nextClass->scheduled_start->toIso8601String() }}").getTime();
// Browser automatically interprets ISO8601 string in its local timezone
```

The server sends ISO8601 format (which includes timezone info), and JavaScript handles the conversion.

## Best Practices

1. **Always store in UTC**: Database should always contain UTC times
2. **Convert on display**: Only convert to user's timezone when rendering
3. **Backup timezone**: Always provide a fallback (usually UTC)
4. **Accept all formats**: The component handles null/missing dates gracefully
5. **Validate user input**: When accepting times from users, convert them to UTC before storing

## Troubleshooting

### Times displaying incorrectly
- Check that user has timezone set: `$user->timezone`
- Verify database has the timezone column: `php artisan migrate`
- Check that blade component is using `:date` (not `{{ }}`)

### Component not found
- Verify file exists at: `resources/views/components/format-time.blade.php`
- Clear view cache: `php artisan view:clear`

### Database migration fails
- Ensure PHP version is 8.2+ (required for Laravel 12)
- Run: `composer install` and `composer update`
- Check database connection in `.env`

## Future Improvements

1. Add user timezone selection in profile settings
2. Auto-detect timezone from user's browser
3. Add timezone selector to registration form
4. Create API endpoints that respect user timezones
5. Add scheduled job timezone-aware handling

## Summary of Files Modified

1. `config/app.php` - Changed timezone to UTC
2. `app/Models/User.php` - Added trait and timezone field
3. `app/Traits/FormatsTimeForUser.php` - Created trait
4. `database/migrations/2025_02_02_000000_add_timezone_to_users_table.php` - Created migration
5. `resources/views/components/format-time.blade.php` - Created component
6. All views updated to use `<x-format-time>` component

## Migration Checklist

- [ ] Run `php artisan migrate`
- [ ] Update user timezone preferences (initially set to their location)
- [ ] Test in multiple browsers with different system timezones
- [ ] Update API responses to include timezone if needed
- [ ] Document timezone in API documentation

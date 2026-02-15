# Timezone Architecture

## Overview

All lessons in the Online LMS are stored in **Africa/Lagos (UTC+1)** timezone in the database. This ensures consistent behavior for cron jobs, scheduled activities, and Nigerian instructors.

## Why Africa/Lagos (UTC+1)?

1. **Consistent Cron Jobs**: All scheduled tasks (lessons:generate-occurrences, lessons:update-status, etc.) read times directly from the database in Africa/Lagos
2. **Single Source of Truth**: No timezone conversion needed for backend operations
3. **Instructor Efficiency**: Nigerian instructors schedule lessons in their local time (Africa/Lagos)
4. **Database Simplicity**: All datetime values in the database are in one consistent timezone

## How It Works

### For Instructors (Creating/Editing Lessons)
```
Instructor in Nigeria (Africa/Lagos)
        ↓
Input: 20:54 (Nigeria local time)
        ↓
Stored in DB: 20:54 Africa/Lagos (UTC+1)
```

### For Students/Learners (Viewing Lessons)
```
Lesson stored in DB: 20:54 Africa/Lagos
        ↓
Student in Texas (Central Time, UTC-5)
        ↓
Converted to: 13:54 (Texas local time)
        ↓
Displayed to Student: 13:54 PM
```

## Key Files Involved

### Database Storage
- **Lessons Table**: `start_time` column stores Africa/Lagos time
- No UTC conversion happens - times are stored as-is

### Backend Processing
- **config/app.php**: `'timezone' => 'Africa/Lagos'`
- **Cron Jobs**: Use times directly from database (all in Africa/Lagos)
- **Scheduled Activities**: Read from DB in Africa/Lagos context

### Frontend Display
- **format-time.blade.php**: Converts FROM Africa/Lagos TO user's timezone
- **format-time usage**: `<x-format-time :date="$lesson->start_time" />`
- **display conversion flow**:
  ```
  Database value (Africa/Lagos)
       ↓
  setTimezone('Africa/Lagos')  // Ensure we're starting from Africa/Lagos
       ↓
  setTimezone(getUserTimezone())  // Convert to user's local timezone
       ↓
  Display to user
  ```

### Lesson Controller
- **LessonController::parseDateTime()**: Parses user input and stores directly in Africa/Lagos
- **No UTC conversion** - input time is stored as-is in Africa/Lagos

### Lesson Model
- **Lesson::getStartTimeAttribute()**: Returns time in Africa/Lagos
- **Lesson::setStartTimeAttribute()**: Ensures times are stored in Africa/Lagos

### Middleware
- **DetectTimezone**: Detects user's browser timezone on every request
- **Stores in session**: `session('user_timezone')`
- **Used for display**: All views convert FROM Africa/Lagos TO user's timezone

## Example Scenarios

### Scenario 1: Instructor Creates Lesson
1. Instructor in Lagos inputs: **20:54 Africa/Lagos**
2. Form sends to LessonController
3. parseDateTime() validates and stores: **20:54**
4. Database stores: **2025-02-15 20:54:00** (no timezone info, Africa/Lagos context)

### Scenario 2: Student Views Lesson
1. Database query retrieves: **2025-02-15 20:54:00** (Africa/Lagos)
2. format-time component processes:
   - Parse as Africa/Lagos: **2025-02-15 20:54:00 in Africa/Lagos (UTC+1)**
   - Get user's timezone from session: **America/Chicago (UTC-6)**
   - Convert: **2025-02-15 13:54:00 in America/Chicago**
3. Display: **Feb 15 01:54 PM**

### Scenario 3: Cron Job Checks Lesson Status
1. Command runs at system time using Africa/Lagos timezone
2. Queries: `WHERE scheduled_start <= NOW()` (both in Africa/Lagos context)
3. Finds lessons that should start now
4. No timezone conversion needed

## Important Notes

- **Never** convert lesson times to UTC
- **Always** treat database times as Africa/Lagos
- **When displaying**: Convert FROM Africa/Lagos TO user's timezone
- **For cron jobs**: Use times directly from database
- **User's timezone**: Detected on login via browser JavaScript, stored in session

## Testing Timezone

To test timezone functionality:

1. Create a lesson as an instructor in Nigeria at 20:54
2. Check database: Value should show as 20:54 (no conversion)
3. Log in as student in different timezone
4. Check lesson display: Should show converted time
5. Check sidebar: Should show user's local time with clock

## Updating Cron Jobs

If adding new cron jobs, remember:
- All times in database are Africa/Lagos
- Don't convert to UTC
- Use times directly from database

Example (WRONG - don't do this):
```php
$lesson->start_time->setTimezone('UTC');  // WRONG
```

Example (CORRECT):
```php
$lesson->start_time;  // Already in Africa/Lagos
```

## Migration Note

If you have existing lessons stored in UTC, you need to:
1. Convert them from UTC to Africa/Lagos
2. Update the Lesson model to handle this
3. Test thoroughly before deploying

Run this if migrating from UTC-based system:
```sql
-- Assuming old times are in UTC, convert to Africa/Lagos (UTC+1)
UPDATE lessons SET start_time = DATE_ADD(start_time, INTERVAL 1 HOUR);
```

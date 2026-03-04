# Implementation Summary: Instructor Course Management and Page Visibility Settings

## Date: March 3, 2026

## Features Implemented

### 1. Page Visibility Settings

#### Objective
Allow admins to control which public pages (Services and Galleries) are visible to visitors through the Home Page Settings admin panel.

#### Changes Made

**Files Modified:**
- `app/Http/Controllers/ServiceController.php` - Added visibility check
- `app/Http/Controllers/GalleryController.php` - Added visibility check
- `app/Http/Controllers/Admin/HomepageSettingController.php` - Added visibility section

**New Files Created:**
- `resources/views/admin/homepage-settings/visibility.blade.php` - Dedicated visibility settings UI with toggle switches

#### How It Works

1. **In Controllers:**
   - `ServiceController::index()` checks `HomepageSetting::getSetting('visibility', 'show_services', true)`
   - `GalleryController::index()` checks `HomepageSetting::getSetting('visibility', 'show_galleries', true)`
   - If disabled (false), returns 404 error to visitors

2. **In Admin Panel:**
   - Admins can access Settings → Page Visibility Settings
   - Toggle switches for Services and Galleries pages
   - Automatic form submission on toggle
   - Real-time visibility of current state

3. **Default Behavior:**
   - Both pages are VISIBLE by default (to maintain backward compatibility)
   - Can be disabled individually through admin panel

#### Routes Affected
- `/services` - Returns 404 if `show_services` is disabled
- `/galleries` - Returns 404 if `show_galleries` is disabled
- `/admin/homepage-settings/visibility` - New admin settings page

### 2. Instructor Course Management System

#### Objective
Enable instructors (tutors) to be assigned to manage specific courses with granular permission controls.

#### Database Changes

**New Migration:**
- `2026_03_03_000000_create_instructor_course_table.php`

**New Table: instructor_course**
```sql
- instructor_id (FK to instructors)
- course_id (FK to courses)
- role (lead, co-instructor, assistant)
- bio (text description)
- order (display order)
- can_manage_content (boolean)
- can_manage_enrollees (boolean)
- can_manage_quizzes (boolean)
- is_active (boolean)
```

**Features:**
- Multiple instructors can manage a single course
- Each instructor can have different roles and permissions
- Role-based access control:
  - **Lead**: Full control over course management
  - **Co-instructor**: Secondary responsible party
  - **Assistant**: Limited support role
- Permission granularity:
  - Can manage course content (create/edit lessons, materials)
  - Can manage quiz questions and view results
  - Can manage student enrollments

#### Model Changes

**Instructor Model (`app/Models/Instructor.php`):**
```php
// New relationships added:
public function courses(): BelongsToMany { ... }
public function activeCourses(): BelongsToMany { ... }
```

**Course Model (`app/Models/Course.php`):**
```php
// New relationships added:
public function instructors(): BelongsToMany { ... }
public function activeInstructors(): BelongsToMany { ... }
```

#### Usage Examples

**Assign instructor to course:**
```php
$instructor = Instructor::find(1);
$instructor->courses()->attach($course->id, [
    'role' => 'lead',
    'can_manage_content' => true,
    'can_manage_enrollees' => false,
    'can_manage_quizzes' => true,
    'is_active' => true
]);
```

**Get courses for instructor:**
```php
$instructor = auth()->user()->instructor;
$courses = $instructor->activeCourses()->get(); // Only active assignments
```

**Check permissions:**
```php
$canManageContent = $instructor->courses()
    ->where('course_id', $courseId)
    ->wherePivot('can_manage_content', true)
    ->exists();
```

#### Documentation

**Created File:**
- `INSTRUCTOR_COURSE_MANAGEMENT.md` - Complete guide including:
  - Database structure details
  - Model relationships
  - How to assign courses to instructors
  - Permission checking examples
  - Future feature requirements
  - API endpoint suggestions

## Testing Instructions

### Test Page Visibility Settings

1. Navigate to `/admin/homepage-settings`
2. Click "Page Visibility Settings"
3. Toggle "Services page" switch
4. Try accessing `/services` (should show 404 if disabled)
5. Toggle back on and verify `/services` works
6. Repeat for Galleries page

### Test Instructor Course Assignment

Use Laravel Tinker:
```bash
php artisan tinker
```

```php
// Create test data
$instructor = \App\Models\Instructor::first();
$course = \App\Models\Course::first();

// Assign instructor to course
$instructor->courses()->attach($course->id, [
    'role' => 'lead',
    'can_manage_content' => true,
    'can_manage_enrollees' => false,
    'can_manage_quizzes' => true,
    'is_active' => true
]);

// Verify assignment
$instructor->courses()->get();  // Should show the course
$course->instructors()->get();  // Should show the instructor
```

## Future Implementation Tasks

### Priority 1: Admin Interface for Course-Instructor Assignment
- Add course admin controller method to assign/remove instructors
- Add course edit view with instructor assignment form
- Show list of assigned instructors with role, permissions, status

### Priority 2: Instructor Dashboard
- Create instructor dashboard showing assigned courses
- Add quick access buttons for managing course content
- Display permission levels for each course
- Show student enrollment counts

### Priority 3: Course Content Management Permissions
- Add policy checks in CourseContent controller
- Verify instructor has `can_manage_content` permission
- Log all changes made by instructors

### Priority 4: Quiz Management Permissions
- Verify instructor has `can_manage_quizzes` permission
- Allow viewing student quiz submissions
- Add audit trail for quiz changes

### Priority 5: Enrollment Management (Optional)
- If `can_manage_enrollees` is enabled
- Allow instructor to approve/reject enrollments
- Send notifications to enrollees

## Database Consistency

All migrations have been run successfully:
- `instructor_course` table created with proper indexes
- Foreign key constraints in place
- Unique constraint prevents duplicate assignments

## Backward Compatibility

✅ **Page Visibility Settings:**
- Existing services and galleries functionality unchanged
- Default behavior: both pages VISIBLE
- Zero impact if settings not configured

✅ **Instructor Course Management:**
- Completely optional feature
- Won't affect any existing course functionality
- Facilitators system remains independent and unaffected
- Instructors can be assigned without breaking existing relationships

## Notes

1. The `instructor_course` table uses a pivot table pattern for many-to-many relationships
2. Both features are opt-in and backward compatible
3. Documentation file included for future development reference
4. Admin UI for course-instructor assignment needs to be implemented
5. Dashboard for instructors to see assigned courses needs to be created

## Files Summary

**Modified:**
- `app/Http/Controllers/ServiceController.php` (1 import added, 1 check added)
- `app/Http/Controllers/GalleryController.php` (1 import added, 1 check added)
- `app/Http/Controllers/Admin/HomepageSettingController.php` (visibility section added)
- `app/Models/Instructor.php` (2 methods added)
- `app/Models/Course.php` (2 methods added)

**Created:**
- `database/migrations/2026_03_03_000000_create_instructor_course_table.php` (new table)
- `resources/views/admin/homepage-settings/visibility.blade.php` (new UI)
- `INSTRUCTOR_COURSE_MANAGEMENT.md` (documentation)
- `IMPLEMENTATION_SUMMARY_PAGE_VISIBILITY_INSTRUCTOR_COURSES.md` (this file)

## Status: ✅ COMPLETE

All features implemented and ready for testing. Next steps: Admin interface for course-instructor assignment and instructor dashboard.

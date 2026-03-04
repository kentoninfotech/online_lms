# Instructor Course Management System

## Overview

This document describes how instructors (tutors) registered in the system can be assigned to manage courses. When an instructor is assigned to a course, they can manage course contents, can manage quizzes, and other course-related materials.

## Database Structure

### instructor_course Pivot Table

The `instructor_course` table connects instructors with courses using a many-to-many relationship. This allows multiple instructors to be assigned to a single course with different roles and permissions.

**Table Structure:**
```sql
CREATE TABLE instructor_course (
    id BIGINT PRIMARY KEY,
    instructor_id BIGINT FOREIGN KEY (instructors.id),
    course_id BIGINT FOREIGN KEY (courses.id),
    role ENUM('lead', 'co-instructor', 'assistant') DEFAULT 'lead',
    bio TEXT,
    order INT DEFAULT 0,
    can_manage_content BOOLEAN DEFAULT true,
    can_manage_enrollees BOOLEAN DEFAULT false,
    can_manage_quizzes BOOLEAN DEFAULT true,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(instructor_id, course_id)
);
```

**Fields:**
- `instructor_id`: References the instructor in the instructors table
- `course_id`: References the course in the courses table
- `role`: Defines the role of the instructor
  - `lead`: Primary/Lead instructor (full control)
  - `co-instructor`: Secondary instructor (shared responsibilities)
  - `assistant`: Assistant instructor (limited responsibilities)
- `bio`: Bio text for this instructor on this course
- `order`: Display order for multiple instructors on same course
- `can_manage_content`: Whether instructor can create/edit course content
- `can_manage_enrollees`: Whether instructor can manage student enrollments
- `can_manage_quizzes`: Whether instructor can create/edit quizzes and access results
- `is_active`: Whether this assignment is currently active

## Model Relationships

### Instructor Model
```php
// Get all courses assigned to this instructor
public function courses(): BelongsToMany { ... }

// Get only active courses assigned to this instructor
public function activeCourses(): BelongsToMany { ... }
```

### Course Model
```php
// Get all instructors assigned to manage this course
public function instructors(): BelongsToMany { ... }

// Get only active instructors for this course
public function activeInstructors(): BelongsToMany { ... }
```

## How to Assign a Course to an Instructor

### 1. Using Laravel Artisan (for testing/development)

```bash
php artisan tinker

# Assign instructor to course
$instructor = \App\Models\Instructor::find(1);
$course = \App\Models\Course::find(1);

$instructor->courses()->attach($course->id, [
    'role' => 'lead',
    'can_manage_content' => true,
    'can_manage_enrollees' => false,
    'can_manage_quizzes' => true,
    'is_active' => true
]);
```

### 2. Using Eloquent Sync (to replace all assignments)

```php
// Remove all previous assignments and attach new ones
$instructor->courses()->sync([
    1 => ['role' => 'lead', 'can_manage_content' => true, 'can_manage_quizzes' => true, 'is_active' => true],
    2 => ['role' => 'co-instructor', 'can_manage_content' => true, 'can_manage_quizzes' => false, 'is_active' => true],
]);
```

### 3. Using Controller (When Admin Interface is Created)

```php
// In a course admin controller
public function assignInstructor(Request $request, Course $course)
{
    $request->validate([
        'instructor_id' => 'required|exists:instructors,id',
        'role' => 'required|in:lead,co-instructor,assistant',
        'can_manage_content' => 'boolean',
        'can_manage_enrollees' => 'boolean',
        'can_manage_quizzes' => 'boolean',
    ]);

    $course->instructors()->attach($request->instructor_id, [
        'role' => $request->role,
        'can_manage_content' => $request->can_manage_content,
        'can_manage_enrollees' => $request->can_manage_enrollees,
        'can_manage_quizzes' => $request->can_manage_quizzes,
        'is_active' => true,
    ]);

    return redirect()->back()->with('success', 'Instructor assigned successfully');
}
```

## Accessing Assigned Courses

### For an Instructor

```php
// Get all courses assigned to logged-in instructor
$instructor = auth()->user()->instructor;
$courses = $instructor->activeCourses()->get();

// Check if instructor can manage a specific course's content
if ($instructor->courses()->wherePivot('course_id', $courseId)->wherePivot('can_manage_content', true)->exists()) {
    // Allow content management
}
```

### For a Course

```php
// Get all instructors for a course
$course = Course::find(1);
$instructors = $course->activeInstructors()->get();

// Get lead instructor
$leadInstructor = $course->instructors()
    ->wherePivot('role', 'lead')
    ->wherePivot('is_active', true)
    ->first();
```

## Future Features

### Admin Dashboard Updates Needed

1. **Course Management Section** - Add ability to assign instructors to courses:
   - View all instructors available
   - Add/remove instructor assignments
   - Set roles and permissions per instructor
   - Activate/deactivate assignments

2. **Instructor Dashboard** - Show assigned courses:
   - List of courses the instructor manages
   - Quick access to manage course content
   - Access to quiz results and student submissions
   - Enrollment management (if permission enabled)

3. **Audit Trail** - Track who made changes to courses and when

## Permissions & Authorization

### Proposed Permission Checks

```php
// Check if instructor can manage a course
if ($instructor->courses()->where('course_id', $courseId)->wherePivot('can_manage_content', true)->exists()) {
    // Allow course content management
}

// Check if instructor can modify quizzes
if ($instructor->courses()->where('course_id', $courseId)->wherePivot('can_manage_quizzes', true)->exists()) {
    // Allow quiz management
}

// Check if instructor can manage enrollees
if ($instructor->courses()->where('course_id', $courseId)->wherePivot('can_manage_enrollees', true)->exists()) {
    // Allow enrollee management
}
```

## Example Usage

### Assigning Multiple Courses to an Instructor

```php
$instructor = Instructor::find(1);

// Assign lead role for course 1
$instructor->courses()->attach(1, [
    'role' => 'lead',
    'can_manage_content' => true,
    'can_manage_enrollees' => true,
    'can_manage_quizzes' => true,
    'is_active' => true,
]);

// Assign co-instructor role for course 2
$instructor->courses()->attach(2, [
    'role' => 'co-instructor',
    'can_manage_content' => true,
    'can_manage_enrollees' => false,
    'can_manage_quizzes' => true,
    'is_active' => true,
]);

// Get all assigned courses
$courses = $instructor->activeCourses()->with('category')->get();
```

## Migration

The migration file `2026_03_03_000000_create_instructor_course_table.php` creates the necessary table structure. Run with:

```bash
php artisan migrate
```

## API Endpoint Example (Future)

```
POST /api/admin/courses/{course}/instructors
{
    "instructor_id": 1,
    "role": "lead",
    "can_manage_content": true,
    "can_manage_enrollees": false,
    "can_manage_quizzes": true
}

GET /api/admin/courses/{course}/instructors
Returns list of all instructors assigned to course

DELETE /api/admin/courses/{course}/instructors/{instructor}
Remove instructor from course
```

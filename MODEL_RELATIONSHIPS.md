# Model Relationships & Authorization Analysis

## Key Models & Relationships

### User Model
- Uses `Spatie\Permission\Traits\HasRoles` for role/permission management
- **Relationships:**
  - `user -> parent()` (HasOne with ParentModel)
  - `user -> student()` (HasOne with Student)
  - `user -> instructor()` (HasOne with Instructor)
  - `user -> attendances()` (HasMany)
  - `user -> subscriptions()` (HasMany)

### Instructor Model
- **Primary Key:** `id`
- **Foreign Key:** `user_id` (links to User)
- **Relationships:**
  - `instructor -> user()` (BelongsTo User)
  - `instructor -> lessons()` (HasMany with Lesson, foreign key = `instructor_id`)

### Student Model
- **Primary Key:** `id`
- **Foreign Key:** `user_id` (links to User)
- **Relationships:**
  - `student -> user()` (BelongsTo User)
  - `student -> lessons()` (HasMany with Lesson, foreign key = `student_id`)
  - `student -> parents()` (BelongsToMany with ParentModel)

### Lesson Model
- **Primary Key:** `id`
- **Foreign Keys:** 
  - `instructor_id` (links to Instructor.id)
  - `student_id` (links to Student.id)
- **Relationships:**
  - `lesson -> instructor()` (BelongsTo Instructor)
  - `lesson -> student()` (BelongsTo Student)
  - `lesson -> occurrences()` (HasMany LessonOccurrence)

### LessonOccurrence Model
- **Primary Key:** `id`
- **Foreign Key:** `lesson_id` (links to Lesson)
- **Relationships:**
  - `occurrence -> lesson()` (BelongsTo Lesson)
  - `occurrence -> zoomSession()` (HasOne)
  - `occurrence -> attendances()` (HasMany)
  - `occurrence -> rescheduleRequests()` (HasMany)

## Role-Based Access Control (RBAC)

Uses **Spatie Laravel Permissions** with roles stored in database:
- **roles table:** Contains role definitions (admin, instructor, student, parent)
- **model_has_roles table:** Links users to roles

### Current Roles
- `admin` - Full access to everything
- `instructor` - Can create and manage own lessons
- `student` - Can view own lessons
- `parent` - Can view child's lessons

---

## Authorization Flow for Lesson Editing

### Problem Identified:
When an **instructor user (ID=2)** tries to edit a **lesson**, the `LessonPolicy.update()` fails with 403 authorization error.

### Data Chain:
```
User (id=2, user_type='instructor', role='instructor')
  ↓ hasOne
Instructor (id=X, user_id=2)
  ↓ hasMany
Lesson (id=Y, instructor_id=X, student_id=Z)
```

### Authorization Check:
```php
public function update(User $user, Lesson $lesson)
{
    $instructorId = $user->instructor?->id;  // Should get Instructor.id
    return $lesson->instructor_id === $instructorId;  // Compare with Lesson.instructor_id
}
```

---

## Potential Issues

1. **Missing Instructor Record:** 
   - User might not have an associated Instructor record in the `instructors` table
   - Solution: Create an Instructor record when creating an instructor user

2. **ID Mismatch:**
   - Lesson.instructor_id might store User.id instead of Instructor.id
   - Solution: Verify that Lesson.instructor_id stores Instructor.id

3. **Role Not Assigned:**
   - User might have user_type='instructor' but not the 'instructor' role in Spatie
   - Solution: Ensure role is assigned in model_has_roles table

---

## Quick Fix: Allow Any Instructor

If you want to allow **any instructor to edit any lesson**, modify LessonPolicy.update():

```php
public function update(User $user, Lesson $lesson)
{
    if ($user->hasRole('admin')) {
        return true;  // Already handled by before()
    }

    // Allow any instructor to edit any lesson
    if ($user->hasRole('instructor')) {
        return true;
    }

    return false;
}
```

This removes the ownership check and allows all instructors to edit all lessons.

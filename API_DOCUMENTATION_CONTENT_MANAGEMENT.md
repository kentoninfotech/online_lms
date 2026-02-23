# Course Content Management API Documentation

## Table of Contents
1. [Routes](#routes)
2. [Controllers](#controllers)
3. [Models](#models)
4. [Request/Response](#request-response)
5. [Validation Rules](#validation-rules)
6. [Database Schema](#database-schema)
7. [File Storage](#file-storage)
8. [Error Handling](#error-handling)

---

## Routes

### API Endpoints (Web Routes)

All routes are within the admin middleware guard. Prefix: `/admin`

#### Index - List Course Contents
```
GET /courses/{course}/content
Name: course-contents.index
Controller: CourseContentController@adminIndex
Params: course (Course model)
Returns: View with $contents collection
Authorization: isAdmin gate
```

#### Create Form
```
GET /courses/{course}/content/create
Name: course-contents.create
Controller: CourseContentController@adminCreate
Params: course (Course model)
Returns: View with create form + $courseContents collection
Authorization: isAdmin gate
```

#### Store (Create)
```
POST /courses/{course}/content
Name: course-contents.store
Controller: CourseContentController@adminStore
Params: course (Course model)
Body: FormRequest (see validation section)
Returns: Redirect to index with success message
Authorization: isAdmin gate
```

#### Show (View Details)
```
GET /courses/{course}/content/{content}
Name: course-contents.show
Controller: CourseContentController@adminShow
Params: course, content (CourseContent model)
Returns: View with content details
Authorization: isAdmin gate
```

#### Edit Form
```
GET /courses/{course}/content/{content}/edit
Name: course-contents.edit
Controller: CourseContentController@adminEdit
Params: course, content
Returns: View with edit form + $courseContents collection
Authorization: isAdmin gate
```

#### Update
```
PUT /courses/{course}/content/{content}
Name: course-contents.update
Controller: CourseContentController@adminUpdate
Params: course, content
Body: FormRequest (see validation section)
Returns: Redirect to index with success message
Authorization: isAdmin gate
```

#### Delete
```
DELETE /courses/{course}/content/{content}
Name: course-contents.destroy
Controller: CourseContentController@adminDestroy
Params: course, content
Returns: Redirect to index with success message
Authorization: isAdmin gate
```

---

## Controllers

### CourseContentController

Location: `app/Http/Controllers/CourseContentController.php`

#### Public Methods (Student Facing)

```php
public function index(Course $course)
```
- **Purpose:** Show course learning hub to enrolled student
- **Auth:** User must be enrolled in course
- **Returns:** View courses.learn.index
- **Variables:** $course, $courseContents, $enrollment

```php
public function show(Course $course, CourseContent $content)
```
- **Purpose:** Display content for student learning
- **Auth:** User enrolled + content published + timeline OK + prerequisites met
- **Returns:** View courses.learn.show
- **Tracking:** Records started_at, increments time_spent_minutes
- **Variables:** $course, $content, $completion

```php
public function markComplete(Request $request, Course $course, CourseContent $content)
```
- **Purpose:** Mark content as completed by student
- **Auth:** User enrolled + student role
- **Validation:** Must meet min_reading_time_minutes
- **Returns:** JSON response
- **Side Effects:** Creates/updates CourseContentCompletion record

#### Admin Methods

```php
public function adminIndex(Course $course)
```
- **Purpose:** List all content in course (admin/tutor)
- **Auth:** isAdmin gate
- **Query:** `$course->contents()->get()`
- **Returns:** View admin.course-contents.index
- **Variables:** $course, $contents

```php
public function adminCreate(Course $course)
```
- **Purpose:** Show create form
- **Auth:** isAdmin gate
- **Query:** Gets other course contents for prerequisites
- **Returns:** View admin.course-contents.create
- **Variables:** $course, $courseContents

```php
public function adminStore(Request $request, Course $course)
```
- **Purpose:** Save new content
- **Auth:** isAdmin gate
- **Validation:** fullForm validation (see section below)
- **File Handling:** Uploads to storage/course-contents/{content_type}/
- **Returns:** Redirect with success message
- **Creates:** New CourseContent record
- **Side Effects:** Stores file if provided

```php
public function adminEdit(Course $course, CourseContent $content)
```
- **Purpose:** Show edit form
- **Auth:** isAdmin gate
- **Query:** Gets courses contents excluding current (for prerequisites)
- **Returns:** View admin.course-contents.edit
- **Variables:** $course, $content, $courseContents

```php
public function adminUpdate(Request $request, Course $course, CourseContent $content)
```
- **Purpose:** Update existing content
- **Auth:** isAdmin gate
- **Validation:** fullForm validation
- **File Handling:** Deletes old file, stores new one if provided
- **Returns:** Redirect with success message
- **Updates:** CourseContent record
- **Side Effects:** File replacement, old file deleted

```php
public function adminDestroy(Course $course, CourseContent $content)
```
- **Purpose:** Delete content
- **Auth:** isAdmin gate
- **File Handling:** Deletes associated file if exists
- **Returns:** Redirect with success message
- **Deletes:** CourseContent record (soft delete if enabled)
- **Side Effects:** Removes from storage, cascades to habits if FK exists

---

## Models

### CourseContent Model

Location: `app/Models/CourseContent.php`

#### Properties

```php
protected $table = 'course_contents';

protected $fillable = [
    'course_id',                    // Foreign key to Course
    'title',                       // Content title
    'description',                 // Content description
    'content_type',               // text|pdf|word|powerpoint|excel|image|video|link
    'content',                    // Raw HTML/text content
    'file_path',                  // Path to uploaded file
    'duration_minutes',           // Estimated duration
    'sequence',                   // Display order
    'section_id',                 // Future: Section grouping
    'is_published',               // Boolean: visible to students
    'is_required',                // Boolean: must complete
    'available_from',             // DateTime: when content available
    'available_until',            // DateTime: when content expires
    'prerequisite_content_id',    // FK to another CourseContent
    'min_reading_time_minutes',   // Minimum engagement time
    'embed_type',                 // default|iframe|popup|fullscreen|modal
    'allow_download',             // Boolean: allow student downloads
    'track_viewing',              // Boolean: track viewing time
];

protected $casts = [
    'is_published' => 'boolean',
    'is_required' => 'boolean',
    'allow_download' => 'boolean',
    'track_viewing' => 'boolean',
    'available_from' => 'datetime',
    'available_until' => 'datetime',
];
```

#### Relationships

```php
public function course(): BelongsTo
```
- Returns the course this content belongs to
- Usage: `$content->course()->first()`

```php
public function completions(): HasMany
```
- Returns all courseContentCompletion records
- Usage: `$content->completions()->get()`

#### Computed Properties

```php
public function getCompletionCount(): int
```
- Returns count of students who completed
- Query: Counts completions where is_completed = true

---

## Request/Response

### Store/Update Request

#### Request Headers
```
Content-Type: multipart/form-data
Accept: application/json OR text/html (for redirect)
X-CSRF-TOKEN: (auto in forms)
```

#### Request Body (Form Data)

```php
$request->validate([
    'title' => 'required|string|max:255',
    'description' => 'nullable|string',
    'content_type' => 'required|in:text,pdf,excel,word,powerpoint,video,link,image',
    'content' => 'nullable|string',
    'file' => 'nullable|file',
    'duration_minutes' => 'nullable|integer|min:1',
    'sequence' => 'required|integer|min:0',
    'is_published' => 'boolean',
    'is_required' => 'boolean',
    'available_from' => 'nullable|date_format:Y-m-d\TH:i',
    'available_until' => 'nullable|date_format:Y-m-d\TH:i|after:available_from',
    'prerequisite_content_id' => 'nullable|exists:course_contents,id',
    'min_reading_time_minutes' => 'nullable|integer|min:0',
    'embed_type' => 'required|in:default,iframe,popup,fullscreen,modal',
    'allow_download' => 'boolean',
    'track_viewing' => 'boolean',
]);
```

#### Response on Success

```php
redirect()->route('course-contents.index', $course)
    ->with('success', 'Content created successfully');
```

HTTP Status: 302 (Redirect)  
Session Flash: success message

#### Response on Failure

```php
// Validation errors
redirect()->back()
    ->withErrors($validator)
    ->withInput();
```

HTTP Status: 302 (Redirect back)  
Session: Error messages + old input

---

## Validation Rules

### Field-by-Field Validation

#### title
- **Rule:** required|string|max:255
- **Meaning:** Must provide, max 255 characters
- **Error:** "The title field is required" or "Max 255 characters"

#### description
- **Rule:** nullable|string
- **Meaning:** Optional, any string
- **Error:** None (nullable)

#### content_type
- **Rule:** required|in:text,pdf,excel,word,powerpoint,video,link,image
- **Meaning:** Must select one of 8 types
- **Error:** "The content type field is required" or "Invalid type selected"

#### content
- **Rule:** nullable|string
- **Meaning:** Optional (only for text type)
- **Error:** None

#### file
- **Rule:** nullable|file
- **Meaning:** Optional file upload
- **Error:** "The file must be a file" (if provided but invalid)

#### duration_minutes
- **Rule:** nullable|integer|min:1
- **Meaning:** Optional, if provided must be >= 1
- **Error:** "Must be at least 1" if provided but 0

#### sequence
- **Rule:** required|integer|min:0
- **Meaning:** Must provide, integer >= 0
- **Error:** "The sequence field is required"

#### is_published
- **Rule:** boolean
- **Meaning:** 0 or 1, optional
- **Error:** "The is published field must be boolean"

#### is_required
- **Rule:** boolean
- **Meaning:** 0 or 1, optional
- **Error:** "The is required field must be boolean"

#### available_from
- **Rule:** nullable|date_format:Y-m-d\TH:i
- **Meaning:** Optional, must be datetime format
- **Error:** "Invalid date format" if provided wrong format

#### available_until
- **Rule:** nullable|date_format:Y-m-d\TH:i|after:available_from
- **Meaning:** Optional, must be AFTER available_from
- **Error:** "Available until must be after available from"

#### prerequisite_content_id
- **Rule:** nullable|exists:course_contents,id
- **Meaning:** Optional, must reference existing content
- **Error:** "The selected prerequisite is invalid"

#### min_reading_time_minutes
- **Rule:** nullable|integer|min:0
- **Meaning:** Optional, if provided >= 0 (0 = no requirement)
- **Error:** "Must be at least 0"

#### embed_type
- **Rule:** required|in:default,iframe,popup,fullscreen,modal
- **Meaning:** Must select one of 5 display modes
- **Error:** "The embed type field is required"

#### allow_download
- **Rule:** boolean
- **Meaning:** 0 or 1, optional
- **Error:** "The allow download field must be boolean"

#### track_viewing
- **Rule:** boolean
- **Meaning:** 0 or 1, optional
- **Error:** "The track viewing field must be boolean"

---

## Database Schema

### course_contents Table

```sql
CREATE TABLE `course_contents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext,
  `content_type` enum('text','pdf','excel','word','powerpoint','video','link','image') NOT NULL,
  `content` longtext,
  `file_path` varchar(255),
  `duration_minutes` int(11),
  `sequence` int(11) NOT NULL DEFAULT 0,
  `section_id` bigint(20) unsigned,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `available_from` datetime NULL,
  `available_until` datetime NULL,
  `prerequisite_content_id` bigint(20) unsigned NULL,
  `min_reading_time_minutes` int(11) NOT NULL DEFAULT 0,
  `embed_type` varchar(255) NOT NULL DEFAULT 'default',
  `allow_download` tinyint(1) NOT NULL DEFAULT 1,
  `track_viewing` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`prerequisite_content_id`) REFERENCES `course_contents` (`id`) ON DELETE SET NULL,
  INDEX `idx_course_id` (`course_id`),
  INDEX `idx_sequence` (`sequence`),
  INDEX `idx_is_published` (`is_published`),
  INDEX `idx_prerequisite` (`prerequisite_content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Key Constraints

- **Foreign Key:** `course_id` → `courses.id` (CASCADE DELETE)
- **Foreign Key:** `prerequisite_content_id` → `course_contents.id` (SET NULL)
- **Unique:** None (multiple contents per course allowed)
- **Indexes:** course_id, sequence, is_published, prerequisite_content_id

---

## File Storage

### Storage Location

Files uploaded to: `storage/app/public/course-contents/{content_type}/`

Example paths:
- `storage/app/public/course-contents/pdf/lecture1.pdf`
- `storage/app/public/course-contents/video/intro.mp4`
- `storage/app/public/course-contents/word/assignment.docx`

### Public URL

Files accessible at: `public/storage/course-contents/{content_type}/{filename}`

Full URL: `https://yourdomain.com/storage/course-contents/pdf/lecture1.pdf`

### File Handling Logic

#### Upload
```php
if ($request->hasFile('file')) {
    $path = $request->file('file')->store(
        'course-contents',
        'public'
    );
    $validated['file_path'] = $path;
}
```

#### Update (with replacement)
```php
if ($request->hasFile('file')) {
    if ($content->file_path) {
        Storage::disk('public')->delete($content->file_path);
    }
    $path = $request->file('file')->store(
        'course-contents',
        'public'
    );
    $validated['file_path'] = $path;
}
```

#### Delete
```php
if ($content->file_path) {
    Storage::disk('public')->delete($content->file_path);
}
```

### Disk Configuration

Uses Laravel's `public` disk:
- Location: `storage/app/public`
- Symlink: `public/storage` → `storage/app/public`
- Access: Public (no authentication needed to download)

---

## Error Handling

### Controller Error Handling

```php
try {
    $validated = $request->validate([...]);
    // Process request
} catch (\Illuminate\Validation\ValidationException $e) {
    return redirect()->back()
        ->withErrors($e->errors())
        ->withInput();
}
```

### Authorization Errors

```php
// If not admin
$this->authorize('isAdmin');
// Throws: AuthorizationException (403 Forbidden)
```

### Model Not Found

```php
// If content doesn't exist
$content = CourseContent::findOrFail($id);
// Throws: ModelNotFoundException (404 Not Found)
```

### File Upload Errors

```php
// If file is invalid
$request->validate(['file' => 'nullable|file']);
// If file too large OR invalid type
// Then: ValidationException with "file" error
```

### Database Errors

```php
// Foreign key violation
$content->prerequisite_content_id = 999; // Non-existent ID
$content->save();
// Throws: ForeignKeyConstraintException (SQLSTATE)
```

---

## Usage Examples

### Creating Content via Controller

```php
$input = [
    'title' => 'Laravel Basics',
    'description' => 'Learn Laravel fundamentals',
    'content_type' => 'video',
    'sequence' => 1,
    'is_published' => true,
    'is_required' => true,
    'embed_type' => 'default',
    'available_from' => '2026-03-01 09:00',
    'available_until' => '2026-03-31 17:00',
    'min_reading_time_minutes' => 45,
    'track_viewing' => true,
    'allow_download' => true,
];

$content = CourseContent::create([
    'course_id' => $course->id,
    ...$input
]);
```

### Querying Content

```php
// Get all published content
$published = CourseContent::where('is_published', true)->get();

// Get required content only
$required = CourseContent::where('is_required', true)->get();

// Get content for a course ordered by sequence
$contents = $course->contents()
    ->orderBy('sequence')
    ->get();

// Get content within availability window
$available = CourseContent::where('is_published', true)
    ->where(function($q) {
        $q->whereNull('available_from')
            ->orWhere('available_from', '<=', now());
    })
    ->where(function($q) {
        $q->whereNull('available_until')
            ->orWhere('available_until', '>=', now());
    })
    ->get();
```

### Checking Prerequisites

```php
$content = CourseContent::find(5);

if ($content->prerequisite_content_id) {
    $prerequisite = $content->prerequisite;
    // Check if student completed prerequisite
    $completed = CourseContentCompletion::where('user_id', auth()->id())
        ->where('course_content_id', $prerequisite->id)
        ->where('is_completed', true)
        ->exists();
}
```

---

## Performance Considerations

### Query Optimization

```php
// Bad: N+1 queries
$contents = CourseContent::all();
foreach ($contents as $content) {
    echo $content->course->title; // Extra query per item
}

// Good: Eager loading
$contents = CourseContent::with('course')->get();
foreach ($contents as $content) {
    echo $content->course->title; // No extra queries
}
```

### Indexing

Recommended database indexes:
- `idx_course_id` - Filter by course
- `idx_is_published` - Filter published status
- `idx_sequence` - Sort by order
- `idx_prerequisite` - Find prerequisites

---

## Future API Endpoints

Planned but not yet implemented:

```
PATCH /courses/{course}/content/{content}    - Partial update
POST /courses/{course}/content/{content}/publish    - Publish
POST /courses/{course}/content/{content}/unpublish  - Unpublish
POST /courses/{course}/content/{content}/duplicate  - Clone content
GET /courses/{course}/content/search          - Search content
POST /courses/{course}/content/reorder         - Bulk reorder
```

---

## Migration

### Rollback Previous Version

```bash
php artisan migrate:rollback --step=1
```

This removes the timeline/prerequisite columns and reverts to the original schema.

---

**API Version:** 1.0  
**Last Updated:** February 22, 2026  
**Framework:** Laravel 12.25.0  
**PHP:** 8.4.16+

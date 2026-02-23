# Developer's Guide
## Online LMS Platform - Development & Contribution

**Version:** 1.0  
**Last Updated:** February 23, 2026  
**Target Audience:** Backend & Frontend Developers

---

## Table of Contents
1. [Environment Setup](#environment-setup)
2. [Project Structure](#project-structure)
3. [Development Workflow](#development-workflow)
4. [Code Standards](#code-standards)
5. [Database Management](#database-management)
6. [API Development](#api-development)
7. [Frontend Development](#frontend-development)
8. [Testing](#testing)
9. [Debugging](#debugging)
10. [Common Tasks](#common-tasks)

---

## Environment Setup

### Local Development Environment

#### Prerequisites
- PHP 8.2 or higher
- MySQL 8.0 or higher
- Node.js 18+ & npm
- Git
- Composer
- Redis (for caching/queue)

#### Installation Steps

1. **Clone Repository**
```bash
git clone <repository-url> online_lms
cd online_lms
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure Database**
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=online_lms
DB_USERNAME=root
DB_PASSWORD=
```

5. **Create Database**
```bash
mysql -u root -p
CREATE DATABASE online_lms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

6. **Run Migrations**
```bash
php artisan migrate
```

7. **Seed Database (Optional)**
```bash
php artisan db:seed
```

8. **Create Storage Links**
```bash
php artisan storage:link
```

9. **Build Assets**
```bash
npm run dev
```

10. **Start Development Server**
```bash
php artisan serve
```

Access application at `http://localhost:8000`

### IDE Configuration

**VS Code Extensions (Recommended)**
```
- Laravel Extension Pack (austenc.laravel-extension-pack)
- PHP Intelephense (bmewburn.vscode-intelephense-client)
- Blade Formatter (shufo.vscode-blade-formatter)
- Thunder Client (rangav.vscode-thunder-client)
- Prettier (esbenp.prettier-vscode)
```

**PHPStorm Extensions**
```
- Laravel Plugin
- Database Tools
- RESTful Client
```

---

## Project Structure

### Application Structure Overview

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── CourseController.php
│   │   │   ├── UserController.php
│   │   │   ├── QuizController.php
│   │   │   ├── HomepageSettingController.php
│   │   │   ├── SiteBuilderController.php
│   │   │   └── ...
│   │   ├── Instructor/
│   │   │   ├── CourseController.php
│   │   │   ├── QuizController.php
│   │   │   ├── LiveSessionController.php
│   │   │   └── ...
│   │   ├── Student/
│   │   │   ├── CourseController.php
│   │   │   ├── EnrollmentController.php
│   │   │   ├── QuizController.php
│   │   │   └── ...
│   │   └── Api/
│   │       ├── CourseController.php
│   │       ├── QuizController.php
│   │       └── ...
│   ├── Middleware/
│   │   ├── CheckRole.php
│   │   ├── AdminOnly.php
│   │   ├── InstructorOrAdmin.php
│   │   └── ...
│   └── Requests/
│       ├── StoreCourseRequest.php
│       ├── UpdateCourseRequest.php
│       └── ...
│
├── Models/
│   ├── User.php
│   ├── Course.php
│   ├── CourseContent.php
│   ├── CourseQuiz.php
│   ├── QuizQuestion.php
│   ├── QuizSubmission.php
│   ├── CourseEnrollment.php
│   ├── HomepageSetting.php
│   └── ...
│
├── Services/
│   ├── CourseService.php
│   ├── EnrollmentService.php
│   ├── QuizService.php
│   ├── CertificateService.php
│   └── ...
│
├── Jobs/
│   ├── ProcessQuizSubmission.php
│   ├── SendEnrollmentNotification.php
│   ├── GenerateCertificate.php
│   └── ...
│
├── Mail/
│   ├── EnrollmentConfirmation.php
│   ├── QuizResultNotification.php
│   └── ...
│
├── Notifications/
│   ├── CoursePublished.php
│   ├── AssignmentSubmitted.php
│   └── ...
│
└── Traits/
    ├── HasImages.php
    ├── HasMetadata.php
    └── ...
```

### Controllers Naming Convention

```
- Create: POST -> store()
- Read: GET -> index(), show()
- Update: PUT/PATCH -> update()
- Delete: DELETE -> destroy()

Example:
Route::resource('courses', CourseController::class);
- GET /courses → index()
- GET /courses/create → create()
- POST /courses → store()
- GET /courses/{id} → show()
- GET /courses/{id}/edit → edit()
- PUT/PATCH /courses/{id} → update()
- DELETE /courses/{id} → destroy()
```

---

## Development Workflow

### Git Workflow

1. **Create Feature Branch**
```bash
git checkout -b feature/course-search
# or
git checkout -b bugfix/quiz-timer-issue
# or
git checkout -b refactor/auth-system
```

2. **Make Changes & Commit**
```bash
git add .
git commit -m "Feat: Add course search functionality with filters"
```

**Commit Message Format:**
```
<type>: <subject>

<body>

<footer>
```

Types: `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`, `chore`

3. **Push & Create Pull Request**
```bash
git push origin feature/course-search
```

4. **Code Review & Merge**
- At least 1 approval required
- CI/CD tests must pass
- Merge to develop branch

5. **Merge to Main**
```bash
git checkout main
git pull origin main
git merge develop
git push origin main
```

### Branch Strategy

```
main (production)
└── develop (staging)
    ├── feature/user-dashboard
    ├── feature/course-import
    ├── bugfix/quiz-calculation
    └── refactor/auth-system
```

---

## Code Standards

### PHP Code Standards

#### PSR-12 Compliance

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index()
    {
        $courses = Course::paginate(15);
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created course in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:course_categories,id',
            'price' => 'nullable|numeric|min:0',
        ]);

        $course = Course::create($validated);

        return redirect()
            ->route('admin.courses.show', $course)
            ->with('success', 'Course created successfully');
    }
}
```

#### Naming Conventions

```
Classes: PascalCase
CourseController, CourseService, User

Functions/Methods: camelCase
storeCourseMaterial(), updateQuizQuestion()

Variables: camelCase
$courseId, $enrolledStudents

Constants: UPPER_SNAKE_CASE
COURSE_STATUS_ACTIVE, MAX_QUIZ_ATTEMPTS

Database Tables: snake_case (plural)
courses, course_enrollments, quiz_submissions

Database Columns: snake_case
course_id, created_at, is_published

Model Properties: snake_case (public $)
public $fillable = ['title', 'description']
public $casts = ['is_published' => 'boolean']
```

#### Code Style Rules

```php
// Line Length: Max 120 characters
// Indentation: 4 spaces (not tabs)
// Opening braces on same line

class CourseController extends Controller {
    public function store(Request $request)
    {
        // Logic here
    }
}

// Type Hints: Always use type hints
public function store(StoreCourseRequest $request): RedirectResponse

// Documentation: Always add docblocks
/**
 * Update the specified course.
 *
 * @param  Course  $course
 * @param  StoreCourseRequest  $request
 * @return RedirectResponse
 */
public function update(Course $course, StoreCourseRequest $request): RedirectResponse
{
    // Implementation
}
```

### Blade Template Standards

```blade
{{-- Use {{-- --}} for comments --}}

{{-- Single variable output --}}
{{ $course->title }}

{{-- Escaped output (default) --}}
{{ htmlspecialchars($variable) }}

{{-- Raw output (use sparingly) --}}
{!! $variable !!}

{{-- Conditional rendering --}}
@if ($course->is_published)
    <span class="badge badge-success">Published</span>
@else
    <span class="badge badge-warning">Draft</span>
@endif

{{-- Loop rendering --}}
@foreach ($courses as $course)
    <div class="course-card">
        <h3>{{ $course->title }}</h3>
        <p>{{ $course->description }}</p>
    </div>
@endforeach

{{-- Reusable components --}}
<x-course-card :course="$course" />

{{-- Form handling --}}
<form action="{{ route('courses.store') }}" method="POST">
    @csrf
    <input type="text" name="title" required>
    @error('title')
        <span class="error">{{ $message }}</span>
    @enderror
</form>
```

---

## Database Management

### Creating Migrations

```bash
# Create migration
php artisan make:migration create_courses_table

# Create migration with model
php artisan make:migration create_courses_table --create=courses

# Create migration with table modification
php artisan make:migration add_status_to_courses --table=courses
```

### Migration Template

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('course_categories')
                ->onDelete('set null');
            
            $table->string('title');
            $table->longText('description');
            $table->enum('level', ['Local', 'International', 'Diploma']);
            $table->decimal('price', 10, 2)->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('is_published')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('instructor_id');
            $table->index('category_id');
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
```

### Creating Models

```bash
# Create model
php artisan make:model Course

# Create model with controller
php artisan make:model Course --controller

# Create model with migration
php artisan make:model Course --migration

# Full setup
php artisan make:model Course -mcs
# -m = migration
# -c = controller
# -s = seeder
```

### Model Example

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'instructor_id',
        'category_id',
        'title',
        'description',
        'level',
        'price',
        'featured_image',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(CourseContent::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(CourseQuiz::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    // Accessors & Mutators
    public function getImagePathAttribute()
    {
        return asset("storage/{$this->featured_image}");
    }

    // Methods
    public function enrollStudent($userId)
    {
        return $this->enrollments()->create([
            'user_id' => $userId,
            'enrolled_at' => now(),
        ]);
    }

    public function getEnrolledCount()
    {
        return $this->enrollments()->count();
    }
}
```

---

## API Development

### RESTful API Guidelines

#### Endpoint Structure

```php
// routes/api.php

Route::prefix('api/v1')->group(function () {
    // Public routes
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{id}', [CourseController::class, 'show']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('enrollments', [EnrollmentController::class, 'store']);
        Route::get('enrollments', [EnrollmentController::class, 'index']);
        
        // Admin only
        Route::middleware('role:admin')->group(function () {
            Route::post('courses', [CourseController::class, 'store']);
            Route::put('courses/{id}', [CourseController::class, 'update']);
            Route::delete('courses/{id}', [CourseController::class, 'destroy']);
        });
    });
});
```

#### API Response Format

```php
// Success Response
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'instructor' => $this->instructor->name,
            'enrollments' => $this->enrollments_count,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }

    public function with($request)
    {
        return [
            'meta' => [
                'api_version' => '1.0',
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }
}

// Controller Usage
public function show(Course $course)
{
    return new CourseResource($course);
}
```

#### Request Validation

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->can('create', Course::class);
    }

    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'category_id' => ['required', 'exists:course_categories,id'],
            'level' => ['required', 'in:Local,International,Diploma'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Course title is required',
            'description.min' => 'Description must be at least 10 characters',
        ];
    }
}
```

---

## Frontend Development

### Vue.js Component Example

```vue
<template>
  <div class="course-list">
    <div v-if="loading" class="spinner">Loading...</div>
    
    <div v-else-if="courses.length > 0" class="courses-grid">
      <CourseCard 
        v-for="course in courses" 
        :key="course.id"
        :course="course"
        @enroll="enrollCourse"
      />
    </div>
    
    <div v-else class="empty-state">
      <p>No courses available</p>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import CourseCard from './CourseCard.vue';
import { getCourses } from '@/api/courses';

export default {
  name: 'CourseList',
  components: {
    CourseCard,
  },
  setup() {
    const courses = ref([]);
    const loading = ref(true);

    onMounted(async () => {
      try {
        const response = await getCourses();
        courses.value = response.data;
      } catch (error) {
        console.error('Error loading courses:', error);
      } finally {
        loading.value = false;
      }
    });

    const enrollCourse = async (courseId) => {
      // Handle enrollment
    };

    return {
      courses,
      loading,
      enrollCourse,
    };
  },
};
</script>

<style scoped>
.courses-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #666;
}
</style>
```

### Blade Component Example

```blade
<!-- resources/views/components/course-card.blade.php -->
<div class="course-card">
    <img src="{{ $course->image_path }}" alt="{{ $course->title }}" class="course-image">
    <div class="course-body">
        <h3>{{ $course->title }}</h3>
        <p class="description">{{ Str::limit($course->description, 100) }}</p>
        <div class="course-meta">
            <span class="instructor">{{ $course->instructor->name }}</span>
            <span class="level">{{ $course->level }}</span>
        </div>
        @if ($course->price)
            <p class="price">${{ number_format($course->price, 2) }}</p>
        @else
            <p class="price">Free</p>
        @endif
        <a href="{{ route('courses.show', $course) }}" class="btn btn-primary">
            View Course
        </a>
    </div>
</div>

<!-- Usage -->
<x-course-card :course="$course" />
```

---

## Testing

### Unit Testing Example

```php
<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a course can be created.
     */
    public function test_can_create_course()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        
        $course = Course::factory()->create([
            'instructor_id' => $instructor->id,
        ]);

        $this->assertNotNull($course->id);
        $this->assertEquals('instructor', $course->instructor->role);
    }

    /**
     * Test course relationships.
     */
    public function test_course_has_owner()
    {
        $course = Course::factory()->hasOwner()->create();
        
        $this->assertNotNull($course->instructor);
        $this->assertInstanceOf(User::class, $course->instructor);
    }
}
```

### Feature Testing Example

```php
<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test viewing course list.
     */
    public function test_can_view_courses()
    {
        $courses = Course::factory(3)->create();

        $response = $this->get('/api/courses');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    /**
     * Test creating a course requires authentication.
     */
    public function test_cannot_create_course_unauthenticated()
    {
        $response = $this->post('/api/courses');

        $response->assertStatus(401);
    }

    /**
     * Test authenticated user can create course.
     */
    public function test_can_create_course_authenticated()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);

        $response = $this->actingAs($instructor)
                         ->post('/api/courses', [
                             'title' => 'New Course',
                             'description' => 'Course description',
                             'category_id' => 1,
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('courses', [
            'title' => 'New Course',
        ]);
    }
}
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/CourseTest.php

# Run specific test method
php artisan test --filter=test_can_create_course

# Run with coverage
php artisan test --coverage

# Run tests in parallel
php artisan test --parallel
```

---

## Debugging

### Debug Tools

#### Laravel Debugbar
```php
// Install
composer require --dev barryvdh/laravel-debugbar

// Usage
// Automatically enabled in development
// Access panel in bottom right of page
```

#### Tinker REPL
```bash
php artisan tinker

# In Tinker
>>> $course = App\Models\Course::find(1);
>>> $course->enrollments()->count();
=> 45
>>> $course->instructor->name;
=> "John Doe"
```

#### VarDump Server
```bash
# Terminal 1
php artisan dump-server

# Terminal 2 - In your code
dump($variable);
// See output in Terminal 1
```

### Debugging Techniques

#### Logging
```php
use Illuminate\Support\Facades\Log;

Log::debug('Course created', ['course_id' => $course->id]);
Log::warning('Low stock warning', ['product' => $product]);
Log::error('Database error', $exception);

// View logs
tail -f storage/logs/laravel.log
```

#### Breakpoints (IDE)
```php
// In PHPStorm: Click line number to set breakpoint
// Press Ctrl+D to debug
// Step over (F10), Step into (F11), Resume (F8)
```

---

## Common Tasks

### Adding a New Feature

```bash
# 1. Create migration
php artisan make:migration create_feature_table

# 2. Create model
php artisan make:model Feature

# 3. Create controller
php artisan make:controller FeatureController

# 4. Create request class
php artisan make:request StoreFeatureRequest

# 5. Define routes
# routes/web.php
Route::resource('features', FeatureController::class);

# 6. Create views/components
# resources/views/features/...

# 7. Write tests
php artisan make:test FeatureTest --unit
php artisan make:test FeatureTest --feature

# 8. Test your implementation
php artisan test
```

### Database Rollback & Reset

```bash
# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Refresh database (reset + migrate)
php artisan migrate:refresh

# Refresh with seeding
php artisan migrate:refresh --seed

# Reset to fresh state
php artisan migrate:fresh
```

### Clearing Cache

```bash
# Clear all cache
php artisan cache:clear

# Clear specific cache
php artisan cache:forget key_name

# Clear route cache
php artisan route:clear

# Clear config cache
php artisan config:clear

# Clear view cache
php artisan view:clear

# Clear all
php artisan optimize:clear
```

### Asset Building

```bash
# Development (with watch)
npm run dev

# Production build
npm run build

# Watch for changes
npm run watch
```

---

## Performance Optimization

### Query Optimization

```php
// Bad: N+1 query problem
$courses = Course::all();
foreach ($courses as $course) {
    echo $course->instructor->name; // Query per course
}

// Good: Eager loading
$courses = Course::with('instructor')->get();
foreach ($courses as $course) {
    echo $course->instructor->name; // No additional queries
}

// Chunking for large datasets
Course::query()->chunk(500, function ($courses) {
    foreach ($courses as $course) {
        // Process...
    }
});
```

### Caching

```php
// Cache query results
$courses = Cache::remember('courses', 3600, function () {
    return Course::with('instructor')->get();
});

// Cache specific data
Cache::put('featured_courses', $courses, 3600);

// Invalidate cache
Cache::forget('courses');
Cache::flush(); // Flush all
```

---

## Useful Commands

```bash
# Server & Development
php artisan serve                      # Start development server
php artisan tinker                     # Interactive shell

# Migrations
php artisan migrate                    # Run migrations
php artisan migrate:rollback           # Undo last migration
php artisan migrate:fresh              # Fresh migration

# Models & Factories
php artisan make:model Course          # Create model
php artisan make:factory CourseFactory # Create factory
php artisan make:seeder CourseSeeder   # Create seeder

# Controllers & Requests
php artisan make:controller CourseController  # Create controller
php artisan make:request StoreCourseRequest   # Create request class

# Testing
php artisan test                       # Run tests
php artisan test --filter=test_name    # Run specific test

# Cache & Views
php artisan cache:clear                # Clear cache
php artisan view:clear                 # Clear compiled views
php artisan config:clear               # Clear config cache

# Queue & Jobs
php artisan queue:work                 # Start queue worker
php artisan queue:failed               # View failed jobs
php artisan queue:retry all            # Retry failed jobs
```

---

## Useful Resources

### Laravel Documentation
- [Laravel Official Docs](https://laravel.com/docs)
- [Laravel API Documentation](https://laravel.com/api/11.x)
- [Eloquent Documentation](https://laravel.com/docs/11.x/eloquent)

### Packages Used
- [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum) - API authentication
- [Laravel Passport](https://laravel.com/docs/11.x/passport) - OAuth 2.0
- [Laravel Horizon](https://laravel.com/docs/11.x/horizon) - Queue monitoring

### Community
- [Laravel Discord Community](https://discord.gg/laravel)
- [Laracasts Video Tutorials](https://laracasts.com)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel)

---

**Document Version:** 1.0  
**Last Updated:** February 23, 2026  
**Next Review Date:** June 23, 2026

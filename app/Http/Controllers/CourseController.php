<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseCarouselImage;
use App\Models\CourseEnrollee;
use App\Models\CourseDragAndDrop;
use App\Models\HomepageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display the landing page with featured courses only
     */
    public function index()
    {
        // Fetch carousel images from HomepageSetting (uploaded via /admin/carousel)
        $carouselImages = HomepageSetting::where('section', 'carousel')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $categories = CourseCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $featuredCourses = Course::where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch all homepage settings organized by section and convert to arrays for views
        $rawSettings = HomepageSetting::getAllSections();
        
        // Initialize all expected sections with empty arrays as defaults
        $homeSettings = [
            'hero' => [],
            'about' => [],
            'features' => [],
            'featured_courses' => [],
            'testimonials' => [],
            'stats' => [],
            'services' => [],
            'galleries' => [],
            'cta' => [],
            'contact' => [],
            'carousel' => [],
            'footer' => []
        ];
        
        // Populate with database values
        foreach ($rawSettings as $section => $settings) {
            $homeSettings[$section] = [];
            foreach ($settings as $key => $setting) {
                // Convert to associative array so views can use array notation: ['value']
                $homeSettings[$section][$key] = [
                    'value' => $setting->value,
                    'image_path' => $setting->image_path,
                    'button_text' => $setting->button_text,
                    'button_link' => $setting->button_link,
                    'title' => $setting->title,
                    'description' => $setting->description
                ];
            }
        }

        return view('courses.index', compact('carouselImages', 'categories', 'featuredCourses', 'homeSettings'));
    }

    /**
     * Display all courses page with category tabs
     */
    public function allCourses()
    {
        $categories = CourseCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $activeCategory = request('category');
        
        if ($activeCategory) {
            $category = CourseCategory::findOrFail($activeCategory);
            $courses = $category->activeCourses()
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        } else {
            $courses = Course::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        }

        return view('courses.all-courses', compact('categories', 'courses', 'activeCategory'));
    }

    /**
     * Show courses filtered by category
     */
    public function byCategory(CourseCategory $category)
    {
        $courses = $category->activeCourses()
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('courses.by-category', compact('category', 'courses'));
    }

    /**
     * Show courses by level and category
     */
    public function byLevelCategory($level, CourseCategory $category)
    {
        $courses = $category->activeCourses()
            ->where('level', $level)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('courses.by-level-category', compact('level', 'category', 'courses'));
    }

    /**
     * Show a single course
     */
    public function show(Course $course)
    {
        $course->load('category', 'facilitator', 'facilitators', 'courseDates.venues');
        
        $hasEnrolled = false;
        $enrollment = null;
        $enrollmentStatus = null;
        
        if (Auth::check()) {
            $enrollment = CourseEnrollee::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->first();
            
            if ($enrollment) {
                $hasEnrolled = true;
                $enrollmentStatus = $enrollment->status;
            }
        }

        $enrollmentCount = CourseEnrollee::where('course_id', $course->id)
            ->where('status', 'active')
            ->count();

        // Eager load dates and venues to prevent N+1 query
        $course->load('courseDates.venues');

        return view('courses.show', compact('course', 'hasEnrolled', 'enrollmentCount', 'enrollmentStatus', 'enrollment'));
    }

    /**
     * Admin: List all courses
     */
    public function adminIndex()
    {
        $this->authorize('isAdmin');

        $courses = Course::with('category', 'facilitator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Admin: Create course form
     */
    public function adminCreate()
    {
        $this->authorize('isAdmin');

        $categories = CourseCategory::where('is_active', true)->get();
        // Get only facilitators who are registered tutors (have user_type = 'instructor')
        $facilitators = \App\Models\Facilitator::whereHas('user', function ($query) {
            $query->where('user_type', 'instructor');
        })->where('is_active', true)->get();

        return view('admin.courses.create', compact('categories', 'facilitators'));
    }

    /**
     * Admin: Store a new course
     */
    public function adminStore(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'code' => 'required|string|unique:courses',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:course_categories,id',
            'facilitator_ids' => 'nullable|array',
            'facilitator_ids.*' => 'exists:facilitators,id',
            'fee' => 'required|numeric|min:0',
            'currency' => 'required|string|in:NGN,USD,GBP,EUR',
            'course_hours' => 'nullable|integer|min:1',
            'is_online' => 'boolean',
            'is_offline' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'featured_image' => 'nullable|image|max:2048',
            'max_enrollees' => 'nullable|integer|min:1',
            'course_dates' => 'nullable|array',
            'course_dates.*.start_date' => 'required|date',
            'course_dates.*.end_date' => 'required|date|after_or_equal:course_dates.*.start_date',
            'course_dates.*.date_label' => 'nullable|string|max:255',
            'course_dates.*.notes' => 'nullable|string',
            'course_dates.*.venues' => 'nullable|array',
            'course_dates.*.venues.*.venue_name' => 'required|string|max:255',
            'course_dates.*.venues.*.address' => 'nullable|string|max:255',
            'course_dates.*.venues.*.city' => 'nullable|string|max:100',
            'course_dates.*.venues.*.state' => 'nullable|string|max:100',
            'course_dates.*.venues.*.country' => 'nullable|string|max:100',
            'course_dates.*.venues.*.capacity' => 'nullable|integer|min:1',
            'course_dates.*.venues.*.notes' => 'nullable|string'
        ]);

        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            
            if (!$file->isValid()) {
                return back()->with('error', 'Uploaded file is not valid. Please try again with a different image.')
                    ->withInput();
            }
            
            try {
                // Create directory if it doesn't exist
                $uploadDir = public_path('uploads/courses');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Generate filename
                $filename = time() . '_' . $file->getClientOriginalName();
                
                // Move file to directory
                $file->move($uploadDir, $filename);
                
                // Store relative path for database
                $validated['featured_image'] = 'uploads/courses/' . $filename;
            } catch (\Exception $e) {
                \Log::error('Featured image upload failed: ' . $e->getMessage());
                return back()->with('error', 'Failed to upload featured image: ' . $e->getMessage())
                    ->withInput();
            }
        }

        // Extract facilitator_ids before creating course
        $facilitatorIds = $validated['facilitator_ids'] ?? [];
        unset($validated['facilitator_ids']);
        
        // Extract course_dates before creating course
        $courseDatesData = $validated['course_dates'] ?? [];
        unset($validated['course_dates']);
        
        // Remove featured_image from validated if temp file exists (will handle after)
        unset($validated['featured_image']);

        $course = Course::create($validated);

        // Handle featured image upload after course creation (to use course ID)
        if (isset($tempFile) && $tempFile) {
            try {
                // Create course-specific directory
                $courseUploadDir = public_path('uploads/courses/' . $course->id);
                if (!is_dir($courseUploadDir)) {
                    mkdir($courseUploadDir, 0755, true);
                }
                
                // Generate filename
                $filename = time() . '_' . $tempFile->getClientOriginalName();
                
                // Move file to course-specific directory
                $tempFile->move($courseUploadDir, $filename);
                
                // Update course with featured image path
                $course->update(['featured_image' => 'uploads/courses/' . $course->id . '/' . $filename]);
            } catch (\Exception $e) {
                \Log::error('Featured image upload failed for course ' . $course->id . ': ' . $e->getMessage());
                // Continue anyway, course is created without image
            }
        }

        // Attach facilitators to course
        if (!empty($facilitatorIds)) {
            $course->facilitators()->attach($facilitatorIds);
        }

        // Create course dates and venues
        if (!empty($courseDatesData)) {
            $dateSequence = 0;
            foreach ($courseDatesData as $dateData) {
                $courseDate = $course->courseDates()->create([
                    'start_date' => $dateData['start_date'],
                    'end_date' => $dateData['end_date'],
                    'date_label' => $dateData['date_label'] ?? null,
                    'notes' => $dateData['notes'] ?? null,
                    'sequence' => $dateSequence++
                ]);

                // Create venues for this date
                if (isset($dateData['venues']) && is_array($dateData['venues'])) {
                    $venueSequence = 0;
                    foreach ($dateData['venues'] as $venueData) {
                        $courseDate->venues()->create([
                            'venue_name' => $venueData['venue_name'],
                            'address' => $venueData['address'] ?? null,
                            'city' => $venueData['city'] ?? null,
                            'state' => $venueData['state'] ?? null,
                            'country' => $venueData['country'] ?? null,
                            'capacity' => $venueData['capacity'] ?? null,
                            'notes' => $venueData['notes'] ?? null,
                            'sequence' => $venueSequence++
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Course created successfully.');
    }

    /**
     * Admin: Edit course form
     */
    public function adminEdit(Course $course)
    {
        $this->authorize('isAdmin');

        $categories = CourseCategory::where('is_active', true)->get();
        // Get only facilitators who are registered tutors (have user_type = 'instructor')
        $facilitators = \App\Models\Facilitator::whereHas('user', function ($query) {
            $query->where('user_type', 'instructor');
        })->where('is_active', true)->get();
        
        // Load the course with its facilitators and course dates with venues
        $course->load('facilitators', 'courseDates.venues');

        return view('admin.courses.edit', compact('course', 'categories', 'facilitators'));
    }

    /**
     * Admin: Update course
     */
    public function adminUpdate(Request $request, Course $course)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'code' => 'required|string|unique:courses,code,' . $course->id,
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:course_categories,id',
            'facilitator_ids' => 'nullable|array',
            'facilitator_ids.*' => 'exists:facilitators,id',
            'fee' => 'required|numeric|min:0',
            'currency' => 'required|string|in:NGN,USD,GBP,EUR',
            'course_hours' => 'nullable|integer|min:1',
            'is_online' => 'boolean',
            'is_offline' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'featured_image' => 'nullable|image|max:2048',
            'max_enrollees' => 'nullable|integer|min:1',
            'course_dates' => 'nullable|array',
            'course_dates.*.start_date' => 'required|date',
            'course_dates.*.end_date' => 'required|date|after_or_equal:course_dates.*.start_date',
            'course_dates.*.date_label' => 'nullable|string|max:255',
            'course_dates.*.notes' => 'nullable|string',
            'course_dates.*.venues' => 'nullable|array',
            'course_dates.*.venues.*.venue_name' => 'required|string|max:255',
            'course_dates.*.venues.*.address' => 'nullable|string|max:255',
            'course_dates.*.venues.*.city' => 'nullable|string|max:100',
            'course_dates.*.venues.*.state' => 'nullable|string|max:100',
            'course_dates.*.venues.*.country' => 'nullable|string|max:100',
            'course_dates.*.venues.*.capacity' => 'nullable|integer|min:1',
            'course_dates.*.venues.*.notes' => 'nullable|string'
        ]);

        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            
            if (!$file->isValid()) {
                return back()->with('error', 'Uploaded file is not valid. Please try again with a different image.')
                    ->withInput();
            }
            
            try {
                // Create directory if it doesn't exist
                $uploadDir = public_path('uploads/courses');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Generate filename
                $filename = time() . '_' . $file->getClientOriginalName();
                
                // Move file to directory
                $file->move($uploadDir, $filename);
                
                // Store relative path for database
                $validated['featured_image'] = 'uploads/courses/' . $filename;
            } catch (\Exception $e) {
                \Log::error('Featured image upload failed: ' . $e->getMessage());
                return back()->with('error', 'Failed to upload featured image: ' . $e->getMessage())
                    ->withInput();
            }
        }

        // Extract facilitator_ids before updating course
        $facilitatorIds = $validated['facilitator_ids'] ?? [];
        unset($validated['facilitator_ids']);
        
        // Extract course_dates before updating course
        $courseDatesData = $validated['course_dates'] ?? [];
        unset($validated['course_dates']);

        $course->update($validated);
        
        // Handle featured image upload after update (to use course ID)
        if (isset($tempFile) && $tempFile) {
            try {
                // Create course-specific directory
                $courseUploadDir = public_path('uploads/courses/' . $course->id);
                if (!is_dir($courseUploadDir)) {
                    mkdir($courseUploadDir, 0755, true);
                }
                
                // Generate filename
                $filename = time() . '_' . $tempFile->getClientOriginalName();
                
                // Move file to course-specific directory
                $tempFile->move($courseUploadDir, $filename);
                
                // Update course with featured image path
                $course->update(['featured_image' => 'uploads/courses/' . $course->id . '/' . $filename]);
            } catch (\Exception $e) {
                \Log::error('Featured image upload failed for course ' . $course->id . ': ' . $e->getMessage());
                // Continue anyway, course is updated without new image
            }
        }

        // Sync facilitators to course
        $course->facilitators()->sync($facilitatorIds);

        // Update course dates and venues
        // Delete all existing dates and venues for this course
        $course->courseDates()->delete();
        
        // Create new dates and venues
        if (!empty($courseDatesData)) {
            $dateSequence = 0;
            foreach ($courseDatesData as $dateData) {
                $courseDate = $course->courseDates()->create([
                    'start_date' => $dateData['start_date'],
                    'end_date' => $dateData['end_date'],
                    'date_label' => $dateData['date_label'] ?? null,
                    'notes' => $dateData['notes'] ?? null,
                    'sequence' => $dateSequence++
                ]);

                // Create venues for this date
                if (isset($dateData['venues']) && is_array($dateData['venues'])) {
                    $venueSequence = 0;
                    foreach ($dateData['venues'] as $venueData) {
                        $courseDate->venues()->create([
                            'venue_name' => $venueData['venue_name'],
                            'address' => $venueData['address'] ?? null,
                            'city' => $venueData['city'] ?? null,
                            'state' => $venueData['state'] ?? null,
                            'country' => $venueData['country'] ?? null,
                            'capacity' => $venueData['capacity'] ?? null,
                            'notes' => $venueData['notes'] ?? null,
                            'sequence' => $venueSequence++
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Admin: Show course details
     */
    public function adminShow(Course $course)
    {
        $this->authorize('isAdmin');

        $course->load('category', 'facilitator', 'courseDates.venues', 'enrollees');

        return view('admin.courses.show', compact('course'));
    }

    /**
     * Admin: Delete course
     */
    public function adminDestroy(Course $course)
    {
        $this->authorize('isAdmin');

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    /**
     * Search courses by title, subtitle, or description
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 10);
        $categoryId = $request->input('category_id');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Please enter at least 2 characters'
            ]);
        }

        // Search in title and subtitle first (higher priority)
        $titleMatches = Course::where('is_active', true);
        
        // Add category filter if specified
        if ($categoryId) {
            $titleMatches->where('category_id', $categoryId);
        }
        
        $titleMatches = $titleMatches->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('subtitle', 'LIKE', "%{$query}%");
            })
            ->select('id', 'title', 'subtitle', 'featured_image', 'fee', 'category_id')
            ->with('category')
            ->limit($limit)
            ->get();

        // If not enough results, search in description
        $descriptionMatches = collect();
        if ($titleMatches->count() < $limit) {
            $remaining = $limit - $titleMatches->count();
            $titleIds = $titleMatches->pluck('id')->toArray();
            
            $descriptionQuery = Course::where('is_active', true)
                ->whereNotIn('id', $titleIds)
                ->where('description', 'LIKE', "%{$query}%");
            
            // Add category filter if specified
            if ($categoryId) {
                $descriptionQuery->where('category_id', $categoryId);
            }
            
            $descriptionMatches = $descriptionQuery
                ->select('id', 'title', 'subtitle', 'featured_image', 'fee', 'category_id')
                ->with('category')
                ->limit($remaining)
                ->get();
        }

        $results = $titleMatches->merge($descriptionMatches);

        return response()->json([
            'success' => true,
            'data' => $results->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'subtitle' => $course->subtitle,
                    'featured_image' => $course->featured_image,
                    'fee' => $course->fee,
                    'category' => $course->category?->name,
                    'url' => route('courses.show', $course)
                ];
            }),
            'count' => $results->count()
        ]);
    }
}

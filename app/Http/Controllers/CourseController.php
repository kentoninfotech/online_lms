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
use Carbon\Carbon;

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

        // Fetch course display settings
        $courseDisplaySettings = [
            'show_featured_courses' => (bool) HomepageSetting::getSetting('course_display', 'show_featured_courses', 1),
            'course_display_mode' => HomepageSetting::getSetting('course_display', 'course_display_mode', 'default'),
            'courses_per_row' => HomepageSetting::getSetting('course_display', 'courses_per_row', 3),
            'max_courses_display' => HomepageSetting::getSetting('course_display', 'max_courses_display', 12),
            'show_all_categories_option' => (bool) HomepageSetting::getSetting('course_display', 'show_all_categories_option', 1),
            'selected_categories' => json_decode(HomepageSetting::getSetting('course_display', 'selected_categories', json_encode([])), true) ?: [],
            'show_all_levels_option' => (bool) HomepageSetting::getSetting('course_display', 'show_all_levels_option', 1),
            'selected_levels' => json_decode(HomepageSetting::getSetting('course_display', 'selected_levels', json_encode(['Local', 'International', 'Diploma'])), true) ?: ['Local', 'International', 'Diploma'],
        ];

        return view('courses.index', compact('carouselImages', 'categories', 'featuredCourses', 'homeSettings', 'courseDisplaySettings'));
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
        // Admin can view all courses, instructor can view their own
        if (auth()->user()->user_type === 'admin') {
            $courses = Course::with('category', 'facilitator')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Instructors see only their assigned courses
            $instructor = auth()->user()->instructor;
            if (!$instructor) {
                return abort(403);
            }
            $courses = $instructor->courses()
                ->with('category', 'facilitator')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Admin: Create course form
     */
    public function adminCreate()
    {
        // Only admin and instructor can create courses
        if (!in_array(auth()->user()->user_type, ['admin', 'instructor'])) {
            abort(403);
        }

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
        // Only admin and instructor can create courses
        if (!in_array(auth()->user()->user_type, ['admin', 'instructor'])) {
            abort(403);
        }

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
                // Generate filename
                $filename = time() . '_' . $file->getClientOriginalName();
                
                // Store file and get path
                $validated['featured_image'] = $file->storeAs('uploads/courses', $filename, 'public');
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
                // Generate filename
                $filename = time() . '_' . $tempFile->getClientOriginalName();
                
                // Store file in course-specific directory
                $path = 'storage/' . $tempFile->storeAs('uploads/courses/' . $course->id, $filename, 'public');
                
                // Update course with featured image path
                $course->update(['featured_image' => $path]);
            } catch (\Exception $e) {
                \Log::error('Featured image upload failed for course ' . $course->id . ': ' . $e->getMessage());
                // Continue anyway, course is created without image
            }
        }

        // Attach facilitators to course
        if (!empty($facilitatorIds)) {
            $course->facilitators()->attach($facilitatorIds);
            
            // Also auto-assign facilitators as instructors
            $this->assignFacilitatorsAsInstructors($course, $facilitatorIds);
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
        $this->authorize('update', $course);

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
        $this->authorize('update', $course);

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
                // Generate filename
                $filename = time() . '_' . $file->getClientOriginalName();
                
                // Store file and get path
                $validated['featured_image'] = 'storage/' . $file->storeAs('uploads/courses', $filename, 'public');
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
                // Generate filename
                $filename = time() . '_' . $tempFile->getClientOriginalName();
                
                // Store file in course-specific directory
                $path = 'storage/' . $tempFile->storeAs('uploads/courses/' . $course->id, $filename, 'public');
                
                // Update course with featured image path
                $course->update(['featured_image' => $path]);
            } catch (\Exception $e) {
                \Log::error('Featured image upload failed for course ' . $course->id . ': ' . $e->getMessage());
                // Continue anyway, course is updated without new image
            }
        }

        // Sync facilitators to course
        $course->facilitators()->sync($facilitatorIds);
        
        // Also auto-assign facilitators as instructors
        if (!empty($facilitatorIds)) {
            $this->assignFacilitatorsAsInstructors($course, $facilitatorIds);
        }

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
        $this->authorize('view', $course);

        $course->load('category', 'facilitator', 'courseDates.venues', 'enrollees');

        return view('admin.courses.show', compact('course'));
    }

    /**
     * Admin: Delete course
     */
    public function adminDestroy(Course $course)
    {
        $this->authorize('delete', $course);

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

    /**
     * Generate venues for course dates that don't have venues
     */
    public function generateVenuesForCourse(Course $course)
    {
        $this->authorize('update', $course);

        $venues = ['Lagos', 'Abuja', 'Port Harcourt', 'Nasarawa', 'Bauchi'];

        // Get all course_dates for this course that don't have a corresponding venue
        $datesToAssignVenues = \DB::table('course_dates')
            ->leftJoin('course_venues', 'course_dates.id', '=', 'course_venues.course_date_id')
            ->where('course_dates.course_id', $course->id)
            ->whereNull('course_venues.id')
            ->select('course_dates.*')
            ->orderBy('course_dates.id')
            ->get();

        if ($datesToAssignVenues->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => '✅ All course dates already have venues assigned.',
                'count' => 0,
                'datesUpdated' => 0
            ]);
        }

        try {
            // Shuffle venues for randomization
            $shuffledVenues = $venues;
            shuffle($shuffledVenues);
            
            $venueIndex = 0;
            $createdCount = 0;
            $datesUpdatedCount = 0;

            foreach ($datesToAssignVenues as $dateRecord) {
                // Try to parse start_date and end_date from date_label if they don't exist
                $updateData = [];
                
                if ($dateRecord->date_label && (!$dateRecord->start_date || !$dateRecord->end_date)) {
                    // Example: "04 - 08 May, 2026" or "04 - 08 May."
                    $dateLabelParts = explode(',', $dateRecord->date_label);
                    $year = trim(end($dateLabelParts));
                    
                    // Remove year from parts if it's a standalone number
                    if (is_numeric(trim($year))) {
                        array_pop($dateLabelParts);
                    } else {
                        // Year might be embedded in the last segment, extract it
                        $lastPart = end($dateLabelParts);
                        preg_match('/\d{4}/', $lastPart, $yearMatch);
                        if (!empty($yearMatch)) {
                            $year = $yearMatch[0];
                        }
                    }

                    $dateSegment = trim(reset($dateLabelParts)); // e.g., "04 - 08 May"

                    // Extract start day, end day, and month
                    // Regex to capture: (Day1) - (Day2) (Month)
                    if (preg_match('/(\d+)\s*-\s*(\d+)\s*([a-zA-Z.]+)/', $dateSegment, $matches)) {
                        if (count($matches) === 4) {
                            $startDay = $matches[1];
                            $endDay = $matches[2];
                            $month = $matches[3];

                            try {
                                $startDate = Carbon::parse("$startDay $month $year")->format('Y-m-d');
                                $endDate = Carbon::parse("$endDay $month $year")->format('Y-m-d');
                                
                                $updateData['start_date'] = $startDate;
                                $updateData['end_date'] = $endDate;
                                $datesUpdatedCount++;
                            } catch (\Exception $dateParseException) {
                                // If date parsing fails, continue with just venue assignment
                            }
                        }
                    }
                }

                // Assign venue, cycling through the list if necessary
                $venueName = $shuffledVenues[$venueIndex % count($shuffledVenues)];
                $venueIndex++;

                // Update course_dates if dates were parsed
                if (!empty($updateData)) {
                    $updateData['updated_at'] = now();
                    \DB::table('course_dates')
                        ->where('id', $dateRecord->id)
                        ->update($updateData);
                }

                // Create venue record
                \DB::table('course_venues')->insert([
                    'course_date_id' => $dateRecord->id,
                    'venue_name' => $venueName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $createdCount++;
            }

            $message = "✅ Successfully generated {$createdCount} venue(s)";
            if ($datesUpdatedCount > 0) {
                $message .= " and updated {$datesUpdatedCount} date(s) with parsed dates";
            }
            $message .= " for this course!";

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $createdCount,
                'datesUpdated' => $datesUpdatedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Error generating venues: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to assign facilitators as instructors in the instructor_course pivot table
     * This allows instructors to see the course in their "My Courses" dashboard
     */
    private function assignFacilitatorsAsInstructors(Course $course, array $facilitatorIds)
    {
        try {
            // Get all facilitators with their user relationships
            $facilitators = \App\Models\Facilitator::whereIn('id', $facilitatorIds)
                ->with('user')
                ->get();

            // For each facilitator, find or create corresponding instructor and attach to course
            foreach ($facilitators as $facilitator) {
                if (!$facilitator->user) {
                    continue; // Skip if no user associated
                }

                // Find or create instructor for this user
                $instructor = \App\Models\Instructor::firstOrCreate(
                    ['user_id' => $facilitator->user_id],
                    [
                        'name' => $facilitator->user->name ?? $facilitator->name,
                        'email' => $facilitator->user->email ?? $facilitator->email,
                        'bio' => $facilitator->bio,
                    ]
                );

                // Attach instructor to course with default settings (only if not already attached)
                if (!$course->instructors()->where('instructor_id', $instructor->id)->exists()) {
                    $course->instructors()->attach($instructor->id, [
                        'role' => 'lead', // Default role
                        'can_manage_content' => true,
                        'can_manage_quizzes' => true,
                        'can_manage_enrollees' => false,
                        'is_active' => true,
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to assign facilitators as instructors: ' . $e->getMessage());
            // Don't fail the course creation/update if instructor assignment fails
        }
    }}
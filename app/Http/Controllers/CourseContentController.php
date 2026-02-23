<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseContent;
use App\Models\CourseContentCompletion;
use App\Models\CourseEnrollee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseContentController extends Controller
{
    /**
     * Show course learning hub
     */
    public function index(Course $course)
    {
        // Verify user is enrolled AND approved
        $enrollment = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Check if enrollment is active (approved)
        if ($enrollment->status !== 'active') {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Your enrollment has not been approved yet. Please wait for admin approval or contact support.');
        }

        $courseContents = $course->contents()
            ->where('is_published', true)
            ->orderBy('sequence')
            ->get();

        return view('courses.learn.index', compact('course', 'courseContents', 'enrollment'));
    }

    /**
     * Show course content for learning
     */
    public function show(Course $course, CourseContent $content)
    {
        // Verify user is enrolled AND approved
        $enrollment = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Check if enrollment is active (approved)
        if ($enrollment->status !== 'active') {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Your enrollment has not been approved yet. Please wait for admin approval or contact support.');
        }

        // Get or create completion record
        $completion = CourseContentCompletion::firstOrCreate(
            [
                'course_enrollee_id' => $enrollment->id,
                'course_content_id' => $content->id,
            ],
            [
                'started_at' => now(),
            ]
        );

        // Mark as started
        if (!$completion->started_at) {
            $completion->update(['started_at' => now()]);
        }

        $courseContents = $course->contents()
            ->where('is_published', true)
            ->orderBy('sequence')
            ->get();

        return view('courses.learn.content', compact('course', 'content', 'completion', 'courseContents', 'enrollment'));
    }

    /**
     * Mark content as completed
     */
    public function markComplete(Course $course, CourseContent $content, Request $request)
    {
        $enrollment = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Verify enrollment is active
        if ($enrollment->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your enrollment has not been approved yet.'
            ], 403);
        }

        $completion = CourseContentCompletion::where('course_enrollee_id', $enrollment->id)
            ->where('course_content_id', $content->id)
            ->firstOrFail();

        // Check minimum viewing time requirement
        $minReadingTime = $content->min_reading_time_minutes ?? 0;
        $timeSpent = (int) $request->input('time_spent_minutes', 0);

        if ($minReadingTime > 0 && $timeSpent < $minReadingTime) {
            $remainingTime = $minReadingTime - $timeSpent;
            return response()->json([
                'success' => false,
                'message' => "You must watch/read for at least {$minReadingTime} minutes. Please spend {$remainingTime} more minute(s).",
                'required_time' => $minReadingTime,
                'time_spent' => $timeSpent,
                'remaining_time' => $remainingTime
            ], 422);
        }

        // Update time spent
        if ($timeSpent > 0) {
            $completion->update(['time_spent_minutes' => $timeSpent]);
        }

        $completion->markCompleted();

        // Update enrollment progress
        $progressPercentage = $enrollment->calculateProgressPercentage();
        $enrollment->update(['progress_percentage' => $progressPercentage]);

        return response()->json([
            'success' => true,
            'message' => 'Content marked as completed',
            'progress' => $progressPercentage
        ]);
    }

    /**
     * Admin: List all course contents
     */
    public function adminIndex(Course $course)
    {
        $this->authorize('isAdmin');

        $contents = $course->contents()->orderBy('sequence', 'asc')->get();

        return view('admin.course-contents.index', compact('course', 'contents'));
    }

    /**
     * Admin: Show single content
     */
    public function adminShow(Course $course, CourseContent $content)
    {
        $this->authorize('isAdmin');

        return view('admin.course-contents.show', compact('course', 'content'));
    }

    /**
     * Admin: Create content form
     */
    public function adminCreate(Course $course)
    {
        $this->authorize('isAdmin');
        
        // Get available prerequisites (other content in this course)
        $courseContents = $course->contents()->get();

        return view('admin.course-contents.create', compact('course', 'courseContents'));
    }

    /**
     * Admin: Store new content
     */
    public function adminStore(Request $request, Course $course)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:text,pdf,excel,word,powerpoint,video,link,image',
            'content' => 'nullable|string',
            'file' => 'nullable|file',
            'duration_minutes' => 'nullable|integer|min:1',
            'sequence' => 'required|integer|min:0',
            'is_published' => 'boolean',
            'is_required' => 'boolean',
            // New fields
            'available_from' => 'nullable|date_format:Y-m-d\TH:i',
            'available_until' => 'nullable|date_format:Y-m-d\TH:i|after:available_from',
            'prerequisite_content_id' => 'nullable|exists:course_contents,id',
            'min_reading_time_minutes' => 'nullable|integer|min:0',
            'embed_type' => 'required|in:default,iframe,popup,fullscreen,modal',
            'allow_download' => 'boolean',
            'track_viewing' => 'boolean',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $uploadDir = public_path('uploads/courses/' . $course->id . '/content');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = time() . '-' . $file->getClientOriginalName();
            $file->move($uploadDir, $filename);
            $validated['file_path'] = 'uploads/courses/' . $course->id . '/content/' . $filename;
        }

        $validated['course_id'] = $course->id;
        $content = CourseContent::create($validated);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Content created successfully.');
    }

    /**
     * Admin: Edit content form
     */
    public function adminEdit(Course $course, CourseContent $content)
    {
        $this->authorize('isAdmin');
        
        // Get available prerequisites (other content in this course, excluding this one)
        $courseContents = $course->contents()->where('id', '!=', $content->id)->get();

        return view('admin.course-contents.edit', compact('course', 'content', 'courseContents'));
    }

    /**
     * Admin: Update content
     */
    public function adminUpdate(Request $request, Course $course, CourseContent $content)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:text,pdf,excel,word,powerpoint,video,link,image',
            'content' => 'nullable|string',
            'file' => 'nullable|file',
            'duration_minutes' => 'nullable|integer|min:1',
            'sequence' => 'required|integer|min:0',
            'is_published' => 'boolean',
            'is_required' => 'boolean',
            // New fields
            'available_from' => 'nullable|date_format:Y-m-d\TH:i',
            'available_until' => 'nullable|date_format:Y-m-d\TH:i|after:available_from',
            'prerequisite_content_id' => 'nullable|exists:course_contents,id',
            'min_reading_time_minutes' => 'nullable|integer|min:0',
            'embed_type' => 'required|in:default,iframe,popup,fullscreen,modal',
            'allow_download' => 'boolean',
            'track_viewing' => 'boolean',
        ]);

        if ($request->hasFile('file')) {
            if ($content->file_path) {
                Storage::disk('public')->delete($content->file_path);
            }
            $file = $request->file('file');
            $filename = Auth::id() . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/courses'), $filename);
            $validated['file_path'] = 'uploads/courses/' . $filename;
        }

        $content->update($validated);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Content updated successfully.');
    }

    /**
     * Admin: Delete content
     */
    public function adminDestroy(Course $course, CourseContent $content)
    {
        $this->authorize('isAdmin');

        if ($content->file_path) {
            Storage::disk('public')->delete($content->file_path);
        }

        $content->delete();

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Content deleted successfully.');
    }

    /**
     * Admin: List all course contents globally
     */
    public function adminListAll()
    {
        $this->authorize('isAdmin');

        $contents = CourseContent::with('course')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.learning-content.index', compact('contents'));
    }

    /**
     * Admin: View single content globally
     */
    public function adminViewContent(CourseContent $content)
    {
        $this->authorize('isAdmin');

        return view('admin.learning-content.show', compact('content'));
    }

    /**
     * Instructor: List course contents for their assigned course
     */
    public function instructorIndex(Course $course)
    {
        $this->authorize('create', new CourseContent());

        // Check if instructor is assigned to this course
        $instructor = Auth::user()->instructor;
        if (!$instructor || $instructor->id !== $course->facilitator_id) {
            abort(403, 'You are not assigned to this course.');
        }

        $contents = $course->contents()->orderBy('sequence', 'asc')->get();

        return view('instructor.course-contents.index', compact('course', 'contents'));
    }

    /**
     * Instructor: Show single content
     */
    public function instructorShow(Course $course, CourseContent $content)
    {
        $this->authorize('view', $content);

        return view('instructor.course-contents.show', compact('course', 'content'));
    }

    /**
     * Instructor: Create content form
     */
    public function instructorCreate(Course $course)
    {
        $this->authorize('create', [$content = new CourseContent(), $course]);

        // Check if instructor is assigned to this course
        $instructor = Auth::user()->instructor;
        if (!$instructor || $instructor->id !== $course->facilitator_id) {
            abort(403, 'You are not assigned to this course.');
        }

        return view('instructor.course-contents.create', compact('course'));
    }

    /**
     * Instructor: Store new content
     */
    public function instructorStore(Request $request, Course $course)
    {
        $this->authorize('create', [$content = new CourseContent(), $course]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:text,pdf,excel,word,powerpoint,video,link,image',
            'content' => 'nullable|string',
            'file' => 'nullable|file',
            'duration_minutes' => 'nullable|integer|min:1',
            'sequence' => 'required|integer|min:0',
            'is_published' => 'boolean',
            'is_required' => 'boolean',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = Auth::id() . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/courses'), $filename);
            $validated['file_path'] = 'uploads/courses/' . $filename;
        }

        $validated['course_id'] = $course->id;
        $content = CourseContent::create($validated);

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Content created successfully.');
    }

    /**
     * Instructor: Edit content form
     */
    public function instructorEdit(Course $course, CourseContent $content)
    {
        $this->authorize('update', $content);

        return view('instructor.course-contents.edit', compact('course', 'content'));
    }

    /**
     * Instructor: Update content
     */
    public function instructorUpdate(Request $request, Course $course, CourseContent $content)
    {
        $this->authorize('update', $content);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:text,pdf,excel,word,powerpoint,video,link,image',
            'content' => 'nullable|string',
            'file' => 'nullable|file',
            'duration_minutes' => 'nullable|integer|min:1',
            'sequence' => 'required|integer|min:0',
            'is_published' => 'boolean',
            'is_required' => 'boolean',
        ]);

        if ($request->hasFile('file')) {
            if ($content->file_path) {
                Storage::disk('public')->delete($content->file_path);
            }
            $file = $request->file('file');
            $filename = Auth::id() . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/courses'), $filename);
            $validated['file_path'] = 'uploads/courses/' . $filename;
        }

        $content->update($validated);

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Content updated successfully.');
    }

    /**
     * Instructor: Delete content
     */
    public function instructorDestroy(Course $course, CourseContent $content)
    {
        $this->authorize('delete', $content);

        if ($content->file_path) {
            Storage::disk('public')->delete($content->file_path);
        }

        $content->delete();

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Content deleted successfully.');
    }
}


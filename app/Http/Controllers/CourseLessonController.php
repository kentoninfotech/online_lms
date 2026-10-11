<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CourseLessonController extends Controller
{
    /**
     * Display calendar for the lesson
     */
    public function calendar(Course $course)
    {
        $this->authorize('view', $course);

        $lessons = $course->lessons()
            ->where('is_published', true)
            ->orderBy('start_time', 'asc')
            ->get();

        $currentMonth = now()->month;
        $currentYear = now()->year;

        return view('admin.course-lessons.calendar', compact('course', 'lessons', 'currentMonth', 'currentYear'));
    }

    /**
     * Display a listing of lessons for a course
     */
    public function index(Course $course)
    {
        $this->authorize('update', $course);

        $lessons = $course->lessons()
            ->with('instructor')
            ->orderBy('start_time', 'desc')
            ->paginate(15);

        return view('admin.course-lessons.index', compact('course', 'lessons'));
    }

    /**
     * Show the form for creating a new lesson
     */
    public function create(Course $course)
    {
        $this->authorize('update', $course);

        $instructors = Instructor::with('user')->get();

        return view('admin.course-lessons.create', compact('course', 'instructors'));
    }

    /**
     * Store a newly created lesson
     */
    public function store(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:Y-m-d H:i',
            'end_time' => 'nullable|date_format:Y-m-d H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url',
            'instructor_id' => 'required|exists:instructors,id',
            'capacity' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $validated['course_id'] = $course->id;
        $validated['is_published'] = $request->has('is_published');

        $lesson = CourseLesson::create($validated);

        return redirect()
            ->route('admin.course-lessons.show', [$course, $lesson])
            ->with('success', 'Lesson created successfully!');
    }

    /**
     * Display the specified lesson
     */
    public function show(Course $course, CourseLesson $lesson)
    {
        $this->authorize('view', $course);
        $this->authorize('view', $lesson);

        $attendees = $lesson->attendees()
            ->with('enrollee.user')
            ->paginate(15);

        return view('admin.course-lessons.show', compact('course', 'lesson', 'attendees'));
    }

    /**
     * Show the form for editing the specified lesson
     */
    public function edit(Course $course, CourseLesson $lesson)
    {
        $this->authorize('update', $course);

        $instructors = Instructor::with('user')->get();

        return view('admin.course-lessons.edit', compact('course', 'lesson', 'instructors'));
    }

    /**
     * Update the specified lesson
     */
    public function update(Request $request, Course $course, CourseLesson $lesson)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:Y-m-d H:i',
            'end_time' => 'nullable|date_format:Y-m-d H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url',
            'instructor_id' => 'required|exists:instructors,id',
            'capacity' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->has('is_published');

        $lesson->update($validated);

        return redirect()
            ->route('admin.course-lessons.show', [$course, $lesson])
            ->with('success', 'Lesson updated successfully!');
    }

    /**
     * Delete the specified lesson
     */
    public function destroy(Course $course, CourseLesson $lesson)
    {
        $this->authorize('update', $course);

        $lesson->delete();

        return redirect()
            ->route('admin.course-lessons.index', $course)
            ->with('success', 'Lesson deleted successfully!');
    }

    /**
     * Get lessons for calendar API
     */
    public function getCalendarLessons(Course $course, $month = null, $year = null)
    {
        $this->authorize('view', $course);

        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $lessons = $course->lessons()
            ->where('is_published', true)
            ->inMonth($month, $year)
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($lesson) {
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->topic,
                    'start' => $lesson->start_time?->toIso8601String(),
                    'end' => $lesson->end_time?->toIso8601String(),
                    'description' => $lesson->description,
                    'location' => $lesson->location,
                    'instructor' => $lesson->instructor->user->name,
                    'status' => $lesson->status,
                    'meetingLink' => $lesson->meeting_link,
                ];
            });

        return response()->json($lessons);
    }

    /**
     * Get upcoming lessons for sidebar
     */
    public function getUpcomingLessons($courseId = null)
    {
        $query = CourseLesson::where('is_published', true)
            ->upcoming()
            ->with('course', 'instructor.user')
            ->limit(10);

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        $lessons = $query->get()
            ->map(function ($lesson) {
                return [
                    'id' => $lesson->id,
                    'topic' => $lesson->topic,
                    'courseId' => $lesson->course_id,
                    'courseName' => $lesson->course->title,
                    'instructor' => $lesson->instructor->user->name,
                    'startTime' => $lesson->start_time,
                    'endTime' => $lesson->end_time,
                    'location' => $lesson->location,
                    'meetingLink' => $lesson->meeting_link,
                    'formattedDateTime' => $lesson->formatted_date_time,
                ];
            });

        return response()->json($lessons);
    }
}

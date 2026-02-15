<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RecurrenceService;
use App\Models\Lesson;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\LessonOccurrence;
use App\Models\Attendance;
use App\Http\Requests\StoreLessonRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LessonController extends Controller
{
    /**
     * Constructor
     */
    protected RecurrenceService $recurrenceService;

    public function __construct(RecurrenceService $recurrenceService)
    {
        $this->recurrenceService = $recurrenceService;
    }

    /**
     * Show lessons
     */
    public function lessons()
    {
        $instructors = Instructor::all();

        $students = Student::all();

        $lessons = Lesson::with(['student.user', 'instructor.user', 'occurrences' => function ($q) {
            $q->orderBy('scheduled_start', 'asc');
        }])
        ->paginate(10);

        // Today’s schedule
        $todayLessons = LessonOccurrence::with(['lesson.student.user', 'lesson.instructor.user', 'zoomSession'])
            ->whereHas('lesson')
            ->whereDate('scheduled_start', Carbon::today())
            ->orderBy('scheduled_start')
            ->get();

        // Recent attendances from all instructors
        $recentAttendances = Attendance::with(['occurrence.lesson.student', 'occurrence.lesson.instructor'])
            ->latest()
            ->take(20)
            ->get();

        return view('dashboard.admin.lessons', 
               compact(
                'lessons', 
                'students', 
                'instructors', 
                'todayLessons',
                'recentAttendances'
        ));
    }

    /**
     * Show create lesson page
     */
    public function create()
    {
        $instructors = Instructor::all();
        $students = Student::all();

        return view('dashboard.add-lesson', compact('instructors', 'students'));
    }

    /**
     * Store lesson records
     */
    public function store(StoreLessonRequest $request)
    { 
        $this->authorize('create', Lesson::class);

        $data = $request->validated();

        // Prepare recurrence meta 
        $recurrenceMeta = null;

        if ($data['recurrence_type'] !== 'none') {
            $recurrenceMeta = [
                'interval'  => (int) ($data['interval'] ?? 1),
                'end_type'  => $data['end_type'] ?? 'count',
                'end_date'  => $data['end_date'] ?? null,
                'count'     => null,
                'days'      => [],
                'mode'      => $data['mode'] ?? 'day',
            ];

            // handle count/end_type
            if ($recurrenceMeta['end_type'] === 'count') {
                $recurrenceMeta['count'] = (int) ($request->count ?? 1);
            } elseif ($recurrenceMeta['end_type'] === 'date') {
                $recurrenceMeta['end_date'] = $request->end_date;
                // Ensure no leftover 'count'
                unset($recurrenceMeta['count']);
            }

            // handle weekly days
            if ($data['recurrence_type'] === 'weekly') {
                $recurrenceMeta['days'] = $data['days'] ?? [];
            }

            // handle monthly mode
            if ($data['recurrence_type'] === 'monthly') {
                $recurrenceMeta['mode'] = $data['mode'] ?? 'day';
            }
        }

        // Convert start_time from Nigeria timezone (Africa/Lagos - UTC+1) to UTC for storage
        // The datetime-local input sends local time without timezone info
        $userTimezone = 'Africa/Lagos';
        
        try {
            $startTime = $this->parseDateTime($data['start_time'], $userTimezone);
            \Log::info('Lesson Start Time Parsed (Create)', [
                'input' => $data['start_time'],
                'timezone' => $userTimezone,
                'stored_time' => $startTime->toIso8601String(),
                'note' => 'Time stored in Africa/Lagos (UTC+1) for consistent cron/activity operations',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to parse lesson start time', [
                'error' => $e->getMessage(),
                'input' => $data['start_time']
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['start_time' => 'Invalid date/time format. Please use YYYY-MM-DD HH:MM format.']);
        }

        $lesson = Lesson::create([
            'subject'          => $data['subject'],
            'student_id'       => $data['student_id'],
            'instructor_id'    => $data['instructor_id'] ?? Auth::user()->instructor->id,
            'start_time'       => $startTime,
            'duration_minutes' => $data['duration_minutes'],
            'recurrence_type'  => $data['recurrence_type'],
            'recurrence_meta'  => $recurrenceMeta,
        ]);

        // Expand into occurrences
        $this->recurrenceService->generateOccurrences($lesson);

        // Determine the redirect route based on the user's role
        $redirTo = auth()->user()->hasRole('admin') ? 'admin.lessons' : 'instructor.lessons';

        return redirect()
            ->route($redirTo)
            ->with('success', 'Lesson created successfully.');
    }

    /**
     * Show lesson edit page
     */
    public function edit(?Lesson $lesson)
    {
        if (!$lesson || !$lesson->id) {
            return redirect()
                ->back()
                ->with('error', 'Lesson not found.');
        }

        $user = auth()->user();
        
        // Debug logging
        \Log::info('Lesson Edit Debug', [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'roles' => $user->getRoleNames()->toArray(),
            'has_instructor_role' => $user->hasRole('instructor'),
            'instructor_record' => $user->instructor,
            'lesson_id' => $lesson->id,
            'lesson_instructor_id' => $lesson->instructor_id,
        ]);

        $this->authorize('update', $lesson);

        $instructors = Instructor::all();
        $students = Student::all();

        return view('dashboard.edit-lesson', compact('lesson', 'instructors', 'students'));
    }

    /**
     * Update lesson record
     */
    public function update(?Lesson $lesson, StoreLessonRequest $request)
    {
        if (!$lesson || !$lesson->id) {
            return redirect()
                ->back()
                ->with('error', 'Lesson not found.');
        }

        $this->authorize('update', $lesson);

        $data = $request->validated();

        // Prepare recurrence meta 
        $recurrenceMeta = null;

        if ($data['recurrence_type'] !== 'none') {
            $recurrenceMeta = [
                'interval'  => (int) ($data['interval'] ?? 1),
                'end_type'  => $data['end_type'] ?? 'count',
                'end_date'  => $data['end_date'] ?? null,
                'count'     => null,
                'days'      => [],
                'mode'      => $data['mode'] ?? 'day',
            ];

            // handle count/end_type
            if ($recurrenceMeta['end_type'] === 'count') {
                $recurrenceMeta['count'] = (int) ($request->count ?? 1);
            } elseif ($recurrenceMeta['end_type'] === 'date') {
                $recurrenceMeta['end_date'] = $request->end_date;
                // Ensure no leftover 'count'
                unset($recurrenceMeta['count']);
            }

            // handle weekly days
            if ($data['recurrence_type'] === 'weekly') {
                $recurrenceMeta['days'] = $data['days'] ?? [];
            }

            // handle monthly mode
            if ($data['recurrence_type'] === 'monthly') {
                $recurrenceMeta['mode'] = $data['mode'] ?? 'day';
            }
        }

        // Convert start_time from Nigeria timezone (Africa/Lagos - UTC+1) to UTC for storage
        // The datetime-local input sends local time without timezone info
        $userTimezone = 'Africa/Lagos';
        try {
            $startTime = $this->parseDateTime($data['start_time'], $userTimezone);
            \Log::info('Lesson Start Time Parsed (Update)', [
                'input' => $data['start_time'],
                'timezone' => $userTimezone,
                'stored_time' => $startTime->toIso8601String(),
                'note' => 'Time stored in Africa/Lagos (UTC+1) for consistent cron/activity operations',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to parse lesson start time on update', [
                'error' => $e->getMessage(),
                'input' => $data['start_time']
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['start_time' => 'Invalid date/time format. Please use YYYY-MM-DD HH:MM format.']);
        }

        $lesson->update([
            'subject'          => $data['subject'],
            'student_id'       => $data['student_id'],
            'instructor_id'    => $data['instructor_id'] ?? Auth::user()->instructor->id,
            'start_time'       => $startTime,
            'duration_minutes' => $data['duration_minutes'],
            'recurrence_type'  => $data['recurrence_type'],
            'recurrence_meta'  => $recurrenceMeta,
        ]);

        // Delete future occurrences
        $this->recurrenceService->removeFutureOccurrences($lesson);

        // Expand into occurrences
        $this->recurrenceService->generateOccurrences($lesson);

        // Determine the redirect route based on the user's role
        $redirTo = auth()->user()->hasRole('admin') ? 'admin.lessons' : 'instructor.lessons';

        return redirect()
            ->route($redirTo)
            ->with('success', 'Lesson created successfully.');
    }

    /**
     * Delete lesson
     */
    public function delete(?Lesson $lesson)
    {
        if (!$lesson || !$lesson->id) {
            return redirect()
                ->back()
                ->with('error', 'Lesson not found.');
        }

        $this->authorize('delete', $lesson);

        $lesson->delete();

        return redirect()
                 ->back()
                 ->with('success', 'Lesson deleted successfully.');
    }

    /**
     * Parse datetime and return Carbon instance in Africa/Lagos timezone
     * 
     * IMPORTANT: Lessons are stored in Africa/Lagos (UTC+1) in the database
     * This is because cron jobs and scheduled activities use this timezone
     * 
     * When displaying to users, convert FROM Africa/Lagos to their local timezone
     */
    private function parseDateTime($dateString, $timezone = 'Africa/Lagos')
    {
        // Trim whitespace
        $dateString = trim($dateString);
        
        // Extract date and time using regex to handle unexpected characters
        // Pattern: YYYY-MM-DD HH:MM or YYYY-MM-DDTHH:MM or variations
        // This handles both HTML5 datetime-local format (2025-01-15T20:54) and standard formats
        if (preg_match('/(\d{4}-\d{2}-\d{2})\s*[T\s]+(\d{2}:\d{2})(?::\d{2})?/', $dateString, $matches)) {
            $dateString = $matches[1] . ' ' . $matches[2];
        }
        
        // Array of formats to try (most specific to least specific)
        $formats = [
            'Y-m-d H:i:s',  // 2025-01-15 14:30:45
            'Y-m-d H:i',    // 2025-01-15 14:30
        ];
        
        $carbonInstance = null;
        foreach ($formats as $format) {
            try {
                // Create instance in Africa/Lagos timezone - DO NOT convert to UTC
                // This time will be stored as-is in the database
                $carbonInstance = Carbon::createFromFormat($format, $dateString, $timezone);
                break;
            } catch (\Exception $e) {
                continue;
            }
        }
        
        if ($carbonInstance === null) {
            throw new \Exception("Unable to parse date: '$dateString'. Expected format: YYYY-MM-DD HH:MM");
        }
        
        // Return the time in Africa/Lagos (UTC+1) - ready to store in DB
        // Do NOT convert to UTC - we store everything in Africa/Lagos for consistent operations
        return $carbonInstance;
    }


}

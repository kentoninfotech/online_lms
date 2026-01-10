<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RecurrenceService;
use App\Models\Lesson;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\LessonOccurrence;
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

        return view('dashboard.admin.lessons', 
               compact(
                'lessons', 
                'students', 
                'instructors', 
                'todayLessons'
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

        
        $lesson = Lesson::create([
            'subject'          => $data['subject'],
            'student_id'       => $data['student_id'],
            'instructor_id'    => $data['instructor_id'] ?? Auth::user()->instructor->id,
            'start_time'       => $data['start_time'],
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
    public function edit(Lesson $lesson)
    {
        if (!$lesson || !$lesson->id) {
            return redirect()
                ->back()
                ->with('error', 'Lesson not found.');
        }

        $this->authorize('update', $lesson);

        $instructors = Instructor::all();
        $students = Student::all();

        return view('dashboard.edit-lesson', compact('lesson', 'instructors', 'students'));
    }

    /**
     * Update lesson record
     */
    public function update(Lesson $lesson, StoreLessonRequest $request)
    {
        
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

        
        $lesson->update([
            'subject'          => $data['subject'],
            'student_id'       => $data['student_id'],
            'instructor_id'    => $data['instructor_id'] ?? Auth::user()->instructor->id,
            'start_time'       => $data['start_time'],
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
    public function delete(Lesson $lesson)
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

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            ->whereHas('lesson')//, fn($q) => $q->where('instructor_id', $instructor->id))
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
     * Create lesson
     */
    public function store(StoreLessonRequest $request)
    {
        $this->authorize('create', Lesson::class);

        $data = $request->validated();

        // Normalize recurrence_meta
        $recurrenceMeta = null;

        if (in_array($request->recurrence_type, ['daily', 'monthly'])) {
            $recurrenceMeta = [
                'count' => (int) $request->count,
            ];
        } elseif ($request->recurrence_type === 'weekly') {
            $recurrenceMeta = [
                'days'  => $request->days,
                'count' => (int) $request->count,
            ];
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
        app(\App\Services\RecurrenceService::class)->generateOccurrences($lesson);

        return redirect()->back()->with('success', 'Lesson created successfully.');
    }

}

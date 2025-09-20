<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\LessonOccurrence;
use App\Models\Attendance;
use App\Models\Lesson;
use App\Models\Student;
use Carbon\Carbon;

class InstructorLessonController extends Controller
{
    public function lessons()
    {
        $instructor = Auth::user()->instructor;

        $students = Student::all();

        $lessons = Lesson::with(['student.user', 'occurrences' => function ($q) {
            $q->orderBy('scheduled_start', 'asc');
        }])
        ->where('instructor_id', $instructor->id)
        ->paginate(10);

        // Today’s schedule
        $todayLessons = LessonOccurrence::with(['lesson.student.user', 'zoomSession'])
            ->whereHas('lesson', fn($q) => $q->where('instructor_id', $instructor->id))
            ->whereDate('scheduled_start', Carbon::today())
            ->orderBy('scheduled_start')
            ->get();

        return view('dashboard.instructor.lessons', compact('lessons', 'students', 'todayLessons'));
    }

}

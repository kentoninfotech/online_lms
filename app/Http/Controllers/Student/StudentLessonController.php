<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Lesson;
use Carbon\Carbon;

class StudentLessonController extends Controller
{
    public function lessons()
    {
        $student = Auth::user()->student;

        $lessons = Lesson::with(['instructor.user', 'occurrences' => function ($q) {
            $q->orderBy('scheduled_start', 'asc');
        }])
        ->where('student_id', $student->id)
        ->paginate(10);

        return view('dashboard.student.lessons', compact('lessons'));
    }

}

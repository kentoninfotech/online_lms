<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\Lesson;
use App\Models\Attendance;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function show(Request $request, Instructor $instructor)
    {
        $instructor->load('user');

        // Filter lessons
        $lessonsQuery = Lesson::with(['student.user', 'occurrences'])
            ->where('instructor_id', $instructor->id);

        if ($request->filled('subject')) {
            $lessonsQuery->where('subject', 'like', '%' . $request->subject . '%');
        }

        $lessons = $lessonsQuery->paginate(10, ['*'], 'lessons_page');

        // Filter attendance
        $attendanceQuery = Attendance::with(['occurrence.lesson.student'])
            ->whereHas('occurrence.lesson', function ($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            });

        if ($request->filled('status')) {
            $attendanceQuery->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $attendanceQuery->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $attendanceQuery->whereDate('created_at', '<=', $request->to);
        }

        $attendances = $attendanceQuery->paginate(10, ['*'], 'attendance_page');

        return view('dashboard.instructor', compact('instructor', 'lessons', 'attendances'));
    }
}

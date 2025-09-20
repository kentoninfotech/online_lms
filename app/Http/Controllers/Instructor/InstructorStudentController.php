<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Attendance;
use App\Models\RescheduleRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstructorStudentController extends Controller
{
    public function students()
    {
        $instructor = Auth::user()->instructor;

        // Get all students this instructor teaches
        $students = Lesson::with('student.user', 'student.subscription.plan')
            ->where('instructor_id', $instructor->id)
            ->get()
            ->pluck('student')
            ->unique('id');

        // Map student with stats
        $students = $students->map(function ($student) use ($instructor) {
            $totalClasses = Attendance::whereHas('occurrence.lesson', function ($q) use ($instructor, $student) {
                    $q->where('instructor_id', $instructor->id)
                      ->where('student_id', $student->id);
                })
                ->count();

            $presentCount = Attendance::whereHas('occurrence.lesson', function ($q) use ($instructor, $student) {
                    $q->where('instructor_id', $instructor->id)
                      ->where('student_id', $student->id);
                })
                ->where('status', 'present')
                ->count();

            $attendancePercent = $totalClasses > 0
                ? round(($presentCount / $totalClasses) * 100, 1)
                : 0;

            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'profile' => $student->profile,
                'subscription' => $student->subscription?->status ?? 'N/A',
                'plan' => $student->subscription?->plan?->name ?? '-',
                'total_classes' => $totalClasses,
                'present_count' => $presentCount,
                'attendance_percent' => $attendancePercent,
            ];
        });

        return view('dashboard.instructor.students', compact('students'));
    }

    public function reschedules()
    {
        $instructor = Auth::user()->instructor;

        $requests = RescheduleRequest::with([
                'occurrence.lesson.student.user',
                'occurrence.lesson.instructor.user',
                'requester'
            ])
            ->whereHas('occurrence.lesson', function ($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            })
            ->latest()
            ->paginate(10);

        return view('dashboard.instructor.reschedules', compact('requests'));
    }
}

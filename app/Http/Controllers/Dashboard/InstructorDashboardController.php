<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\LessonOccurrence;
use App\Models\RescheduleRequest;
use App\Models\Attendance;
use App\Models\Student;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InstructorDashboardController extends Controller
{
    public function index(AttendanceService $attendanceService)
    {
        $instructor = Auth::user()->instructor;

        // FOR DEMO PURPOSE REMOVE LATER
        $students = Student::all();

        // Today’s schedule
        $todayLessons = LessonOccurrence::with(['lesson.student.user', 'zoomSession'])
            ->whereHas('lesson', fn($q) => $q->where('instructor_id', $instructor->id))
            ->whereDate('scheduled_start', Carbon::today())
            ->orderBy('scheduled_start')
            ->get();

        // Next class
        $nextClass = LessonOccurrence::with(['lesson.student.user', 'zoomSession'])
            ->whereHas('lesson', fn($q) => $q->where('instructor_id', $instructor->id))
            ->where('scheduled_start', '>', now())
            ->orderBy('scheduled_start')
            ->first();

        // Upcoming classes
        $upcoming = LessonOccurrence::with(['lesson.student.user', 'zoomSession'])
            ->whereHas('lesson', fn($q) => $q->where('instructor_id', $instructor->id))
            ->where('scheduled_start', '>', now())
            ->orderBy('scheduled_start')
            ->take(5)
            ->get();

        // Pending reschedule requests
        $reschedules = RescheduleRequest::with(['occurrence.lesson.student.user'])
            ->whereHas('occurrence.lesson', fn($q) => $q->where('instructor_id', $instructor->id))
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Recent attendance
        $recentAttendance = Attendance::with('occurrence.lesson.student.user')
            ->whereHas('occurrence.lesson', fn($q) => $q->where('instructor_id', $instructor->id))
            ->latest()
            ->take(10)
            ->get();

        // Notifications
        $notifications = auth()->user()->notifications()->latest()->take(5)->get();

        // Lessons this month
        $monthlyStats      = $attendanceService->getMonthlyStats($instructor);
        $lessonsThisMonth = $monthlyStats['lessonsThisMonth'];

        return view('dashboard.instructor.index', compact(
            'instructor',
            'students',
            'todayLessons',
            'nextClass',
            'upcoming',
            'reschedules',
            'recentAttendance',
            'notifications',
            'lessonsThisMonth',
        ));
    }
}

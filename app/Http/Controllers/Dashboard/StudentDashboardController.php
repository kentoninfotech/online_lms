<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AttendanceService;
use App\Models\LessonOccurrence;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    public function index(AttendanceService $attendanceService)
    {
        $student = Auth::user()->student;

        // Upcoming lessons (next 7 days)
        $upcoming = LessonOccurrence::whereHas('lesson', fn($q) => $q->where('student_id', $student->id))
            ->where('scheduled_start', '>=', now())
            ->orderBy('scheduled_start')
            ->take(5)
            ->get();

        // Ongoing lesson
        $ongoingClass = LessonOccurrence::with(['lesson.instructor.user', 'zoomSession'])
            ->whereHas('lesson', fn($q) => $q->where('student_id', $student->id))
            ->ongoing()
            ->orderBy('scheduled_start')
            ->first();

        // Next class
        $nextClass = $student->lessons()
            ->with(['occurrences' => function($q) {
                $q->where('scheduled_start', '>', now())->orderBy('scheduled_start');
            }, 'instructor.user'])
            ->get()
            ->pluck('occurrences')
            ->flatten()
            ->sortBy('scheduled_start')
            ->first();

        // Notifications
        $notifications = auth()->user()->notifications()->latest()->take(5)->get();

        // Recent attendance records
        $attendance = Attendance::where('attendable_type', get_class($student))
            ->where('attendable_id', $student->id)
            ->latest('join_time')
            ->take(10)
            ->get();

        // Pull from service
        $attendanceStats   = $attendanceService->getAttendanceStats($student);
        $lifetimeBreakdown = $attendanceService->getLifetimeBreakdown($student);
        $monthlyStats      = $attendanceService->getMonthlyStats($student);

        return view('dashboard.student.index', [
            'student'                => $student,
            'ongoingClass'           => $ongoingClass,
            'upcoming'               => $upcoming,
            'nextClass'              => $nextClass,
            'notifications'          => $notifications,
            'attendance'             => $attendance,
            'attendanceStats'        => $attendanceStats,
            'presentCount'           => $lifetimeBreakdown['present'],
            'lateCount'              => $lifetimeBreakdown['late'],
            'absentCount'            => $lifetimeBreakdown['absent'],
            'subscription'           => $student->subscription,
            // 'subscription'           => $student->subscription,
            'lessonsThisMonth'       => $monthlyStats['lessonsThisMonth'],
            'monthTotalClasses'      => $monthlyStats['monthTotalClasses'],
            'monthPresentCount'      => $monthlyStats['monthPresentCount'],
            'monthAttendancePercent' => $monthlyStats['monthAttendancePercent'],
        ]);
        
    }
}

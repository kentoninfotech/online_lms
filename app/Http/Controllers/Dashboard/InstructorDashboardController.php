<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\Course;
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
        $user = Auth::user();
        $instructor = $user->instructor;

        // If instructor record doesn't exist, create it
        if (!$instructor) {
            $instructor = Instructor::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }

        // FOR DEMO PURPOSE REMOVE LATER
        $students = Student::all();

        // Today’s schedule
        $todayLessons = LessonOccurrence::with(['lesson.student.user', 'zoomSession'])
            ->whereHas('lesson', fn($q) => $q->where('instructor_id', $instructor->id))
            ->whereDate('scheduled_start', Carbon::today())
            ->orderBy('scheduled_start')
            ->get();

        // Ongoing lesson
        $ongoingClass = LessonOccurrence::with(['lesson.student.user', 'zoomSession'])
            ->whereHas('lesson', fn($q) => $q->where('instructor_id', $instructor->id))
            ->ongoing()
            ->orderBy('scheduled_start')
            ->first();

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
            'ongoingClass',
            'nextClass',
            'upcoming',
            'reschedules',
            'recentAttendance',
            'notifications',
            'lessonsThisMonth',
        ));
    }

    /**
     * Display all courses assigned to the instructor for management
     */
    public function myCourses()
    {
        $user = Auth::user();
        $instructor = $user->instructor;

        // If instructor record doesn't exist, create it
        if (!$instructor) {
            $instructor = Instructor::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }

        // Get all courses assigned to this instructor with pagination
        $courses = $instructor->courses()
            ->with(['category', 'activeInstructors', 'enrollees'])
            ->paginate(12);

        // Get stats
        $totalCourses = $instructor->courses()->count();
        $activeCourses = $instructor->activeCourses()->count();
        $totalEnrollees = $instructor->courses()
            ->withCount('enrollees')
            ->get()
            ->sum('enrollees_count');

        return view('dashboard.instructor.my-courses', compact(
            'instructor',
            'courses',
            'totalCourses',
            'activeCourses',
            'totalEnrollees'
        ));
    }
}
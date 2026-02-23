<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\ParentModel;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\plan;
use App\Models\LessonOccurrence;
use App\Models\RescheduleRequest;
use App\Models\Course;
use App\Models\Facilitator;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Top KPIs
        $totalStudents     = Student::count();
        $totalInstructors  = Instructor::count();
        $totalCourses      = Course::count();
        $totalTutors       = Facilitator::count();
        $activeSubs        = Subscription::where('status', 'active')->count();
        $pendingPayments   = Payment::where('status', 'pending')->count();

        // Upcoming lessons (next 7 days)
        $upcomingLessons = LessonOccurrence::with(['lesson.student', 'lesson.instructor', 'zoomSession'])
            ->where('scheduled_start', '>=', now())
            ->orderBy('scheduled_start')
            ->take(5)
            ->get();

        // Recent payments
        $recentPayments = Payment::with(['subscription.student', 'parent'])
            ->latest()
            ->take(5)
            ->get();

        // Pending reschedules
        $pendingReschedules = RescheduleRequest::with(['occurrence.lesson.student', 'occurrence.lesson.instructor'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Recent courses
        $recentCourses = Course::latest()->take(5)->get();

        // Recent tutors
        $recentTutors = Facilitator::latest()->take(5)->get();

        // Notifications
        $notifications = Auth::user()->notifications()->latest()->take(5)->get();

        return view('dashboard.admin.index', compact(
            'totalStudents',
            'totalInstructors',
            'totalCourses',
            'totalTutors',
            'activeSubs',
            'pendingPayments',
            'upcomingLessons',
            'recentPayments',
            'pendingReschedules',
            'recentCourses',
            'recentTutors',
            'notifications'
        ));
    }

    public function students()
    {
        $students = Student::with(['subscription.plan', 'parents'])->paginate(20);
        return view('dashboard.admin.students', compact('students'));
    }

    public function instructors()
    {
        $instructors = Instructor::withCount('lessons')->paginate(20);
        return view('dashboard.admin.instructors', compact('instructors'));
    }

    public function parents()
    {
        $parents = ParentModel::with('students')->paginate(20);
        return view('dashboard.admin.parents', compact('parents'));
    }

    public function subscriptions()
    {
        $subscriptions = Subscription::with(['student', 'plan'])->latest()->paginate(20);
        return view('dashboard.admin.subscriptions', compact('subscriptions'));
    }

    public function payments()
    {
        $parents = ParentModel::all();

        $payments = Payment::with(['subscription.student', 'parent'])
            ->latest()
            ->paginate(20);

        return view('dashboard.admin.payments', compact('payments', 'parents'));
    }

    public function reschedules()
    {
        $requests = RescheduleRequest::with([
                'occurrence.lesson.student.user',
                'occurrence.lesson.instructor.user',
                'requester'
            ])
            ->latest()
            ->paginate(20);

        return view('dashboard.admin.reschedules', compact('requests'));
    }

}

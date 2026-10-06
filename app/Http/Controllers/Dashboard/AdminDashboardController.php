<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SiteVisitor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
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
use Illuminate\Http\Request;

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

    /**
     * Fix missing course dates and venues
     */
    public function fixCourseDatesAndVenues()
    {
        try {
            Artisan::call('course:generate-dates-venues');
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => '✅ Course dates and venues generated successfully!',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Error generating course dates and venues: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function visitors(Request $request)
    {
        $visitorsQuery = SiteVisitor::query()
            ->when($request->filled('country'), function ($query) use ($request) {
                $query->where('country', $request->string('country'));
            })
            ->when($request->filled('from'), function ($query) use ($request) {
                $query->whereDate('visited_at', '>=', $request->date('from'));
            })
            ->when($request->filled('to'), function ($query) use ($request) {
                $query->whereDate('visited_at', '<=', $request->date('to'));
            })
            ->with('user')
            ->latest('visited_at');

        $visitors = $visitorsQuery->paginate(30)->withQueryString();

        $stats = [
            'total' => SiteVisitor::count(),
            'today' => SiteVisitor::whereDate('visited_at', today())->count(),
            'this_week' => SiteVisitor::whereBetween('visited_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'unique_countries' => SiteVisitor::whereNotNull('country')->distinct('country')->count('country'),
        ];

        $topCountries = SiteVisitor::query()
            ->whereNotNull('country')
            ->selectRaw('country, COUNT(*) as total_visits')
            ->groupBy('country')
            ->orderByDesc('total_visits')
            ->limit(10)
            ->get();

        $availableCountries = SiteVisitor::query()
            ->whereNotNull('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        return view('admin.visitors.index', compact('visitors', 'stats', 'topCountries', 'availableCountries'));
    }

}

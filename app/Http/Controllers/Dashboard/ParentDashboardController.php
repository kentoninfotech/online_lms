<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\AttendanceService;
use App\Models\RescheduleRequest;
use App\Models\LessonOccurrence;
use App\Models\Payment;
use App\Models\Subscription;
use Carbon\Carbon;

class ParentDashboardController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index()
    {
        $user = Auth::user();

        // Guard: must be a parent with parentModel
        if ($user->user_type !== 'parent' || ! $user->parent) {
            return redirect()->route('login') // adapt route to your main dashboard route
                ->with('success', 'You are not authorized to access the Parent Dashboard.');
        }

        $parent = $user->parent;
        $children = $parent->students ?? collect();

        // Determine selected child (query param or first child)
        $selectedId = request('child_id');
        $child = null;
        if ($children->isNotEmpty()) {
            if ($selectedId) {
                $child = $children->firstWhere('id', intval($selectedId)) ?? $children->first();
            } else {
                $child = $children->first();
            }
        }

        // Defaults (safe for blade)
        $upcoming = collect();
        $attendanceStats = collect();
        $presentCount = $lateCount = $absentCount = 0;
        $subscription = null;
        $notifications = $user->notifications()->latest()->take(5)->get();
        $monthTotalClasses = $monthPresentCount = $monthAttendancePercent = 0;
        $lessonsThisMonth = 0;
        $nextClass = null;

        // If a child exists, fill real data
        if ($child) {
            $upcoming = LessonOccurrence::whereHas('lesson', fn($q) => $q->where('student_id', $child->id))
                ->where('scheduled_start', '>=', now())
                ->orderBy('scheduled_start')
                ->take(5)
                ->get();

            $attendanceStats = $this->attendanceService->getAttendanceStats($child);
            $breakdown = $this->attendanceService->getLifetimeBreakdown($child);
            $presentCount = $breakdown['present'] ?? 0;
            $lateCount    = $breakdown['late'] ?? 0;
            $absentCount  = $breakdown['absent'] ?? 0;

            $monthly = $this->attendanceService->getMonthlyStats($child);
            $monthTotalClasses = $monthly['monthTotalClasses'] ?? 0;
            $monthPresentCount = $monthly['monthPresentCount'] ?? 0;
            $monthAttendancePercent = $monthly['monthAttendancePercent'] ?? 0;
            $lessonsThisMonth = $monthly['lessonsThisMonth'] ?? 0;

            // Next class (nearest future occurrence)
            $nextClass = LessonOccurrence::whereHas('lesson', fn($q) => $q->where('student_id', $child->id))
                ->where('scheduled_start', '>', now())
                ->orderBy('scheduled_start')
                ->first();

            $subscription = $child->subscription ?? null;
        }

        return view('dashboard.parent.index', compact(
            'parent',
            'children',
            'child',
            'upcoming',
            'attendanceStats',
            'presentCount',
            'lateCount',
            'absentCount',
            'subscription',
            'notifications',
            'monthTotalClasses',
            'monthPresentCount',
            'monthAttendancePercent',
            'lessonsThisMonth',
            'nextClass'
        ));
    }

    public function children()
    {
        $parent = Auth::user()->parent;
        $children = $parent->students()->with('subscription.plan')->get();

        return view('dashboard.parent.children', compact('parent', 'children'));
    }

    public function payments()
    {
        $parent = Auth::user()->parent;

        $payments = Payment::with(['subscription.plan', 'parent'])
            ->where('parent_id', $parent->id)
            ->latest()
            ->paginate(10);

        // Get all student IDs linked to this parent
        $studentIds = $parent->students->pluck('id');

        // Fetch only subscriptions for those students
        $subscriptions = Subscription::whereIn('student_id', $studentIds)
            ->select('id', 'student_id', 'plan_id', 'status')
            ->with(['plan:id,name,price', 'student:id,name'])
            ->get();
        

        return view('dashboard.parent.payments', compact('payments', 'subscriptions'));
    }

    public function upcoming()
    {
        $parent = Auth::user()->parent;

        // Fetch all upcoming lessons for all children
        $occurrences = LessonOccurrence::whereHas('lesson', function ($q) use ($parent) {
                $q->whereIn('student_id', $parent->students->pluck('id'));
            })
            ->where('scheduled_start', '>', now())
            ->with(['lesson.student.user', 'lesson.instructor.user'])
            ->orderBy('scheduled_start')
            ->paginate(20);

        return view('dashboard.parent.lessons', compact('occurrences'));
    }

    public function reschedules()
    {
        $parent = Auth::user()->parent;
        $requests = RescheduleRequest::whereHas('occurrence.lesson', function ($q) use ($parent) {
                $q->whereIn('student_id', $parent->students->pluck('id'));
            })
            ->with(['occurrence.lesson.student.user', 'occurrence.lesson.instructor.user'])
            ->latest()
            ->paginate(10);

        return view('dashboard.parent.reschedules', compact('requests'));
    }
}

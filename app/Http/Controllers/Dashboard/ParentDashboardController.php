<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\LessonOccurrence;
use App\Models\Student;
use App\Services\AttendanceService;

class ParentDashboardController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index()
    {
        $parent = Auth::user()->parent; // relation user -> parentModel
        $children = $parent->students;

        $childId = request('child_id', $children->first()?->id);
        $child   = $children->where('id', $childId)->first();

        if (! $child) {
            return view('dashboard.parent.index', compact('parent', 'children'));
        }

        // Upcoming lessons
        $upcoming = LessonOccurrence::whereHas('lesson', fn($q) => $q->where('student_id', $child->id))
            ->where('scheduled_start', '>=', now())
            ->orderBy('scheduled_start')
            ->take(5)
            ->get();

        // Attendance stats via service
        $attendanceStats   = $this->attendanceService->getAttendanceStats($child);
        $breakdown         = $this->attendanceService->getLifetimeBreakdown($child);
        $monthlyStats      = $this->attendanceService->getMonthlyStats($child);

        $subscription = $child->subscription;
        $notifications = $parent->user->notifications()->latest()->take(5)->get();

        return view('dashboard.parent.index', compact(
            'parent',
            'children',
            'child',
            'upcoming',
            'attendanceStats',
            'breakdown',
            'monthlyStats',
            'subscription',
            'notifications'
        ));
    }
}

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
use App\Models\Attendance;
use App\Models\Lesson;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Top KPIs
        $totalStudents     = Student::count();
        $totalInstructors  = Instructor::count();
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

        // Recent attendances
        $recentAttendances = Attendance::with([
            'occurrence.lesson.student',
            'occurrence.lesson.instructor',
            'attendable'
        ])->latest()->take(5)->get();

        // Notifications
        $notifications = Auth::user()->notifications()->latest()->take(5)->get();

        return view('dashboard.admin.index', compact(
            'totalStudents',
            'totalInstructors',
            'activeSubs',
            'pendingPayments',
            'upcomingLessons',
            'recentPayments',
            'pendingReschedules',
            'recentAttendances',
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

    public function statistics()
    {
        // Total counts
        $totalStudents = Student::count();
        $totalInstructors = Instructor::count();
        $totalParents = ParentModel::count();
        $totalLessons = Lesson::count();
        $totalLessonOccurrences = LessonOccurrence::count();

        // Attendance statistics
        $totalAttendances = Attendance::count();
        $presentAttendances = Attendance::where('status', 'present')->count();
        $absentAttendances = Attendance::where('status', 'absent')->count();
        $lateAttendances = Attendance::where('status', 'late')->count();

        // Calculate attendance rate percentage
        $attendanceRate = $totalAttendances > 0
            ? round(($presentAttendances / $totalAttendances) * 100, 2)
            : 0;

        // Calculate impact percentage
        // Impact = (Total attendances / (Total lesson occurrences * Total students)) * 100
        $maxPossibleAttendances = $totalLessonOccurrences > 0 ? $totalLessonOccurrences : 1;
        $impactPercentage = $totalAttendances > 0
            ? round(($totalAttendances / $maxPossibleAttendances) * 100, 2)
            : 0;

        // Subscription and payment stats
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $expiredSubscriptions = Subscription::where('status', 'expired')->count();
        $totalRevenue = Payment::where('status', 'approved')->sum('amount');
        $pendingPayments = Payment::where('status', 'pending')->count();

        // Lesson completion rate
        $completedLessons = LessonOccurrence::where('status', 'completed')->count();
        $lessonCompletionRate = $totalLessonOccurrences > 0
            ? round(($completedLessons / $totalLessonOccurrences) * 100, 2)
            : 0;

        // Average attendance duration
        $avgDuration = Attendance::where('status', 'present')->avg('duration_minutes');
        $avgDuration = $avgDuration ? round($avgDuration, 2) : 0;

        // Get attendance trend by month (last 6 months)
        $attendanceTrend = Attendance::selectRaw('
            YEAR(created_at) as year,
            MONTH(created_at) as month,
            COUNT(*) as count,
            SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count
        ')
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupByRaw('YEAR(created_at), MONTH(created_at)')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        $stats = compact(
            'totalStudents',
            'totalInstructors',
            'totalParents',
            'totalLessons',
            'totalLessonOccurrences',
            'totalAttendances',
            'presentAttendances',
            'absentAttendances',
            'lateAttendances',
            'attendanceRate',
            'impactPercentage',
            'activeSubscriptions',
            'expiredSubscriptions',
            'totalRevenue',
            'pendingPayments',
            'lessonCompletionRate',
            'avgDuration',
            'attendanceTrend'
        );

        return view('dashboard.admin.statistics', $stats);
    }

    public function downloadStatisticsPdf()
    {
        // Total counts
        $totalStudents = Student::count();
        $totalInstructors = Instructor::count();
        $totalParents = ParentModel::count();
        $totalLessons = Lesson::count();
        $totalLessonOccurrences = LessonOccurrence::count();

        // Attendance statistics
        $totalAttendances = Attendance::count();
        $presentAttendances = Attendance::where('status', 'present')->count();
        $absentAttendances = Attendance::where('status', 'absent')->count();
        $lateAttendances = Attendance::where('status', 'late')->count();

        // Calculate attendance rate percentage
        $attendanceRate = $totalAttendances > 0
            ? round(($presentAttendances / $totalAttendances) * 100, 2)
            : 0;

        // Calculate impact percentage
        $maxPossibleAttendances = $totalLessonOccurrences > 0 ? $totalLessonOccurrences : 1;
        $impactPercentage = $totalAttendances > 0
            ? round(($totalAttendances / $maxPossibleAttendances) * 100, 2)
            : 0;

        // Subscription and payment stats
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $expiredSubscriptions = Subscription::where('status', 'expired')->count();
        $totalRevenue = Payment::where('status', 'approved')->sum('amount');
        $pendingPayments = Payment::where('status', 'pending')->count();

        // Lesson completion rate
        $completedLessons = LessonOccurrence::where('status', 'completed')->count();
        $lessonCompletionRate = $totalLessonOccurrences > 0
            ? round(($completedLessons / $totalLessonOccurrences) * 100, 2)
            : 0;

        // Average attendance duration
        $avgDuration = Attendance::where('status', 'present')->avg('duration_minutes');
        $avgDuration = $avgDuration ? round($avgDuration, 2) : 0;

        $stats = compact(
            'totalStudents',
            'totalInstructors',
            'totalParents',
            'totalLessons',
            'totalLessonOccurrences',
            'totalAttendances',
            'presentAttendances',
            'absentAttendances',
            'lateAttendances',
            'attendanceRate',
            'impactPercentage',
            'activeSubscriptions',
            'expiredSubscriptions',
            'totalRevenue',
            'pendingPayments',
            'lessonCompletionRate',
            'avgDuration'
        );

        $pdf = Pdf::loadView('dashboard.admin.statistics-pdf', $stats);
        return $pdf->download('admin-statistics-' . now()->format('Y-m-d') . '.pdf');
    }

    public function attendances()
    {
        $status = request('status');
        
        $query = Attendance::with([
            'occurrence.lesson.student',
            'occurrence.lesson.instructor',
            'attendable'
        ])->latest();

        if ($status && in_array($status, ['present', 'absent', 'late'])) {
            $query->where('status', $status);
        }

        $attendances = $query->paginate(500);

        return view('dashboard.admin.attendances', compact('attendances', 'status'));
    }

    public function bulkUpdateAttendances()
    {
        $ids = request('attendance_ids', []);
        $status = request('status');

        if (empty($ids) || !in_array($status, ['present', 'absent', 'late'])) {
            return response()->json(['error' => 'Invalid data'], 422);
        }

        $attendances = Attendance::whereIn('id', $ids)->with('occurrence')->get();

        foreach ($attendances as $attendance) {
            $updateData = ['status' => $status];

            // If marking as present, auto-generate join/leave times
            if ($status === 'present' && $attendance->occurrence) {
                $joinTime = $attendance->occurrence->scheduled_start;
                
                // Subtract 2-5 minutes from lesson duration for leave time
                $minutesToSubtract = rand(2, 5);
                $lessonDuration = $attendance->occurrence->duration_minutes;
                $leaveDuration = $lessonDuration - $minutesToSubtract;
                $leaveTime = $joinTime->copy()->addMinutes($leaveDuration);
                
                $updateData['join_time'] = $joinTime;
                $updateData['leave_time'] = $leaveTime;
                $updateData['duration_minutes'] = $leaveDuration;
            }

            $attendance->update($updateData);
        }

        return response()->json(['success' => true, 'message' => 'Attendance updated successfully']);
    }

}

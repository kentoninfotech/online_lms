<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LessonOccurrence;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    public function index()
    {
        // $student = [
        //     'id' => 1,
        //     'name' => 'John Doe',
        //     'email' => 'example@g.com',
        //     'subscription' => 'Premium',
        // ];
        $student = Auth::user()->student;

        // Upcoming lessons (next 7 days)
        $upcoming = LessonOccurrence::whereHas('lesson', fn($q) => $q->where('student_id', $student->id))
            ->where('scheduled_start', '>=', Carbon::now())
            ->orderBy('scheduled_start')
            ->take(5)
            ->get();

        // Past attendance
        $attendance = Attendance::where('attendable_type', get_class($student))
            ->where('attendable_id', $student->id)
            ->latest('join_time')
            ->take(10)
            ->get();

        // Attendance % (grouped monthly)
        $attendanceStats = Attendance::where('attendable_type', get_class($student))
            ->where('attendable_id', $student->id)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total, SUM(status="present") as present')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($row) => [
                'month'    => $row->month,
                'percent'  => $row->total > 0 ? round(($row->present / $row->total) * 100, 1) : 0,
            ]);

        // Subscription
        $subscription = $student->subscription;

        return view('dashboard.student.index', compact('student', 'upcoming', 'attendance', 'attendanceStats', 'subscription'));
    }
}

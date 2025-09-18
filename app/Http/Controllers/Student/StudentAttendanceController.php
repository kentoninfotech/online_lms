<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class StudentAttendanceController extends Controller
{
    public function attendance()
    {
        $student = Auth::user()->student;

        // Past attendance
        $attendance = Attendance::where('attendable_type', get_class($student))
            ->where('attendable_id', $student->id)
            ->latest('join_time')
            ->paginate(10);

        return view('dashboard.student.attendance', compact('attendance'));
    }
}

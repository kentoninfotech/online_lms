<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LessonOccurrence;
use App\Services\AttendanceService;

class AttendanceController extends Controller
{
    /**
     * Manually finalize attendance for a lesson occurrence.
     */
    public function finalize(LessonOccurrence $occurrence, AttendanceService $attendanceService)
    {
        $attendanceService->finalize($occurrence);

        return redirect()
               ->back()
               ->with('success', "Attendance finalized for occurrence #{$occurrence->id}");
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Manually finalize attendance for a lesson occurrence.
     */
    public function finalize(LessonOccurrence $occurrence, AttendanceService $attendanceService): RedirectResponse
    {
        $attendanceService->finalize($occurrence);

        return redirect()
            ->back()
            ->with('success', "Attendance finalized for occurrence #{$occurrence->id}");
    }
}

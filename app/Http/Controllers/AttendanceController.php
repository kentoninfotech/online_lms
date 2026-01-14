<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LessonOccurrence;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Update attendance status
     */
    public function updateStatus(Request $request, Attendance $attendance)
    {
        // Verify the instructor owns this attendance record
        if ($attendance->occurrence->lesson->instructor_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:present,absent,late,rescheduled'
        ]);

        $attendance->update(['status' => $validated['status']]);

        return response()->json(['success' => true, 'message' => 'Attendance status updated']);
    }

    /**
     * Save lesson report
     */
    public function saveReport(Request $request, Attendance $attendance)
    {
        // Verify the instructor owns this attendance record
        if ($attendance->occurrence->lesson->instructor_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'report' => 'required|string|min:10'
        ]);

        $attendance->update(['raw' => $validated['report']]);

        return response()->json(['success' => true, 'message' => 'Report saved successfully']);
    }

    /**
     * Get lesson report
     */
    public function getReport(Request $request, Attendance $attendance)
    {
        // Verify the instructor owns this attendance record
        if ($attendance->occurrence->lesson->instructor_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(['report' => $attendance->raw]);
    }

}

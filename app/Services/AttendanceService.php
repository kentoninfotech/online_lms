<?php

namespace App\Services;

use App\Models\LessonOccurrence;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Instructor;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Finalize attendance for a given occurrence.
     */
    public function finalize(LessonOccurrence $occurrence): void
    {
        $lesson = $occurrence->lesson;
        $scheduledStart = $occurrence->scheduled_start;
        $scheduledEnd   = $scheduledStart->copy()->addMinutes($occurrence->duration_minutes);

        // Grace + threshold (dynamic from settings)
        $lateGrace     = (int) setting('attendance_grace_period_minutes', 10);
        $minThreshold  = (int) setting('attendance_min_percentage', 0); // 0 = disabled

        // --- Check Student ---
        $this->evaluateUserAttendance(
            $occurrence,
            $lesson->student_id,
            Student::class,
            $scheduledStart,
            $scheduledEnd,
            $lateGrace,
            $minThreshold
        );

        // --- Check Instructor ---
        $this->evaluateUserAttendance(
            $occurrence,
            $lesson->instructor_id,
            Instructor::class,
            $scheduledStart,
            $scheduledEnd,
            $lateGrace,
            $minThreshold,
            true // instructor flag
        );

        $occurrence->update(['status' => 'completed']);
        Log::info("Attendance finalized for occurrence {$occurrence->id}");
    }

    /**
     * Evaluate attendance record for one participant (student/instructor).
     */
    private function evaluateUserAttendance(
        LessonOccurrence $occurrence,
        ?int $userId,
        string $userType,
        Carbon $scheduledStart,
        Carbon $scheduledEnd,
        int $lateGrace,
        int $minThreshold,
        bool $isInstructor = false
    ): void {
        if (!$userId) return;

        $attendance = Attendance::where('lesson_occurrence_id', $occurrence->id)
            ->where('attendable_type', $userType)
            ->where('attendable_id', $userId)
            ->first();

        if (!$attendance) {
            // No attendance record, mark as absent
            Attendance::create([
                'lesson_occurrence_id' => $occurrence->id,
                'attendable_type'      => $userType,
                'attendable_id'        => $userId,
                'status'               => 'absent',
            ]);

            if ($isInstructor) {
                Log::warning("Instructor absent for occurrence {$occurrence->id}");
            }
            return;
        }

        // calculate join/leave & attendance %
        $join = $attendance->join_time;
        $leave = $attendance->leave_time ?? $scheduledEnd;
        $duration = $join && $leave ? $join->diffInMinutes($leave) : 0;
        $attendance->duration_minutes = $duration;

        // --- Late? ---
        if ($join && $join->gt($scheduledStart->copy()->addMinutes($lateGrace))) {
            $attendance->status = 'late';
        } else {
            $attendance->status = $attendance->status ?? 'present';
        }

        //  attendance % Threshold? (only if not absent/late)
        $expectedDuration = $scheduledStart->diffInMinutes($scheduledEnd);
        if ($expectedDuration > 0 && $minThreshold > 0) {
            $percentage = ($duration / $expectedDuration) * 100;
            if ($percentage < $minThreshold) {
                $attendance->status = 'absent';
            }
        }

        $attendance->save();
    }
}

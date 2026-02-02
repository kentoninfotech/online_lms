<?php

namespace App\Services;

use App\Models\LessonOccurrence;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
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
        $lateGrace    = (int) Setting::where('key','attendance_grace_period_minutes')->first()->value ?? 10;
        $minThreshold = (int) Setting::where('key','attendance_min_percentage')->first()->value ?? 0;

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
                
                Attendance::updateOrCreate(
                [
                    'lesson_occurrence_id' => $occurrence->id,
                    'attendable_id' => $userId,
                    'attendable_type' => $userType,
                ],[
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

    /**
     * Get monthly attendance % stats (line chart).
     */
    public function getAttendanceStats($attendable): Collection
    {
        $attendanceStats = Attendance::selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as ym,
                DATE_FORMAT(created_at, '%b %Y') as month,
                ROUND(SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) / COUNT(*) * 100, 1) as percent,
                MIN(created_at) as sort_date
            ")
            ->where('attendable_type', get_class($attendable))
            ->where('attendable_id', $attendable->id)
            ->groupBy('ym', 'month')
            ->orderBy('sort_date')
            ->get()
            ->keyBy('ym');

        $start = Attendance::where('attendable_type', get_class($attendable))
            ->where('attendable_id', $attendable->id)
            ->min('created_at');

        if (!$start) {
            return collect(); // no records
        }

        $end = now();
        $period = Carbon::parse($start)->startOfMonth()->monthsUntil($end);

        $filledStats = collect();
        foreach ($period as $date) {
            $ym = $date->format('Y-m');
            if ($attendanceStats->has($ym)) {
                $filledStats->push($attendanceStats[$ym]);
            } else {
                $filledStats->push((object)[
                    'ym'      => $ym,
                    'month'   => $date->format('M Y'),
                    'percent' => 0,
                ]);
            }
        }

        return $filledStats;
    }

    /**
     * Get overall breakdown (present, late, absent).
     */
    public function getLifetimeBreakdown($attendable): array
    {
        return [
            'present' => Attendance::where('attendable_type', get_class($attendable))
                ->where('attendable_id', $attendable->id)
                ->where('status', 'present')
                ->count(),
            'late' => Attendance::where('attendable_type', get_class($attendable))
                ->where('attendable_id', $attendable->id)
                ->where('status', 'late')
                ->count(),
            'absent' => Attendance::where('attendable_type', get_class($attendable))
                ->where('attendable_id', $attendable->id)
                ->where('status', 'absent')
                ->count(),
        ];
    }

    /**
     * Get monthly summary stats (for current month).
     */
    public function getMonthlyStats($attendable): array
    {
        $monthStart = now()->startOfMonth();
        $monthEnd   = now()->endOfMonth();

        $monthAttendances = Attendance::where('attendable_type', get_class($attendable))
            ->where('attendable_id', $attendable->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->get();

        $lessonsThisMonth = LessonOccurrence::whereHas('lesson', function (Builder $q) use ($attendable) {
            // Check if the user is a student or an instructor
            $column = ($attendable->user_type === 'student') ? 'student_id' : 'instructor_id';
            $q->where($column, $attendable->id);
        })
        ->whereBetween('scheduled_start', [$monthStart, $monthEnd])
        ->count();


        // 1. Determine the filtering column and ID based on the user type
        $column = $attendable->user_type === 'student' ? 'student_id' : 'instructor_id';
        $attendableId = $attendable->id;

        // 2. Get the IDs of the relevant lessons
        // Assuming 'Lesson' is the correct model name
        $relevantLessonIds = \App\Models\Lesson::query()
            ->where($column, $attendableId)
            ->pluck('id');

        // 3. Query the LessonOccurrences using whereIn for efficiency
        $lessonsThisMonth = LessonOccurrence::whereIn('lesson_id', $relevantLessonIds) // Efficiently filters by related lesson IDs
            ->whereBetween('scheduled_start', [$monthStart, $monthEnd])
            ->count();


        $monthTotalClasses   = $monthAttendances->count();
        $monthPresentCount   = $monthAttendances->where('status', 'present')->count();
        $monthAttendancePercent = $monthTotalClasses > 0
            ? round(($monthPresentCount / $monthTotalClasses) * 100, 1)
            : 0;

        return [
            'monthTotalClasses'      => $monthTotalClasses,
            'monthPresentCount'      => $monthPresentCount,
            'monthAttendancePercent' => $monthAttendancePercent,
            'lessonsThisMonth'       => $lessonsThisMonth,
        ];
    }

}

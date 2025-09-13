<?php

namespace App\Services;

use App\Models\LessonOccurrence;
use App\Services\ZoomService;
use App\Models\RescheduleRequest;
use App\Models\RescheduleUsage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Admin\AdminRescheduleApprovalRequired;
use App\Notifications\Admin\AdminRescheduleAutoApproved;
use App\Notifications\Admin\InstructorDecisionNoticeToAdmin;
use App\Notifications\Instructor\InstructorRescheduleApprovalRequired;
use App\Notifications\Instructor\InstructorRescheduleAutoApproved;
use App\Notifications\Instructor\AdminDecisionNoticeToInstructor;
use App\Notifications\Parent\ParentRescheduleDecision;
use App\Notifications\Student\StudentRescheduleDecision;
use App\Jobs\CreateZoomSession;

class RescheduleService
{
    /**
     * Handle a reschedule request
     */
    public function requestReschedule(LessonOccurrence $occurrence, User $requester, Carbon $proposedStart, string $reason): RescheduleRequest
    {
        return DB::transaction(function () use ($occurrence, $requester, $proposedStart, $reason) {
            // Guard time check
            $guardMinutes = (int) (Setting::where('key', 'reschedule_guard_time_minutes')->value('value') ?? 120);
            // Use diffInMinutes with signed value (negative if scheduled_start < now)
            $minutesUntilStart = now()->diffInMinutes($occurrence->scheduled_start, false);
            if ($minutesUntilStart < $guardMinutes) {
                throw new \Exception("Cannot reschedule within {$guardMinutes} minutes of start time");
            }

            // Get Student - subscription plan (assume lesson belongs to student)
            $student = $occurrence->lesson->student;
            $plan = $student->subscription?->plan;
            // Reschedule limit from plan or global setting
            $limit = $plan?->reschedule_limit ?? Setting::where('key','reschedule_limit')->value('value');

            // Get or create usage record
            $usage = $this->getActiveUsage($student->id, $plan?->id, $plan?->cycle);

            // Create request record
            $request = RescheduleRequest::create([
                'lesson_occurrence_id' => $occurrence->id,
                'requested_by' => $requester->id,
                'proposed_start' => $proposedStart,
                'reason' => $reason,
                'status' => 'pending',
            ]);

            // Load relations for notifications
            $request->load(['occurrence.lesson', 'requester']);

            // If within limit → auto approve
            if ($usage->reschedule_count < $limit) {
                $this->approveRequest($request, auto: true);
                $usage->increment('reschedule_count');

                // Notify Instructor + Admin only
                $instructor = $occurrence->lesson->instructor?->user;
                $admin = User::where('user_type', 'admin')->first();

                if ($instructor) $instructor->notify(new InstructorRescheduleAutoApproved($request));
                if ($admin) $admin->notify(new AdminRescheduleAutoApproved($request));
            } else {
                // Manual approval → notify both Admin + Instructor
                $admin = User::where('user_type', 'admin')->first();
                $instructor = $occurrence->lesson->instructor?->user;

                if ($admin) $admin->notify(new AdminRescheduleApprovalRequired($request));
                if ($instructor) $instructor->notify(new InstructorRescheduleApprovalRequired($request));
            }

            return $request;
        });
    }

    /**
     * Approve request (manual)
     */
    public function approveRequest(RescheduleRequest $request, bool $auto = false, ?User $approver = null): void
    {
        $request->update([
            'status'      => 'approved',
            'approved_by' => $approver?->id,
        ]);

        // Update occurrence time
        $occurrence = $request->occurrence;
        // Double check occurrence exists
        if (! $occurrence) {
            return;
        }
        // Update scheduled start time
        $occurrence->update([
            'scheduled_start' => $request->proposed_start,
        ]);

        // Recreate Zoom meeting
        if ($occurrence->zoomSession) {
            // delete existing zoom meeting and DB record
            app(ZoomService::class)->deleteMeeting($occurrence->zoomSession->zoom_meeting_id);
            // remove record so that CreateZoomSession job can create a new one
            // (cannot reuse existing ZoomSession record as meeting ID has changed)
            $occurrence->zoomSession->delete();
            CreateZoomSession::dispatch($occurrence);
        }
  
        // Notify relevant parties if not auto-approved
        if (! $auto) {
            // Notify Parent + Student + Instructor + Admin
            $instructor = $occurrence->lesson->instructor?->user;
            $student = $occurrence->lesson->student?->user;
            $parent = $occurrence->lesson->student->parents()->first()?->user;
            $admin = User::where('user_type','admin')->first();

            // Notify Instructor only if not the approver
            if ($instructor && $instructor->id !== $approver?->id) {
                $instructor->notify(new AdminDecisionNoticeToInstructor($request, 'approved', $admin?->name ?? 'System'));
            }
            // Notify Admin only if not the approver
            if ($admin && $admin->id !== $approver?->id) {
                $admin->notify(new InstructorDecisionNoticeToAdmin($request, 'approved', $instructor?->name ?? 'Instructor'));
            }
            // Notify Student + Parent 
            if ($student) $student->notify(new StudentRescheduleDecision($request, 'approved'));
            if ($parent) $parent->notify(new ParentRescheduleDecision($request, 'approved'));
        }
    }

    /**
     * Reject request
     */
    public function rejectRequest(RescheduleRequest $request, ?User $approver = null, ?string $reason = null): void
    {
        $request->update([
            'status'          => 'rejected',
            'approved_by'     => $approver?->id,
            'decision_reason' => $reason,
        ]);

        $occurrence = $request->occurrence;
        $instructor = $occurrence->lesson->instructor?->user;
        $student = $occurrence->lesson->student?->user;
        $parent = $occurrence->lesson->student->parents()->first()?->user;
        $admin = User::where('user_type','admin')->first();

        // Notify Instructor only if not the approver
        if ($instructor && $instructor->id !== $approver?->id) {
            $instructor->notify(new AdminDecisionNoticeToInstructor($request, 'rejected', $admin?->name ?? 'System'));
        }
        // Notify Admin only if not the approver
        if ($admin && $admin->id !== $approver?->id) {
            $admin->notify(new InstructorDecisionNoticeToAdmin($request, 'rejected', $instructor?->name ?? 'Instructor'));
        }
        // Notify Student + Parent 
        if ($student) $student->notify(new StudentRescheduleDecision($request, 'rejected'));
        if ($parent) $parent->notify(new ParentRescheduleDecision($request, 'rejected'));
    }

    /**
     * Get or create active usage record
     */
    private function getActiveUsage(int $studentId, ?int $planId, ?string $cycle): RescheduleUsage
    {
        $now = now();

        [$periodStart, $periodEnd] = match ($cycle) {
            'daily'     => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'weekly'    => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'monthly'   => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            default     => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };

        return RescheduleUsage::query()
        ->where('student_id', $studentId)
        ->where('plan_id', $planId)
        ->where('period_start', $periodStart)
        ->where('period_end', $periodEnd)
        ->first()
        ?? RescheduleUsage::create([
            'student_id'       => $studentId,
            'plan_id'          => $planId,
            'period_start'     => $periodStart,
            'period_end'       => $periodEnd,
            'reschedule_count' => 0,
        ]);
    }
}

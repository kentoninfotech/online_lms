<?php

namespace App\Jobs;

use App\Models\ZoomSession;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Instructor;
use App\Services\ZoomService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Admin\WebhookFallbackDetected;
use App\Notifications\Admin\JobFailedNotification;
use App\Models\User;


class SyncZoomParticipantsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Max attempts and backoff strategy
    public $tries = 3;
    public $backoff = [60, 300, 900]; // seconds: 1m, 5m, 15m

    /**     
     * Create a new job instance.
     */
    public function __construct(public ZoomSession $session, public bool $fallbackTriggered = false) {}

    /**
     * Execute the job.
     */
    public function handle(ZoomService $zoom)
    {
        if (!$this->session->zoom_meeting_id) return;

        try {
            $next = null;
            do {
                $res = $zoom->listPastParticipants($this->session->zoom_meeting_id, $next);

                foreach ($res['participants'] ?? [] as $p) {
                    $zoomUserId = $p['id'] ?? $p['user_id'] ?? null;
                    $email = strtolower($p['user_email'] ?? $p['email'] ?? '');
                    $join = isset($p['join_time']) ? Carbon::parse($p['join_time']) : null;
                    $leave = isset($p['leave_time']) ? Carbon::parse($p['leave_time']) : null;
                    $dur = $p['duration'] ?? ($join && $leave ? $join->diffInMinutes($leave) : null);

                    // attempt to find attendable by zoom user id or email
                    $attendable = null;
                    if ($zoomUserId) {
                        $attendable = Student::where('zoom_user_id', $zoomUserId)->first() ?: Instructor::where('zoom_user_id', $zoomUserId)->first();
                    }
                    if (!$attendable && !empty($email)) {
                        $attendable = Student::where('email', $email)->first() ?: Instructor::where('email', $email)->first();
                    }

                    $attendance = Attendance::firstOrNew([
                        'lesson_occurrence_id' => $this->session->lesson_occurrence_id,
                        'zoom_user_id' => $zoomUserId,
                    ]);

                    if ($attendable) {
                        $attendance->attendable_type = get_class($attendable);
                        $attendance->attendable_id = $attendable->id;
                    }

                    if ($join) $attendance->join_time = $join;
                    if ($leave) $attendance->leave_time = $leave;
                    if ($dur !== null) $attendance->duration_minutes = (int)$dur;
                    $attendance->status = 'present';
                    $attendance->raw = $p;
                    $attendance->save();
                }

                $next = $res['next_page_token'] ?? null;
            } while (!empty($next));

            // mark session ended after successful sync
            $this->session->update(['status' => 'ended']);

            if ($this->fallbackTriggered) {
                Log::warning("Fallback sync performed for ZoomSession {$this->session->id} (meeting {$this->session->zoom_meeting_id})");
                $admin = User::where('user_type', 'admin')->first();
                if ($admin) Notification::send($admin, new WebhookFallbackDetected($this->session));
            }
        } catch (\Throwable $e) {
            Log::error("SyncZoomParticipantsJob failed for session {$this->session->id}: " . $e->getMessage());

            // If rate-limited (429), let the job be retried according to $backoff
            if (method_exists($e, 'getCode') && $e->getCode() === 429) {
                // rethrow to trigger retry/backoff
                throw $e;
            }

            // on unexpected failure, rethrow to let Laravel handle retries and failed()
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::critical("Zoom sync job permanently failed for session {$this->session->id}: " . $exception->getMessage());
        $admin = \App\Models\User::where('user_type', 'admin')->first();
        if ($admin) Notification::send($admin, new JobFailedNotification(
            "Zoom sync job failed for session {$this->session->id}",
            $exception->getMessage()
        ));
    }
}

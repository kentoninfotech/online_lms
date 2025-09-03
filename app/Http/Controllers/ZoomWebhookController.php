<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZoomSession;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Instructor;
use App\Services\ZoomService;
use App\Services\AttendanceService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ZoomWebhookController extends Controller
{
    public function __construct(private ZoomService $zoom) {}

    /**
     * Handle incoming Zoom webhook
     */
    public function handle(Request $request, AttendanceService $attendanceService)
    {
        $raw = $request->getContent();

        // Accept common header names
        $signature = $request->header('x-zm-signature')
            ?: $request->header('x-zoom-signature')
            ?: $request->header('x-zm-signature-256');

        // Skip verification in testing (to allow fake webhooks)
        // REVIEW: in production, this should never be skipped
        if (! app()->environment('testing')) {
            if (! $this->zoom->verifyWebhook($raw, $signature)) {
                Log::warning('Invalid Zoom webhook signature');
                return response()->json(['ok' => false], 401);
            }
        }

        $event = $request->input('event');
        $payload = $request->input('payload', []);
        $meetingId = (string) data_get($payload, 'object.id');

        if (! $meetingId) {
            Log::info('Zoom webhook without meeting id');
            return response()->json(['ok' => true]);
        }

        $session = ZoomSession::where('zoom_meeting_id', $meetingId)->first();
        if (! $session) {
            Log::info("Zoom webhook for meeting {$meetingId} but no local ZoomSession found");
            return response()->json(['ok' => true]);
        }

        try {
            match ($event) {
                'meeting.started'     => $session->update(['status' => 'started']),
                'meeting.ended'       => function () use ($session, $attendanceService) {
                    $session->update(['status' => 'ended']);
                    // Finalize attendance
                    $occ = $session->occurrence;
                    if ($occ) {
                        $attendanceService->finalize($occ);
                    }
                },
                'participant.joined'  => $this->participantJoined($session, data_get($payload, 'object.participant', [])),
                'participant.left'    => $this->participantLeft($session, data_get($payload, 'object.participant', [])),
                default               => Log::info("Unhandled Zoom event: {$event}")
            };
        } catch (\Throwable $e) {
            Log::error('Zoom webhook handler error: ' . $e->getMessage());
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Extract Zoom identifiers safely
     */
    private function extractZoomIdentifiers(array $p): array
    {
        return [
            'zoom_user_id' => $p['id'] ?? $p['user_id'] ?? null,
            'email'        => strtolower($p['email'] ?? $p['user_email'] ?? ''),
            'join_time'    => isset($p['join_time']) ? Carbon::parse($p['join_time']) : null,
            'leave_time'   => isset($p['leave_time']) ? Carbon::parse($p['leave_time']) : null,
        ];
    }

    /**
     * Handle participant joined event
     */
    private function participantJoined(ZoomSession $session, array $p)
    {
        $ids = $this->extractZoomIdentifiers($p);
        $zoomUserId = $ids['zoom_user_id'];
        $email      = $ids['email'];
        $join       = $ids['join_time'] ?? now();

        $attendable = $this->findAttendable($zoomUserId, $email);

        // Auto-save zoom_user_id if found by email only
        if ($attendable && $zoomUserId && ! $attendable->zoom_user_id) {
            $attendable->zoom_user_id = $zoomUserId;
            $attendable->save();
        }

        $attendance = Attendance::firstOrNew([
            'lesson_occurrence_id' => $session->lesson_occurrence_id,
            'zoom_user_id'         => $zoomUserId,
        ]);

        if ($attendable) {
            $attendance->attendable_type = get_class($attendable);
            $attendance->attendable_id   = $attendable->id;
        }

        if (! $attendance->join_time) {
            $attendance->join_time = $join;
        }

        $attendance->raw = array_merge((array)$attendance->raw, $p);
        $attendance->save();
    }

    /**
     * Handle participant left event
     */
    private function participantLeft(ZoomSession $session, array $p)
    {
        $ids = $this->extractZoomIdentifiers($p);
        $zoomUserId = $ids['zoom_user_id'];
        $email      = $ids['email'];
        $leave      = $ids['leave_time'] ?? now();

        $attendable = $this->findAttendable($zoomUserId, $email);

        $attendance = Attendance::where('lesson_occurrence_id', $session->lesson_occurrence_id)
            ->where('zoom_user_id', $zoomUserId)
            ->first();

        if (! $attendance) {
            $attendance = new Attendance();
            $attendance->lesson_occurrence_id = $session->lesson_occurrence_id;
            $attendance->zoom_user_id         = $zoomUserId;

            if ($attendable) {
                $attendance->attendable_type = get_class($attendable);
                $attendance->attendable_id   = $attendable->id;
            }
        }

        $attendance->leave_time = $leave;
        if ($attendance->join_time) {
            $attendance->duration_minutes = $attendance->join_time->diffInMinutes($attendance->leave_time);
        }

        $attendance->raw    = array_merge((array)$attendance->raw, $p);
        $attendance->status = 'present';
        $attendance->save();
    }

    /**
     * Find attendable (Student or Instructor) by zoom user id or email
     */
    private function findAttendable(?string $zoomUserId, string $email)
    {
        // 1. Direct match by Zoom ID (preferred)
        if ($zoomUserId) {
            $att = Student::where('zoom_user_id', $zoomUserId)->first()
                ?: Instructor::where('zoom_user_id', $zoomUserId)->first();
            if ($att) {
                return $att;
            }
        }

        // 2. Fallback match by email
        if (!empty($email)) {
            $att = Student::where('email', $email)->first()
                ?: Instructor::where('email', $email)->first();

            // If found by email but no zoom_user_id yet → update it
            if ($att && $zoomUserId && empty($att->zoom_user_id)) {
                $att->zoom_user_id = $zoomUserId;
                $att->save();
            }

            return $att;
        }

        return null;
    }
}

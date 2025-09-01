<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZoomSession;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Instructor;
use App\Services\ZoomService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ZoomWebhookController extends Controller
{
    /**     
     * Create a new controller instance.
     */
    public function __construct(private ZoomService $zoom) {}

    /**
     * Handle incoming Zoom webhook
     */
    public function handle(Request $request)
    {
        $raw = $request->getContent();

        // Accept common header names; adapt as needed
        $signature = $request->header('x-zm-signature') ?: $request->header('x-zoom-signature') ?: $request->header('x-zm-signature-256');

        if (!$this->zoom->verifyWebhook($raw, $signature)) {
            Log::warning('Invalid Zoom webhook signature');
            return response()->json(['ok' => false], 401);
        }

        $event = $request->input('event');
        $payload = $request->input('payload', []);

        $meetingId = (string) data_get($payload, 'object.id');

        if (!$meetingId) {
            Log::info('Zoom webhook without meeting id');
            return response()->json(['ok' => true]);
        }

        $session = ZoomSession::where('zoom_meeting_id', $meetingId)->first();
        if (!$session) {
            Log::info("Zoom webhook for meeting {$meetingId} but no local ZoomSession found");
            return response()->json(['ok' => true]);
        }

        try {
            match ($event) {
                'meeting.started' => $session->update(['status' => 'started']),
                'meeting.ended' => $session->update(['status' => 'ended']),
                'participant.joined' => $this->participantJoined($session, data_get($payload, 'object.participant', [])),
                'participant.left' => $this->participantLeft($session, data_get($payload, 'object.participant', [])),
                default => Log::info("Unhandled Zoom event: {$event}")
            };
        } catch (\Throwable $e) {
            Log::error('Zoom webhook handler error: ' . $e->getMessage());
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Handle participant joined event
     */
    private function participantJoined(ZoomSession $session, array $p)
    {
        $zoomUserId = $p['id'] ?? $p['user_id'] ?? null;
        $email = strtolower($p['email'] ?? $p['user_email'] ?? '');
        $join = isset($p['join_time']) ? Carbon::parse($p['join_time']) : now();

        $attendable = $this->findAttendable($zoomUserId, $email);

        $attendance = Attendance::firstOrNew([
            'lesson_occurrence_id' => $session->lesson_occurrence_id,
            'zoom_user_id' => $zoomUserId,
        ]);

        if ($attendable) {
            $attendance->attendable_type = get_class($attendable);
            $attendance->attendable_id = $attendable->id;
        }
        $attendance->join_time = $join;
        $attendance->raw = array_merge((array)$attendance->raw, $p);
        $attendance->save();
    }

    /**
     * Handle participant left event
     */
    private function participantLeft(ZoomSession $session, array $p)
    {
        $zoomUserId = $p['id'] ?? $p['user_id'] ?? null;
        $email = strtolower($p['email'] ?? $p['user_email'] ?? '');
        $leave = isset($p['leave_time']) ? Carbon::parse($p['leave_time']) : now();

        $attendable = $this->findAttendable($zoomUserId, $email);

        $attendance = Attendance::where('lesson_occurrence_id', $session->lesson_occurrence_id)
            ->where('zoom_user_id', $zoomUserId)
            ->first();

        if (!$attendance) {
            // create a minimal record if we didn't see the join (fallback)
            $attendance = new Attendance();
            $attendance->lesson_occurrence_id = $session->lesson_occurrence_id;
            $attendance->zoom_user_id = $zoomUserId;
            if ($attendable) {
                $attendance->attendable_type = get_class($attendable);
                $attendance->attendable_id = $attendable->id;
            }
        }

        $attendance->leave_time = $leave;
        if ($attendance->join_time) {
            $attendance->duration_minutes = $attendance->join_time->diffInMinutes($attendance->leave_time);
        }
        $attendance->raw = array_merge((array)$attendance->raw, $p);
        $attendance->status = 'present';
        $attendance->save();
    }

    /**
     * Find attendable (Student or Instructor) by zoom user id or email
     */
    private function findAttendable(?string $zoomUserId, string $email)
    {
        if ($zoomUserId) {
            $att = \App\Models\Student::where('zoom_user_id', $zoomUserId)->first()
                ?: \App\Models\Instructor::where('zoom_user_id', $zoomUserId)->first();
            if ($att) return $att;
        }

        if (!empty($email)) {
            return \App\Models\Student::where('email', $email)->first()
                ?: \App\Models\Instructor::where('email', $email)->first();
        }

        return null;
    }
}

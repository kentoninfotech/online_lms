<?php

namespace App\Jobs;

use App\Models\LessonOccurrence;
use App\Models\ZoomSession;
use App\Services\ZoomService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateZoomSession implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public LessonOccurrence $occurrence;

    /**
     * Create a new job instance.
     */
    public function __construct(LessonOccurrence $occurrence)
    {
        $this->occurrence = $occurrence;
    }

    /**
     * Execute the job.
     */
    public function handle(ZoomService $zoom): void
    {
        try {
            // Skip if a ZoomSession already exists
            if ($this->occurrence->zoomSession) {
                Log::info("ZoomSession already exists for occurrence {$this->occurrence->id}");
                return;
            }

            $hostZoomUserId = optional($this->occurrence->lesson->instructor)->zoom_user_id;

            // Call Zoom API
            $meeting = $zoom->createMeeting($this->occurrence, $hostZoomUserId);

            // Persist in DB
            ZoomSession::updateOrCreate(
                ['lesson_occurrence_id' => $this->occurrence->id],
                [
                    'zoom_meeting_id' => $meeting['id'],
                    'join_url'        => $meeting['join_url'],
                    'start_url'       => $meeting['start_url'],
                    'password'        => $meeting['password'] ?? null,
                    'status'          => 'scheduled',
                    'raw'             => $meeting['raw'] ?? [],
                ]
            );

            Log::info("ZoomSession created for occurrence {$this->occurrence->id}");
        } catch (\Throwable $e) {
            Log::error("Failed to create Zoom meeting for occurrence {$this->occurrence->id}: " . $e->getMessage());
            $this->fail($e);
        }
    }
}

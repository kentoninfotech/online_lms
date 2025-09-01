<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LessonOccurrence;
use App\Models\Setting;
use App\Services\ZoomService;
use Carbon\Carbon;

class CreateZoomSessions extends Command
{
    protected $signature = 'lessons:create-zoom-sessions';
    protected $description = 'Create Zoom meetings for upcoming occurrences within horizon window';

    public function handle(ZoomService $zoom)
    {
        $horizon = (int) (Setting::where('key','zoom_meeting_horizon_days')->value('value') ?? 1);
        $cutoff = Carbon::now()->addDays($horizon);

        $occurrences = LessonOccurrence::whereBetween('scheduled_start', [now(), $cutoff])
            ->doesntHave('zoomSession')
            ->whereHas('lesson.instructor', fn($q) => $q->whereNotNull('zoom_user_id'))
            ->with('lesson.instructor')
            ->get();

        $this->info("Creating Zoom sessions for {$occurrences->count()} occurrences (horizon={$horizon}d).");

        foreach ($occurrences as $occ) {
            try {
                $hostZoomId = $occ->lesson->instructor->zoom_user_id ?? null;
                $resp = $zoom->createMeeting($occ, $hostZoomId);

                // create zoom session row
                $occ->zoomSession()->create([
                    'zoom_meeting_id' => (string)($resp['id'] ?? null),
                    'join_url' => $resp['join_url'] ?? null,
                    'start_url' => $resp['start_url'] ?? null,
                    'status' => 'scheduled',
                    'raw' => $resp,
                ]);

                $this->info("Created Zoom session for occurrence {$occ->id}");
            } catch (\Throwable $e) {
                $this->error("Failed to create Zoom meeting for occurrence {$occ->id}: " . $e->getMessage());
                // do not abort whole run
            }
        }
    }
}

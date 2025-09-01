<?php

namespace App\Observers;

use App\Models\LessonOccurrence;
use App\Services\ZoomService;

class LessonOccurrenceObserver
{
    public function created(LessonOccurrence $occurrence)
    {
        // Intentionally empty: Zoom sessions are created by CreateZoomSessions command (rolling horizon).
        // If you want immediate creation for dev, you can call ZoomService here.
        
        // Only create meeting if not already has one
        // if (!$occurrence->zoomSession) {
        //     app(ZoomService::class)->createMeeting($occurrence);
        // }
    }
}

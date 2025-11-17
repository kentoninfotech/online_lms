<?php

namespace App\Observers;

use App\Models\LessonOccurrence;
use App\Services\ZoomService;
use App\Jobs\CreateZoomSession;

class LessonOccurrenceObserver
{
    // public function created(LessonOccurrence $occurrence)
    // {
    //     // Intentionally empty: Zoom sessions are created by CreateZoomSessions command (rolling horizon).
    //     // If you want immediate creation for dev, you can call ZoomService here.
    //     dispatch(new CreateZoomSession($occurrence));
    // }
}

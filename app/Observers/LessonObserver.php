<?php

namespace App\Observers;

use App\Models\Lesson;
use App\Services\RecurrenceService;

class LessonObserver
{
    protected RecurrenceService $recurrenceService;

    public function __construct(RecurrenceService $recurrenceService)
    {
        $this->recurrenceService = $recurrenceService;
    }

    // public function created(Lesson $lesson): void
    // {
    //     $this->recurrenceService->generateOccurrences($lesson);
    // }

    // public function updated(Lesson $lesson): void
    // {
    //     if ($lesson->wasChanged(['start_time', 'duration_minutes', 'recurrence_type', 'recurrence_meta'])) {
    //         $lesson->occurrences()
    //             ->where('scheduled_start', '>=', now())
    //             ->delete();

    //         $this->recurrenceService->generateOccurrences($lesson);
    //     }
    // }
}


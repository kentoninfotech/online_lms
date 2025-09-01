<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\LessonOccurrence;
use Carbon\Carbon;

class RecurrenceService
{
    public function generateOccurrences(Lesson $lesson, ?int $horizonDays = null): void
    {
        if (!$horizonDays) {
            $horizonDays = $this->getPlanHorizon($lesson);
        }

        if ($lesson->recurrence_type === 'none' || !$lesson->recurrence_type) {
            $this->createSingleOccurrence($lesson);
            return;
        }

        $start       = Carbon::parse($lesson->start_time);
        $duration    = $lesson->duration_minutes;
        $endHorizon  = now()->addDays($horizonDays);
        $meta        = $lesson->recurrence_meta ? json_decode($lesson->recurrence_meta, true) : [];

        $occurrences = $this->expandRecurrence(
            $lesson->recurrence_type,
            $start,
            $duration,
            $meta,
            $endHorizon
        );

        foreach ($occurrences as $occ) {
            LessonOccurrence::updateOrCreate(
                [
                    'lesson_id'       => $lesson->id,
                    'scheduled_start' => $occ['start'],
                ],
                [
                    'duration_minutes' => $duration,
                    'status'           => 'scheduled',
                ]
            );
        }
    }

    private function createSingleOccurrence(Lesson $lesson): void
    {
        LessonOccurrence::updateOrCreate(
            [
                'lesson_id' => $lesson->id,
                'scheduled_start' => $lesson->start_time,
            ],
            [
                'duration_minutes' => $lesson->duration_minutes,
                'status' => 'scheduled',
            ]
        );
    }

    private function expandRecurrence(string $type, Carbon $start, int $duration, array $meta, Carbon $horizon): array
    {
        $events = [];
        $interval = $meta['interval'] ?? 1;
        $cursor = $start->copy();

        while ($cursor->lte($horizon)) {
            if ($type === 'daily') {
                $events[] = ['start' => $cursor->copy()];
                $cursor->addDays($interval);

            } elseif ($type === 'weekly') {
                $days = $meta['days'] ?? ['Monday'];
                foreach ($days as $day) {
                    $next = $cursor->copy()->next($day);
                    if ($next->lte($horizon)) {
                        $events[] = ['start' => $next];
                    }
                }
                $cursor->addWeeks($interval);

            } elseif ($type === 'monthly') {
                $dayOfMonth = $meta['day'] ?? $start->day;
                $next = $cursor->copy()->addMonths($interval)->day($dayOfMonth);
                if ($next->lte($horizon)) {
                    $events[] = ['start' => $next];
                }
                $cursor->addMonths($interval);

            } else {
                break;
            }
        }

        return $events;
    }

    private function getPlanHorizon(Lesson $lesson): int
    {
        $subscription = $lesson->student?->activeSubscription();
        return $subscription ? $subscription->plan->getHorizonDays() : 30;
    }
}

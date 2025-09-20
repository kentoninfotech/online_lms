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
        $meta        = $lesson->recurrence_meta ?? []; // ? json_decode($lesson->recurrence_meta, true) : [];

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
                'lesson_id'        => $lesson->id,
                'scheduled_start'  => $lesson->start_time,
            ],
            [
                'duration_minutes' => $lesson->duration_minutes,
                'status'           => 'scheduled',
            ]
        );
    }

    private function expandRecurrence(string $type, Carbon $start, int $duration, array $meta, Carbon $horizon): array
    {
        $events   = [];
        $interval = $meta['interval'] ?? 1;
        $count    = $meta['count'] ?? null; // max number of recurrences
        $cursor   = $start->copy();

        while ($cursor->lte($horizon)) {
            if ($type === 'daily') {
                $events[] = ['start' => $cursor->copy()];
                if ($count && count($events) >= $count) break;
                $cursor->addDays($interval);

            } elseif ($type === 'weekly') {
                $days = $meta['days'] ?? [$start->format('l')];

                foreach ($days as $day) {
                    // If the start date itself matches, include it once
                    if ($cursor->isSameDay($start) && $cursor->isDayOfWeek($day)) {
                        $events[] = ['start' => $cursor->copy()];
                        if ($count && count($events) >= $count) break 2;
                    }

                    // Otherwise find the next matching weekday
                    $next = $cursor->copy()->next($day);
                    if ($next->lte($horizon)) {
                        $events[] = ['start' => $next->copy()];
                        if ($count && count($events) >= $count) break 2;
                    }
                }

                $cursor->addWeeks($interval);

            } elseif ($type === 'monthly') {
                $dayOfMonth = $meta['day'] ?? $start->day;
                $next = $cursor->copy()->addMonths($interval)->day($dayOfMonth);

                if ($next->lte($horizon)) {
                    $events[] = ['start' => $next];
                    if ($count && count($events) >= $count) break;
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

<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\LessonOccurrence;
use App\Models\Setting;
use Carbon\Carbon;

class RecurrenceService
{
    /**
     * Generate occurrences for a given lesson up to the allowed horizon.
     */
    public function generateOccurrences(Lesson $lesson, ?int $horizonDays = null): void
    {
        // Determine absolute system horizon
        $endHorizon = $horizonDays
            ? now()->addDays($horizonDays)
            : $this->getPlanHorizon($lesson);

        // Handle non-recurring lessons
        if ($lesson->recurrence_type === 'none' || !$lesson->recurrence_type) {
            $this->createSingleOccurrence($lesson);
            return;
        }

        $start    = Carbon::parse($lesson->start_time);
        $duration = $lesson->duration_minutes;
        $meta     = $lesson->recurrence_meta ?? [];

        // Expand recurrence dates
        $occurrences = $this->expandRecurrence(
            $lesson->recurrence_type,
            $start,
            $duration,
            $meta,
            $endHorizon
        );

        // Create or update LessonOccurrence records
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

        /**
         * Final enforcement of allowed range.
         * If user set End Date, respect it but not beyond horizon.
         */
        $limitDate = ($meta['end_type'] ?? null) === 'date' && !empty($meta['end_date'])
            ? Carbon::parse($meta['end_date'])->min($endHorizon)
            : $endHorizon;

        // Remove future occurrences beyond this limit
        $this->removeExcessOccurrences($lesson, $limitDate);
    }

    /**
     * Create a single occurrence (for non-recurring lessons).
     */
    private function createSingleOccurrence(Lesson $lesson): void
    {
        LessonOccurrence::updateOrCreate(
            [
                'lesson_id'       => $lesson->id,
                'scheduled_start' => $lesson->start_time,
            ],
            [
                'duration_minutes' => $lesson->duration_minutes,
                'status'           => 'scheduled',
            ]
        );
    }

    /**
     * Expand recurrence pattern into concrete occurrences.
     */
    private function expandRecurrence(string $type, Carbon $start, int $duration, array $meta, Carbon $horizon): array
    {
        $events    = [];
        $interval  = $meta['interval'] ?? 1;
        $count     = $meta['count'] ?? null;
        $days      = $meta['days'] ?? [];
        $mode      = $meta['mode'] ?? 'day';
        $endType   = $meta['end_type'] ?? 'count';
        $endDate   = !empty($meta['end_date']) ? Carbon::parse($meta['end_date']) : null;

        // Determine actual upper limit
        $limitDate = $endType === 'date' && $endDate
            ? $endDate->copy()->min($horizon)
            : $horizon;

        switch ($type) {
            /** -----------------------------
             *  DAILY RECURRENCE
             *  count = number of days
             *  ----------------------------- */
            case 'daily':
                $cursor = $start->copy();
                $limit  = $endType === 'count'
                    ? ($count ?? ceil($start->diffInDays($limitDate) / $interval))
                    : PHP_INT_MAX;

                for ($i = 0; $i < $limit; $i++) {
                    if ($cursor->gt($limitDate)) break;
                    $events[] = ['start' => $cursor->copy()];
                    $cursor->addDays($interval);
                }
                break;

            /** -----------------------------
             *  WEEKLY RECURRENCE
             *  count = number of weeks
             *  days = weekdays (e.g. ['mon','wed'])
             *  ----------------------------- */
            case 'weekly':
                $map = [
                    'mon' => 'Monday', 'monday' => 'Monday',
                    'tue' => 'Tuesday', 'tues' => 'Tuesday', 'tuesday' => 'Tuesday',
                    'wed' => 'Wednesday', 'wednesday' => 'Wednesday',
                    'thu' => 'Thursday', 'thur' => 'Thursday', 'thursday' => 'Thursday',
                    'fri' => 'Friday', 'friday' => 'Friday',
                    'sat' => 'Saturday', 'saturday' => 'Saturday',
                    'sun' => 'Sunday', 'sunday' => 'Sunday',
                ];
                $days = collect($days)->map(fn($d) => $map[strtolower($d)] ?? ucfirst(strtolower($d)))->unique()->values()->all();
                if (empty($days)) $days = [$start->format('l')];

                $weeks = $endType === 'count'
                    ? ($count ?? ceil($start->diffInWeeks($limitDate) / $interval))
                    : PHP_INT_MAX;

                $currentWeekStart = $start->copy()->startOfWeek();

                for ($week = 0; $week < $weeks; $week++) {
                    foreach ($days as $day) {
                        $dayDate = (clone $currentWeekStart)->next($day)->setTimeFrom($start);

                        if ($week === 0 && $dayDate->lt($start)) continue;
                        if ($dayDate->gt($limitDate)) break 2;

                        $events[] = ['start' => $dayDate->copy()];
                    }
                    $currentWeekStart->addWeeks($interval)->startOfWeek();
                }
                break;

            /** -----------------------------
             *  MONTHLY RECURRENCE
             *  count = number of months
             *  mode  = "day" | "weekday"
             *  ----------------------------- */
            case 'monthly':
                $months = $endType === 'count'
                    ? ($count ?? ceil($start->diffInMonths($limitDate) / $interval))
                    : PHP_INT_MAX;

                for ($m = 0; $m < $months; $m++) {
                    $monthDate = $start->copy()->addMonths($m * $interval);
                    if ($monthDate->gt($limitDate)) break;

                    if ($mode === 'day') {
                        $target = $monthDate->copy()->day($start->day)->setTimeFrom($start);
                        if ($target->day !== $start->day) {
                            $target = $target->endOfMonth()->setTimeFrom($start);
                        }
                        $events[] = ['start' => $target];
                    } else {
                        // weekday mode (e.g., 2nd Monday of every month)
                        $weekday = $start->format('l');
                        $weekOfMonth = intdiv($start->day - 1, 7) + 1;

                        $target = $monthDate->copy()->startOfMonth()->modify("+" . ($weekOfMonth - 1) . " week")->next($weekday);

                        if ($target->month !== $monthDate->month) {
                            $target = $monthDate->copy()->startOfMonth()->modify("+" . ($weekOfMonth - 2) . " week")->next($weekday);
                        }

                        $target->setTimeFrom($start);
                        if ($target->lte($limitDate)) {
                            $events[] = ['start' => $target];
                        }
                    }
                }
                break;

            /** -----------------------------
             *  FALLBACK
             *  ----------------------------- */
            default:
                $events[] = ['start' => $start->copy()];
                break;
        }

        return collect($events)
            ->unique(fn($e) => $e['start']->timestamp)
            ->sortBy(fn($e) => $e['start'])
            ->values()
            ->all();
    }

    /**
     * Compute earliest valid horizon based on plan/subscription/system settings.
     */
    private function getPlanHorizon(Lesson $lesson): Carbon
    {
        $now = now();
        $defaultHorizonDays = (int) Setting::where('key', 'recurrence_horizon_days')->value('value') ?? 30;
        $defaultHorizon = $now->copy()->addDays($defaultHorizonDays);

        $subscription = $lesson->student?->activeSubscription()?->first();
        $planHorizonDays = $subscription?->plan?->getHorizonDays() ?? $defaultHorizonDays;
        $planHorizon = $now->copy()->addDays($planHorizonDays);
        $subscriptionEnd = $subscription?->end_date;

        $horizons = collect([$defaultHorizon, $planHorizon, $subscriptionEnd])
            ->filter()
            ->sort()
            ->values();

        return $horizons->first() ?? $defaultHorizon;
    }

    /**
     * Remove any occurrences beyond allowed limit.
     */
    private function removeExcessOccurrences(Lesson $lesson, Carbon $horizonEnd): void
    {
        LessonOccurrence::where('lesson_id', $lesson->id)
            ->where('scheduled_start', '>', $horizonEnd)
            ->delete();
    }
}

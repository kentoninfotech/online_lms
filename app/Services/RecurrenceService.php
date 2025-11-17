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
        $meta     = $this->sanitizeMeta($lesson->recurrence_meta ?? []);
        // $meta     = $lesson->recurrence_meta ?? [];

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
        $days      = $meta['days'] ?? [];
        $mode      = $meta['mode'] ?? 'day';
        $endType   = $meta['end_type'] ?? 'count';
        $count    = ($endType === 'count') ? ($meta['count'] ?? null) : null;
        $endDate  = ($endType === 'date' && !empty($meta['end_date'])) ? Carbon::parse($meta['end_date']) : null;

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
                    if ($cursor->gt($limitDate)) {
                        if ($cursor->isSameDay($limitDate)) {
                                $events[] = ['start' => $cursor->copy()];
                        }
                        break;
                    }

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
                $days = collect($days)
                    ->map(fn($d) => $map[strtolower($d)] ?? ucfirst(strtolower($d)))
                    ->unique()
                    ->values()
                    ->all();

                if (empty($days)) $days = [$start->format('l')];

                $weeks = $endType === 'count'
                    ? ($count ?? ceil($start->diffInWeeks($limitDate) / $interval))
                    : PHP_INT_MAX;

                $currentWeekStart = $start->copy()->startOfWeek();

                for ($week = 0; $week < $weeks; $week++) {
                    foreach ($days as $day) {
                        $dayDate = (clone $currentWeekStart)->modify($day)->setTimeFrom($start);

                        // Ensure correct week context (avoid going backward)
                        if ($dayDate->lt($currentWeekStart)) {
                            $dayDate->addWeek();
                        }

                        // Skip past days only in the very first week — but allow exact start date
                        if ($week === 0 && $dayDate->lt($start) && !$dayDate->isSameDay($start)) {
                            continue;
                        }

                        if ($dayDate->gt($limitDate)) {
                            // include end date if it matches exactly
                            if ($dayDate->isSameDay($limitDate)) {
                                $events[] = ['start' => $dayDate->copy()];
                            }
                            break 2;
                        }

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
                // Calculate total months needed based on end type
                $months = $endType === 'count'
                    ? ($count ?? ceil($start->diffInMonths($limitDate) / $interval))
                    : ceil($start->diffInMonths($limitDate) / $interval) + 1; // +1 to include potential end month

                for ($m = 0; $m < $months; $m++) {
                    $monthDate = $start->copy()->addMonths($m * $interval);

                    if ($mode === 'day') {
                        // Repeat on same day of month (e.g. 15th of every 2 months)
                        // Handle months with fewer days by using last day if original day exceeds month length
                        $targetDay = min($start->day, $monthDate->daysInMonth);
                        $target = $monthDate->copy()->day($targetDay)->setTimeFrom($start);

                        // Include if within limit and add to events
                        if ($target->lte($limitDate)) {
                            $events[] = ['start' => $target];
                        } elseif ($target->isSameDay($limitDate)) {
                            // Include the end date if it matches exactly
                            $events[] = ['start' => $target];
                            break;
                        } elseif ($target->gt($limitDate)) {
                            break;
                        }
                    } else {
                        // "weekday" mode → e.g. 2nd Monday of every month
                        $weekday = $start->format('l');
                        $weekOfMonth = intdiv($start->day - 1, 7) + 1;
                        
                        // Start from the first occurrence of the weekday
                        $target = $monthDate->copy()->startOfMonth();
                        while ($target->format('l') !== $weekday) {
                            $target->addDay();
                        }
                        
                        // Add weeks to get to the correct occurrence (e.g., 2nd Monday)
                        $target->addWeeks($weekOfMonth - 1);
                        
                        // If we've gone into next month, go back a week
                        if ($target->month !== $monthDate->month) {
                            $target->subWeek();
                        }
                        
                        $target->setTimeFrom($start);

                        // Include if within limit and add to events
                        if ($target->lte($limitDate)) {
                            $events[] = ['start' => $target];
                        } elseif ($target->isSameDay($limitDate)) {
                            // Include the end date if it matches exactly
                            $events[] = ['start' => $target];
                            break;
                        } elseif ($target->gt($limitDate)) {
                            break;
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
     * Remove future occurrences.
     */
    public function removeFutureOccurrences(Lesson $lesson): void
    {
        LessonOccurrence::where('lesson_id', $lesson->id)
            ->where('scheduled_start', '>', now())
            ->delete();
    }

    /**
     * Remove any occurrences beyond allowed limit.
     */
    private function removeExcessOccurrences(Lesson $lesson, Carbon $horizonEnd): void
    {
        LessonOccurrence::where('lesson_id', $lesson->id)
            ->where('scheduled_start', '>', $horizonEnd->copy()->endOfDay())
            ->delete();
    }

    /**
     * Sanitize recurrence meta data.
     */
    private function sanitizeMeta(array $meta): array
    {
        // Clean interval
        $meta['interval'] = isset($meta['interval']) && is_numeric($meta['interval'])
            ? max(1, (int) $meta['interval'])
            : 1;

        // Clean count
        if (isset($meta['count'])) {
            $meta['count'] = is_numeric($meta['count']) ? max(1, (int) $meta['count']) : 1;
        }

        // Clean end_date
        if (isset($meta['end_date']) && !strtotime($meta['end_date'])) {
            $meta['end_date'] = null;
        }

        // Clean days for weekly
        if (isset($meta['days']) && is_array($meta['days'])) {
            $meta['days'] = array_filter(array_map(function ($day) {
                return strtolower(trim($day));
            }, $meta['days']));
        }

        // Monthly mode
        if (isset($meta['mode']) && !in_array($meta['mode'], ['day','weekday'])) {
            $meta['mode'] = 'day';
        }

        return $meta;
    }


}

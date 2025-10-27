<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LessonOccurrence;
use App\Models\Setting;
use App\Notifications\ClassReminder;

class SendClassReminders extends Command
{
    protected $signature = 'reminders:classes';
    protected $description = 'Send reminders for upcoming classes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $minutesAhead = config('reminders.class_minutes', 20); // default 20 minutes
        $minutesAhead = (int) Setting::where('key','class_reminders_minutes')->first()->value ?? 20; // default 20 minutes
        $now = now();
        $target = $now->copy()->addMinutes($minutesAhead);

        $occurrences = LessonOccurrence::whereBetween('scheduled_start', [$now, $target])->get();

        foreach ($occurrences as $occurrence) {
            $lesson = $occurrence->lesson;
            if (! $lesson) continue;

            $student   = $lesson->student?->user;
            $instructor = $lesson->instructor?->user;

            if ($student) {
                $student->notify(new ClassReminder($occurrence));
            }
            if ($instructor) {
                $instructor->notify(new ClassReminder($occurrence));
            }
        }

        $this->info("Class reminders sent for {$occurrences->count()} occurrences.");
    }
}

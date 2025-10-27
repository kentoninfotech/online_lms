<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LessonOccurrence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateLessonStatusCommand extends Command
{
    protected $signature = 'lessons:update-status';
    protected $description = 'Update lesson occurrences to ongoing or completed based on time.';

    public function handle()
    {
        $now = Carbon::now();

        // Mark ongoing
        $ongoing = LessonOccurrence::where('status', 'scheduled')
            ->where('scheduled_start', '<=', $now)
            ->whereRaw("DATE_ADD(scheduled_start, INTERVAL duration_minutes MINUTE) > ?", [$now])
            ->update(['status' => 'ongoing']);

        // Mark Ended
        $ended = LessonOccurrence::whereIn('status', ['scheduled', 'ongoing'])
            ->whereRaw("DATE_ADD(scheduled_start, INTERVAL duration_minutes MINUTE) <= ?", [$now])
            ->update(['status' => 'ended']);

        Log::info("Lesson status updated. Ongoing: {$ongoing}, Ended: {$ended}");
        $this->info("Updated {$ongoing} ongoing, {$ended} ended.");
    }
}

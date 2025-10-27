<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LessonOccurrence;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FinalizeAttendanceCommand extends Command
{
    protected $signature = 'attendance:finalize';
    protected $description = 'Finalize attendance for completed or ended lesson occurrences';

    public function handle()
    {
        $attendanceService = app(AttendanceService::class);
        $now = Carbon::now();

        // Fetch all occurrences that should now be finalized:
        // - Status is "ongoing" or "scheduled" (but their end time has passed)
        // - They haven’t been recently updated (buffer window)
        $occurrences = LessonOccurrence::whereIn('status', ['ended', 'ongoing'])
            ->whereRaw("DATE_ADD(scheduled_start, INTERVAL duration_minutes MINUTE) <= ?", [$now->copy()->subMinutes(5)])
            ->get();

        if ($occurrences->isEmpty()) {
            $this->info('No lesson occurrences ready for attendance finalization.');
            return Command::SUCCESS;
        }

        $this->info("Found {$occurrences->count()} occurrences ready to finalize.");

        foreach ($occurrences as $occurrence) {
            try {
                // Skip if already finalized by a previous run (service handles this gracefully)
                if ($occurrence->status === 'completed') {
                    $this->line("Occurrence #{$occurrence->id} already marked completed. Skipping.");
                    continue;
                }

                $attendanceService->finalize($occurrence);

                $this->info("Attendance finalized for occurrence #{$occurrence->id}");
                Log::info("Attendance finalized for occurrence {$occurrence->id}");
            } catch (\Throwable $e) {
                $this->error("Failed for occurrence #{$occurrence->id}: {$e->getMessage()}");
                Log::error("FinalizeAttendanceCommand error for occurrence {$occurrence->id}: " . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $this->info('🎯 Attendance finalization completed successfully.');
        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetRescheduleUsageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reschedule:reset-usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'reset reschedule usage records for all students at the beginning of a new plan cycle';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $resetUsage = RescheduleUsage::where('period_end', '<', Carbon::now())
             ->update(['reschedule_count' => 0]);

        $this->info("Reset {$resetUsage} reschedule usage records.");
        return Command::SUCCESS;
    }
}

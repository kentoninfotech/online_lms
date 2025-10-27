<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateSubscriptionStatusCommand extends Command
{
    protected $signature = 'subscriptions:update-status';
    protected $description = 'Auto-update student subscription statuses based on expiry dates and grace period.';

    public function handle()
    {
        $graceDays = (int) Setting::where('key', 'billing_grace_period_days')->value('value') ?? 7;

        $now = Carbon::now();
        $graceCutoff = $now->copy()->subDays($graceDays);

        // Move active > grace
        $graced = Subscription::where('status', 'active')
            ->whereDate('end_date', '<', $now)
            ->update(['status' => 'grace']);

        // Move grace > expired
        $expired = Subscription::where('status', 'grace')
            ->whereDate('end_date', '<', $graceCutoff)
            ->update(['status' => 'expired']);

        Log::info("Subscription status update completed. Graced: {$graced}, Expired: {$expired}");
        $this->info("Graced: {$graced}, Expired: {$expired}");

        return Command::SUCCESS;
    }
}

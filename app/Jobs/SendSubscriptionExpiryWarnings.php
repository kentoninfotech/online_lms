<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Notifications\Parent\SubscriptionExpiryWarning;
use App\Models\Subscription;
use App\Models\Setting;

class SendSubscriptionExpiryWarnings implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $days = (int) (Setting::where('key', 'subscription_expiry_warning_days')->value('value') ?? 3);
        $targetDate = now()->addDays($days)->startOfDay();

        $subscriptions = Subscription::whereDate('end_date', $targetDate)->get();

        foreach ($subscriptions as $subscription) {
            $student = $subscription->student->user;
            $parent = $subscription->student->parents()->first()?->user;

            // Notify parent
            if ($parent) {
                $parent->notify(new SubscriptionExpiryWarning($subscription));
            }
            // Notify student as well
            // if ($student) {
            //     $student->notify(new \App\Notifications\Student\SubscriptionExpiryWarning($subscription));
            // }
        }
    }

}

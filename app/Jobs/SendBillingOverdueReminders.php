<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Notifications\Parent\BillingOverdue;
use App\Models\Subscription;
use App\Models\Setting;

class SendBillingOverdueReminders implements ShouldQueue
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
    public function handle()
    {
        $graceDays = (int) (Setting::where('key', 'billing_grace_period_days')->value('value') ?? 7);

        $subscriptions = Subscription::with('student.user', 'student.parents.user')
            ->whereDate('end_date', '<', now())
            ->whereDate('end_date', '>=', now()->subDays($graceDays))
            ->get();

        foreach ($subscriptions as $sub) {
            $daysPassed = now()->diffInDays($sub->end_date);
            $daysLeft   = $graceDays - $daysPassed;

            foreach ($sub->student->parents as $parent) {
                if ($parent->user) {
                    $parent->user->notify(new BillingOverdue($sub, $daysLeft));
                }
            }
        }
    }
}

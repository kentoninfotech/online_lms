<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\Plan;
use App\Models\Student;
use Illuminate\Support\Carbon;

class SubscriptionService
{
    /**
     * Create a new subscription (pending by default).
     */
    public function createSubscription(Student $student, Plan $plan): Subscription
    {
        return Subscription::create([
            'student_id'  => $student->id,
            'plan_id'     => $plan->id,
            'status'      => 'pending',
            'start_date'  => now(),
            'end_date'    => now()->addDays($plan->getCycleLength()),
        ]);
    }

    /**
     * Activate subscription after payment is verified.
     */
    public function activate(Subscription $subscription): void
    {
        // Get associated plan
        $plan = $subscription->plan;
        // Update subscription status and dates
        $subscription->update([
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays($plan->getCycleLength()),
        ]);
    }

    /**
     * Reject subscription (e.g. invalid payment).
     */
    public function reject(Subscription $subscription): void
    {
        $subscription->update([
            'status' => 'rejected',
        ]);
    }
}


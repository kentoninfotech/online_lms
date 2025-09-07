<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    /**
     * Store offline payment evidence.
     */
    public function uploadEvidence(Subscription $subscription, User $parent, UploadedFile $file, int $amount): Payment
    {
        $path = $file->store('payments', 'public');

        return Payment::create([
            'subscription_id' => $subscription->id,
            'parent_id'       => $parent->id,
            'amount'          => $amount,
            'file_path'       => $path,
            'status'          => 'pending',
        ]);
    }

    /**
     * Approve payment and activate subscription.
     */
    public function approve(Payment $payment, SubscriptionService $subs): void
    {
        $payment->update([
            'status'      => 'approved',
        ]);

        $subs->activate($payment->subscription);
    }

    /**
     * Reject payment with reason.
     */
    public function reject(Payment $payment, SubscriptionService $subs): void
    {
        $payment->update([
            'status'         => 'rejected',
        ]);

        $subs->reject($payment->subscription);
    }
}

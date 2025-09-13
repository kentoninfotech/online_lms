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

        $payment = Payment::create([
            'subscription_id' => $subscription->id,
            'parent_id'       => $parent->id,
            'amount'          => $amount,
            'file_path'       => $path,
            'status'          => 'pending',
        ]);

        // Notify admin of new payment
        $admin = User::where('user_type', 'admin')->first();
        if ($admin) {
            $admin->notify(new PaymentSubmitted($payment));
        }

        return $payment;
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

        // Notify parent of approval
        $parent = $payment->parent;
        if ($parent) {
            $parent->notify(new PaymentApproved($payment));
        }
    }

    /**
     * Reject payment with reason.
     */
    public function reject(Payment $payment, SubscriptionService $subs, string $reason): void
    {
        $payment->update([
            'status'          => 'rejected',
            'decision_reason' => $reason,
        ]);

        $subs->reject($payment->subscription);
        
        // Notify parent of rejection
        $parent = $payment->parent;
        if ($parent) {
            $parent->notify(new PaymentRejected($payment, $reason));
        }
    }
}

<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaymentPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasRole('admin')) return true;

        if ($user->hasRole('parent')) {
            return $user->parent?->id === $payment->parent_id;
        }

        return false;
    }

    /**
     * Determine whether the user can upload payment proof.
     */
    public function upload(User $user)
    {
        return $user->hasRole('admin') ||
            $user->hasRole('parent');
    }

    /**
     * Determine whether the user can approve/reject the payment.
     */
    public function approve(User $user, Payment $payment)
    {
        return $user->hasRole('admin');
    }

}

<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubscriptionPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        if ($user->hasRole('admin')) return true;

        if ($user->hasRole('parent')) {
            return $user->parent?->students->contains('id', $student_id);
        }

        if ($user->hasRole('student')) {
            return $user->student?->id === $student_id;
        }

        return false;

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Subscription $subscription): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Subscription $subscription): bool
    {
        return $user->hasRole('admin');
    }

}

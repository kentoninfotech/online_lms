<?php

namespace App\Policies;

use App\Models\RescheduleRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReschedulePolicy
{
    public function request(User $user)
    {
        return $user->hasAnyRole(['parent', 'student']);
    }

    /**
     * Determine whether the user can approve/reject the reschedule request.
     */
    public function approve(User $user, RescheduleRequest $rescheduleRequest)
    {
        return $user->hasAnyRole(['instructor', 'admin']);
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Anyone can view their own record; admin can view all.
     */
    public function view(User $user, User $target)
    {
        return $user->id === $target->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Editing profile (self-edit or admin).
     */
    public function update(User $user, User $target)
    {
        return $user->id === $target->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $target)
    {
        return $user->hasRole('admin');
    }

}

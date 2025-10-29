<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Anyone can view their own record; admin can view all.
     */
    public function view(User $authUser, User $target)
    {
        return $authUser->id === $target->id || $authUser->hasRole('admin');
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
    public function update(User $authUser, User $target)
    {
        return $authUser->id === $target->id || $authUser->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $authUser, User $target)
    {
        return $authUser->hasRole('admin');
    }

}

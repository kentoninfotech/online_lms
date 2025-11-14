<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LessonPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lesson $lesson)
    {
        return $user->hasRole('admin') ||
            $lesson->instructor_id === $user->instructor->id ||
            $lesson->student_id === optional($user->student)->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->hasRole('admin') ||
            $user->hasRole('instructor');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lesson $lesson)
    {
        return $user->hasRole('admin') ||
            $lesson->instructor_id === $user->instructor->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->hasRole('admin') ||
            $lesson->instructor_id === $user->instructor->id;
    }

}

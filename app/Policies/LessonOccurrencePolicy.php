<?php

namespace App\Policies;

use App\Models\LessonOccurrence;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LessonOccurrencePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LessonOccurrence $occurrence): bool
    {
        return $user->hasRole('admin') ||
            $occurrence->lesson->instructor_id === $user->instructor->id ||
            $occurrence->lesson->student_id === optional($user->student)->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LessonOccurrence $lessonOccurrence): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LessonOccurrence $lessonOccurrence): bool
    {
        return false;
    }

}

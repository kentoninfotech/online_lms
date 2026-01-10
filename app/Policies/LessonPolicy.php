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
        // 1. Allow if the user is an admin.
        if ($user->hasRole('admin')) {
            return true;
        }

        // 2. ONLY continue if the user is an instructor.
        // If they aren't an instructor, this check should fail, and the rest of the expression short-circuits.
        if (!$user->hasRole('instructor')) {
            return false;
        }

        // 3. Allow instructor to edit their own lessons
        // Get the instructor ID - handle case where instructor relationship might not be loaded
        $instructorId = $user->instructor?->id;
        
        if ($instructorId === null) {
            return false;
        }
        
        return $lesson->instructor_id === $instructorId;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        // Admin can always delete
        if ($user->hasRole('admin')) {
            return true;
        }

        // Instructor can delete their own lessons
        if ($user->hasRole('instructor')) {
            return $lesson->instructor_id === $user->instructor?->id;
        }

        return false;
    }

}

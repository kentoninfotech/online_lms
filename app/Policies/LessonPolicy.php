<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LessonPolicy
{
    /**
     * Intercept all authorization checks - admins can do anything
     */
    public function before(User $user, string $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        return null; // Let individual methods decide
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lesson $lesson)
    {
        // Instructor can view all lessons (or their own)
        if ($user->hasRole('instructor')) {
            return true;
        }

        // Student can view their own lessons
        if ($user->hasRole('student')) {
            $studentId = $user->student ? $user->student->id : null;
            return $studentId && $lesson->student_id === $studentId;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->hasRole('admin') || $user->hasRole('instructor');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lesson $lesson)
    {
        // Only instructors can update lessons (admins already handled by before())
        if (!$user->hasRole('instructor')) {
            return false;
        }

        // Allow any instructor to edit any lesson
        // If you want to restrict to only their own lessons, implement the check below:
        // $instructorId = $user->instructor?->id;
        // if (!$instructorId) return false;
        // return $lesson->instructor_id === $instructorId;

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        // Only instructors can delete lessons (admins already handled by before())
        if (!$user->hasRole('instructor')) {
            return false;
        }

        // Allow any instructor to delete any lesson
        return true;
    }
}

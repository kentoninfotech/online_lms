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
        // Admin can view all lessons
        if ($user->hasRole('admin')) {
            return true;
        }

        // Instructor can view their own lessons
        if ($user->hasRole('instructor')) {
            $instructorId = $user->instructor?->id;
            if ($instructorId && $lesson->instructor_id === $instructorId) {
                return true;
            }
        }

        // Student can view their own lessons
        $studentId = $user->student?->id;
        if ($studentId && $lesson->student_id === $studentId) {
            return true;
        }

        return false;
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
        if (!$user->hasRole('instructor')) {
            return false;
        }

        // 3. Allow instructor to edit their own lessons
        $instructorId = $user->instructor?->id;
        
        // Log for debugging
        \Log::info('Lesson Update Authorization Check', [
            'user_id' => $user->id,
            'instructor_id' => $instructorId,
            'lesson_id' => $lesson->id,
            'lesson_instructor_id' => $lesson->instructor_id,
            'match' => $lesson->instructor_id === $instructorId,
        ]);
        
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

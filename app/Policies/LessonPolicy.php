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
        if ($user->user_type === 'admin') {
            return true;
        }
        return null; // Let individual methods decide
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lesson $lesson)
    {
        // Instructor can view lessons for courses they teach
        if ($user->user_type === 'instructor') {
            $instructor = $user->instructor;
            if ($instructor && $lesson->course) {
                return $lesson->course->instructors()
                    ->where('instructor_id', $instructor->id)
                    ->exists();
            }
            return false;
        }

        // Student can view lessons in their enrolled courses
        if ($user->user_type === 'student') {
            $student = $user->student;
            if ($student && $lesson->course) {
                return $lesson->course->enrollees()
                    ->where('student_id', $student->id)
                    ->exists();
            }
            return false;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return in_array($user->user_type, ['admin', 'instructor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lesson $lesson)
    {
        // Only instructors can update lessons (admins already handled by before())
        if ($user->user_type !== 'instructor') {
            return false;
        }

        // Check if instructor teaches the course this lesson belongs to
        $instructor = $user->instructor;
        if (!$instructor || !$lesson->course) {
            return false;
        }

        return $lesson->course->instructors()
            ->where('instructor_id', $instructor->id)
            ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        // Only instructors can delete lessons (admins already handled by before())
        if ($user->user_type !== 'instructor') {
            return false;
        }

        // Check if instructor teaches the course this lesson belongs to
        $instructor = $user->instructor;
        if (!$instructor || !$lesson->course) {
            return false;
        }

return $lesson->course->instructors()
            ->where('instructor_id', $instructor->id)
            ->exists();
    }
}

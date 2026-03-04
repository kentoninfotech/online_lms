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
        // Admin can always view
        if ($user->user_type === 'admin') {
            return true;
        }

        // Instructor can view if they teach the course the lesson belongs to
        if ($user->user_type === 'instructor') {
            $instructor = $user->instructor;
            if ($instructor && $occurrence->lesson && $occurrence->lesson->course) {
                return $occurrence->lesson->course->instructors()
                    ->where('instructor_id', $instructor->id)
                    ->exists();
            }
            return false;
        }

        // Student can view if they're enrolled in the course
        if ($user->user_type === 'student') {
            $student = $user->student;
            if ($student && $occurrence->lesson && $occurrence->lesson->course) {
                return $occurrence->lesson->course->enrollees()
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

<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CourseLesson;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class CourseLessonPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the lesson
     */
    public function view(User $user, CourseLesson $lesson): bool
    {
        // Admin can always view
        if ($user->user_type === 'admin') {
            return true;
        }

        // Instructor can view if assigned to the course
        if ($user->user_type === 'instructor') {
            $instructor = $user->instructor;
            if (!$instructor) {
                return false;
            }
            
            return $lesson->course->instructors()
                ->where('instructor_id', $instructor->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can update the lesson
     */
    public function update(User $user, CourseLesson $lesson): bool
    {
        // Admin can always update
        if ($user->user_type === 'admin') {
            return true;
        }

        // Instructor can update if assigned to the course
        if ($user->user_type === 'instructor') {
            $instructor = $user->instructor;
            if (!$instructor) {
                return false;
            }
            
            return $lesson->course->instructors()
                ->where('instructor_id', $instructor->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can delete the lesson
     */
    public function delete(User $user, CourseLesson $lesson): bool
    {
        // Only admin can delete
        return $user->user_type === 'admin';
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the course
     */
    public function view(User $user, Course $course): bool
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
            
            return $course->instructors()
                ->where('instructor_id', $instructor->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can update the course
     */
    public function update(User $user, Course $course): bool
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
            
            // Check if this instructor is assigned to this course
            return $course->instructors()
                ->where('instructor_id', $instructor->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can delete the course
     */
    public function delete(User $user, Course $course): bool
    {
        // Only admin can delete
        return $user->user_type === 'admin';
    }

    /**
     * Determine if the user can create course content
     */
    public function createContent(User $user, Course $course): bool
    {
        // Admin can always create content
        if ($user->user_type === 'admin') {
            return true;
        }

        // Instructor can create if assigned to the course
        if ($user->user_type === 'instructor') {
            $instructor = $user->instructor;
            if (!$instructor) {
                return false;
            }
            
            return $course->instructors()
                ->where('instructor_id', $instructor->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can manage course quizzes
     */
    public function manageQuizzes(User $user, Course $course): bool
    {
        // Admin can always manage
        if ($user->user_type === 'admin') {
            return true;
        }

        // Instructor can manage if assigned to the course
        if ($user->user_type === 'instructor') {
            $instructor = $user->instructor;
            if (!$instructor) {
                return false;
            }
            
            return $course->instructors()
                ->where('instructor_id', $instructor->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can view course enrollees
     */
    public function viewEnrollees(User $user, Course $course): bool
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
            
            return $course->instructors()
                ->where('instructor_id', $instructor->id)
                ->exists();
        }

        return false;
    }
}

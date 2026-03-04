<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseContent;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class CourseContentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create course content.
     */
    public function create(User $user, Course $course): bool
    {
        // Admin can always create
        if ($user->user_type === 'admin') {
            return true;
        }

        // Instructor can create content for courses they're assigned to
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
     * Determine whether the user can update course content.
     */
    public function update(User $user, CourseContent $content): bool
    {
        // Admin can always update
        if ($user->user_type === 'admin') {
            return true;
        }

        // Instructor can update content for courses they're assigned to
        if ($user->user_type === 'instructor') {
            $instructor = $user->instructor;
            if (!$instructor) {
                return false;
            }
            
            return $content->course->instructors()
                ->where('instructor_id', $instructor->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete course content.
     */
    public function delete(User $user, CourseContent $content): bool
    {
        // Admin can always delete
        if ($user->user_type === 'admin') {
            return true;
        }

        // Instructor can delete content for courses they're assigned to
        if ($user->user_type === 'instructor') {
            $instructor = $user->instructor;
            if (!$instructor) {
                return false;
            }
            
            return $content->course->instructors()
                ->where('instructor_id', $instructor->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can view all course contents.
     */
    public function viewAny(User $user): bool
    {
        // Admin and instructors can view course contents
        return in_array($user->user_type, ['admin', 'instructor']);
    }

    /**
     * Determine whether the user can view course content.
     */
    public function view(User $user, CourseContent $content): bool
    {
        // Admin can always view
        if ($user->user_type === 'admin') {
            return true;
        }

        // Instructor can view content for courses they're assigned to
        if ($user->user_type === 'instructor') {
            $instructor = $user->instructor;
            if (!$instructor) {
                return false;
            }
            
            return $content->course->instructors()
                ->where('instructor_id', $instructor->id)
                ->exists();
        }

        return false;
    }
}


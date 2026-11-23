<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CourseContentResource;

class CourseContentResourcePolicy
{
    /**
     * Determine if the user can view the resource
     */
    public function view(User $user, CourseContentResource $resource): bool
    {
        $course = $resource->getCourse();
        
        // Admin can always view
        if ($user->user_type === 'admin') {
            return true;
        }

        // Instructor assigned to the course can view
        if ($user->user_type === 'instructor') {
            $instructor = $user->instructor;
            if ($instructor && $course->instructors()->where('instructor_id', $instructor->id)->exists()) {
                return true;
            }
        }

        // Enrolled students can view
        if ($user->user_type === 'student') {
            return $user->enrollments()
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can delete the resource
     */
    public function delete(User $user, CourseContentResource $resource): bool
    {
        $content = $resource->content;
        
        // Admin can always delete
        if ($user->user_type === 'admin') {
            return true;
        }

        // Instructor assigned to the course can delete
        if ($user->user_type === 'instructor') {
            $instructor = $user->instructor;
            if ($instructor && $content->course->instructors()->where('instructor_id', $instructor->id)->exists()) {
                return true;
            }
        }

        return false;
    }
}

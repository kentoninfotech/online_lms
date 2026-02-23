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
        if ($user->hasRole('admin')) {
            return true;
        }

        // Instructor can create content for courses they facilitate
        if ($user->hasRole('instructor')) {
            $facilitator = $user->instructor;
            return $facilitator && $course->facilitator_id === $facilitator->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update course content.
     */
    public function update(User $user, CourseContent $content): bool
    {
        // Admin can always update
        if ($user->hasRole('admin')) {
            return true;
        }

        // Instructor can update content for courses they facilitate
        if ($user->hasRole('instructor')) {
            $facilitator = $user->instructor;
            return $facilitator && $content->course->facilitator_id === $facilitator->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete course content.
     */
    public function delete(User $user, CourseContent $content): bool
    {
        // Admin can always delete
        if ($user->hasRole('admin')) {
            return true;
        }

        // Instructor can delete content for courses they facilitate
        if ($user->hasRole('instructor')) {
            $facilitator = $user->instructor;
            return $facilitator && $content->course->facilitator_id === $facilitator->id;
        }

        return false;
    }

    /**
     * Determine whether the user can view all course contents.
     */
    public function viewAny(User $user): bool
    {
        // Admin and instructors can view course contents
        return $user->hasRole(['admin', 'instructor']);
    }

    /**
     * Determine whether the user can view course content.
     */
    public function view(User $user, CourseContent $content): bool
    {
        // Admin can always view
        if ($user->hasRole('admin')) {
            return true;
        }

        // Instructor can view content for courses they facilitate
        if ($user->hasRole('instructor')) {
            $facilitator = $user->instructor;
            return $facilitator && $content->course->facilitator_id === $facilitator->id;
        }

        return false;
    }
}

<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudentPolicy
{
    /**
     * Allow view if:
     *  - Admin
     *  - Student is self
     *  - Parent linked to student
     *  - Instructor assigned to a lesson with this student
     */
    public function view(User $authUser, Student $student)
    {
        if ($authUser->hasRole('admin')) return true;

        if ($authUser->hasRole('student') && $authUser->student?->id === $student->id)
            return true;

        if ($authUser->hasRole('parent')) {
            return $authUser->parent?->students->contains('id', $student->id);
        }

        if ($authUser->hasRole('instructor')) {
            return $authUser->instructor
                ? $authUser->instructor->lessons()->where('student_id', $student->id)->exists()
                : false;
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
    public function update(User $user, Student $student)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->hasRole('admin');
    }

}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;

class UserController extends Controller
{
    public function edit(User $user, $role)
    {
        return view('dashboard.edit-user', compact('user', 'role'));
    }

    public function update(UserUpdateRequest $request, User $user, $role)
    {
        $user->update($request->only('name', 'email'));

        // Role-specific updates
        if ($role === 'student' && $user->student) {
            $user->student->update($request->only('name', 'email', 'address', 'number'));
        }

        if ($role === 'parent' && $user->parent) {
            $user->parent->update($request->only('name', 'email', 'number', 'occupation', 'address'));
        }

        if ($role === 'instructor' && $user->instructor) {
            $user->instructor->update($request->only('name', 'email', 'address', 'number', 'specialization', 'bio'));
        }

        return redirect()->route('users.edit', [$user, $role])
                         ->with('success', ucfirst($role) . ' updated successfully.')
                         ->withFragment('profile');
    }
}

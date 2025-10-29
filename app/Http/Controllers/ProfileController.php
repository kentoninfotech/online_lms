<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show profile
     */
    public function show()
    {
        $user = Auth::user();

        // Authorization check
        $this->authorize('view', $user);

        // Detect role/relationship
        $roleData = null;

        if ($user->hasRole('student') && $user->student) {
            $roleData = $user->student;
            $roleType = 'student';
        } elseif ($user->hasRole('instructor') && $user->instructor) {
            $roleData = $user->instructor;
            $roleType = 'instructor';
        } elseif ($user->hasRole('parent') && $user->parent) {
            $roleData = $user->parent;
            $roleType = 'parent';
        } else {
            $roleData = $user; // fallback for admin
            $roleType = 'admin';
        }

        return view('dashboard.profile', compact('user', 'roleData', 'roleType'));
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request, User $user)
    {
        if (!auth()->user()->hasRole('admin') || auth()->id() == $user->id) {
            $request->validate([
                'current_password' => 'required|current_password',
                'password' => 'required|confirmed|min:4',
            ]);
        } else {
            // Admin can bypass current password
            $request->validate([
                'password' => 'required|confirmed|min:4',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
               ->back()
               ->with('success', 'Password updated successfully.')
               ->withFragment('password');
    }

    /**
     * Update profile picture
     */
    public function updateProfilePicture(Request $request, User $user)
    {
        $request->validate([
            'profile' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Delete old profile if exists
        if ($user->profile) {
            Storage::disk('public')->delete($user->profile);
        }

        // Store new profile
        $path = $request->file('profile')->store('profiles', 'public');

        $user->update(['profile' => $path]);

        return redirect()
               ->back()
               ->with('success', 'Profile picture updated successfully.')
               ->withFragment('picture');
    }
}

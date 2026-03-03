<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminAccountController extends Controller
{
    /**
     * Display a listing of admin accounts.
     */
    public function index()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })
        ->with('roles')
        ->paginate(15);

        return view('admin.accounts.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin account.
     */
    public function create()
    {
        return view('admin.accounts.create');
    }

    /**
     * Store a newly created admin account in database.
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d@$!%*?&]+$/',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number. Symbols (@$!%*?&) are optional.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create the admin user
            $admin = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'admin',
                'email_verified_at' => now(), // Admins are auto-verified
            ]);

            // Assign admin role
            $admin->assignRole('admin');

            return redirect()
                ->route('admin.accounts.index')
                ->with('success', 'Admin account created successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error creating admin account: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing an admin account.
     */
    public function edit(User $admin)
    {
        // Ensure user is actually an admin
        if (!$admin->hasRole('admin')) {
            abort(403, 'User is not an administrator');
        }

        return view('admin.accounts.edit', compact('admin'));
    }

    /**
     * Update the specified admin account in database.
     */
    public function update(Request $request, User $admin)
    {
        // Ensure user is actually an admin
        if (!$admin->hasRole('admin')) {
            abort(403, 'User is not an administrator');
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d@$!%*?&]+$/',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number. Symbols (@$!%*?&) are optional.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $admin->name = $request->name;
            $admin->email = $request->email;

            // Update password if provided
            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
            }

            $admin->save();

            return redirect()
                ->route('admin.accounts.index')
                ->with('success', 'Admin account updated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error updating admin account: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete the specified admin account.
     */
    public function destroy(Request $request, User $admin)
    {
        // Ensure user is actually an admin
        if (!$admin->hasRole('admin')) {
            abort(403, 'User is not an administrator');
        }

        // Prevent deleting the current authenticated admin
        if ($admin->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'You cannot delete your own admin account!');
        }

        // Require confirmation password
        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()
                ->back()
                ->with('error', 'Incorrect password. Admin account not deleted.');
        }

        try {
            // Remove admin role
            $admin->removeRole('admin');
            
            // Delete the user
            $admin->delete();

            return redirect()
                ->route('admin.accounts.index')
                ->with('success', 'Admin account deleted successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error deleting admin account: ' . $e->getMessage());
        }
    }
}

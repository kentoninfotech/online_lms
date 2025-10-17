<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\StoreInstructorRequest;
use App\Http\Requests\StoreParentRequest;
use App\Http\Requests\StoreStudentRequest;
use App\Models\ParentModel;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create(string $role)
    {
        $parent_list = [];
        
        if ($role === 'student'){
            $parent_list = ParentModel::all();
        }

        return view('dashboard.add-user', compact('role', 'parent_list'));
    }

    public function store(string $role)
    {
        // pick correct request
        $request = match($role) {
            'student'     => app(StoreStudentRequest::class),
            'parent'      => app(StoreParentRequest::class),
            'instructor'  => app(StoreInstructorRequest::class),
            default       => abort(404),
        };


        $user = User::create([
            'name'        => $request['name'],
            'email'       => $request['email'],
            'password'    => Hash::make($request['password'] ?? 'password123'),
            'user_type'   => $role,
        ]);

        // assign role
        $user->assignRole($role);


        if ($role === 'student') {
            $user->student()->create($request->only('name', 'email', 'address', 'number'));
            // Link student to parent
            if($request['parent_id']){
                $parent = ParentModel::findOrFail($request['parent_id']);
                // Link parents to student
                $user->student->parents()->syncWithoutDetaching([$parent->id]);
                // $parent->students()->syncWithoutDetaching([$user->student->id]);
            }

        } elseif ($role === 'parent') {
            $user->parent()->create($request->only('name', 'email', 'address', 'number'));
        } elseif ($role === 'instructor') {
            $user->instructor()->create($request->only('name', 'email', 'address', 'number'));
        }

        return redirect()
                 ->back()
                 ->with('success', ucfirst($role).' created successfully!');
    }

    /**
     * Edit user
     */
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

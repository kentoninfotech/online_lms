<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\StoreInstructorRequest;
use App\Http\Requests\StoreParentRequest;
use App\Http\Requests\StoreStudentRequest;
use Illuminate\Http\Request;
use App\Models\ParentModel;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function create(string $role)
    {
        // Authorization check
        $this->authorize('create', User::class);

        $parent_list = [];
        
        if ($role === 'student'){
            $parent_list = ParentModel::all();
        }

        return view('dashboard.add-user', compact('role', 'parent_list'));
    }

    public function store(string $role)
    {
        // Authorization check
        $this->authorize('create', User::class);

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
            $user->student()->create($request->only('name', 'email', 'address', 'number', 'dob', 'gender'));
            // Link student to parent
            if($request['parent_id']){
                $parent = ParentModel::findOrFail($request['parent_id']);
                // Link parents to student
                $user->student->parents()->syncWithoutDetaching([$parent->id]);
                // $parent->students()->syncWithoutDetaching([$user->student->id]);
            }

        } elseif ($role === 'parent') {
            $user->parent()->create($request->only('name', 'email', 'address', 'number', 'occupation'));
        } elseif ($role === 'instructor') {
            $user->instructor()->create($request->only('name', 'email', 'address', 'number', 'zoom_link', 'specialization', 'bio'));
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
        // Authorization check
        $this->authorize('update', $user);

        return view('dashboard.edit-user', compact('user', 'role'));
    }

    public function update(UserUpdateRequest $request, User $user, $role)
    {
        // Authorization check
        $this->authorize('update', $user);

        $user->update($request->only('name', 'email'));

        // Role-specific updates
        if ($role === 'student' && $user->student) {
            $user->student->update($request->only('name', 'email', 'address', 'number', 'dob', 'gender'));
        }

        if ($role === 'parent' && $user->parent) {
            $user->parent->update($request->only('name', 'email', 'number', 'occupation', 'address'));
        }

        if ($role === 'instructor' && $user->instructor) {
            $user->instructor->update($request->only('name', 'email', 'address', 'number', 'zoom_link', 'specialization', 'bio'));
        }

        return redirect()->route('users.edit', [$user, $role])
                         ->with('success', ucfirst($role) . ' updated successfully.')
                         ->withFragment('profile');
    }

    public function delete(User $user)
    {
        // Authorization check
        $this->authorize('delete', $user);

        // Delete user related data based on role
        if ($user->hasRole('student')){
            // Detach all linked parents
            $user->student->parents()->detach();
            $user->student->delete();
        }elseif ($user->hasRole('instructor')){
            $user->instructor->delete();
        }elseif ($user->hasRole('parent')){
            // Detach all linked students
            $user->parent->students()->detach();
            $user->parent->delete();
        }

        // check if user has profile picture and delete the file
        if ($user->profile){
            Storage::disk('public')->delete($user->profile);
        }

        // Finally, delete the user
        $user->delete();

        return redirect()
                 ->back()
                 ->with('success', 'User deleted successfully!');
    }

    /**
     * Add parent to student
     */
    public function addParentToStudent(Request $request, Student $student)
    {
        // Authorization check - admin or the student themselves
        if (auth()->user()->id !== $student->user_id && !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'parent_id' => 'required|exists:parents,id',
        ]);

        $parent = ParentModel::findOrFail($validated['parent_id']);

        // Check if already linked
        if ($student->parents()->where('parent_id', $parent->id)->exists()) {
            return redirect()->back()
                ->with('error', 'This parent is already linked to this student.')
                ->withFragment('parents');
        }

        // Link parent to student
        $student->parents()->syncWithoutDetaching([$parent->id]);

        return redirect()->back()
            ->with('success', 'Parent added successfully.')
            ->withFragment('parents');
    }

    /**
     * Remove parent from student
     */
    public function removeParentFromStudent(Student $student, ParentModel $parent)
    {
        // Authorization check - admin or the student themselves
        if (auth()->user()->id !== $student->user_id && !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        // Check if the parent is linked to this student
        if (!$student->parents()->where('parent_id', $parent->id)->exists()) {
            return redirect()->back()
                ->with('error', 'This parent is not linked to this student.')
                ->withFragment('parents');
        }

        // Remove the link
        $student->parents()->detach($parent->id);

        return redirect()->back()
            ->with('success', 'Parent removed successfully.')
            ->withFragment('parents');
    }

    /**
     * Add child to parent
     */
    public function addChildToParent(Request $request, ParentModel $parent)
    {
        // Authorization check - admin or the parent themselves
        if (auth()->user()->id !== $parent->user_id && !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        // Check if already linked
        if ($parent->students()->where('student_id', $student->id)->exists()) {
            return redirect()->back()
                ->with('error', 'This student is already linked to this parent.')
                ->withFragment('add-child');
        }

        // Link student to parent
        $parent->students()->syncWithoutDetaching([$student->id]);

        return redirect()->back()
            ->with('success', 'Child added successfully.')
            ->withFragment('add-child');
    }

    /**
     * Remove child from parent
     */
    public function removeChildFromParent(ParentModel $parent, Student $student)
    {
        // Authorization check - admin or the parent themselves
        if (auth()->user()->id !== $parent->user_id && !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        // Check if the student is linked to this parent
        if (!$parent->students()->where('student_id', $student->id)->exists()) {
            return redirect()->back()
                ->with('error', 'This student is not linked to this parent.')
                ->withFragment('add-child');
        }

        // Remove the link
        $parent->students()->detach($student->id);

        return redirect()->back()
            ->with('success', 'Child removed successfully.')
            ->withFragment('add-child');
    }
}

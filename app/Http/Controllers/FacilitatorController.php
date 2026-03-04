<?php

namespace App\Http\Controllers;

use App\Models\Facilitator;
use Illuminate\Http\Request;

class FacilitatorController extends Controller
{
    /**
     * Admin: List facilitators
     */
    public function adminIndex()
    {
        $this->authorize('isAdmin');

        $facilitators = Facilitator::with('user', 'assignedCourses')
            ->paginate(15);

        return view('admin.facilitators.index', compact('facilitators'));
    }

    /**
     * Admin: Create facilitator
     */
    public function adminCreate()
    {
        $this->authorize('isAdmin');

        $users = \App\Models\User::where('user_type', 'instructor')
            ->whereDoesntHave('facilitator')
            ->get();

        return view('admin.facilitators.create', compact('users'));
    }

    /**
     * Admin: Store facilitator
     */
    public function adminStore(Request $request)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:facilitators',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:facilitators',
            'phone' => 'nullable|string',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
            'qualification' => 'nullable|string',
            'expertise' => 'nullable|string',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $validated['profile_image'] = 'storage/' . $file->storeAs('uploads/facilitators', $filename, 'public');
        }

        Facilitator::create($validated);

        return redirect()->route('admin.facilitators.index')
            ->with('success', 'Facilitator created successfully.');
    }

    /**
     * Admin: Edit facilitator
     */
    public function adminEdit(Facilitator $facilitator)
    {
        $this->authorize('isAdmin');

        return view('admin.facilitators.edit', compact('facilitator'));
    }

    /**
     * Admin: Update facilitator
     */
    public function adminUpdate(Request $request, Facilitator $facilitator)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:facilitators,email,' . $facilitator->id,
            'phone' => 'nullable|string',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
            'qualification' => 'nullable|string',
            'expertise' => 'nullable|string',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $validated['profile_image'] = 'storage/' . $file->storeAs('uploads/facilitators', $filename, 'public');
        }

        $facilitator->update($validated);

        return redirect()->route('admin.facilitators.index')
            ->with('success', 'Facilitator updated successfully.');
    }

    /**
     * Admin: Show facilitator details
     */
    public function adminShow(Facilitator $facilitator)
    {
        $this->authorize('isAdmin');

        $facilitator->load('user', 'assignedCourses');

        return view('admin.facilitators.show', compact('facilitator'));
    }

    /**
     * Admin: Delete facilitator
     */
    public function adminDestroy(Facilitator $facilitator)
    {
        $this->authorize('isAdmin');

        $facilitator->delete();

        return redirect()->route('admin.facilitators.index')
            ->with('success', 'Facilitator deleted successfully.');
    }

    /**
     * Show facilitator profile
     */
    public function show(Facilitator $facilitator)
    {
        $courses = $facilitator->assignedCourses()->where('is_active', true)->paginate(12);

        return view('facilitators.show', compact('facilitator', 'courses'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Student;
use App\Models\ParentModel;
use Illuminate\Support\Facades\Auth;

class LinkStudentParentController extends Controller
{
    // generateLinkCode
    public function generateLinkCode(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('student') || !$user->student) {
            return back()->with('error', 'Only students can generate link codes.')
                         ->withFragment('link-code');
        }

        $student = $user->student;

        // Generate a unique 8-character alphanumeric code
        $code = strtoupper(Str::random(8)); // 8 characters
        // $code = strtoupper(bin2hex(random_bytes(4))); // 8 characters

        // Save to student's record
        $student->link_code = $code;
        $student->save();

        return redirect()
               ->back()
               ->with('success', 'New link code generated.')
               ->withFragment('link-code');
    }

    // linkChild
    public function linkChild(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'link_code'    => 'required|string',
            'child_email'  => 'required|email',
        ]);

        if (!$user->hasRole('parent') || !$user->parent) {
            return back()->with('error', 'Only parents can link children.')
                         ->withFragment('link-child'); 
        }

        $parent = $user->parent;

        // Find student by Email and link code
        $student = Student::whereHas('user', function ($q) use ($request) {
            $q->where('email', $request->child_email);
        })
           ->where('link_code', $request->link_code)
           ->first();

        if (!$student) {
            return back()->with('error', 'Invalid email or link code.')
                         ->withFragment('link-child');
        }

        // Check if already linked
        if ($parent->students()->where('student_id', $student->id)->exists()) {
            return back()->with('error', 'This child is already linked to your account.')
                         ->withFragment('link-child');
        }

        // Link student to parent
        $parent->students()->syncWithoutDetaching([$student->id]);

        // Optionally, clear the link code so it can't be reused
        $student->link_code = null;
        $student->save();

        return redirect()
               ->back()
               ->with('success', 'Child linked successfully.')
               ->withFragment('link-child');
    }

}

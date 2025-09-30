<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParentModel;

class ParentController extends Controller
{
    public function show(ParentModel $parent)
    {
        // Eager load related user and students
        $parent->load(['user', 'students.user']);

        return view('dashboard.parent', compact('parent'));
    }
}

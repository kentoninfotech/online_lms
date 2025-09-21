<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LessonOccurrence;

class ZoomController extends Controller
{
    public function addZoom(Request $request, LessonOccurrence $occurrence)
    {
        dd($occurrence);
        dd($request->all());

        return redirect()->back()->with('success', 'Zoom Meeting Added!');
    }
}

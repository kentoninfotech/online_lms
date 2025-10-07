<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LessonOccurrence;
use App\Models\ZoomSession;

class ZoomController extends Controller
{
    public function addZoom(Request $request, LessonOccurrence $occurrence)
    {
        $validated =  $request->validate([
            'zoom_meeting_id' => 'nullable|string',
            'topic'           => 'nullable|string',
            'join_url'        => 'nullable|string',
            'start_url'       => 'nullable|string',
        ]);

        $occurrence->ZoomSession()->create($validated);

        return redirect()->back()->with('success', 'Zoom Meeting Added!');
    }
}

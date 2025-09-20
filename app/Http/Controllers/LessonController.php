<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Http\Requests\StoreLessonRequest;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function store(StoreLessonRequest $request)
    {
        $data = $request->validated();

        // Normalize recurrence_meta
        $recurrenceMeta = null;

        if (in_array($request->recurrence_type, ['daily', 'monthly'])) {
            $recurrenceMeta = [
                'count' => (int) $request->count,
            ];
        } elseif ($request->recurrence_type === 'weekly') {
            $recurrenceMeta = [
                'days'  => $request->days,
                'count' => (int) $request->count,
            ];
        }


        $lesson = Lesson::create([
            'subject'          => $data['subject'],
            'student_id'       => $data['student_id'],
            'instructor_id'    => Auth::user()->instructor->id,// $data['instructor_id'],
            'start_time'       => $data['start_time'],
            'duration_minutes' => $data['duration_minutes'],
            'recurrence_type'  => $data['recurrence_type'],
            'recurrence_meta'  => $recurrenceMeta,
        ]);

        // Expand into occurrences
        app(\App\Services\RecurrenceService::class)->generateOccurrences($lesson);

        return redirect()->back()->with('success', 'Lesson created successfully.');
    }

}

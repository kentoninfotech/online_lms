<?php

namespace App\Http\Controllers;

use App\Models\LessonOccurrence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class CalendarController extends Controller
{
    public function fetchEvents(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasRole('student')) {
            $occurrences = LessonOccurrence::whereHas('lesson', function ($q) use ($user) {
                $q->where('student_id', $user->student->id);
            })->with('lesson')->get();
        } elseif ($user->hasRole('instructor')) {
            $occurrences = LessonOccurrence::whereHas('lesson', function ($q) use ($user) {
                $q->where('instructor_id', $user->instructor->id);
            })->with('lesson')->get();
        } else {
            $occurrences = LessonOccurrence::with('lesson')->get();
        }

        // Map to FullCalendar format
        $events = $occurrences->map(function ($occurrence) {
            // Calculate the end time
            $startTime = Carbon::parse($occurrence->scheduled_start);
            $endTime = $startTime->copy()->addMinutes($occurrence->duration_minutes);

            // Determine styling based on status
            $color = match ($occurrence->status) {
                'scheduled' => '#ff00eaff', 
                'completed' => '#00ff2aff', 
                'pending'   => '#f59e0b', 
                'cancelled' => '#ef4444',
                default     => '#3b82f6', 
            };

            return [
                'id' => $occurrence->id,
                'title' => $occurrence->lesson->subject ?? 'Lesson',
                'instructor' => $occurrence->lesson->instructor->name ?? 'N/A',
                'student' => $occurrence->lesson->student->name ?? 'N/A',
                'start' => $startTime->toIso8601String(), // Required ISO format
                'end' => $endTime->toIso8601String(),   // Required ISO format
                'color' => $color,
                'extendedProps' => [
                    'status' => $occurrence->status,
                    'lessonId' => $occurrence->lesson_id,
                ],
                'url' => route('lesson.join', $occurrence) // Link to lesson details
            ];
        });

        return response()->json($events);
    }


}

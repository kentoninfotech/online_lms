<?php

namespace App\Http\Controllers;

use App\Models\LessonOccurrence;
use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JoinClassController extends Controller
{
    public function join(LessonOccurrence $occurrence, Request $request)
    {
        $user = auth()->user();
        $now = Carbon::now();

        $zoomSession = $occurrence->zoomSession;
        if (! $occurrence->lesson->instructor->zoom_link) {
            return back()->with('error', 'Zoom meeting is not available for this class.');
        }

        // Guard: class expired
        if ($now->gt($occurrence->scheduled_end)) {
            return back()->with('error', 'This class has already ended.');
        }

        // Guard: too early
        if ($user->hasRole('student') && $now->lt($occurrence->scheduled_start->subMinutes(10))) {
            return redirect()->route('lesson.waiting', $occurrence);
        }

        // Guard: active subscription
        if ($user->hasRole('student') && (!$user->student || !$user->student->hasActiveSubscription())) {
            return back()->with('error', 'Your subscription is not active.');
        }

        // Guard: instructor match
        if ($user->hasRole('instructor') && $occurrence->lesson->instructor_id !== $user->instructor->id) {
            abort(403, 'You are not the instructor for this class.');
        }

        // Mark attendance
        $this->markAttendance($occurrence, $user);

        // Redirect to Zoom link
        return $user->hasRole('instructor') || $user->hasRole('admin')
            ? redirect($occurrence->lesson->instructor->zoom_link)
            : redirect($occurrence->lesson->instructor->zoom_link);
            // ? redirect($zoomSession->start_url)
            // : redirect($zoomSession->join_url);
    }

    public function waiting(LessonOccurrence $occurrence)
    {
        $remaining = $occurrence->scheduled_start->diffInSeconds(now());

        return view('dashboard.waiting', compact('occurrence', 'remaining'));
    }
  
    private function markAttendance(LessonOccurrence $occurrence, $user)
    {
        $now = now();
        $settingsGraceMinutes = (int) Setting::where('key','attendance_grace_period_minutes')->first()->value ?? 10;

        $attendableType = match (true) {
            $user->hasRole('student') => \App\Models\Student::class,
            $user->hasRole('instructor') => \App\Models\Instructor::class,
            default => \App\Models\User::class,
        };

        $attendableId = match (true) {
            $user->hasRole('student') => $user->student->id ?? null,
            $user->hasRole('instructor') => $user->instructor->id ?? null,
            default => $user->id,
        };

        if (!$attendableId) return;

        // Grace period check
        $graceDeadline = $occurrence->scheduled_start->copy()->addMinutes($settingsGraceMinutes);
        $status = $now->lte($graceDeadline) ? 'present' : 'late';

        // Calculate duration
        $leaveTime = $occurrence->scheduled_end;
        
        $durationMinutes = max(0, $now->diffInMinutes($leaveTime, false));
        // $durationMinutes = max(0, $leaveTime->diffInMinutes($now));

        Attendance::updateOrCreate(
            [
                'lesson_occurrence_id' => $occurrence->id,
                'attendable_id' => $attendableId,
                'attendable_type' => $attendableType,
            ],
            [
                'join_time' => $now,
                'leave_time' => $leaveTime,
                'duration_minutes' => $durationMinutes,
                'status' => $status,
            ]
        );
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseLiveSession;
use App\Models\LiveSessionAttendance;
use App\Models\CourseEnrollee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveSessionController extends Controller
{
    /**
     * Show live session
     */
    public function show(Course $course, CourseLiveSession $session)
    {
        // Record attendance
        if (Auth::check()) {
            $attendance = LiveSessionAttendance::firstOrCreate([
                'live_session_id' => $session->id,
                'user_id' => Auth::id(),
            ], [
                'joined_at' => now(),
                'attendance_status' => 'present'
            ]);
        }

        $attendees = $session->attendances()->with('user')->get();

        return view('courses.live-session', compact('course', 'session', 'attendees'));
    }

    /**
     * Admin: List all live sessions
     */
    public function adminIndex(Course $course)
    {
        $this->authorize('update', $course);

        $sessions = $course->liveSessions()->with('facilitator')->get();

        return view('admin.live-sessions.index', compact('course', 'sessions'));
    }

    /**
     * Admin: Show single live session
     */
    public function adminShow(Course $course, CourseLiveSession $session)
    {
        $this->authorize('update', $course);

        return view('admin.live-sessions.show', compact('course', 'session'));
    }

    /**
     * Admin: Create live session
     */
    public function adminCreate(Course $course)
    {
        $this->authorize('update', $course);

        $facilitators = \App\Models\Facilitator::where('is_active', true)->get();

        return view('admin.live-sessions.create', compact('course', 'facilitators'));
    }

    /**
     * Admin: Store live session
     */
    public function adminStore(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'facilitator_id' => 'required|exists:facilitators,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_start' => 'required|date_format:Y-m-d H:i',
            'scheduled_end' => 'required|date_format:Y-m-d H:i|after:scheduled_start',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'is_compulsory' => 'boolean',
            'max_points' => 'nullable|integer|min:0|max:100',
            'jitsi_room_name' => 'nullable|string|max:255|unique:course_live_sessions',
            'chat_enabled' => 'boolean',
            'session_type' => 'required|in:zoom,meet,teams,jitsi,other',
            'meeting_link' => 'nullable|url',
            'meeting_id' => 'nullable|string',
            'meeting_password' => 'nullable|string',
        ]);

        // Generate Jitsi room name automatically if not provided
        if (!$validated['jitsi_room_name']) {
            $validated['jitsi_room_name'] = 'room-' . $course->id . '-' . time();
        }

        $validated['course_id'] = $course->id;
        $validated['status'] = 'scheduled';
        $validated['is_compulsory'] = $validated['is_compulsory'] ?? false;
        $validated['chat_enabled'] = $validated['chat_enabled'] ?? true;

        CourseLiveSession::create($validated);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Live session scheduled successfully.');
    }

    /**
     * Admin: Update live session
     */
    public function adminUpdate(Request $request, Course $course, CourseLiveSession $session)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'facilitator_id' => 'required|exists:facilitators,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_start' => 'required|date_format:Y-m-d H:i',
            'scheduled_end' => 'required|date_format:Y-m-d H:i|after:scheduled_start',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'is_compulsory' => 'boolean',
            'max_points' => 'nullable|integer|min:0|max:100',
            'jitsi_room_name' => 'nullable|string|max:255|unique:course_live_sessions,jitsi_room_name,' . $session->id,
            'chat_enabled' => 'boolean',
            'session_type' => 'required|in:zoom,meet,teams,jitsi,other',
            'meeting_link' => 'nullable|url',
            'meeting_id' => 'nullable|string',
            'meeting_password' => 'nullable|string',
            'status' => 'in:scheduled,live,completed,cancelled'
        ]);

        // Generate Jitsi room name if not provided and not already set
        if (!$validated['jitsi_room_name'] && !$session->jitsi_room_name) {
            $validated['jitsi_room_name'] = 'room-' . $course->id . '-' . time();
        }

        $validated['is_compulsory'] = $validated['is_compulsory'] ?? false;
        $validated['chat_enabled'] = $validated['chat_enabled'] ?? true;

        $session->update($validated);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Live session updated successfully.');
    }

    /**
     * Admin: Delete live session
     */
    public function adminDestroy(Course $course, CourseLiveSession $session)
    {
        $this->authorize('update', $course);

        $session->delete();

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Live session deleted successfully.');
    }

    /**
     * List user's upcoming live sessions
     */
    public function upcomingSessions()
    {
        $sessions = CourseLiveSession::where('status', 'scheduled')
            ->where('scheduled_start', '>=', now())
            ->orderBy('scheduled_start')
            ->paginate(10);

        return view('courses.upcoming-sessions', compact('sessions'));
    }

    /**
     * Admin: List all live sessions globally
     */
    public function adminListAll()
    {
        if (auth()->user()->user_type !== 'admin') {
            abort(403);
        }

        $sessions = CourseLiveSession::with('course', 'facilitator')
            ->orderBy('scheduled_start', 'desc')
            ->paginate(15);

        return view('admin.live-sessions-all.index', compact('sessions'));
    }
}

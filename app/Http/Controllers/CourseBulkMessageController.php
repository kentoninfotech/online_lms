<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseBulkMessage;
use App\Models\CourseBulkMessageRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendCourseAnnouncementJob;

class CourseBulkMessageController extends Controller
{
    /**
     * Show bulk message form for course
     */
    public function create(Course $course)
    {
        // Check if user is admin or course tutor
        if (!Auth::user()->hasRole('admin') && $course->facilitator_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $enrolleeCount = $course->enrollees()->where('status', 'active')->count();

        return view('courses.bulk-message.create', compact('course', 'enrolleeCount'));
    }

    /**
     * Store bulk message
     */
    public function store(Request $request, Course $course)
    {
        // Check authorization
        if (!Auth::user()->hasRole('admin') && $course->facilitator_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'methods' => 'required|array|min:1',
            'methods.*' => 'in:email,sms',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        try {
            DB::beginTransaction();

            // Create bulk message
            $bulkMessage = CourseBulkMessage::create([
                'course_id' => $course->id,
                'sender_id' => Auth::id(),
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'methods' => $validated['methods'],
                'status' => $validated['scheduled_at'] ? 'scheduled' : 'draft',
                'scheduled_at' => $validated['scheduled_at'] ?? null,
            ]);

            // Get enrolled students
            $enrollees = $course->enrollees()
                ->where('status', 'active')
                ->with('user')
                ->get();

            // Create recipient records
            foreach ($enrollees as $enrollee) {
                CourseBulkMessageRecipient::create([
                    'course_bulk_message_id' => $bulkMessage->id,
                    'user_id' => $enrollee->user_id,
                    'email' => $enrollee->user->email,
                    'phone' => $enrollee->user->phone ?? null,
                    'status' => 'pending',
                ]);
            }

            // Update total recipients count
            $bulkMessage->update(['total_recipients' => $enrollees->count()]);

            // Send immediately if not scheduled
            if (!$validated['scheduled_at']) {
                SendCourseAnnouncementJob::dispatch($bulkMessage);
                $bulkMessage->update(['status' => 'sent']);
            }

            DB::commit();

            return redirect()->route('courses.show', $course)
                ->with('success', 'Announcement sent successfully to ' . $enrollees->count() . ' students.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to send announcement: ' . $e->getMessage());
        }
    }

    /**
     * Show bulk message history for course
     */
    public function history(Course $course)
    {
        // Check authorization
        if (!Auth::user()->hasRole('admin') && $course->facilitator_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $messages = $course->bulkMessages()
            ->with('sender', 'recipients')
            ->paginate(15);

        return view('courses.bulk-message.history', compact('course', 'messages'));
    }

    /**
     * Show message details
     */
    public function show(CourseBulkMessage $message)
    {
        // Check authorization
        if (!Auth::user()->hasRole('admin') && $message->sender_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $message->load('course', 'sender', 'recipients');
        $sentCount = $message->recipients()->where('status', 'sent')->count();
        $failedCount = $message->recipients()->where('status', 'failed')->count();

        return view('courses.bulk-message.show', compact('message', 'sentCount', 'failedCount'));
    }

    /**
     * Send a scheduled announcement immediately
     */
    public function send(CourseBulkMessage $message)
    {
        // Check authorization
        if (!Auth::user()->hasRole('admin') && $message->sender_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Only allow sending scheduled messages
        if ($message->status !== 'scheduled') {
            return back()->with('error', 'Only scheduled announcements can be sent.');
        }

        try {
            // Dispatch the job to send the announcement
            SendCourseAnnouncementJob::dispatch($message);
            
            // Update status to sent
            $message->update([
                'status' => 'sent',
                'scheduled_at' => null
            ]);

            return back()->with('success', 'Announcement sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send announcement: ' . $e->getMessage());
        }
    }
}

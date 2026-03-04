<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseDiscussion;
use App\Models\DiscussionReply;
use App\Models\CourseEnrollee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseDiscussionController extends Controller
{
    /**
     * Show course discussions
     */
    public function index(Course $course)
    {
        // Verify user is enrolled
        $enrollment = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        $discussions = $course->discussions()
            ->with('author')
            ->withCount('replies')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('courses.discussions.index', compact('course', 'discussions', 'enrollment'));
    }

    /**
     * Create new discussion
     */
    public function create(Course $course)
    {
        $enrollment = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        return view('courses.discussions.create', compact('course'));
    }

    /**
     * Store new discussion
     */
    public function store(Request $request, Course $course)
    {
        $enrollment = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Map 'content' from form to 'message' in database
        $discussion = CourseDiscussion::create([
            'course_id' => $course->id,
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'message' => $validated['content'],
        ]);

        return redirect()->route('courses.discussions.show', [$course, $discussion])
            ->with('success', 'Discussion created successfully.');
    }

    /**
     * Show single discussion with replies
     */
    public function show(Course $course, CourseDiscussion $discussion)
    {
        $enrollment = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Eager load the discussion's author (user)
        $discussion->load('author');

        $replies = $discussion->replies()
            ->with('author')
            ->orderBy('created_at')
            ->paginate(15);

        // Get recent course announcements
        $announcements = $course->bulkMessages()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('courses.discussions.show', compact('course', 'discussion', 'replies', 'enrollment', 'announcements'));
    }

    /**
     * Admin: List all discussions
     */
    public function adminIndex(Course $course = null)
    {
        if ($course) {
            // Tutor viewing discussions for their course
            $this->authorize('update', $course);
            $discussions = $course->discussions()
                ->with('user')
                ->withCount('replies')
                ->orderByDesc('is_pinned')
                ->orderByDesc('created_at')
                ->paginate(20);
        } else {
            // Admin viewing all discussions
            if (auth()->user()->user_type !== 'admin') {
                abort(403);
            }
            $discussions = CourseDiscussion::with('user', 'course')
                ->withCount('replies')
                ->paginate(20);
        }

        return view('admin.discussions.index', compact('discussions', 'course'));
    }

    /**
     * Admin: Show single discussion
     */
    public function adminShow(CourseDiscussion $discussion)
    {
        $this->authorize('update', $discussion->course);

        $replies = $discussion->replies()->with('user')->get();

        return view('admin.discussions.show', compact('discussion', 'replies'));
    }

    /**
     * Admin: Delete discussion
     */
    public function adminDestroy(CourseDiscussion $discussion)
    {
        $this->authorize('update', $discussion->course);

        $discussion->delete();

        return redirect()->route(
                auth()->user()->user_type === 'instructor' ? 'tutor.discussions.index' : 'admin.discussions.index'
            )
            ->with('success', 'Discussion deleted successfully.');
    }

    /**
     * Reply to discussion
     */
    public function reply(Request $request, Course $course, CourseDiscussion $discussion)
    {
        $enrollment = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        $validated = $request->validate([
            'message' => 'required|string',
            'reply_to_id' => 'nullable|exists:discussion_replies,id',
        ]);

        $validated['discussion_id'] = $discussion->id;
        $validated['user_id'] = Auth::id();

        DiscussionReply::create($validated);

        $discussion->increment('replies_count');

        return redirect()->route('courses.discussions.show', [$course, $discussion])
            ->with('success', 'Reply posted successfully.');
    }

    /**
     * Admin: Pin/Unpin discussion
     */
    public function togglePin(Course $course, CourseDiscussion $discussion)
    {
        $this->authorize('update', $discussion->course);

        $discussion->update(['is_pinned' => !$discussion->is_pinned]);

        return redirect()->route('courses.discussions.show', [$course, $discussion])
            ->with('success', 'Discussion updated.');
    }

    /**
     * Admin: Lock/Unlock discussion
     */
    public function toggleLock(Course $course, CourseDiscussion $discussion)
    {
        $this->authorize('update', $discussion->course);

        $discussion->update(['is_locked' => !$discussion->is_locked]);

        return redirect()->route('courses.discussions.show', [$course, $discussion])
            ->with('success', 'Discussion updated.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollee;
use App\Models\CourseVenue;
use App\Models\CoursePayment;
use App\Http\Requests\EnrollmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseEnrollmentController extends Controller
{
    /**
     * Show enrollment form
     */
    public function create(Course $course)
    {
        $courseDates = $course->courseDates()->with('venues')->get();

        return view('courses.enrollment', compact('course', 'courseDates'));
    }

    /**
     * Process enrollment
     */
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'course_date_id' => 'required|exists:course_dates,id',
            'course_venue_id' => 'required|exists:course_venues,id',
        ]);


        // Check if user is already enrolled in this course/date/venue
        $exists = CourseEnrollee::where([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'course_date_id' => $validated['course_date_id'],
            'course_venue_id' => $validated['course_venue_id'],
        ])->exists();

        if ($exists) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'You are already enrolled in this course session.');
        }

        // Check venue capacity
        $venue = CourseVenue::findOrFail($validated['course_venue_id']);
        if ($venue->isAtCapacity()) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'This venue is at full capacity.');
        }

        DB::beginTransaction();

        try {
            // Determine enrollment status based on whether course is free
            // Free courses require admin approval, paid courses require payment
            $enrollmentStatus = $course->is_free ? 'pending' : 'pending';
            $paymentStatus = $course->is_free ? 'completed' : 'pending';
            
            // Create enrollment
            $enrollment = CourseEnrollee::create([
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'course_date_id' => $validated['course_date_id'],
                'course_venue_id' => $validated['course_venue_id'],
                'status' => $enrollmentStatus,
                'payment_status' => $paymentStatus,
                'enrolled_at' => now(),
            ]);

            $payment = null;
            
            // Only create payment record if course is not free
            if (!$course->is_free) {
                $payment = CoursePayment::create([
                    'course_enrollee_id' => $enrollment->id,
                    'user_id' => Auth::id(),
                    'course_id' => $course->id,
                    'amount' => $course->fee,
                    'currency' => $course->currency,
                    'reference_id' => 'CRS-' . time() . '-' . Auth::id(),
                    'payment_method' => 'pending',
                    'status' => 'pending',
                ]);
            }

            // Update venue enrolled count
            $venue->increment('enrolled_count');

            // Update course enrolled count
            $course->increment('enrolled_count');

            DB::commit();

            // Redirect based on whether course is free
            if ($course->is_free) {
                return redirect()->route('my-enrollments')
                    ->with('success', 'Enrollment submitted! Admin will review your enrollment shortly.');
            } else {
                return redirect()->route('course.payment.show', $payment)
                    ->with('success', 'Enrollment created. Please proceed to payment.');
            }

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->route('courses.show', $course)
                ->with('error', 'An error occurred during enrollment: ' . $e->getMessage());
        }
    }

    /**
     * Show user enrollments
     */
    public function myEnrollments()
    {
        $enrollments = CourseEnrollee::where('user_id', Auth::id())
            ->with('course', 'courseDate', 'venue')
            ->paginate(12);

        return view('courses.my-enrollments', compact('enrollments'));
    }

    /**
     * Admin: View all enrollments
     */
    public function adminIndex(Course $course = null)
    {
        if ($course) {
            // Tutor viewing enrollments for their course
            $this->authorize('viewEnrollees', $course);
            $enrollments = $course->enrollees()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            // Admin viewing all enrollments
            if (auth()->user()->user_type !== 'admin') {
                abort(403);
            }
            $enrollments = CourseEnrollee::with('user', 'course', 'courseDate', 'venue')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return view('admin.course-enrollments.index', compact('enrollments', 'course'));
    }

    /**
     * Admin: Show single enrollment
     */
    public function adminShow(CourseEnrollee $enrollment)
    {
        // Check if user can view this course's enrollments
        $this->authorize('viewEnrollees', $enrollment->course);

        $enrollment->load('user', 'course', 'courseDate', 'venue');

        return view('admin.course-enrollments.show', compact('enrollment'));
    }

    /**
     * Admin: Update enrollment status
     */
    public function adminUpdate(Request $request, CourseEnrollee $enrollment)
    {
        // Check if user can update this course's enrollments
        $this->authorize('update', $enrollment->course);

        $validated = $request->validate([
            'status' => 'required|in:pending,active,completed,cancelled',
        ]);

        $enrollment->update($validated);

        return redirect()->route(
                auth()->user()->user_type === 'instructor' ? 'tutor.course-enrollments.show' : 'admin.course-enrollments.show',
                $enrollment
            )
            ->with('success', 'Enrollment status updated successfully.');
    }
}

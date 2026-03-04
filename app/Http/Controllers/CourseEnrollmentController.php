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
        // Load course dates - they're optional but available for selection
        $courseDates = $course->courseDates()->with('venues')->get();

        return view('courses.enrollment', compact('course', 'courseDates'));
    }

    /**
     * Process enrollment
     */
    public function store(Request $request, Course $course)
    {
        // Date and venue are completely optional - users can enroll without selecting them
        $validated = $request->validate([
            'course_date_id' => 'nullable|exists:course_dates,id',
            'course_venue_id' => 'nullable|max:255',
        ], [
            'course_date_id.exists' => 'The selected course date is invalid.',
        ]);

        // Convert empty strings to null for consistency
        $validated['course_date_id'] = $validated['course_date_id'] ?: null;
        $validated['course_venue_id'] = $validated['course_venue_id'] ?: null;

        // Debug log
        \Log::info('Enrollment form submitted', [
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'validated_data' => $validated,
            'request_all' => $request->all(),
        ]);

        // Convert empty strings to null
        $validated['course_date_id'] = $validated['course_date_id'] ?: null;
        $validated['course_venue_id'] = $validated['course_venue_id'] ?: null;

        // For hybrid courses, convert 'online-na' to null (means student chose online)
        if ($validated['course_venue_id'] === 'online-na') {
            $validated['course_venue_id'] = null;
        }

        // Check venue capacity (only if venue was selected)
        $venue = null;
        if (isset($validated['course_venue_id']) && $validated['course_venue_id']) {
            $venue = CourseVenue::findOrFail($validated['course_venue_id']);
            if ($venue->isAtCapacity()) {
                return redirect()->route('courses.show', $course)
                    ->with('error', 'This venue is at full capacity.');
            }
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
                'course_date_id' => $validated['course_date_id'] ?? null,
                'course_venue_id' => $validated['course_venue_id'] ?? null,
                'status' => $enrollmentStatus,
                'payment_status' => $paymentStatus,
                'enrolled_at' => now(),
            ]);

            // Always create payment record (even for free courses)
            $payment = CoursePayment::create([
                'course_enrollee_id' => $enrollment->id,
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'amount' => $course->fee ?? 0,
                'currency' => $course->currency,
                'reference_id' => 'CRS-' . time() . '-' . Auth::id(),
                'payment_method' => $course->is_free ? 'free' : 'pending',
                'status' => $course->is_free ? 'completed' : 'pending',
            ]);

            // Update venue enrolled count (only if venue was selected)
            if ($venue) {
                $venue->increment('enrolled_count');
            }

            // Update course enrolled count
            $course->increment('enrolled_count');

            DB::commit();

            // Debug log successful creation
            \Log::info('Enrollment and payment created successfully', [
                'enrollment_id' => $enrollment->id,
                'payment_id' => $payment->id,
                'course_id' => $course->id,
                'user_id' => Auth::id(),
                'amount' => $payment->amount,
            ]);

            // Always redirect to payment page (shows payment status or confirmation for free courses)
            // Use payment ID explicitly for safer route binding
            $paymentUrl = route('course.payment.show', ['payment' => $payment->id]);
            
            \Log::info('Enrollment redirect', [
                'payment_id' => $payment->id,
                'payment_url' => $paymentUrl,
                'course_id' => $course->id,
            ]);
            
            return redirect($paymentUrl)
                ->with('success', 'Enrollment created. Proceeding to payment...');

        } catch (\Exception $e) {

            DB::rollBack();

            // Log the full error for debugging
            \Log::error('Enrollment Error', [
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('courses.show', $course)
                ->with('error', 'An error occurred during enrollment. Please try again or contact support. Error: ' . $e->getMessage());
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

    /**
     * Admin: Delete enrollment
     */
    public function adminDestroy(CourseEnrollee $enrollment)
    {
        // Check if user can update this course's enrollments
        $this->authorize('update', $enrollment->course);

        $courseName = $enrollment->course?->title ?? 'Unknown Course';
        $studentName = $enrollment->user?->name ?? 'Unknown Student';
        
        // Soft delete the enrollment
        $enrollment->delete();

        return redirect()->route(
                auth()->user()->user_type === 'instructor' ? 'tutor.course-enrollments.index' : 'admin.course-enrollments.index'
            )
            ->with('success', "Enrollment for {$studentName} in {$courseName} has been deleted successfully.");
    }
}

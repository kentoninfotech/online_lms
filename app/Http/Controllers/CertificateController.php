<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollee;
use App\Models\QuizSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    /**
     * View certificate
     */
    public function view(Course $course)
    {
        // Verify enrollment and completion
        $courseEnrollee = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Check if course is completed
        if (!$courseEnrollee->is_completed) {
            abort(403, 'Course not completed');
        }

        return view('certificates.pdf', compact('course', 'courseEnrollee'));
    }

    /**
     * Download certificate as PDF
     * Uses DomPDF or similar
     */
    public function download(Course $course, ?QuizSubmission $submission = null)
    {
        // Verify enrollment
        $courseEnrollee = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Check if course is completed (passed all required quizzes)
        $requiredQuizzes = $course->quizzes()->where('is_required', true)->get();
        
        foreach ($requiredQuizzes as $quiz) {
            $lastSubmission = QuizSubmission::where('course_enrollee_id', $courseEnrollee->id)
                ->where('quiz_id', $quiz->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$lastSubmission || !$lastSubmission->is_passed) {
                return redirect()->back()->with('error', 'You must pass all required quizzes to download the certificate.');
            }
        }

        // Generate PDF
        $html = view('certificates.pdf', compact('course', 'courseEnrollee'))->render();

        // If DomPDF is installed, use it
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
                ->setPaper('a4', 'landscape')
                ->download('Certificate-' . Str::slug($courseEnrollee->user->name) . '.pdf');
        }

        // Fallback: return HTML view with print stylesheet
        return view('certificates.pdf', compact('course', 'courseEnrollee'));
    }

    /**
     * Mark course as completed
     */
    public function markComplete(Course $course)
    {
        $courseEnrollee = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Update completion status
        $courseEnrollee->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Course marked as completed!');
    }

    /**
     * Generate certificate for course completion
     */
    public function generate(Course $course, CourseEnrollee $enrollment)
    {
        // Verify the enrollment belongs to the current user
        if ($enrollment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Verify enrollment is for this course
        if ($enrollment->course_id !== $course->id) {
            abort(404, 'Enrollment not found for this course');
        }

        // Check if course is 100% complete
        if (!$enrollment->isCourseComplete()) {
            return redirect()->back()->with('error', 'You must complete 100% of the course to generate a certificate.');
        }

        // Generate certificate
        $certificate = $enrollment->generateCertificate();

        if (!$certificate) {
            return redirect()->back()->with('info', 'Certificate already generated. You can download it below.');
        }

        return redirect()->back()->with('success', 'Certificate generated successfully! You can now download it.');
    }

    /**
     * Download certificate as PDF
     */
    public function downloadCertificate(Course $course, CourseEnrollee $enrollment)
    {
        // Verify the enrollment belongs to the current user
        if ($enrollment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Verify enrollment is for this course
        if ($enrollment->course_id !== $course->id) {
            abort(404, 'Enrollment not found for this course');
        }

        // Get the certificate
        $certificate = $enrollment->certificate()->where('is_revoked', false)->latest()->first();

        if (!$certificate) {
            return redirect()->back()->with('error', 'No valid certificate found. Please generate a certificate first.');
        }

        // Check if certificate is still valid
        if (!$certificate->isValid()) {
            return redirect()->back()->with('error', 'Your certificate has expired or been revoked.');
        }

        // Generate PDF view
        $html = view('certificates.completion-certificate', compact('course', 'enrollment', 'certificate'))->render();

        // If DomPDF is installed, use it
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
                ->setPaper('a4', 'landscape')
                ->download('Certificate-' . Str::slug($course->title) . '-' . Str::slug($enrollment->user->name) . '.pdf');
        }

        // Fallback: return HTML view with print stylesheet
        return view('certificates.completion-certificate', compact('course', 'enrollment', 'certificate'));
    }
}

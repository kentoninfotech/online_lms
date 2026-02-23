<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseQuiz;
use App\Models\QuizSubmission;
use App\Models\QuizSubmissionAnswer;
use Illuminate\Http\Request;

class QuizSubmissionController extends Controller
{
    /**
     * List all submissions for a quiz
     */
    public function submissions(Course $course, CourseQuiz $quiz)
    {
        $this->authorize('isAdmin');

        $submissions = QuizSubmission::where('quiz_id', $quiz->id)
            ->with(['courseEnrollee.user', 'answers.question'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.course-quizzes.submissions', compact('course', 'quiz', 'submissions'));
    }

    /**
     * View individual submission for grading
     */
    public function viewSubmission(Course $course, CourseQuiz $quiz, QuizSubmission $submission)
    {
        $this->authorize('isAdmin');

        $submission->load(['courseEnrollee.user', 'answers.question', 'answers']);

        return view('admin.course-quizzes.view-submission', compact('course', 'quiz', 'submission'));
    }

    /**
     * Mark submission as reviewed
     */
    public function markReviewed(Request $request, Course $course, CourseQuiz $quiz, QuizSubmission $submission)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'general_feedback' => 'nullable|string',
        ]);

        // Update score if different
        if ($validated['score'] !== $submission->score) {
            // Recalculate if score changed
            $totalPoints = $submission->answers()->sum('points_earned');
            $submission->update([
                'score' => $validated['score'],
                'is_passed' => $validated['score'] >= $quiz->passing_score,
            ]);
        }

        // Store general feedback
        if ($validated['general_feedback']) {
            $submission->update([
                'tutor_feedback' => $validated['general_feedback'],
            ]);
        }

        // Mark as reviewed
        $submission->update([
            'reviewed_at' => now(),
            'reviewed_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('admin.course-quizzes.submissions', [$course, $quiz])
            ->with('success', 'Submission marked as reviewed.');
    }

    /**
     * Save feedback for specific question
     */
    public function saveFeedback(Request $request, Course $course, CourseQuiz $quiz, QuizSubmission $submission)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'feedback' => 'nullable|string',
            'points' => 'nullable|integer|min:0',
        ]);

        $questionId = $request->input('question_id');

        $answer = QuizSubmissionAnswer::where('submission_id', $submission->id)
            ->where('question_id', $questionId)
            ->first();

        if (!$answer) {
            return response()->json(['success' => false, 'message' => 'Answer not found']);
        }

        // Update feedback if provided
        if (isset($validated['feedback'])) {
            $answer->update([
                'tutor_feedback' => $validated['feedback'],
            ]);
        }

        // Update points if provided and different
        if (isset($validated['points']) && $validated['points'] !== $answer->points_earned) {
            $answer->update([
                'points_earned' => $validated['points'],
            ]);

            // Recalculate submission score
            $totalPoints = 0;
            $earnedPoints = 0;

            foreach ($submission->answers as $ans) {
                $totalPoints += $ans->question->points;
                $earnedPoints += $ans->points_earned;
            }

            $newScore = $totalPoints > 0 ? (int)(($earnedPoints / $totalPoints) * 100) : 0;

            $submission->update([
                'score' => $newScore,
                'is_passed' => $newScore >= $quiz->passing_score,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Feedback saved']);
    }
}

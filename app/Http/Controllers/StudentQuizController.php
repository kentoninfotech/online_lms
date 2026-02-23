<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseQuiz;
use App\Models\QuizSubmission;
use App\Models\QuizSubmissionAnswer;
use App\Models\CourseEnrollee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentQuizController extends Controller
{
    /**
     * Take a quiz
     */
    public function take(Course $course, CourseQuiz $quiz)
    {
        // Verify enrollment
        $enrollment = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Check attempts
        $attemptCount = QuizSubmission::where('course_enrollee_id', $enrollment->id)
            ->where('quiz_id', $quiz->id)
            ->count();

        if ($attemptCount >= $quiz->attempts_allowed) {
            return redirect()->route('student.course.show', $course)
                ->with('error', 'You have exceeded the maximum number of attempts for this quiz.');
        }

        $questions = $quiz->questions()
            ->with('answers')
            ->orderBy($quiz->shuffle_questions ? \DB::raw('RAND()') : 'sequence')
            ->get();

        $attemptsRemaining = $quiz->attempts_allowed - $attemptCount;

        return view('student.course.quiz.take', compact('course', 'quiz', 'questions', 'attemptsRemaining'));
    }

    /**
     * Submit quiz answers
     */
    public function submit(Request $request, Course $course, CourseQuiz $quiz)
    {
        // Verify enrollment
        $enrollment = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Check attempts
        $attemptCount = QuizSubmission::where('course_enrollee_id', $enrollment->id)
            ->where('quiz_id', $quiz->id)
            ->count();

        if ($attemptCount >= $quiz->attempts_allowed) {
            return redirect()->route('student.course.show', $course)
                ->with('error', 'You have exceeded the maximum number of attempts.');
        }

        $answers = $request->input('answers', []);
        $timeTaken = $request->input('time_taken_minutes', 0);
        $questions = $quiz->questions()->with('answers')->get();

        $totalQuestions = $questions->count();
        $correctAnswers = 0;
        $totalPoints = 0;
        $earnedPoints = 0;

        // Create submission record
        $submission = QuizSubmission::create([
            'course_enrollee_id' => $enrollment->id,
            'quiz_id' => $quiz->id,
            'attempt_number' => $attemptCount + 1,
            'total_questions' => $totalQuestions,
            'time_taken_minutes' => $timeTaken,
            'score' => 0,
            'is_passed' => false,
        ]);

        // Process each question
        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $totalPoints += $question->points;
            $isCorrect = false;
            $pointsEarned = 0;

            // Grade the answer based on question type
            if ($question->question_type === 'short_answer') {
                // For short answers, check if user answer is in the acceptable answers
                $correctAnswers_ = is_array($question->correct_answer) ? $question->correct_answer : [$question->correct_answer];
                $isCorrect = $this->checkShortAnswer($userAnswer, $correctAnswers_);
                if ($isCorrect) {
                    $pointsEarned = $question->points;
                }
            } else if (in_array($question->question_type, ['multiple_choice', 'multiple_answer'])) {
                // For multiple choice/answer, compare selected answer IDs
                $userSelectedIds = is_array($userAnswer) ? array_map('intval', $userAnswer) : [intval($userAnswer)];
                $correctAnswerIds = $question->answers()
                    ->where('is_correct', true)
                    ->pluck('id')
                    ->map('intval')
                    ->toArray();

                if ($question->question_type === 'multiple_choice') {
                    // Single correct answer
                    $isCorrect = count($userSelectedIds) === 1 && $userSelectedIds[0] === $correctAnswerIds[0] ?? null;
                } else {
                    // Multiple correct answers must all match
                    $isCorrect = count($userSelectedIds) === count($correctAnswerIds) &&
                                 empty(array_diff($userSelectedIds, $correctAnswerIds));
                }

                if ($isCorrect) {
                    $pointsEarned = $question->points;
                }
            } else if ($question->question_type === 'true_false') {
                $isCorrect = $userAnswer === $question->correct_answer;
                if ($isCorrect) {
                    $pointsEarned = $question->points;
                }
            } else if ($question->question_type === 'yes_no') {
                $isCorrect = $userAnswer === $question->correct_answer;
                if ($isCorrect) {
                    $pointsEarned = $question->points;
                }
            }

            // Store answer
            QuizSubmissionAnswer::create([
                'submission_id' => $submission->id,
                'question_id' => $question->id,
                'student_answer' => is_array($userAnswer) ? json_encode($userAnswer) : $userAnswer,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
            ]);

            if ($isCorrect) {
                $correctAnswers++;
                $earnedPoints += $pointsEarned;
            }
        }

        // Calculate final score
        $score = $totalPoints > 0 ? (int)(($earnedPoints / $totalPoints) * 100) : 0;
        $isPassed = $score >= $quiz->passing_score;

        $submission->update([
            'correct_answers' => $correctAnswers,
            'score' => $score,
            'is_passed' => $isPassed,
        ]);

        return redirect()->route('student.quiz.results', [$course, $quiz, $submission])
            ->with('success', 'Quiz submitted successfully.');
    }

    /**
     * View quiz results
     */
    public function results(Course $course, CourseQuiz $quiz, QuizSubmission $submission)
    {
        // Verify enrollment
        $enrollment = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Verify submission belongs to current user
        if ($submission->course_enrollee_id !== $enrollment->id) {
            abort(403, 'Unauthorized');
        }

        $attemptsRemaining = $quiz->attempts_allowed - QuizSubmission::where('course_enrollee_id', $enrollment->id)
            ->where('quiz_id', $quiz->id)
            ->count();

        $previousAttempts = QuizSubmission::where('course_enrollee_id', $enrollment->id)
            ->where('quiz_id', $quiz->id)
            ->where('id', '!=', $submission->id)
            ->orderBy('attempt_number', 'desc')
            ->get();

        return view('student.course.quiz.results', compact('course', 'quiz', 'submission', 'attemptsRemaining', 'previousAttempts'));
    }

    /**
     * Check if short answer is acceptable
     */
    private function checkShortAnswer($userAnswer, $acceptableAnswers)
    {
        if (!$userAnswer) {
            return false;
        }

        $userAnswer = strtolower(trim($userAnswer));

        foreach ($acceptableAnswers as $acceptable) {
            $acceptable = strtolower(trim($acceptable));

            // Exact match (case-insensitive)
            if ($userAnswer === $acceptable) {
                return true;
            }

            // Partial match if answer contains at least 80% of the acceptable answer
            similar_text($userAnswer, $acceptable, $percent);
            if ($percent >= 80) {
                return true;
            }
        }

        return false;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseQuiz;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\QuizSubmission;
use App\Models\QuizSubmissionAnswer;
use App\Models\CourseEnrollee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseQuizController extends Controller
{
    /**
     * Show quiz for user
     */
    public function show(Course $course, CourseQuiz $quiz)
    {
        // Verify enrollment
        $enrollment = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        $questions = $quiz->questions()->with('answers')->get();
        $attemptCount = $enrollment->quizSubmissions()
            ->where('quiz_id', $quiz->id)
            ->count();

        return view('courses.learn.quiz', compact('course', 'quiz', 'questions', 'enrollment', 'attemptCount'));
    }

    /**
     * Submit quiz answers
     */
    public function submit(Request $request, Course $course, CourseQuiz $quiz)
    {
        $enrollment = CourseEnrollee::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Check attempts
        $attempts = $enrollment->quizSubmissions()
            ->where('quiz_id', $quiz->id)
            ->count();

        if ($attempts >= $quiz->attempts_allowed) {
            return redirect()->route('courses.learn.quiz', [$course, $quiz])
                ->with('error', 'You have exceeded the maximum number of attempts.');
        }

        $answers = $request->input('answers', []);
        $questions = $quiz->questions()->with('answers')->get();

        $totalQuestions = $questions->count();
        $correctAnswers = 0;
        $totalPoints = 0;
        $earnedPoints = 0;

        $submission = QuizSubmission::create([
            'course_enrollee_id' => $enrollment->id,
            'quiz_id' => $quiz->id,
            'attempt_number' => $attempts + 1,
            'total_questions' => $totalQuestions,
            'submitted_at' => now(),
        ]);

        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $correctAnswer = $question->answers()->where('is_correct', true)->first();
            $totalPoints += $question->points;

            $isCorrect = $userAnswer === $correctAnswer->answer ?? null;

            QuizSubmissionAnswer::create([
                'submission_id' => $submission->id,
                'question_id' => $question->id,
                'user_answer' => $userAnswer,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $question->points : 0,
            ]);

            if ($isCorrect) {
                $correctAnswers++;
                $earnedPoints += $question->points;
            }
        }

        $score = $totalPoints > 0 ? (int)(($earnedPoints / $totalPoints) * 100) : 0;
        $isPassed = $score >= $quiz->passing_score;

        $submission->update([
            'correct_answers' => $correctAnswers,
            'score' => $score,
            'is_passed' => $isPassed,
        ]);

        return redirect()->route('courses.learn.quiz-result', [$course, $quiz, $submission])
            ->with('success', 'Quiz submitted successfully.');
    }

    /**
     * Show quiz result
     */
    public function result(Course $course, CourseQuiz $quiz, QuizSubmission $submission)
    {
        $this->authorize('view', $submission);

        $answers = $submission->answers()
            ->with('question.answers')
            ->get();

        return view('courses.learn.quiz-result', compact('course', 'quiz', 'submission', 'answers'));
    }

    /**
     * Admin: List all course quizzes
     */
    public function adminIndex(Course $course)
    {
        $this->authorize('manageQuizzes', $course);

        $quizzes = $course->quizzes()->withCount('questions')->get();

        return view('admin.course-quizzes.index', compact('course', 'quizzes'));
    }

    /**
     * Admin: Show single quiz
     */
    public function adminShow(Course $course, CourseQuiz $quiz)
    {
        $this->authorize('manageQuizzes', $course);

        $questions = $quiz->questions()->withCount('answers')->get();

        return view('admin.course-quizzes.show', compact('course', 'quiz', 'questions'));
    }

    /**
     * Admin: Create quiz
     */
    public function adminCreate(Course $course)
    {
        $this->authorize('manageQuizzes', $course);

        return view('admin.course-quizzes.create', compact('course'));
    }

    /**
     * Admin: Store quiz
     */
    public function adminStore(Request $request, Course $course)
    {
        $this->authorize('manageQuizzes', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'attempts_allowed' => 'required|integer|min:1',
            'show_correct_answers' => 'boolean',
            'shuffle_questions' => 'boolean',
            'sequence' => 'required|integer|min:0',
            'is_required' => 'boolean',
        ]);

        $validated['course_id'] = $course->id;
        $quiz = CourseQuiz::create($validated);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Quiz created successfully.');
    }

    /**
     * Admin: Edit quiz
     */
    public function adminEdit(Course $course, CourseQuiz $quiz)
    {
        $this->authorize('manageQuizzes', $course);

        return view('admin.course-quizzes.edit', compact('course', 'quiz'));
    }

    /**
     * Admin: Update quiz
     */
    public function adminUpdate(Request $request, Course $course, CourseQuiz $quiz)
    {
        $this->authorize('manageQuizzes', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'attempts_allowed' => 'required|integer|min:1',
            'show_correct_answers' => 'boolean',
            'shuffle_questions' => 'boolean',
            'sequence' => 'required|integer|min:0',
            'is_required' => 'boolean',
        ]);

        $quiz->update($validated);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Quiz updated successfully.');
    }

    /**
     * Admin: Delete quiz
     */
    public function adminDestroy(Course $course, CourseQuiz $quiz)
    {
        $this->authorize('manageQuizzes', $course);

        $quiz->delete();

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Quiz deleted successfully.');
    }

    /**
     * Admin: List all quizzes globally
     */
    public function adminListAll()
    {
        if (auth()->user()->user_type !== 'admin') {
            abort(403);
        }

        $quizzes = CourseQuiz::with('course')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.quizzes.list-all', compact('quizzes'));
    }

    /**
     * Admin: View single quiz globally
     */
    public function adminViewQuiz(CourseQuiz $quiz)
    {
        if (auth()->user()->user_type !== 'admin') {
            abort(403);
        }

        $questions = $quiz->questions()->get();

        return view('admin.quizzes.show-global', compact('quiz', 'questions'));
    }

    /**
     * Admin: List all quiz submissions
     */
    public function adminListSubmissions()
    {
        if (auth()->user()->user_type !== 'admin') {
            abort(403);
        }

        $submissions = QuizSubmission::with('quiz', 'enrollee.user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.quiz-submissions.index', compact('submissions'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseQuiz;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;

class QuizQuestionController extends Controller
{
    /**
     * Show all questions for a quiz
     */
    public function index(Course $course, CourseQuiz $quiz)
    {
        $this->authorize('isAdmin');

        $questions = $quiz->questions()
            ->with('answers')
            ->orderBy('sequence')
            ->get();

        return view('admin.course-quizzes.questions', compact('course', 'quiz', 'questions'));
    }

    /**
     * Store a new question
     */
    public function store(Request $request, Course $course, CourseQuiz $quiz)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'question' => 'required|string',
            'question_type' => 'required|in:multiple_choice,multiple_answer,true_false,yes_no,short_answer',
            'points' => 'required|integer|min:1',
            'difficulty_level' => 'nullable|in:easy,medium,hard',
            'answers' => 'array',
            'correct_answers' => 'array',
            'correct_answer_tf' => 'nullable|in:true,false',
            'correct_answer_yn' => 'nullable|in:yes,no',
            'short_answers' => 'array',
        ]);

        // Create question
        $question = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => $validated['question'],
            'question_type' => $validated['question_type'],
            'points' => $validated['points'],
            'difficulty_level' => $validated['difficulty_level'] ?? 'medium',
            'sequence' => $quiz->questions()->max('sequence') + 1 ?? 1,
            'correct_answer' => $this->getCorrectAnswer($validated),
        ]);

        // Create answer options for multiple choice/answer
        if (in_array($validated['question_type'], ['multiple_choice', 'multiple_answer'])) {
            $answers = $validated['answers'] ?? [];
            foreach ($answers as $index => $answerText) {
                if ($answerText) {
                    QuizAnswer::create([
                        'question_id' => $question->id,
                        'answer' => $answerText, // Populate the 'answer' column
                        'answer_text' => $answerText,
                        'is_correct' => in_array($index, $validated['correct_answers'] ?? []),
                        'sequence' => $index,
                    ]);
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Question added successfully',
                'redirect' => route('admin.quiz-questions.index', [$course, $quiz])
            ]);
        }

        return redirect()->route('admin.quiz-questions.index', [$course, $quiz])
            ->with('success', 'Question added successfully');
    }

    /**
     * Update a question
     */
    public function update(Request $request, Course $course, CourseQuiz $quiz, QuizQuestion $question)
    {
        $this->authorize('isAdmin');

        $validated = $request->validate([
            'question' => 'required|string',
            'question_type' => 'required|in:multiple_choice,multiple_answer,true_false,yes_no,short_answer',
            'points' => 'required|integer|min:1',
            'difficulty_level' => 'nullable|in:easy,medium,hard',
            'answers' => 'array',
            'correct_answers' => 'array',
            'correct_answer_tf' => 'nullable|in:true,false',
            'correct_answer_yn' => 'nullable|in:yes,no',
            'short_answers' => 'array',
        ]);

        $question->update([
            'question' => $validated['question'],
            'question_type' => $validated['question_type'],
            'points' => $validated['points'],
            'difficulty_level' => $validated['difficulty_level'] ?? 'medium',
            'correct_answer' => $this->getCorrectAnswer($validated),
        ]);

        // Update answers if multiple choice/answer
        if (in_array($validated['question_type'], ['multiple_choice', 'multiple_answer'])) {
            // Delete existing answers
            $question->answers()->delete();

            // Create new answers
            $answers = $validated['answers'] ?? [];
            foreach ($answers as $index => $answerText) {
                if ($answerText) {
                    QuizAnswer::create([
                        'question_id' => $question->id,
                        'answer' => $answerText, // Populate the 'answer' column
                        'answer_text' => $answerText,
                        'is_correct' => in_array($index, $validated['correct_answers'] ?? []),
                        'sequence' => $index,
                    ]);
                }
            }
        } else {
            // Delete answers for non-multiple choice types
            $question->answers()->delete();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully',
                'redirect' => route('admin.quiz-questions.index', [$course, $quiz])
            ]);
        }

        return redirect()->route('admin.quiz-questions.index', [$course, $quiz])
            ->with('success', 'Question updated successfully');
    }

    /**
     * Delete a question
     */
    public function destroy(Course $course, CourseQuiz $quiz, QuizQuestion $question)
    {
        $this->authorize('isAdmin');

        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question deleted successfully'
        ]);
    }

    /**
     * Get correct answer based on question type
     */
    private function getCorrectAnswer($validated)
    {
        $type = $validated['question_type'];

        if ($type === 'true_false') {
            return $validated['correct_answer_tf'] ?? 'true';
        } elseif ($type === 'yes_no') {
            return $validated['correct_answer_yn'] ?? 'yes';
        } elseif ($type === 'short_answer') {
            return $validated['short_answers'] ?? [];
        } elseif (in_array($type, ['multiple_choice', 'multiple_answer'])) {
            // Store the indices of correct answers
            return $validated['correct_answers'] ?? [];
        }

        return null;
    }
}

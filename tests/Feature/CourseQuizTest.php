<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseQuiz;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\CourseEnrollee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourseQuizTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->category = CourseCategory::factory()->create();
        $this->course = Course::factory()
            ->for($this->category)
            ->create();
        
        $this->quiz = CourseQuiz::factory()
            ->for($this->course)
            ->create([
                'passing_score' => 70,
                'attempts_allowed' => 3,
            ]);
        
        // Create questions with answers
        for ($i = 0; $i < 5; $i++) {
            $question = QuizQuestion::factory()
                ->for($this->quiz)
                ->create(['question_type' => 'multiple_choice']);
            
            // Create 4 answer options
            for ($j = 0; $j < 4; $j++) {
                QuizAnswer::factory()
                    ->for($question)
                    ->create([
                        'is_correct' => $j === 0,
                    ]);
            }
        }
        
        $this->user = User::factory()->create();
        $this->enrollment = CourseEnrollee::factory()
            ->for($this->user)
            ->for($this->course)
            ->create();
    }

    public function test_user_can_view_quiz()
    {
        $this->actingAs($this->user);
        
        $response = $this->get(route('courses.learn.quiz', [$this->course, $this->quiz]));
        
        $response->assertStatus(200);
        $response->assertViewHas('quiz', $this->quiz);
    }

    public function test_user_can_submit_quiz()
    {
        $this->actingAs($this->user);
        
        // Get quiz questions and answers
        $questions = $this->quiz->questions()->with('answers')->get();
        $answers = [];
        
        foreach ($questions as $question) {
            $correctAnswer = $question->answers()->where('is_correct', true)->first();
            $answers[$question->id] = $correctAnswer->answer;
        }
        
        $response = $this->post(
            route('courses.learn.quiz.submit', [$this->course, $this->quiz]),
            ['answers' => $answers]
        );
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('quiz_submissions', [
            'course_enrollee_id' => $this->enrollment->id,
            'quiz_id' => $this->quiz->id,
            'is_passed' => true,
        ]);
    }

    public function test_quiz_respects_attempt_limit()
    {
        $this->actingAs($this->user);
        
        $this->quiz->update(['attempts_allowed' => 1]);
        
        // First attempt
        $answers = [];
        foreach ($this->quiz->questions as $q) {
            $answers[$q->id] = 'A';
        }
        
        $this->post(
            route('courses.learn.quiz.submit', [$this->course, $this->quiz]),
            ['answers' => $answers]
        );
        
        // Second attempt should fail
        $response = $this->post(
            route('courses.learn.quiz.submit', [$this->course, $this->quiz]),
            ['answers' => $answers]
        );
        
        $response->assertSessionHasErrors();
    }

    public function test_quiz_score_calculation()
    {
        $this->actingAs($this->user);
        
        // Get first question correct answer
        $firstQuestion = $this->quiz->questions()->first();
        $correctAnswer = $firstQuestion->answers()->where('is_correct', true)->first();
        
        $answers = [];
        foreach ($this->quiz->questions as $question) {
            if ($question->id === $firstQuestion->id) {
                $answers[$question->id] = $correctAnswer->answer;
            } else {
                $answers[$question->id] = 'Z'; // Wrong answer
            }
        }
        
        $response = $this->post(
            route('courses.learn.quiz.submit', [$this->course, $this->quiz]),
            ['answers' => $answers]
        );
        
        $submission = \App\Models\QuizSubmission::latest()->first();
        
        // Score should be 20% (1 out of 5 correct)
        $this->assertEquals(20, $submission->score);
        $this->assertFalse($submission->is_passed); // Below 70% passing
    }
}

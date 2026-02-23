@extends('layouts.app')

@section('title', 'Quiz - ' . $quiz->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ $quiz->title }}</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>Quiz Instructions</h5>
                        <ul class="mb-0">
                            <li>Total Questions: <strong>{{ $questions->count() }}</strong></li>
                            @if($quiz->duration_minutes)
                                <li>Time Limit: <strong>{{ $quiz->duration_minutes }} minutes</strong></li>
                            @endif
                            <li>Passing Score: <strong>{{ $quiz->pass_percentage ?? 70 }}%</strong></li>
                            <li>Attempts Used: <strong>{{ $attemptCount }}</strong></li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        {!! $quiz->description !!}
                    </div>

                    <hr>

                    <form action="{{ route('courses.learn.quiz.submit', [$course, $quiz]) }}" method="POST">
                        @csrf

                        @foreach($questions as $qIndex => $question)
                            <div class="question-card mb-4 p-4 border rounded">
                                <h5 class="mb-3">Question {{ $qIndex + 1 }} of {{ $questions->count() }}</h5>
                                <p class="lead">{{ $question->question_text }}</p>

                                @if($question->question_type === 'multiple_choice')
                                    <div class="btn-group-vertical w-100" role="group">
                                        @foreach($question->answers as $answer)
                                            <label class="btn btn-outline-secondary text-start">
                                                <input type="radio" name="answer_{{ $question->id }}" value="{{ $answer->id }}" required>
                                                {{ $answer->answer_text }}
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif($question->question_type === 'true_false')
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="answer_{{ $question->id }}" id="true_{{ $question->id }}" value="true" required>
                                        <label class="btn btn-outline-secondary" for="true_{{ $question->id }}">True</label>

                                        <input type="radio" class="btn-check" name="answer_{{ $question->id }}" id="false_{{ $question->id }}" value="false" required>
                                        <label class="btn btn-outline-secondary" for="false_{{ $question->id }}">False</label>
                                    </div>
                                @elseif($question->question_type === 'short_answer')
                                    <input type="text" name="answer_{{ $question->id }}" class="form-control" placeholder="Enter your answer" required>
                                @endif
                            </div>
                        @endforeach

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Submit Quiz
                            </button>
                            <a href="{{ route('courses.learn', $course) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Course
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

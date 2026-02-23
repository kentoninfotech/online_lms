@extends('layouts.app')

@section('title', 'Quiz Result - ' . $quiz->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            @php
                $passed = $submission->is_passed;
                $scorePercentage = $submission->score;
            @endphp
            
            <!-- Result Header Card -->
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-{{ $passed ? 'success' : 'warning' }} text-white py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1">{{ $passed ? '🎉 Congratulations!' : '📚 Quiz Completed' }}</h4>
                            <p class="mb-0 opacity-75">{{ $quiz->title }}</p>
                        </div>
                        <div class="text-center">
                            <h1 class="display-4 fw-bold mb-0">{{ $scorePercentage }}%</h1>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Status Alert -->
                    <div class="alert alert-{{ $passed ? 'success' : 'warning' }} mb-4" role="alert">
                        @if($passed)
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Excellent work!</strong> You have successfully passed this quiz with a score of <strong>{{ $scorePercentage }}%</strong>.
                        @else
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Not quite there yet.</strong> You need a <strong>{{ $quiz->passing_score }}%</strong> to pass. You scored <strong>{{ $scorePercentage }}%</strong>.
                        @endif
                    </div>

                    <!-- Quiz Statistics -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3 col-6">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-1">Score</h6>
                                    <h3 class="mb-0">{{ $scorePercentage }}%</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-1">Correct Answers</h6>
                                    <h3 class="mb-0">{{ $submission->correct_answers }}/{{ $submission->total_questions }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-1">Time Taken</h6>
                                    <h3 class="mb-0">{{ $submission->time_taken_minutes ?? '--' }} min</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-1">Attempt</h6>
                                    <h3 class="mb-0">#{{ $submission->attempt_number }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Score Progress Bar -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Overall Performance</small>
                            <small class="fw-bold">Passing Score: {{ $quiz->passing_score }}%</small>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-{{ $passed ? 'success' : 'warning' }}" role="progressbar" 
                                style="width: {{ min($scorePercentage, 100) }}%;" 
                                aria-valuenow="{{ $scorePercentage }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $scorePercentage }}%
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    @if($submission->notes)
                    <div class="alert alert-info">
                        <h6 class="mb-2"><i class="bi bi-chat-left-text me-2"></i>Instructor Notes</h6>
                        <p class="mb-0">{{ $submission->notes }}</p>
                    </div>
                    @endif

                    <!-- Answer Review (if enabled) -->
                    @if($quiz->show_correct_answers)
                    <hr class="my-4">
                    
                    <h5 class="mb-3"><i class="bi bi-justify me-2"></i>Answer Review</h5>
                    
                    @forelse($submission->answers as $index => $answer)
                        <div class="card mb-3 border-{{ $answer->is_correct ? 'success' : 'danger' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">Question {{ $index + 1 }}: {{ $answer->question->question }}</h6>
                                    <span class="badge bg-{{ $answer->is_correct ? 'success' : 'danger' }}">
                                        {{ $answer->is_correct ? '✓ Correct' : '✗ Incorrect' }}
                                    </span>
                                </div>
                                
                                <p class="mb-2 text-muted small">
                                    <strong>Question Type:</strong> {{ ucfirst(str_replace('_', ' ', $answer->question->question_type)) }}
                                </p>

                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="p-2 bg-light rounded">
                                            <small class="d-block text-muted mb-1">Your Answer</small>
                                            <span class="badge bg-{{ $answer->is_correct ? 'success' : 'danger' }}">
                                                {{ $answer->user_answer ?? '(Not answered)' }}
                                            </span>
                                        </div>
                                    </div>
                                    @if(!$answer->is_correct)
                                    <div class="col-md-6">
                                        <div class="p-2 bg-light rounded">
                                            <small class="d-block text-muted mb-1">Correct Answer</small>
                                            <span class="badge bg-success">
                                                {{ $answer->question->answers()
                                                    ->where('is_correct', true)
                                                    ->first()?->answer_text ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Answer details are not available for this quiz.
                        </div>
                    @endforelse
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row g-2 mb-4">
                <div class="col-md-6">
                    <a href="{{ route('courses.learn', $course) }}" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-arrow-left me-2"></i>Back to Course
                    </a>
                </div>
                @if(!$passed && $attemptCount < $quiz->attempts_allowed)
                <div class="col-md-6">
                    <a href="{{ route('courses.learn.quiz', [$course, $quiz]) }}" class="btn btn-warning btn-lg w-100">
                        <i class="bi bi-arrow-clockwise me-2"></i>Try Again
                    </a>
                </div>
                @endif
            </div>

            <!-- Help Text -->
            @if(!$passed && $attemptCount >= $quiz->attempts_allowed)
            <div class="alert alert-danger text-center">
                <i class="bi bi-exclamation-circle me-2"></i>
                <strong>You have exhausted all attempts</strong> for this quiz. Contact your instructor for additional attempts if needed.
            </div>
            @endif

            <!-- Certificate Section (if passed) -->
            @if($passed)
            <div class="card border-success bg-light-success">
                <div class="card-body text-center py-4">
                    <h5 class="mb-3 text-success">
                        <i class="bi bi-award me-2"></i>Certificate of Achievement
                    </h5>
                    <p class="mb-3 text-muted">
                        You've earned a certificate for completing this quiz successfully!
                    </p>
                    <div class="btn-group">
                        @if(method_exists($submission, 'downloadCertificate'))
                        <a href="{{ route('certificate.download', ['submission' => $submission->id, 'type' => 'quiz']) }}" class="btn btn-success">
                            <i class="bi bi-download me-2"></i>Download Certificate (PDF)
                        </a>
                        @endif
                        <a href="#" class="btn btn-outline-success" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>Print Certificate
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@extends('layouts.app')

@section('content')
<div class="container py-5 max-width-1000">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $course) }}">{{ $course->title }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.course-quizzes.submissions', [$course, $quiz]) }}">{{ $quiz->title }} Results</a></li>
                    <li class="breadcrumb-item active">Student Submission</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1">{{ $submission->courseEnrollee->user->name }}'s Submission</h1>
            <p class="text-muted">{{ $quiz->title }}</p>
        </div>
        <div>
            @if(!$submission->reviewed_at)
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#markReviewedModal">
                <i class="bi bi-check-circle"></i> Mark as Reviewed
            </button>
            @else
            <span class="badge bg-info p-2">
                <i class="bi bi-check-circle"></i> Reviewed on {{ $submission->reviewed_at->format('M d, Y') }}
            </span>
            @endif
        </div>
    </div>

    <!-- Score Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Score</h6>
                    <div class="display-4 fw-bold" style="color: {{ $submission->is_passed ? '#28a745' : '#dc3545' }};">
                        {{ $submission->score }}%
                    </div>
                    <small>Passing: {{ $quiz->passing_score }}%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Result</h6>
                    <h4 class="mb-0">
                        @if($submission->is_passed)
                        <span class="badge bg-success fs-6">PASSED</span>
                        @else
                        <span class="badge bg-danger fs-6">FAILED</span>
                        @endif
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Correct Answers</h6>
                    <h4 class="mb-0">{{ $submission->correct_answers }}/{{ $submission->total_questions }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Time Taken</h6>
                    <h4 class="mb-0">{{ $submission->time_taken_minutes }}m</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Info -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Student Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> {{ $submission->courseEnrollee->user->name }}</p>
                    <p><strong>Email:</strong> {{ $submission->courseEnrollee->user->email }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Attempt #:</strong> {{ $submission->attempt_number }} of {{ $quiz->attempts_allowed }}</p>
                    <p><strong>Submitted:</strong> {{ $submission->created_at->format('M d, Y g:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Answer Review -->
    <div class="accordion mb-4" id="answersAccordion">
        @foreach($quiz->questions as $index => $question)
        @php
            $answer = $submission->answers()->where('question_id', $question->id)->first();
            $isCorrect = $answer && $answer->is_correct;
        @endphp
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button {{ $isCorrect ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#question{{ $index }}">
                    <span class="badge {{ $isCorrect ? 'bg-success' : 'bg-danger' }} me-3">
                        {{ $isCorrect ? '✓' : '✗' }}
                    </span>
                    Question {{ $index + 1 }}: {{ Str::limit($question->question, 60) }}
                    <span class="ms-auto me-3 fw-bold">{{ $answer ? $answer->points_earned : 0 }}/{{ $question->points }}</span>
                </button>
            </h2>
            <div id="question{{ $index }}" class="accordion-collapse collapse {{ $isCorrect ? 'show' : '' }}" data-bs-parent="#answersAccordion">
                <div class="accordion-body">
                    <h6 class="mb-3">{{ $question->question }}</h6>

                    <!-- Student's Answer -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <strong class="text-muted">Student's Answer:</strong>
                        @if($answer)
                            @if($question->question_type === 'short_answer')
                                <p class="mt-2 mb-0">{{ $answer->student_answer }}</p>
                            @elseif(in_array($question->question_type, ['multiple_choice', 'multiple_answer']))
                                @php
                                    $selectedAnswers = json_decode($answer->student_answer, true);
                                    if (!is_array($selectedAnswers)) {
                                        $selectedAnswers = [$selectedAnswers];
                                    }
                                @endphp
                                <div class="mt-2">
                                    @foreach($question->answers as $ans)
                                        @if(in_array($ans->id, $selectedAnswers))
                                        <p class="mb-1" style="color: #28a745;">{{ $ans->answer_text }}</p>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-2 mb-0">{{ ucfirst($answer->student_answer) }}</p>
                            @endif
                        @else
                            <p class="mt-2 mb-0 text-danger"><em>Not answered</em></p>
                        @endif
                    </div>

                    <!-- Correct Answer -->
                    <div class="mb-3 p-3 bg-light rounded" style="border-left: 4px solid #28a745;">
                        <strong class="text-success">Correct Answer:</strong>
                        @if($question->question_type === 'short_answer')
                            @php
                                $correctAnswers = is_array($question->correct_answer) ? $question->correct_answer : [$question->correct_answer];
                            @endphp
                            <div class="mt-2">
                                @foreach($correctAnswers as $correct)
                                <p class="mb-1">{{ $correct }}</p>
                                @endforeach
                            </div>
                        @elseif(in_array($question->question_type, ['multiple_choice', 'multiple_answer']))
                            <div class="mt-2">
                                @foreach($question->answers as $ans)
                                    @if($ans->is_correct)
                                    <p class="mb-1">{{ $ans->answer_text }} ✓</p>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="mt-2 mb-0">{{ ucfirst($question->correct_answer) }}</p>
                        @endif
                    </div>

                    <!-- Tutor Feedback Form -->
                    <div>
                        <label class="form-label"><strong>Tutor Feedback (Optional)</strong></label>
                        <textarea class="form-control question-feedback" rows="3" data-question-id="{{ $question->id }}" placeholder="Add feedback for this answer...">{{ $answer?->tutor_feedback ?? '' }}</textarea>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 save-feedback" data-question-id="{{ $question->id }}" data-answer-id="{{ $answer?->id }}">
                            Save Feedback
                        </button>
                    </div>

                    <!-- Points Adjustment -->
                    <div class="mt-2 border-top pt-2">
                        <label class="form-label"><strong>Adjust Points (if needed)</strong></label>
                        <div class="input-group">
                            <input type="number" class="form-control points-adjust" min="0" max="{{ $question->points }}" 
                                value="{{ $answer ? $answer->points_earned : 0 }}" data-question-id="{{ $question->id }}">
                            <span class="input-group-text">/ {{ $question->points }}</span>
                            <button type="button" class="btn btn-outline-primary save-points" data-question-id="{{ $question->id }}" data-answer-id="{{ $answer?->id }}">
                                Update
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Action Buttons -->
    <div class="d-flex gap-2">
        <a href="{{ route('admin.course-quizzes.submissions', [$course, $quiz]) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Results
        </a>
        @if(!$submission->reviewed_at)
        <button type="button" class="btn btn-success ms-auto" data-bs-toggle="modal" data-bs-target="#markReviewedModal">
            <i class="bi bi-check-circle"></i> Mark as Reviewed
        </button>
        @endif
    </div>
</div>

<!-- Mark as Reviewed Modal -->
<div class="modal fade" id="markReviewedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Submission as Reviewed</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="reviewedForm" method="POST" action="{{ route('admin.quiz.mark-reviewed', [$course, $quiz, $submission]) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Final Score Confirmation *</label>
                        <div class="input-group input-group-lg">
                            <input type="number" name="score" class="form-control" value="{{ $submission->score }}" min="0" max="100" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="form-text mt-2">Current Score: <strong>{{ $submission->score }}%</strong></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">General Feedback (Optional)</label>
                        <textarea name="general_feedback" class="form-control" rows="3" placeholder="Add general feedback about the submission..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Mark as Reviewed</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Save feedback
document.querySelectorAll('.save-feedback').forEach(btn => {
    btn.addEventListener('click', function() {
        const questionId = this.dataset.questionId;
        const answerId = this.dataset.answerId;
        const feedback = document.querySelector(`.question-feedback[data-question-id="${questionId}"]`).value;

        if (!answerId) {
            alert('Cannot save feedback - answer not found');
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('feedback', feedback);

        fetch(`{{ route('admin.quiz.save-feedback', [$course, $quiz, $submission]) }}?question_id=${questionId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Feedback saved!');
                this.textContent = 'Saved ✓';
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-success');
                setTimeout(() => {
                    this.textContent = 'Save Feedback';
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-primary');
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to save feedback');
        });
    });
});

// Save points adjustment
document.querySelectorAll('.save-points').forEach(btn => {
    btn.addEventListener('click', function() {
        const questionId = this.dataset.questionId;
        const answerId = this.dataset.answerId;
        const points = document.querySelector(`.points-adjust[data-question-id="${questionId}"]`).value;

        if (!answerId) {
            alert('Cannot save points - answer not found');
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('points', points);

        fetch(`{{ route('admin.quiz.save-feedback', [$course, $quiz, $submission]) }}?question_id=${questionId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Points updated!');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update points');
        });
    });
});
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('student.course.show', $course) }}">{{ $course->title }}</a></li>
                    <li class="breadcrumb-item active">{{ $quiz->title }} - Results</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1">{{ $quiz->title }} - Results</h1>
        </div>
    </div>

    <!-- Score Card -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Your Score</h6>
                    <div class="display-4 fw-bold" style="color: {{ $submission->is_passed ? '#28a745' : '#dc3545' }}">
                        {{ $submission->score }}%
                    </div>
                    <p class="mb-0 mt-2">
                        @if($submission->is_passed)
                        <span class="badge bg-success">PASSED ✓</span>
                        @else
                        <span class="badge bg-danger">FAILED ✗</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Questions</h6>
                    <h4 class="mb-1">{{ $submission->correct_answers }}/{{ $submission->total_questions }}</h4>
                    <small class="text-muted">Correct Answers</small>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Passing Score</h6>
                    <h4 class="mb-1">{{ $quiz->passing_score }}%</h4>
                    <small class="text-muted">Required to Pass</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 border-end">
                            <h6 class="text-muted mb-2">Time Taken</h6>
                            <h5>{{ $submission->time_taken_minutes }} minute{{ $submission->time_taken_minutes !== 1 ? 's' : '' }}</h5>
                        </div>
                        <div class="col-md-3 border-end">
                            <h6 class="text-muted mb-2">Attempt</h6>
                            <h5>#{{ $submission->attempt_number }} of {{ $quiz->attempts_allowed }}</h5>
                        </div>
                        <div class="col-md-3 border-end">
                            <h6 class="text-muted mb-2">Submitted</h6>
                            <h5>{{ $submission->created_at->format('M d, Y') }}</h5>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted mb-2">Status</h6>
                            <h5>
                                @if($submission->reviewed_at)
                                <span class="badge bg-info">Reviewed</span>
                                @else
                                <span class="badge bg-warning">Pending</span>
                                @endif
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Answer Review -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Answer Review</h5>
        </div>
        <div class="card-body p-0">
            @foreach($quiz->questions as $index => $question)
            @php
                $answer = $submission->answers()->where('question_id', $question->id)->first();
                $isCorrect = $answer && $answer->is_correct;
            @endphp
            <div class="border-bottom p-3" style="{{ $loop->last ? 'border-bottom: none;' : '' }}">
                <div class="d-flex align-items-start gap-3">
                    <!-- Status Badge -->
                    <div class="flex-shrink-0">
                        <div class="badge" style="font-size: 14px; padding: 8px 12px; background-color: {{ $isCorrect ? '#28a745' : '#dc3545' }}; color: white;">
                            {{ $isCorrect ? '✓' : '✗' }}
                        </div>
                    </div>

                    <!-- Question Content -->
                    <div class="flex-grow-1">
                        <h6 class="mb-2">
                            Question {{ $index + 1 }}
                            <span class="text-muted ms-2">({{ $question->points }} point{{ $question->points !== 1 ? 's' : '' }})</span>
                        </h6>
                        <p class="mb-3 text-dark">{{ $question->question }}</p>

                        <!-- Your Answer -->
                        <div class="mb-2">
                            <small class="text-muted d-block mb-1"><strong>Your Answer:</strong></small>
                            @if($answer)
                                @if($question->question_type === 'short_answer')
                                    <p class="mb-0 ps-3 text-muted">{{ $answer->student_answer }}</p>
                                @elseif(in_array($question->question_type, ['multiple_choice', 'multiple_answer']))
                                    @php
                                        $selectedAnswers = json_decode($answer->student_answer, true);
                                        if (!is_array($selectedAnswers)) {
                                            $selectedAnswers = [$selectedAnswers];
                                        }
                                    @endphp
                                    <div class="ps-3">
                                        @foreach($question->answers as $ans)
                                            @if(in_array($ans->id, $selectedAnswers))
                                            <p class="mb-1" style="color: #28a745;">{{ $ans->answer_text }}</p>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <p class="mb-0 ps-3 text-muted">{{ ucfirst($answer->student_answer) }}</p>
                                @endif
                            @else
                                <p class="mb-0 ps-3 text-muted text-danger"><em>Not answered</em></p>
                            @endif
                        </div>

                        <!-- Correct Answer (if showing) -->
                        @if($quiz->show_correct_answers || $submission->reviewed_at)
                        <div>
                            <small class="text-muted d-block mb-1"><strong>Correct Answer:</strong></small>
                            @if($question->question_type === 'short_answer')
                                <div class="ps-3">
                                    @php
                                        $correctAnswers = is_array($question->correct_answer) ? $question->correct_answer : [$question->correct_answer];
                                    @endphp
                                    @foreach($correctAnswers as $correct)
                                    <p class="mb-1" style="color: #28a745;">{{ $correct }}</p>
                                    @endforeach
                                </div>
                             @elseif(in_array($question->question_type, ['multiple_choice', 'multiple_answer']))
                                <div class="ps-3">
                                    @foreach($question->answers as $ans)
                                        @if($ans->is_correct)
                                        <p class="mb-1" style="color: #28a745;">{{ $ans->answer_text }} ✓</p>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p class="mb-0 ps-3" style="color: #28a745;">{{ ucfirst($question->correct_answer) }}</p>
                            @endif
                        </div>
                        @endif

                        <!-- Tutor Feedback -->
                        @if($submission->reviewed_at && $answer && $answer->tutor_feedback)
                        <div class="mt-2 p-2 bg-light rounded border-start border-info">
                            <small class="text-muted d-block mb-1"><strong>Tutor Feedback:</strong></small>
                            <p class="mb-0">{{ $answer->tutor_feedback }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Points -->
                    <div class="flex-shrink-0 text-end">
                        <div class="fw-bold">{{ $answer ? $answer->points_earned : 0 }}/{{ $question->points }}</div>
                        <small class="text-muted">Points</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('student.course.show', $course) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Course
                </a>

                @if($submission->is_passed && $course->requires_certificate)
                <a href="{{ route('student.certificate.download', ['course' => $course, 'submission' => $submission]) }}" class="btn btn-success ms-auto">
                    <i class="bi bi-file-pdf"></i> Download Certificate
                </a>
                @endif

                @if($attemptsRemaining > 0 && !$submission->is_passed)
                <a href="{{ route('student.quiz.take', [$course, $quiz]) }}" class="btn btn-warning ms-auto">
                    <i class="bi bi-arrow-repeat"></i> Try Again ({{ $attemptsRemaining }} attempt{{ $attemptsRemaining !== 1 ? 's' : '' }} left)
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Previous Attempts -->
    @if($submission->attempt_number > 1)
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Previous Attempts</h5>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr class="border-top">
                        <th>Attempt</th>
                        <th>Score</th>
                        <th>Correct/Total</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($previousAttempts as $attempt)
                    <tr>
                        <td>#{{ $attempt->attempt_number }}</td>
                        <td>
                            <strong style="color: {{ $attempt->is_passed ? '#28a745' : '#dc3545' }};">
                                {{ $attempt->score }}%
                            </strong>
                        </td>
                        <td>{{ $attempt->correct_answers }}/{{ $attempt->total_questions }}</td>
                        <td>
                            @if($attempt->is_passed)
                            <span class="badge bg-success">PASSED</span>
                            @else
                            <span class="badge bg-danger">FAILED</span>
                            @endif
                        </td>
                        <td>{{ $attempt->created_at->format('M d, Y g:i A') }}</td>
                        <td>
                            <a href="{{ route('student.quiz.results', [$course, $quiz, $attempt]) }}" class="btn btn-sm btn-outline-primary">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">No previous attempts</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

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
                    <li class="breadcrumb-item active">{{ $quiz->title }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1">{{ $quiz->title }}</h1>
            <p class="text-muted">{{ $quiz->description }}</p>
        </div>
        @if($timeLimit)
        <div class="card border-warning bg-light">
            <div class="card-body text-center">
                <div class="h5 mb-1" id="timer">{{ sprintf('%02d:%02d', intval($timeLimit), ($timeLimit * 60) % 60) }}</div>
                <small class="text-muted">Time Remaining</small>
            </div>
        </div>
        @endif
    </div>

    <!-- Quiz Info -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="alert alert-info">
                <strong>Quiz Requirements:</strong>
                <ul class="mb-0 mt-2">
                    <li>Passing Score: <strong>{{ $quiz->passing_score }}%</strong></li>
                    <li>Total Questions: <strong>{{ $quiz->questions->count() }}</strong></li>
                    <li>Total Points: <strong>{{ $quiz->questions->sum('points') }}</strong></li>
                    @if($quiz->time_limit_minutes)
                    <li>Time Limit: <strong>{{ $quiz->time_limit_minutes }} minutes</strong></li>
                    @endif
                    <li>Attempts Remaining: <strong>{{ $attemptsRemaining }}/{{ $quiz->attempts_allowed }}</strong></li>
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-light">
                <div class="card-body">
                    <h6 class="card-title">Progress</h6>
                    <div id="progressBar" class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted d-block mt-2"><span id="answered">0</span> of {{ $quiz->questions->count() }} answered</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quiz Form -->
    <form id="quizForm" method="POST" action="{{ route('student.quiz.submit', [$course, $quiz]) }}">
        @csrf
        <input type="hidden" name="time_taken_minutes" id="timeTaken" value="0">

        @forelse($quiz->questions as $index => $question)
        <div class="card mb-3 question-card" data-question-id="{{ $question->id }}">
            <div class="card-header bg-primary bg-opacity-10 border-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="badge bg-primary">Question {{ $index + 1 }} of {{ $quiz->questions->count() }}</span>
                        <span class="text-muted ms-2">({{ $question->points }} point{{ $question->points !== 1 ? 's' : '' }})</span>
                    </h5>
                    <small class="text-success" style="display: none;">✓ Answered</small>
                </div>
            </div>
            <div class="card-body">
                <p class="h6 mb-3">{{ $question->question }}</p>

                <!-- Multiple Choice -->
                @if($question->question_type === 'multiple_choice')
                <div class="form-check">
                    @foreach($question->answers as $answer)
                    <div>
                        <input type="radio" class="form-check-input question-input" 
                            name="answers[{{ $question->id }}]" 
                            value="{{ $answer->id }}"
                            id="answer_{{ $answer->id }}">
                        <label class="form-check-label" for="answer_{{ $answer->id }}">
                            {{ $answer->answer_text }}
                        </label>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Multiple Answer -->
                @if($question->question_type === 'multiple_answer')
                <div class="form-check">
                    @foreach($question->answers as $answer)
                    <div>
                        <input type="checkbox" class="form-check-input question-input" 
                            name="answers[{{ $question->id }}][]" 
                            value="{{ $answer->id }}"
                            id="answer_{{ $answer->id }}">
                        <label class="form-check-label" for="answer_{{ $answer->id }}">
                            {{ $answer->answer_text }}
                        </label>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- True/False -->
                @if($question->question_type === 'true_false')
                <div class="btn-group d-block" role="group">
                    <input type="radio" class="btn-check question-input" 
                        name="answers[{{ $question->id }}]" 
                        value="true" id="tf_true_{{ $question->id }}">
                    <label class="btn btn-outline-primary" for="tf_true_{{ $question->id }}">True</label>

                    <input type="radio" class="btn-check question-input" 
                        name="answers[{{ $question->id }}]" 
                        value="false" id="tf_false_{{ $question->id }}">
                    <label class="btn btn-outline-primary" for="tf_false_{{ $question->id }}">False</label>
                </div>
                @endif

                <!-- Yes/No -->
                @if($question->question_type === 'yes_no')
                <div class="btn-group d-block" role="group">
                    <input type="radio" class="btn-check question-input" 
                        name="answers[{{ $question->id }}]" 
                        value="yes" id="yn_yes_{{ $question->id }}">
                    <label class="btn btn-outline-primary" for="yn_yes_{{ $question->id }}">Yes</label>

                    <input type="radio" class="btn-check question-input" 
                        name="answers[{{ $question->id }}]" 
                        value="no" id="yn_no_{{ $question->id }}">
                    <label class="btn btn-outline-primary" for="yn_no_{{ $question->id }}">No</label>
                </div>
                @endif

                <!-- Short Answer -->
                @if($question->question_type === 'short_answer')
                <textarea class="form-control question-input" 
                    name="answers[{{ $question->id }}]" 
                    rows="3" 
                    placeholder="Type your answer here..."></textarea>
                <small class="text-muted d-block mt-2">Be as specific as possible. Spelling and grammar matter.</small>
                @endif
            </div>
        </div>
        @empty
        <div class="alert alert-warning">
            <strong>No questions in this quiz yet.</strong> Please contact your instructor.
        </div>
        @endforelse

        @if($quiz->questions->count() > 0)
        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex gap-2">
                    <a href="{{ route('student.course.show', $course) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Course
                    </a>
                    <button type="button" class="btn btn-outline-primary ms-auto" id="reviewBtn">
                        <i class="bi bi-arrow-counterclockwise"></i> Review Answers
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                        <i class="bi bi-send"></i> Submit Quiz
                    </button>
                </div>
            </div>
        </div>
        @endif
    </form>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Your Answers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="reviewContent">
                <!-- Generated dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="document.getElementById('quizForm').submit()">
                    <i class="bi bi-send"></i> Submit Quiz
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.question-card {
    transition: all 0.3s ease;
}
.question-card.answered {
    border-left: 4px solid #28a745;
}
#timer {
    font-family: 'Courier New', monospace;
    color: #ff6b6b;
}
</style>

<script>
// Initialize progress tracking
document.querySelectorAll('.question-input').forEach(input => {
    input.addEventListener('change', updateProgress);
});

function updateProgress() {
    const totalQuestions = {{ $quiz->questions->count() }};
    const answered = Array.from(document.querySelectorAll('input[name^="answers"]')).filter(input => {
        if (input.type === 'checkbox') {
            return document.querySelector(`[name="${input.name}"]:checked`) !== null;
        } else if (input.type === 'radio') {
            return document.querySelector(`[name="${input.name}"]:checked`) !== null;
        } else if (input.type === 'text' || input.type === 'textarea' || input.nodeName === 'TEXTAREA') {
            return input.value.trim() !== '';
        }
        return false;
    });

    const uniqueQuestions = new Set();
    answered.forEach(input => {
        const questionId = input.getAttribute('name').match(/\d+/)[0];
        uniqueQuestions.add(questionId);
    });

    const count = uniqueQuestions.size;
    const percentage = (count / totalQuestions) * 100;

    document.getElementById('answered').textContent = count;
    document.querySelector('.progress-bar').style.width = percentage + '%';
    document.getElementById('submitBtn').disabled = count === 0;

    // Mark questions as answered
    document.querySelectorAll('.question-card').forEach(card => {
        const questionId = card.getAttribute('data-question-id');
        if (uniqueQuestions.has(questionId)) {
            card.classList.add('answered');
            card.querySelector('small.text-success').style.display = 'block';
        } else {
            card.classList.remove('answered');
            card.querySelector('small.text-success').style.display = 'none';
        }
    });
}

// Timer countdown
@if($timeLimit)
let timeRemaining = {{ $timeLimit }};
const timerElement = document.getElementById('timer');
const timeTakenInput = document.getElementById('timeTaken');
const startTime = Date.now();

const countdownInterval = setInterval(() => {
    timeRemaining--;
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

    if (timeRemaining <= 0) {
        clearInterval(countdownInterval);
        alert('Time is up! Your quiz will be submitted automatically.');
        document.getElementById('quizForm').submit();
    } else if (timeRemaining === 300) { // 5 minutes
        timerElement.parentElement.classList.add('bg-warning', 'text-dark');
        timerElement.parentElement.classList.remove('bg-light');
    } else if (timeRemaining === 60) { // 1 minute
        timerElement.parentElement.classList.add('bg-danger', 'text-white');
        timerElement.parentElement.classList.remove('bg-warning', 'text-dark');
    }
}, 1000);

document.getElementById('quizForm').addEventListener('submit', () => {
    const elapsedSeconds = Math.floor((Date.now() - startTime) / 1000);
    const elapsedMinutes = Math.round(elapsedSeconds / 60);
    timeTakenInput.value = elapsedMinutes;
});
@endif

// Review button
document.getElementById('reviewBtn').addEventListener('click', () => {
    const reviewContent = document.getElementById('reviewContent');
    let html = '<div class="list-group">';

    const formData = new FormData(document.getElementById('quizForm'));
    let questionIndex = 1;

    document.querySelectorAll('.question-card').forEach(card => {
        const questionId = card.querySelector('.question-card').getAttribute('data-question-id');
        const questionText = card.querySelector('.h6').textContent;
        const answered = card.classList.contains('answered');

        html += `
            <div class="list-group-item">
                <h6 class="mb-2">
                    <span class="badge ${answered ? 'bg-success' : 'bg-secondary'}">
                        ${answered ? '✓ Answered' : 'Not Answered'}
                    </span>
                    Question ${questionIndex}
                </h6>
                <p class="mb-0 text-muted">${questionText}</p>
            </div>
        `;
        questionIndex++;
    });

    html += '</div>';
    reviewContent.innerHTML = html;
    new bootstrap.Modal(document.getElementById('reviewModal')).show();
});

// Initial progress check
updateProgress();
</script>
@endsection

@extends('layouts.app')

@section('title', 'Manage Questions - ' . $quiz->title)

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Manage Questions</h3>
                <p class="text-muted">{{ $quiz->title }}</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $course) }}">{{ $course->title }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.course-quizzes.index', $course) }}">Quizzes</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.course-quizzes.show', [$course, $quiz]) }}">{{ $quiz->title }}</a></li>
                        <li class="breadcrumb-item active">Questions</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.course-quizzes.show', [$course, $quiz]) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <strong>Errors:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-9">
            <!-- Add Question Button -->
            <div class="mb-4">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                    <i class="bi bi-plus-circle"></i> Add Question
                </button>
            </div>

            <!-- Questions List -->
            @forelse($quiz->questions as $index => $question)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Question {{ $index + 1 }}</h6>
                        <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }} - {{ $question->points }} points</small>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editQuestionModal{{ $question->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('admin.quiz-questions.destroy', [$course, $quiz, $question]) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this question?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>{{ $question->question }}</strong></p>
                    
                    @if($question->question_type === 'multiple_choice' || $question->question_type === 'multiple_answer')
                    <div class="ms-3">
                        @foreach($question->answers as $answer)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" disabled 
                                {{ is_array($question->correct_answer) && in_array($answer->id, $question->correct_answer) ? 'checked' : (is_string($question->correct_answer) && $question->correct_answer == $answer->id ? 'checked' : '') }}>
                            <label class="form-check-label">
                                {{ $answer->answer_text }}
                                @if(is_array($question->correct_answer) && in_array($answer->id, $question->correct_answer))
                                    <span class="badge bg-success ms-2">Correct</span>
                                @elseif(is_string($question->correct_answer) && $question->correct_answer == $answer->id)
                                    <span class="badge bg-success ms-2">Correct</span>
                                @endif
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @elseif($question->question_type === 'true_false')
                    <div class="ms-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" disabled {{ $question->correct_answer === 'true' ? 'checked' : '' }}>
                            <label class="form-check-label">
                                True
                                @if($question->correct_answer === 'true')
                                    <span class="badge bg-success ms-2">Correct</span>
                                @endif
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" disabled {{ $question->correct_answer === 'false' ? 'checked' : '' }}>
                            <label class="form-check-label">
                                False
                                @if($question->correct_answer === 'false')
                                    <span class="badge bg-success ms-2">Correct</span>
                                @endif
                            </label>
                        </div>
                    </div>
                    @elseif($question->question_type === 'short_answer')
                    <div class="ms-3">
                        <p class="text-muted"><strong>Correct Answer(s):</strong> {{ is_array($question->correct_answer) ? implode(', ', $question->correct_answer) : $question->correct_answer }}</p>
                    </div>
                    @elseif($question->question_type === 'yes_no')
                    <div class="ms-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" disabled {{ $question->correct_answer === 'yes' ? 'checked' : '' }}>
                            <label class="form-check-label">
                                Yes
                                @if($question->correct_answer === 'yes')
                                    <span class="badge bg-success ms-2">Correct</span>
                                @endif
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" disabled {{ $question->correct_answer === 'no' ? 'checked' : '' }}>
                            <label class="form-check-label">
                                No
                                @if($question->correct_answer === 'no')
                                    <span class="badge bg-success ms-2">Correct</span>
                                @endif
                            </label>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Edit Question Modal -->
            <div class="modal fade" id="editQuestionModal{{ $question->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <form action="{{ route('admin.quiz-questions.update', [$course, $quiz, $question]) }}" method="POST" class="modal-content">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Question {{ $index + 1 }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @include('admin.course-quizzes.partials.question-form', ['question' => $question, 'edit' => true])
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            @empty
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No questions added yet. Click the "Add Question" button to get started.
            </div>
            @endforelse
        </div>

        <!-- Stats Sidebar -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">Quiz Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted mb-1">Total Questions</p>
                        <h3 class="mb-0">{{ $quiz->questions->count() }}</h3>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Total Points</p>
                        <h3 class="mb-0">{{ $quiz->questions->sum('points') }}</h3>
                    </div>
                    <div>
                        <p class="text-muted mb-1">Question Types</p>
                        @foreach($quiz->questions->groupBy('question_type') as $type => $questions)
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                            <span class="badge bg-primary">{{ $questions->count() }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Question Modal -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.quiz-questions.store', [$course, $quiz]) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.course-quizzes.partials.question-form', ['question' => null, 'edit' => false])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Question</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

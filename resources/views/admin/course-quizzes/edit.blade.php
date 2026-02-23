@extends('layouts.app')

@section('title', 'Edit Quiz - ' . $quiz->title)

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Edit Quiz</h3>
                <p class="text-muted">{{ $course->title }}</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $course) }}">{{ $course->title }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.course-quizzes.index', $course) }}">Quizzes</a></li>
                        <li class="breadcrumb-item active">{{ $quiz->title }}</li>
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
        <div class="col-lg-8">
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings-content" type="button" role="tab">
                        <i class="bi bi-gear me-2"></i>Quiz Settings
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions-content" type="button" role="tab">
                        <i class="bi bi-info-circle me-2"></i>Manage Questions
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Settings Tab -->
                <div class="tab-pane fade show active" id="settings-content" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quiz Settings</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.course-quizzes.update', [$course, $quiz]) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Basic Information -->
                                <h6 class="fw-bold mb-3">Basic Information</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Quiz Title *</label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                        placeholder="Enter quiz title" value="{{ old('title', $quiz->title) }}" required>
                                    @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Optional description for this quiz">{{ old('description', $quiz->description) }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                <!-- Quiz Configuration -->
                                <h6 class="fw-bold mb-3">Quiz Configuration</h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Passing Score (%) *</label>
                                            <input type="number" name="passing_score" class="form-control @error('passing_score') is-invalid @enderror"
                                                placeholder="70" value="{{ old('passing_score', $quiz->passing_score) }}" min="0" max="100" required>
                                            <small class="form-text text-muted">Score needed to pass this quiz</small>
                                            @error('passing_score')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Time Limit (Minutes)</label>
                                            <input type="number" name="time_limit_minutes" class="form-control @error('time_limit_minutes') is-invalid @enderror"
                                                placeholder="0" value="{{ old('time_limit_minutes', $quiz->time_limit_minutes) }}" min="0">
                                            <small class="form-text text-muted">Leave 0 for no time limit</small>
                                            @error('time_limit_minutes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Maximum Attempts Allowed *</label>
                                    <input type="number" name="attempts_allowed" class="form-control @error('attempts_allowed') is-invalid @enderror"
                                        placeholder="1" value="{{ old('attempts_allowed', $quiz->attempts_allowed) }}" min="1" required>
                                    @error('attempts_allowed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Sequence (Ordering) *</label>
                                    <input type="number" name="sequence" class="form-control @error('sequence') is-invalid @enderror"
                                        placeholder="1" value="{{ old('sequence', $quiz->sequence) }}" min="1" required>
                                    @error('sequence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                <!-- Display Options -->
                                <h6 class="fw-bold mb-3">Display Options</h6>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="show_correct_answers" value="1" id="showAnswers"
                                        {{ old('show_correct_answers', $quiz->show_correct_answers) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="showAnswers">
                                        Show correct answers after submission
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="shuffle_questions" value="1" id="shuffleQuestions"
                                        {{ old('shuffle_questions', $quiz->shuffle_questions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="shuffleQuestions">
                                        Shuffle question order for each attempt
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="is_required" value="1" id="isRequired"
                                        {{ old('is_required', $quiz->is_required) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isRequired">
                                        This quiz is required to complete the course
                                    </label>
                                </div>

                                <hr>

                                <!-- Publishing -->
                                <h6 class="fw-bold mb-3">Publishing</h6>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="is_published" value="1" id="isPublished"
                                        {{ old('is_published', $quiz->is_published) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isPublished">
                                        <strong>Publish this quiz</strong> (Students can take this quiz)
                                    </label>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <a href="{{ route('admin.course-quizzes.show', [$course, $quiz]) }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Questions Tab -->
                <div class="tab-pane fade" id="questions-content" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Manage Questions</h5>
                            <a href="{{ route('admin.quiz-questions.index', [$course, $quiz]) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil me-2"></i>Manage Questions
                            </a>
                        </div>
                        <div class="card-body">
                            @if($quiz->questions->count() > 0)
                                <div class="list-group">
                                    @foreach($quiz->questions as $question)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $question->question }}</h6>
                                                <p class="mb-0 text-muted small">
                                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                                    <span class="ms-2">{{ $question->points }} points</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Total Questions: {{ $quiz->questions->count() }} | Total Points: {{ $quiz->questions->sum('points') }}</small>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>No questions added yet. Click "Manage Questions" to add questions to this quiz.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">Quiz Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted mb-1">Status</p>
                        @if($quiz->is_published)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Total Questions</p>
                        <h3 class="mb-0">{{ $quiz->questions->count() }}</h3>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Total Points</p>
                        <h3 class="mb-0">{{ $quiz->questions->sum('points') }}</h3>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Passing Score</p>
                        <h3 class="mb-0">{{ $quiz->passing_score }}%</h3>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Total Attempts</p>
                        <h3 class="mb-0">{{ $quiz->submissions->count() }}</h3>
                    </div>
                    <hr>
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

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.quiz-questions.index', [$course, $quiz]) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-plus-circle me-2"></i>Add Questions
                        </a>
                        <a href="{{ route('admin.course-quizzes.submissions', [$course, $quiz]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-eye me-2"></i>View Submissions
                        </a>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash me-2"></i>Delete Quiz
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger">Delete Quiz</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this quiz? This action cannot be undone.</p>
                <p class="text-muted"><strong>{{ $quiz->title }}</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.course-quizzes.destroy', [$course, $quiz]) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Quiz</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

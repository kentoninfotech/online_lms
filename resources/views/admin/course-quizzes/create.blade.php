@extends('layouts.app')

@section('title', 'Create Quiz - ' . $course->title)

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Create Quiz</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        @php
                            $showRoute = auth()->user()->user_type === 'instructor'
                                ? route('tutor.courses.show', $course)
                                : route('admin.courses.show', $course);
                            $indexRoute = auth()->user()->user_type === 'instructor'
                                ? route('tutor.course-quizzes.index', $course)
                                : route('admin.course-quizzes.index', $course);
                        @endphp
                        <li class="breadcrumb-item"><a href="{{ $showRoute }}">{{ $course->title }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ $indexRoute }}">Quizzes</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="{{ $indexRoute }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

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
            @php
                $storeRoute = auth()->user()->user_type === 'instructor'
                    ? route('tutor.course-quizzes.store', $course)
                    : route('admin.course-quizzes.store', $course);
            @endphp
            <form action="{{ $storeRoute }}" method="POST">
                @csrf

                <!-- Basic Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle"></i> Quiz Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Quiz Title *</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                placeholder="e.g., Module 1 Assessment" value="{{ old('title') }}" required>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" 
                                placeholder="Enter quiz instructions or description">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Quiz Settings Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-sliders"></i> Quiz Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Passing Score (%) *</label>
                                <input type="number" name="passing_score" class="form-control @error('passing_score') is-invalid @enderror" 
                                    placeholder="70" value="{{ old('passing_score', 70) }}" min="0" max="100" required>
                                <small class="form-text text-muted">Minimum percentage needed to pass</small>
                                @error('passing_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Time Limit (minutes)</label>
                                <input type="number" name="time_limit_minutes" class="form-control @error('time_limit_minutes') is-invalid @enderror" 
                                    placeholder="Leave empty for no limit" value="{{ old('time_limit_minutes') }}" min="1">
                                <small class="form-text text-muted">Leave empty for unlimited time</small>
                                @error('time_limit_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Attempts Allowed *</label>
                                <input type="number" name="attempts_allowed" class="form-control @error('attempts_allowed') is-invalid @enderror" 
                                    placeholder="3" value="{{ old('attempts_allowed', 3) }}" min="1" required>
                                @error('attempts_allowed')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Is Required? *</label>
                                <select name="is_required" class="form-select @error('is_required') is-invalid @enderror">
                                    <option value="0" {{ old('is_required') === '0' ? 'selected' : '' }}>No - Optional</option>
                                    <option value="1" {{ old('is_required') === '1' ? 'selected' : '' }}>Yes - Required</option>
                                </select>
                                @error('is_required')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                             <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Sequence *</label>
                                <input type="number" name="sequence" class="form-control @error('sequence') is-invalid @enderror" 
                                    placeholder="3" value="{{ old('sequence', 3) }}" min="1" required>
                                @error('sequence')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="shuffle_questions" value="0">
                                    <input class="form-check-input" type="checkbox" name="shuffle_questions" value="1" id="shuffle" 
                                        {{ old('shuffle_questions') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="shuffle">
                                        Shuffle Questions Order
                                    </label>
                                </div>
                            </div>                            

                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="show_correct_answers" value="0">
                                    <input class="form-check-input" type="checkbox" name="show_correct_answers" value="1" id="showcorrect" 
                                        {{ old('show_correct_answers', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="showcorrect">
                                        Show Correct Answers After Submit
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Create Quiz
                    </button>
                    <a href="{{ route('admin.course-quizzes.index', $course) }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightbulb"></i> Quiz Tips
                    </h5>
                </div>
                <div class="card-body small">
                    <div class="mb-3">
                        <strong>Passing Score:</strong>
                        <p class="text-muted">Set the minimum percentage students need to pass (typically 60-80%)</p>
                    </div>
                    <div class="mb-3">
                        <strong>Time Limit:</strong>
                        <p class="text-muted">Leave empty for no time restrictions. Set a limit for timed assessments</p>
                    </div>
                    <div class="mb-3">
                        <strong>Attempts:</strong>
                        <p class="text-muted">Allow students to retake the quiz multiple times to improve their score</p>
                    </div>
                    <div>
                        <strong>Shuffle:</strong>
                        <p class="text-muted">Randomize question order to prevent cheating and ensure fairness</p>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle"></i> <strong>Next Step:</strong> After creating the quiz, you'll add questions. Multiple question types are supported including multiple choice, short answer, true/false, and more.
            </div>
        </div>
    </div>
</div>
@endsection

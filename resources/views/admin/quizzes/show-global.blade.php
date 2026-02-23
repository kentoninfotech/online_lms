@extends('layouts.app')

@section('title', 'View Quiz - ' . $quiz->title)

@section('content')

<div class="pc-container" style="margin-top: -2rem;">
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.quizzes.show', $quiz) }}">Quizzes</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($quiz->title, 30) }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="pc-container" style="margin-top: 0.5rem;">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ $quiz->title }}</h5>
                    <div>
                        <a href="{{ route('admin.course-quizzes.edit', [$quiz->course, $quiz]) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.course-quizzes.destroy', [$quiz->course, $quiz]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted">Course</h6>
                            <p><a href="{{ route('admin.courses.show', $quiz->course) }}">{{ $quiz->course->title }}</a></p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Status</h6>
                            <p>
                                @if($quiz->is_published)
                                    <span class="badge badge-light-success">Published</span>
                                @else
                                    <span class="badge badge-light-warning">Draft</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted">Duration (minutes)</h6>
                            <p>{{ $quiz->duration_minutes ?? 'Not limited' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Attempts Allowed</h6>
                            <p>{{ $quiz->attempts_allowed }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted">Show Correct Answers</h6>
                            <p>
                                @if($quiz->show_correct_answers)
                                    <span class="badge badge-light-success">Yes</span>
                                @else
                                    <span class="badge badge-light-danger">No</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Shuffle Questions</h6>
                            <p>
                                @if($quiz->shuffle_questions)
                                    <span class="badge badge-light-success">Yes</span>
                                @else
                                    <span class="badge badge-light-danger">No</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted">Questions ({{ $questions->count() }})</h6>
                            <div class="list-group">
                                @foreach($questions as $idx => $question)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <strong>#{{ $idx + 1 }}. {{ Str::limit($question->question_text, 60) }}</strong>
                                            <span class="badge badge-light-secondary">{{ $question->answers->count() }} options</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <small class="text-muted">Created: {{ $quiz->created_at->format('M d, Y H:i') }}
                            | Last Updated: {{ $quiz->updated_at->format('M d, Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted">Course</h6>
                        <p class="mb-0">{{ $quiz->course->title }}</p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-muted">Total Submissions</h6>
                        <p class="mb-0"><strong>{{ $quiz->submissions()->count() }}</strong></p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-muted">Total Questions</h6>
                        <p class="mb-0"><strong>{{ $questions->count() }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

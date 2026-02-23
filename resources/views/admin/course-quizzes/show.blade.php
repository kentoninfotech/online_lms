@extends('layouts.app')

@section('title', 'View Quiz - ' . $quiz->title)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">{{ $quiz->title }}</h3>
                <p class="text-muted">{{ $course->title }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.course-quizzes.edit', [$course, $quiz]) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('admin.course-quizzes.index', $course) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Quiz Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Description</label>
                        <div class="border rounded p-3" style="background-color: #f8f9fa;">
                            {!! $quiz->description ?? '<em class="text-muted">No description</em>' !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Duration</label>
                            <div>{{ $quiz->duration_minutes ?? 'No time limit' }} {{ $quiz->duration_minutes ? 'minutes' : '' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Pass Score</label>
                            <div>{{ $quiz->pass_percentage ?? 'N/A' }}%</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Status</label>
                            <div>
                                <span class="badge {{ $quiz->is_active ? 'bg-success' : 'bg-warning' }}">
                                    {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Randomize Questions</label>
                            <div>
                                <span class="badge {{ $quiz->randomize_questions ? 'bg-info' : 'bg-secondary' }}">
                                    {{ $quiz->randomize_questions ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Questions ({{ $quiz->questions_count ?? 0 }})</h5>
                    <a href="{{ route('admin.course-quizzes.edit', [$course, $quiz]) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-circle me-1"></i>Add Question
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Answers</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($questions) && $questions->count() > 0)
                                @foreach($questions as $question)
                                    <tr>
                                        <td>
                                            <strong>{{ Str::limit($question->question_text, 60) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($question->question_type) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $question->answers_count ?? 0 }} Answers</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.course-quizzes.edit', [$course, $quiz]) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No questions added yet. <a href="{{ route('admin.course-quizzes.edit', [$course, $quiz]) }}">Add a question</a>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Metadata</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Created</label>
                        <div>{{ $quiz->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Last Updated</label>
                        <div>{{ $quiz->updated_at->format('M d, Y H:i') }}</div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.course-quizzes.edit', [$course, $quiz]) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Quiz
                        </a>
                        <form action="{{ route('admin.course-quizzes.destroy', [$course, $quiz]) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>Delete Quiz
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

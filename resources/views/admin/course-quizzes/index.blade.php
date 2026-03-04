@extends('layouts.app')

@section('title', 'Course Quizzes - ' . $course->title)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Course Quizzes</h3>
                <p class="text-muted">{{ $course->title }}</p>
            </div>
            <div class="col-auto">
                @php
                    $isInstructor = auth()->user()->user_type === 'instructor';
                    $createRoute = $isInstructor ? route('tutor.course-quizzes.create', $course) : route('admin.course-quizzes.create', $course);
                    $backRoute = $isInstructor ? route('tutor.courses.show', $course) : route('admin.courses.show', $course);
                @endphp
                <a href="{{ $createRoute }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create Quiz
                </a>
                <a href="{{ $backRoute }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quizzes ({{ $quizzes->count() }})</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Questions</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quizzes as $quiz)
                                <tr>
                                    <td>
                                        <strong>{{ $quiz->title }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($quiz->description ?? 'No description', 60) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $quiz->questions_count ?? 0 }} Questions</span>
                                    </td>
                                    <td>
                                        @if($quiz->duration_minutes)
                                            {{ $quiz->duration_minutes }} mins
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $quiz->is_active ? 'bg-success' : 'bg-warning' }}">
                                            {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $isInstructor = auth()->user()->user_type === 'instructor';
                                            $showRoute = $isInstructor ? route('tutor.course-quizzes.show', [$course, $quiz]) : route('admin.course-quizzes.show', [$course, $quiz]);
                                            $editRoute = $isInstructor ? route('tutor.course-quizzes.edit', [$course, $quiz]) : route('admin.course-quizzes.edit', [$course, $quiz]);
                                            $destroyRoute = $isInstructor ? route('tutor.course-quizzes.destroy', [$course, $quiz]) : route('admin.course-quizzes.destroy', [$course, $quiz]);
                                        @endphp
                                        <a href="{{ $showRoute }}" class="btn btn-outline-info btn-sm" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ $editRoute }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ $destroyRoute }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this quiz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        @php
                                            $createRoute = auth()->user()->user_type === 'instructor'
                                                ? route('tutor.course-quizzes.create', $course)
                                                : route('admin.course-quizzes.create', $course);
                                        @endphp
                                        No quizzes created yet. <a href="{{ $createRoute }}">Create one now</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

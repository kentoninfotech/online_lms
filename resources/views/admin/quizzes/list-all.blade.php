@extends('layouts.app')

@section('title', 'All Quizzes - Admin')

@section('content')

<div class="pc-container" style="margin-top: -2rem;">
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Quizzes</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h4 class="m-0">Quiz Management</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="pc-container" style="margin-top: 0.5rem;">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>All Course Quizzes</h5>
                    <span class="d-block text-muted" style="font-size: 0.875rem;"><i class="bi bi-info-circle"></i> Total Quizzes: {{ $quizzes->total() }}</span>
                </div>
                <div class="card-body">
                    @if($quizzes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Course</th>
                                        <th>Questions</th>
                                        <th>Duration (mins)</th>
                                        <th>Attempts</th>
                                        <th>Published</th>
                                        <th>Created</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quizzes as $quiz)
                                        <tr>
                                            <td>
                                                <strong>{{ Str::limit($quiz->title, 40) }}</strong>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.courses.show', $quiz->course) }}" class="badge badge-light-primary">
                                                    {{ $quiz->course->title }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-info">{{ $quiz->questions->count() }}</span>
                                            </td>
                                            <td>
                                                {{ $quiz->time_limit_minutes ?? '—' }}
                                            </td>
                                            <td>
                                                {{ $quiz->attempts_allowed }}
                                            </td>
                                            <td>
                                                @if($quiz->is_published)
                                                    <span class="badge badge-light-success">Published</span>
                                                @else
                                                    <span class="badge badge-light-warning">Draft</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $quiz->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                                <a href="{{ route('admin.course-quizzes.edit', [$quiz->course, $quiz]) }}" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $quizzes->links('pagination::bootstrap-5') }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No quizzes found. <a href="{{ route('admin.courses.index') }}">Create quizzes for your courses</a>.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
    .card {
        border: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table tbody tr {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .table tbody tr:hover {
        background-color: #f0f2f5;
    }

    .table tbody tr:nth-child(even) {
        background-color: #f3f4f6;
    }

    .table tbody tr:nth-child(even):hover {
        background-color: #eef0f5;
    }
</style>

@extends('layouts.app')

@section('title', 'Quiz Submissions - Admin')

@section('content')

<div class="pc-container" style="margin-top: -2rem;">
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.quizzes.index') }}">Quizzes</a></li>
                        <li class="breadcrumb-item active">Submissions</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h4 class="m-0">Quiz Submissions</h4>
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
                    <h5>All Quiz Submissions</h5>
                    <span class="d-block text-muted" style="font-size: 0.875rem;"><i class="bi bi-info-circle"></i> Total Submissions: {{ $submissions->total() }}</span>
                </div>
                <div class="card-body">
                    @if($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Quiz</th>
                                        <th>Course</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $submission)
                                        <tr>
                                            <td>
                                                @if($submission->enrollee->user)
                                                    <strong>{{ $submission->enrollee->user->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $submission->enrollee->user->email }}</small>
                                                @else
                                                    <span class="text-muted">User Deleted</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.quizzes.show', $submission->quiz) }}" class="badge badge-light-primary">
                                                    {{ Str::limit($submission->quiz->title, 30) }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-secondary">{{ Str::limit($submission->quiz->course->title, 25) }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $submission->score ?? '—' }}/{{ $submission->quiz->questions->count() }}</strong>
                                                @if($submission->score)
                                                    <br>
                                                    <small>({{ round(($submission->score / $submission->quiz->questions->count()) * 100) }}%)</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($submission->status === 'submitted')
                                                    <span class="badge badge-light-success">Submitted</span>
                                                @elseif($submission->status === 'in_progress')
                                                    <span class="badge badge-light-warning">In Progress</span>
                                                @else
                                                    <span class="badge badge-light-info">{{ ucfirst($submission->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $submission->created_at->format('M d, Y H:i') }}</small>
                                            </td>
                                            <td class="text-end">
                                                <a href="#" class="btn btn-sm btn-info" onclick="alert('Detailed submission view coming soon')">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $submissions->links('pagination::bootstrap-5') }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No quiz submissions yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

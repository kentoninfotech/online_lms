@extends('layouts.app')

@section('title', 'Discussions - ' . $course->title)

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h2>Course Discussions</h2>
            <p class="text-muted">{{ $course->title }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('courses.discussions.create', $course) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Start Discussion
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($discussions->count() > 0)
        <div class="row">
            @foreach($discussions as $discussion)
                <div class="col-lg-12 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <h5>
                                        <a href="{{ route('courses.discussions.show', [$course, $discussion]) }}" class="text-decoration-none">
                                            {{ $discussion->title }}
                                        </a>
                                        @if($discussion->is_pinned)
                                            <i class="bi bi-pin-fill text-warning ms-2" title="Pinned"></i>
                                        @endif
                                        @if($discussion->is_locked)
                                            <i class="bi bi-lock-fill text-danger ms-2" title="Locked"></i>
                                        @endif
                                    </h5>
                                    <p class="card-text text-muted small mb-3">
                                        {{ Str::limit($discussion->content, 200) }}
                                    </p>
                                    <small class="text-muted">
                                        Started by <strong>{{ $discussion->user?->name ?? 'Deleted User' }}</strong>
                                        on {{ $discussion->created_at->format('M d, Y \a\t H:i') }}
                                    </small>
                                </div>
                                <div class="col-lg-4 text-end">
                                    <div class="badge bg-info mb-2">
                                        <i class="bi bi-chat-dots me-1"></i>
                                        {{ $discussion->replies_count ?? 0 }} Replies
                                    </div>
                                    <br>
                                    <a href="{{ route('courses.discussions.show', [$course, $discussion]) }}" class="btn btn-sm btn-outline-primary">
                                        View Discussion
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($discussions->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $discussions->links() }}
            </div>
        @endif
    @else
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-chat-dots display-4 mb-3"></i>
            <h4>No Discussions Yet</h4>
            <p class="mb-3">Be the first to start a discussion!</p>
            <a href="{{ route('courses.discussions.create', $course) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Start a Discussion
            </a>
        </div>
    @endif
</div>
@endsection

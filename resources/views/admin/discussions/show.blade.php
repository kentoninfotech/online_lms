@extends('layouts.app')

@section('title', 'Discussion Thread')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">{{ $discussion->title }}</h3>
                <p class="text-muted">
                    @if($discussion->course)
                        <a href="{{ route('admin.courses.show', $discussion->course) }}">{{ $discussion->course->title }}</a>
                    @endif
                </p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.discussions.index') }}" class="btn btn-secondary">
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
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Original Post</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="flex-grow-1">
                            <strong>{{ $discussion->user?->name ?? 'Deleted User' }}</strong>
                            <br>
                            <small class="text-muted">{{ $discussion->created_at->format('M d, Y \a\t H:i') }}</small>
                        </div>
                        @if($discussion->is_pinned)
                            <span class="badge bg-warning">
                                <i class="bi bi-pin-fill"></i> Pinned
                            </span>
                        @endif
                        @if($discussion->is_locked)
                            <span class="badge bg-danger">
                                <i class="bi bi-lock-fill"></i> Locked
                            </span>
                        @endif
                    </div>

                    <div class="border rounded p-3" style="background-color: #f8f9fa;">
                        {!! $discussion->content ?? '<em class="text-muted">No content</em>' !!}
                    </div>

                    @if($discussion->updated_at->diffInSeconds($discussion->created_at) > 60)
                        <small class="text-muted d-block mt-2">
                            Edited {{ $discussion->updated_at->format('M d, Y \a\t H:i') }}
                        </small>
                    @endif
                </div>
            </div>

            @if($replies && $replies->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Replies ({{ $replies->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @foreach($replies as $reply)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="flex-grow-1">
                                        <strong>{{ $reply->user?->name ?? 'Deleted User' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $reply->created_at->format('M d, Y \a\t H:i') }}</small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <form action="{{ route('admin.discussions.destroy', $discussion) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this reply?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Reply">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="border rounded p-2 mt-2" style="background-color: #f8f9fa;">
                                    {!! $reply->content ?? '<em class="text-muted">No content</em>' !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center text-muted py-4">
                        No replies yet.
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Discussion Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Started By</label>
                        <div>{{ $discussion->user?->name ?? 'Deleted User' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Total Replies</label>
                        <div><span class="badge bg-info">{{ $replies?->count() ?? 0 }} Replies</span></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Posted</label>
                        <div>{{ $discussion->created_at->format('M d, Y H:i') }}</div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.discussions.pin', $discussion) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-{{ $discussion->is_pinned ? 'warning' : 'outline-warning' }} w-100">
                                <i class="bi {{ $discussion->is_pinned ? 'bi-pin-slash' : 'bi-pin' }} me-2"></i>
                                {{ $discussion->is_pinned ? 'Unpin Discussion' : 'Pin Discussion' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.discussions.lock', $discussion) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-{{ $discussion->is_locked ? 'danger' : 'outline-danger' }} w-100">
                                <i class="bi {{ $discussion->is_locked ? 'bi-unlock' : 'bi-lock' }} me-2"></i>
                                {{ $discussion->is_locked ? 'Unlock Discussion' : 'Lock Discussion' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.discussions.destroy', $discussion) }}" method="POST" onsubmit="return confirm('Delete this discussion and all its replies?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>Delete Discussion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

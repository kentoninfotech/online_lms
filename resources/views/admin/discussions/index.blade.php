@extends('layouts.app')

@section('title', 'Course Discussions')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Course Discussions</h3>
                <p class="text-muted">Manage all course discussions</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Discussions ({{ $discussions->count() }})</h5>
                    <div>
                        <form class="d-flex gap-2" method="GET">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search discussions..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Search</button>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Course</th>
                                <th>Started By</th>
                                <th>Replies</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($discussions as $discussion)
                                <tr>
                                    <td>
                                        <strong>{{ $discussion->title }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($discussion->content ?? 'No content', 60) }}</small>
                                    </td>
                                    <td>
                                        @if($discussion->course)
                                            <a href="{{ route('admin.courses.show', $discussion->course) }}">
                                                {{ Str::limit($discussion->course->title, 30) }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($discussion->user)
                                            {{ $discussion->user->name }}
                                            <br>
                                            <small class="text-muted">{{ $discussion->created_at->format('M d, Y') }}</small>
                                        @else
                                            <span class="text-muted">Deleted User</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $discussion->replies_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if($discussion->is_pinned)
                                                <span class="badge bg-warning" title="Pinned">
                                                    <i class="bi bi-pin-fill"></i>
                                                </span>
                                            @endif
                                            @if($discussion->is_locked)
                                                <span class="badge bg-danger" title="Locked">
                                                    <i class="bi bi-lock-fill"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.discussions.show', $discussion) }}" class="btn btn-outline-info btn-sm" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.discussions.pin', $discussion) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning btn-sm" title="{{ $discussion->is_pinned ? 'Unpin' : 'Pin' }}">
                                                <i class="bi {{ $discussion->is_pinned ? 'bi-pin-slash' : 'bi-pin' }}"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.discussions.lock', $discussion) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="{{ $discussion->is_locked ? 'Unlock' : 'Lock' }}">
                                                <i class="bi {{ $discussion->is_locked ? 'bi-unlock' : 'bi-lock' }}"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.discussions.destroy', $discussion) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this discussion?');">
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
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No discussions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($discussions->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $discussions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Live Sessions - ' . $course->title)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Live Sessions</h3>
                <p class="text-muted">{{ $course->title }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.live-sessions.create', $course) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Schedule Session
                </a>
                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-secondary">
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
                    <h5 class="card-title">Sessions ({{ $sessions->count() }})</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Scheduled Date & Time</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                                <tr>
                                    <td>
                                        <strong>{{ $session->title }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($session->description ?? 'No description', 60) }}</small>
                                    </td>
                                    <td>
                                        @if($session->scheduled_at)
                                            {{ $session->scheduled_at->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ $session->scheduled_at->format('h:i A') }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->duration_minutes)
                                            {{ $session->duration_minutes }} mins
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->scheduled_at && $session->scheduled_at->isFuture())
                                            <span class="badge bg-info">Upcoming</span>
                                        @elseif($session->scheduled_at && $session->scheduled_at->isPast())
                                            <span class="badge bg-secondary">Completed</span>
                                        @else
                                            <span class="badge bg-warning">Scheduled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.live-sessions.show', [$course, $session]) }}" class="btn btn-outline-info btn-sm" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.live-sessions.edit', [$course, $session]) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.live-sessions.destroy', [$course, $session]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this session?');">
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
                                        No live sessions scheduled yet. <a href="{{ route('admin.live-sessions.create', $course) }}">Schedule one now</a>
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

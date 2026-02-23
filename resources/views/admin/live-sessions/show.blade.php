@extends('layouts.app')

@section('title', 'View Session - ' . $session->title)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">{{ $session->title }}</h3>
                <p class="text-muted">{{ $course->title }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.live-sessions.edit', [$course, $session]) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('admin.live-sessions.index', $course) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Session Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Description</label>
                        <div class="border rounded p-3" style="background-color: #f8f9fa;">
                            {!! $session->description ?? '<em class="text-muted">No description</em>' !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Scheduled Date</label>
                            <div>
                                @if($session->scheduled_at)
                                    {{ $session->scheduled_at->format('l, F d, Y') }}
                                @else
                                    <span class="text-muted">Not scheduled</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Start Time</label>
                            <div>
                                @if($session->scheduled_at)
                                    {{ $session->scheduled_at->format('h:i A') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Duration</label>
                            <div>{{ $session->duration_minutes ?? 'Not specified' }} {{ $session->duration_minutes ? 'minutes' : '' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Meeting Link</label>
                            <div>
                                @if($session->meeting_link)
                                    <a href="{{ $session->meeting_link }}" target="_blank" class="text-break">
                                        {{ Str::limit($session->meeting_link, 50) }}
                                    </a>
                                @else
                                    <span class="text-muted">No link provided</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Session Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Current Status</label>
                            <div>
                                @if($session->scheduled_at && $session->scheduled_at->isFuture())
                                    <span class="badge bg-info"><i class="bi bi-calendar-event me-1"></i>Upcoming</span>
                                @elseif($session->scheduled_at && $session->scheduled_at->isPast())
                                    <span class="badge bg-secondary"><i class="bi bi-check-circle me-1"></i>Completed</span>
                                @else
                                    <span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Scheduled</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Visibility</label>
                            <div>
                                <span class="badge {{ $session->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $session->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
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
                        <div>{{ $session->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Last Updated</label>
                        <div>{{ $session->updated_at->format('M d, Y H:i') }}</div>
                    </div>

                    @if($session->scheduled_at && $session->scheduled_at->isFuture())
                        <div class="alert alert-info mb-3">
                            <small>
                                <i class="bi bi-info-circle me-1"></i>
                                This session starts <strong>{{ $session->scheduled_at->diffForHumans() }}</strong>
                            </small>
                        </div>
                    @endif

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.live-sessions.edit', [$course, $session]) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Session
                        </a>
                        <form action="{{ route('admin.live-sessions.destroy', [$course, $session]) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>Delete Session
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

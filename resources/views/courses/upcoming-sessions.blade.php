@extends('layouts.landing')

@section('title', 'Upcoming Live Sessions')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Upcoming Live Sessions</h2>

    @if($sessions->count() > 0)
        <div class="row">
            @foreach($sessions as $session)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ $session->title }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-building me-2"></i>
                                    {{ $session->course->title ?? 'N/A' }}
                                </small>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    @if($session->scheduled_at)
                                        {{ $session->scheduled_at->format('M d, Y') }}<br>
                                        <i class="bi bi-clock me-2"></i>{{ $session->scheduled_at->format('H:i') }}
                                    @else
                                        <em>Date not set</em>
                                    @endif
                                </div>
                            </div>

                            @if($session->duration_minutes)
                                <div class="mb-3 text-muted small">
                                    <i class="bi bi-hourglass-split me-2"></i>
                                    {{ $session->duration_minutes }} minutes
                                </div>
                            @endif

                            <hr>

                            <p class="small">{{ Str::limit($session->description, 100) }}</p>

                            <div class="d-grid gap-2">
                                @if($session->meeting_link)
                                    <a href="{{ $session->meeting_link }}" target="_blank" class="btn btn-success btn-sm">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Join Now
                                    </a>
                                @endif
                                <a href="{{ route('courses.live-session', [$session->course, $session]) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-info-circle me-1"></i>Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-calendar-check display-4 mb-3"></i>
            <h4>No Upcoming Sessions</h4>
            <p class="mb-3">There are no scheduled live sessions at this time.</p>
            <a href="{{ route('courses.index') }}" class="btn btn-primary">
                <i class="bi bi-search me-2"></i>Browse Courses
            </a>
        </div>
    @endif
</div>
@endsection

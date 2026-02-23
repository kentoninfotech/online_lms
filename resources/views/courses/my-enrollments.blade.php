@extends('layouts.landing')

@section('title', 'My Enrollments')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>My Enrollments</h2>
            <p class="text-muted">Manage your course enrollments and track progress</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('courses.all') }}" class="btn btn-outline-primary">
                <i class="bi bi-search me-2"></i>Browse Courses
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($enrollments->count() > 0)
        <div class="row">
            @foreach($enrollments as $enrollment)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('courses.show', $enrollment->course) }}" class="text-decoration-none">
                                    {{ $enrollment->course->title }}
                                </a>
                            </h5>
                            
                            <p class="card-text text-muted small mb-3">
                                {{ Str::limit($enrollment->course->subtitle ?? $enrollment->course->description, 80) }}
                            </p>

                            <div class="mb-3">
                                <label class="small text-muted d-block mb-1">Progress</label>
                                <div class="progress" style="height: 20px;">
                                    @php
                                        $progress = $enrollment->progress_percentage ?? 0;
                                    @endphp
                                    <div class="progress-bar bg-{{ $progress >= 75 ? 'success' : ($progress >= 50 ? 'info' : ($progress >= 25 ? 'warning' : 'danger')) }}" 
                                         role="progressbar" 
                                         style="width: {{ $progress }}%" 
                                         aria-valuenow="{{ $progress }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ $progress }}%
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 text-center mb-3">
                                <div class="col-6">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-event"></i>
                                        {{ $enrollment->courseDate->date_label ?? 'No date' }}
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt"></i>
                                        {{ $enrollment->venue->venue_name ?? 'No venue' }}
                                    </small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : ($enrollment->status === 'completed' ? 'info' : ($enrollment->status === 'cancelled' ? 'danger' : 'warning')) }}">
                                    {{ ucfirst($enrollment->status ?? 'pending') }}
                                </span>
                                <span class="badge bg-{{ $enrollment->payment_status === 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($enrollment->payment_status ?? 'pending') }}
                                </span>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('courses.learn', $enrollment->course) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-play-fill me-1"></i>Continue Learning
                                </a>
                                <a href="{{ route('courses.show', $enrollment->course) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-info-circle me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                        <div class="card-footer bg-light text-muted small">
                            Enrolled {{ $enrollment->created_at->format('M d, Y') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($enrollments->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $enrollments->links() }}
            </div>
        @endif
    @else
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-info-circle display-4 mb-3"></i>
            <h4>No Enrollments Yet</h4>
            <p class="mb-3">You haven't enrolled in any courses yet.</p>
            <a href="{{ route('courses.all') }}" class="btn btn-primary">
                <i class="bi bi-search me-2"></i>Explore Courses
            </a>
        </div>
    @endif
</div>
@endsection

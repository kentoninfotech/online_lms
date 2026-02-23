@extends('layouts.landing')

@section('title', $category->name . ' Courses')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h2>{{ $category->name }}</h2>
            <p class="text-muted">{{ $category->description ?? 'Explore our ' . $category->name . ' courses' }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to All Courses
            </a>
        </div>
    </div>

    <!-- Course Search Bar -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto" style="position: relative; z-index: 999999999999999999999999999;">
            @include('components.course-search-bar')
        </div>
    </div>

    @if($courses->count() > 0)
        <div class="row">
            @foreach($courses as $course)
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="card h-100 shadow-sm hover-effect">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('courses.show', $course) }}" class="text-decoration-none">
                                    {{ $course->title }}
                                </a>
                            </h5>
                            <p class="card-text text-muted small">
                                {{ Str::limit($course->subtitle ?? $course->description, 100) }}
                            </p>

                            <div class="mb-3">
                                <span class="badge bg-primary" style="max-width: 150px; word-wrap: break-word; white-space: normal; overflow-wrap: break-word;">{{ $category->name }}</span>
                                @if($course->is_featured)
                                    <span class="badge bg-warning">Featured</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">
                                    <i class="bi bi-person me-2"></i>
                                    {{ $course->facilitator->name ?? 'TBD' }}
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-clock me-2"></i>
                                    {{ $course->course_hours ?? '0' }} hours
                                </div>
                                <div class="h5 mt-3">
                                    {{ $course->currency ?? 'NGN' }} {{ number_format($course->fee ?? 0, 2) }}
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-info-circle me-1"></i>View Details
                                </a>
                                <a href="{{ route('courses.enroll', $course) }}" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-check-circle me-1"></i>Enroll Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-inbox display-4 mb-3"></i>
            <h4>No Courses Found</h4>
            <p class="mb-3">There are no courses in this category yet.</p>
            <a href="{{ route('courses.index') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left me-2"></i>Browse All Courses
            </a>
        </div>
    @endif
</div>
@endsection

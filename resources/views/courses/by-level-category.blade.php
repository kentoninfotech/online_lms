@extends('layouts.landing')

@section('title', $level . ' - ' . $category->name . ' Courses')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h2>{{ $level }} - {{ $category->name }}</h2>
            <p class="text-muted">Explore our {{ strtolower($level) }} level {{ strtolower($category->name) }} courses</p>
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
                                <span class="badge bg-info">{{ $level }}</span>
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

                            <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-eye me-2"></i>View Course
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row mt-5">
            <div class="col">
                {{ $courses->links() }}
            </div>
        </div>
    @else
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            No courses found in this category at this level. Please explore other categories or levels.
        </div>
    @endif
</div>
@endsection

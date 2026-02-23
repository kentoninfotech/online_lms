@extends('layouts.landing')

@section('title', $facilitator->name . ' - Course Facilitator')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Facilitator Info -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    @if($facilitator->profile_image)
                        <img src="{{ asset($facilitator->profile_image) }}" alt="{{ $facilitator->name }}" class="img-fluid rounded-circle mb-3" style="max-width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                            <i class="bi bi-person-circle" style="font-size: 80px; color: #ccc;"></i>
                        </div>
                    @endif

                    <h4 class="fw-bold mb-2">{{ $facilitator->name }}</h4>
                    <p class="text-muted mb-3">{{ $facilitator->title ?? 'Course Facilitator' }}</p>

                    @if($facilitator->bio)
                        <p class="small text-muted mb-3">
                            {{ Str::limit($facilitator->bio, 150) }}
                        </p>
                    @endif

                    <hr>

                    <div class="mb-3">
                        @if($facilitator->email)
                            <p class="small mb-2">
                                <strong>Email:</strong><br>
                                <a href="mailto:{{ $facilitator->email }}">{{ $facilitator->email }}</a>
                            </p>
                        @endif

                        @if($facilitator->phone)
                            <p class="small mb-2">
                                <strong>Phone:</strong><br>
                                <a href="tel:{{ $facilitator->phone }}">{{ $facilitator->phone }}</a>
                            </p>
                        @endif

                        @if($facilitator->qualification)
                            <p class="small">
                                <strong>Qualification:</strong><br>
                                {{ $facilitator->qualification }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses Taught -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-book me-2"></i>Courses by {{ $facilitator->name }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($courses->count() > 0)
                        <div class="row g-3">
                            @foreach($courses as $course)
                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        @if($course->featured_image)
                                            <img src="{{ asset($course->featured_image) }}" alt="{{ $course->title }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                <i class="bi bi-image" style="font-size: 3rem; color: #ccc;"></i>
                                            </div>
                                        @endif

                                        <div class="card-body">
                                            <h6 class="card-title fw-bold">{{ $course->title }}</h6>
                                            <p class="card-text small text-muted">
                                                {{ Str::limit($course->description, 80) }}
                                            </p>

                                            <div class="mb-3">
                                                @if($course->category)
                                                    <span class="badge bg-primary">{{ $course->category->name }}</span>
                                                @endif
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="h6 mb-0 text-primary fw-bold">
                                                    ₦{{ number_format($course->fee) }}
                                                </span>
                                                <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-primary">
                                                    View Course
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No courses yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- About Section -->
            @if($facilitator->bio)
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-chat-square-text me-2"></i>About {{ $facilitator->name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $facilitator->bio }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-4">
        <a href="{{ route('courses.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Courses
        </a>
    </div>
</div>
@endsection

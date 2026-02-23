@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">{{ $course->title }}</h3>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Course Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Course Code</h6>
                            <p><code>{{ $course->code }}</code></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Title</h6>
                            <p>{{ $course->title }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Category</h6>
                            <p>
                                @if($course->category)
                                    <span class="badge bg-info">{{ $course->category->name }}</span>
                                @else
                                    <span class="badge bg-secondary">Uncategorized</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Facilitators</h6>
                            <p>
                                @if($course->facilitators->count() > 0)
                                    <div>
                                        @foreach($course->facilitators as $facilitator)
                                            <a href="{{ route('admin.facilitators.show', $facilitator) }}" class="text-decoration-none d-block">
                                                <span class="badge bg-primary">{{ $facilitator->name }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @elseif($course->facilitator)
                                    <a href="{{ route('admin.facilitators.show', $course->facilitator) }}" class="text-decoration-none">
                                        <span class="badge bg-primary">{{ $course->facilitator->name }}</span>
                                    </a>
                                @else
                                    <span class="badge bg-secondary">Unassigned</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($course->subtitle)
                        <div class="mb-3">
                            <h6>Subtitle</h6>
                            <p>{{ $course->subtitle }}</p>
                        </div>
                    @endif

                    @if($course->description)
                        <div class="mb-3">
                            <h6>Description</h6>
                            <div class="text-body">
                                {!! $course->description !!}
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Course Hours</h6>
                            <p>{{ $course->course_hours ?? 'Not specified' }} hours</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Max Enrollees</h6>
                            <p>{{ $course->max_enrollees ?? 'Unlimited' }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <h6>Course Fee</h6>
                            <p class="h5">{{ $course->currency }} {{ number_format($course->fee, 2) }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6>Status</h6>
                            <p>
                                @if($course->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6>Type</h6>
                            <p>
                                @if($course->is_online && $course->is_offline)
                                    <span class="badge bg-info">Online & Offline</span>
                                @elseif($course->is_online)
                                    <span class="badge bg-info">Online</span>
                                @elseif($course->is_offline)
                                    <span class="badge bg-warning">Offline</span>
                                @else
                                    <span class="badge bg-secondary">Not Specified</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($course->is_featured)
                        <div class="alert alert-info">
                            <i class="bi bi-star-fill me-2"></i>This course is featured
                        </div>
                    @endif
                </div>
            </div>

            @if($course->featured_image)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Featured Image</h5>
                    </div>
                    <div class="card-body">
                        <img src="{{ asset($course->featured_image) }}" alt="{{ $course->title }}" class="img-fluid rounded">
                    </div>
                </div>
            @endif

            @if($course->courseDates->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Course Dates</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Venues</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->courseDates as $date)
                                    <tr>
                                        <td>{{ $date->start_date?->format('M d, Y') }}</td>
                                        <td>{{ $date->end_date?->format('M d, Y') }}</td>
                                        <td>
                                            @if($date->venues->count() > 0)
                                                <small>
                                                    @foreach($date->venues as $venue)
                                                        <span class="badge bg-secondary">{{ $venue->venue_name }}</span>
                                                    @endforeach
                                                </small>
                                            @else
                                                <small class="text-muted">No venues</small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Enrollments</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted">Total Enrollees</h6>
                        <h2 class="mb-0">{{ $course->enrollees->count() }}</h2>
                    </div>
                    <hr>
                    <div>
                        <a href="{{ route('admin.course-enrollments.index', ['course_id' => $course->id]) }}" class="btn btn-sm btn-outline-primary w-100">
                            <i class="bi bi-eye me-2"></i>View Enrollments
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Course Management</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.course-contents.index', $course) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-book-fill me-2"></i>Manage Content
                        </a>
                        <a href="{{ route('admin.live-sessions.create', $course) }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-camera-video me-2"></i>Create Live Session
                        </a>
                        <a href="{{ route('admin.course-quizzes.index', $course) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-question-circle me-2"></i>Manage Quizzes
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-chat-dots me-2"></i>Discussions
                        </a>
                        <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure? This will delete the course and all associated content.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-trash me-2"></i>Delete Course
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Metadata</h5>
                </div>
                <div class="card-body">
                    <small>
                        <p class="text-muted mb-1">
                            <strong>Created:</strong> {{ $course->created_at?->format('M d, Y H:i') }}
                        </p>
                        <p class="text-muted mb-1">
                            <strong>Updated:</strong> {{ $course->updated_at?->format('M d, Y H:i') }}
                        </p>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

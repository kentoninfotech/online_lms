@extends('layouts.app')

@section('title', 'Lesson: ' . $lesson->topic)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">{{ $lesson->topic }}</h3>
                <p class="text-muted">Course: <strong>{{ $course->title }}</strong></p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.course-lessons.edit', [$course, $lesson]) }}" class="btn btn-primary me-2">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('admin.course-lessons.index', $course) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lesson Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Status</h6>
                            <p>
                                @if ($lesson->status === 'upcoming')
                                    <span class="badge bg-info">Upcoming</span>
                                @elseif ($lesson->status === 'ongoing')
                                    <span class="badge bg-warning">Ongoing</span>
                                @elseif ($lesson->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Published</h6>
                            <p>
                                @if ($lesson->is_published)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-warning">No</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Instructor</h6>
                            <p>{{ $lesson->instructor->user->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Duration</h6>
                            <p>
                                @if ($lesson->duration_minutes)
                                    {{ $lesson->duration_minutes }} minutes
                                @else
                                    Not specified
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Start Time</h6>
                            <p>
                                @if ($lesson->start_time)
                                    {{ $lesson->start_time->format('M d, Y h:i A') }}
                                @else
                                    Not scheduled
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>End Time</h6>
                            <p>
                                @if ($lesson->end_time)
                                    {{ $lesson->end_time->format('M d, Y h:i A') }}
                                @else
                                    Not scheduled
                                @endif
                            </p>
                        </div>
                    </div>

                    @if ($lesson->location)
                        <div class="mb-3">
                            <h6>Location</h6>
                            <p>{{ $lesson->location }}</p>
                        </div>
                    @endif

                    @if ($lesson->meeting_link)
                        <div class="mb-3">
                            <h6>Meeting Link</h6>
                            <p>
                                <a href="{{ $lesson->meeting_link }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="bi bi-link-45deg me-1"></i>Join Meeting
                                </a>
                            </p>
                        </div>
                    @endif

                    @if ($lesson->description)
                        <div class="mb-3">
                            <h6>Description</h6>
                            <p>{{ $lesson->description }}</p>
                        </div>
                    @endif

                    @if ($lesson->notes)
                        <div class="mb-3">
                            <h6>Notes</h6>
                            <p>{{ $lesson->notes }}</p>
                        </div>
                    @endif

                    @if ($lesson->capacity)
                        <div class="mb-3">
                            <h6>Capacity</h6>
                            <p>{{ $lesson->capacity }} students</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people me-2"></i>Attendees ({{ $attendees->total() }})
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attendees as $attendee)
                                <tr>
                                    <td>
                                        <small>{{ $attendee->enrollee->user->name ?? 'Unknown' }}</small>
                                    </td>
                                    <td>
                                        <small>
                                            @if ($attendee->status === 'attended')
                                                <span class="badge bg-success">Attended</span>
                                            @elseif ($attendee->status === 'absent')
                                                <span class="badge bg-danger">Absent</span>
                                            @else
                                                <span class="badge bg-info">Registered</span>
                                            @endif
                                        </small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">
                                        <small>No attendees yet</small>
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

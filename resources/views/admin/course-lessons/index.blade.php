@extends('layouts.app')

@section('title', 'Course Lessons')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Course Lessons</h3>
                <p class="text-muted">Course: <strong>{{ $course->title }}</strong></p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.course-lessons.calendar', $course) }}" class="btn btn-info me-2">
                    <i class="bi bi-calendar me-2"></i>Calendar View
                </a>
                <a href="{{ route('admin.course-lessons.create', $course) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add Lesson
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Lessons</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Topic</th>
                                <th>Instructor</th>
                                <th>Date & Time</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lessons as $lesson)
                                <tr>
                                    <td>
                                        <strong>{{ $lesson->topic }}</strong>
                                        @if ($lesson->location)
                                            <br><small class="text-muted">{{ $lesson->location }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $lesson->instructor->user->name }}</td>
                                    <td>
                                        <small>
                                            @if ($lesson->start_time)
                                                {{ $lesson->start_time->format('M d, Y h:i A') }}
                                            @else
                                                <span class="badge bg-secondary">Not Scheduled</span>
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        @if ($lesson->duration_minutes)
                                            {{ $lesson->duration_minutes }} mins
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($lesson->status === 'upcoming')
                                            <span class="badge bg-info">Upcoming</span>
                                        @elseif ($lesson->status === 'ongoing')
                                            <span class="badge bg-warning">Ongoing</span>
                                        @elseif ($lesson->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @else
                                            <span class="badge bg-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($lesson->is_published)
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <span class="badge bg-warning">Draft</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.course-lessons.show', [$course, $lesson]) }}"
                                                class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.course-lessons.edit', [$course, $lesson]) }}"
                                                class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST"
                                                action="{{ route('admin.course-lessons.destroy', [$course, $lesson]) }}"
                                                style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox"></i> No lessons created yet.
                                        <a href="{{ route('admin.course-lessons.create', $course) }}">Create one now</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($lessons->hasPages())
                <div class="mt-3">
                    {{ $lessons->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

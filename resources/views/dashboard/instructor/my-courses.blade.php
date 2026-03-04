@extends('layouts.app')

@section('title', 'My Courses - Tutor Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 mb-0">My Courses</h2>
            <p class="text-muted mt-1">Manage all courses assigned to you</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 opacity-75">Total Courses</h6>
                            <h3 class="mb-0">{{ $totalCourses }}</h3>
                        </div>
                        <i class="bi bi-book-fill" style="font-size: 2.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 opacity-75">Active Courses</h6>
                            <h3 class="mb-0">{{ $activeCourses }}</h3>
                        </div>
                        <i class="bi bi-check-circle-fill" style="font-size: 2.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 opacity-75">Total Enrollees</h6>
                            <h3 class="mb-0">{{ $totalEnrollees }}</h3>
                        </div>
                        <i class="bi bi-people-fill" style="font-size: 2.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-2 opacity-75">Avg. Enrollees</h6>
                            <h3 class="mb-0">{{ $totalCourses > 0 ? round($totalEnrollees / $totalCourses) : 0 }}</h3>
                        </div>
                        <i class="bi bi-graph-up" style="font-size: 2.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0">Courses List</h6>
                </div>
                <div class="card-body p-0">
                    @if($courses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 10%;">Code</th>
                                        <th style="width: 20%;">Course Title</th>
                                        <th style="width: 15%;">Category</th>
                                        <th style="width: 8%;">Status</th>
                                        <th style="width: 8%;">Enrollees</th>
                                        <th style="width: 8%;">Hours</th>
                                        <th style="width: 8%;">Level</th>
                                        <th style="width: 17%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $course)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ $course->code }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($course->featured_image)
                                                        <img src="{{ asset($course->featured_image) }}" 
                                                             alt="{{ $course->title }}"
                                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                                                    @else
                                                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 4px; margin-right: 10px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="bi bi-book" style="color: white; font-size: 1.2rem;"></i>
                                                        </div>
                                                    @endif
                                                    <strong>{{ substr($course->title, 0, 35) }}@if(strlen($course->title) > 35)...@endif</strong>
                                                </div>
                                            </td>
                                            <td>
                                                @if($course->category)
                                                    <span class="text-muted">{{ $course->category->name }}</span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($course->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $course->enrollees_count ?? 0 }}</span>
                                            </td>
                                            <td>
                                                {{ $course->course_hours ?? '—' }}
                                            </td>
                                            <td>
                                                <span class="text-uppercase small">
                                                    {{ substr($course->level ?? 'N/A', 0, 1) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('courses.show', $course->id) }}" 
                                                       class="btn btn-outline-primary" 
                                                       title="View course">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('tutor.courses.edit', $course->id) }}" 
                                                       class="btn btn-outline-info" 
                                                       title="Edit course details">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <a href="{{ route('tutor.course-contents.index', $course->id) }}" 
                                                       class="btn btn-outline-success" 
                                                       title="Manage content">
                                                        <i class="bi bi-file-earmark-text"></i>
                                                    </a>
                                                    <a href="{{ route('tutor.course-quizzes.index', $course->id) }}" 
                                                       class="btn btn-outline-warning" 
                                                       title="Manage quizzes">
                                                        <i class="bi bi-question-circle"></i>
                                                    </a>
                                                    <a href="{{ route('tutor.course-enrollments.index', $course->id) }}" 
                                                       class="btn btn-outline-danger" 
                                                       title="View enrollees">
                                                        <i class="bi bi-people"></i>
                                                    </a>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button id="btnMore{{ $course->id }}" type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="More options">
                                                            <i class="bi bi-three-dots"></i>
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="btnMore{{ $course->id }}">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('tutor.live-sessions.index', $course->id) }}">
                                                                    <i class="bi bi-camera-video"></i> Live Sessions
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('tutor.discussions.index', $course->id) }}">
                                                                    <i class="bi bi-chat-dots"></i> Discussions
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('tutor.course.announcement.create', $course->id) }}">
                                                                    <i class="bi bi-megaphone"></i> Announcements
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc; margin-bottom: 20px; display: block;"></i>
                            <h5 class="mb-2">No Courses Assigned</h5>
                            <p class="text-muted mb-0">You haven't been assigned to any courses yet. Contact your administrator to assign courses to you.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pagination -->
            @if($courses->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $courses->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.85rem;
    }

    .dropdown-menu {
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .dropdown-item {
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        padding-left: 1.25rem;
    }

    .badge {
        font-weight: 500;
    }

    .table td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }

    .table thead th {
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
    }
</style>
@endsection

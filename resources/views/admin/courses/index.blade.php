@extends('layouts.app')

@section('title', 'Manage Courses')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Manage Courses</h3>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create Course
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Courses</h5>
                    <p class="text-muted mb-0">Total: {{ $courses->total() }} courses</p>
                </div>
                
                <!-- Course Search Bar -->
                <div class="card-body border-bottom">
                    <div style="max-width: 500px;">
                        @include('components.course-search-bar')
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Facilitator</th>
                                <th>Content Count</th>
                                <th>Fee</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($courses as $course)
                                <tr>
                                    <td>
                                        <strong>{{ $course->code }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.courses.show', $course) }}" class="text-decoration-none">
                                            {{ Str::limit($course->title, 40) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $course->category?->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        {{ $course->facilitator?->name ?? 'Unassigned' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $course->contents()->count() }} contents</span>
                                    </td>
                                    <td>
                                        {{ $course->currency }} {{ number_format($course->fee, 2) }}
                                    </td>
                                    <td>
                                        @if($course->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-info" title="View" data-bs-toggle="tooltip">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.course-contents.index', $course) }}" class="btn btn-outline-secondary" title="View Contents" data-bs-toggle="tooltip">
                                                <i class="bi bi-collection"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-outline-primary" title="Edit" data-bs-toggle="tooltip">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                        <p class="mt-2">No courses found. <a href="{{ route('admin.courses.create') }}">Create one now</a>.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($courses->hasPages())
                    <div class="card-footer">
                        {{ $courses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

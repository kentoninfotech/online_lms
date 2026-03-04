@extends('layouts.app')

@section('title', $facilitator->name)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">{{ $facilitator->name }}</h3>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.facilitators.edit', $facilitator) }}" class="btn btn-primary me-2">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('admin.facilitators.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Profile Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Facilitator Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            @if($facilitator->profile_image)
                                <img src="{{ asset($facilitator->profile_image) }}" alt="{{ $facilitator->name }}" class="img-fluid rounded" style="max-height: 250px;">
                            @else
                                <div class="bg-light rounded p-5 text-center">
                                    <i class="bi bi-person-circle" style="font-size: 100px; color: #ccc;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $facilitator->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><a href="mailto:{{ $facilitator->email }}">{{ $facilitator->email }}</a></td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $facilitator->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($facilitator->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Verified:</strong></td>
                                    <td>
                                        @if($facilitator->is_verified)
                                            <span class="badge bg-success">Verified</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($facilitator->bio)
                        <div class="mb-3">
                            <h6>Biography</h6>
                            <p>{{ $facilitator->bio }}</p>
                        </div>
                    @endif

                    @if($facilitator->qualification)
                        <div class="mb-3">
                            <h6>Qualification</h6>
                            <p>{{ $facilitator->qualification }}</p>
                        </div>
                    @endif

                    @if($facilitator->expertise)
                        <div class="mb-3">
                            <h6>Expertise</h6>
                            <p>{{ $facilitator->expertise }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Associated Courses -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Associated Courses ({{ $facilitator->assignedCourses->count() }})</h5>
                </div>
                <div class="table-responsive">
                    @if($facilitator->assignedCourses->count() > 0)
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Course Name</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Enrollees</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($facilitator->assignedCourses as $course)
                                    <tr>
                                        <td>{{ $course->title }}</td>
                                        <td>
                                            @if($course->category)
                                                <span class="badge bg-info">{{ $course->category->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($course->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $course->enrollees->count() }}</td>
                                        <td>
                                            <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="card-body text-center text-muted">
                            <p>No courses associated yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Statistics Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted">Total Courses</h6>
                        <h2 class="mb-0">{{ $facilitator->assignedCourses->count() }}</h2>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-muted">Total Enrollees</h6>
                        <h2 class="mb-0">
                            {{ $facilitator->assignedCourses->reduce(function($carry, $course) {
                                return $carry + $course->enrollees->count();
                            }, 0) }}
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Account Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Account Details</h5>
                </div>
                <div class="card-body">
                    <small>
                        <p class="text-muted mb-2">
                            <strong>Linked User:</strong> {{ $facilitator->user->name ?? 'N/A' }}
                        </p>
                        <p class="text-muted mb-2">
                            <strong>Created:</strong> {{ $facilitator->created_at?->format('M d, Y H:i') }}
                        </p>
                        <p class="text-muted mb-0">
                            <strong>Updated:</strong> {{ $facilitator->updated_at?->format('M d, Y H:i') }}
                        </p>
                    </small>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.facilitators.edit', $facilitator) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil me-2"></i>Edit Facilitator
                        </a>
                        <form action="{{ route('admin.facilitators.destroy', $facilitator) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100 btn-sm" onclick="return confirm('Are you sure you want to delete this facilitator?');">
                                <i class="bi bi-trash me-2"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

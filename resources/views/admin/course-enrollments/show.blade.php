@extends('layouts.app')

@section('title', 'Enrollment Details')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Enrollment Details</h3>
                <p class="text-muted">{{ $enrollment->user?->name ?? 'N/A' }} - {{ $enrollment->course?->title ?? 'N/A' }}</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.course-enrollments.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Student Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Student Name</label>
                            <div>
                                @if($enrollment->user)
                                    <strong>{{ $enrollment->user->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $enrollment->user->email }}</small>
                                @else
                                    <span class="text-muted">User deleted</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Enrollment Date</label>
                            <div>{{ $enrollment->created_at->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Course Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Course</label>
                            <div>
                                @if($enrollment->course)
                                    <a href="{{ route('admin.courses.show', $enrollment->course) }}">
                                        <strong>{{ $enrollment->course->title }}</strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">Code: {{ $enrollment->course->code }}</small>
                                @else
                                    <span class="text-muted">Course deleted</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Category</label>
                            <div>
                                @if($enrollment->course?->category)
                                    {{ $enrollment->course->category->name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Selected Date</label>
                            <div>
                                @if($enrollment->courseDate)
                                    <strong>{{ $enrollment->courseDate->date_label }}</strong>
                                    <br>
                                    <small class="text-muted">Sequence: {{ $enrollment->courseDate->sequence }}</small>
                                @else
                                    <span class="text-muted">Not selected</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Selected Venue</label>
                            <div>
                                @if($enrollment->venue)
                                    <strong>{{ $enrollment->venue->venue_name }}</strong>
                                @else
                                    <span class="text-muted">Not selected</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Course Fee</label>
                            <div>
                                @if($enrollment->course)
                                    <strong>
                                        {{ $enrollment->course->currency ?? 'NGN' }} 
                                        {{ number_format($enrollment->course->fee ?? 0, 2) }}
                                    </strong>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Payment Status</label>
                            <div>
                                @php
                                    $paymentStatus = $enrollment->payment_status ?? 'pending';
                                @endphp
                                <span class="badge bg-{{ $paymentStatus === 'completed' ? 'success' : ($paymentStatus === 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($paymentStatus) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($enrollment->payment_reference)
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Payment Reference</label>
                                <div class="text-break"><small>{{ $enrollment->payment_reference }}</small></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Enrollment Actions</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Current Status</label>
                        <div class="mb-3">
                            @php
                                $status = $enrollment->status ?? 'pending';
                            @endphp
                            <span class="badge bg-{{ $status === 'active' ? 'success' : ($status === 'completed' ? 'info' : ($status === 'cancelled' ? 'danger' : 'warning')) }}">
                                {{ ucfirst($status) }}
                            </span>
                        </div>
                    </div>

                    <hr>

                    <!-- Status Update Form -->
                    <div class="mb-4">
                        <p class="text-muted small mb-2">Update Enrollment Status</p>
                        <form action="{{ route('admin.course-enrollments.update', $enrollment) }}" method="POST" class="mb-2">
                            @csrf
                            @method('PUT')
                            
                            <select name="status" class="form-select form-select-sm mb-2">
                                <option value="pending" {{ ($enrollment->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ ($enrollment->status ?? 'pending') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ ($enrollment->status ?? 'pending') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ ($enrollment->status ?? 'pending') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>

                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-check-circle me-2"></i>Update Status
                            </button>
                        </form>
                    </div>

                    <hr>

                    <!-- Quick Action Buttons -->
                    <div class="mb-4">
                        <p class="text-muted small mb-2">Quick Actions</p>
                        
                        @if(($enrollment->status ?? 'pending') !== 'active')
                            <form action="{{ route('admin.course-enrollments.update', $enrollment) }}" method="POST" class="mb-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="active">
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="bi bi-check-circle me-2"></i>Activate
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-success btn-sm w-100" disabled>
                                <i class="bi bi-check-circle me-2"></i>Active
                            </button>
                        @endif

                        @if(($enrollment->status ?? 'pending') !== 'cancelled')
                            <form action="{{ route('admin.course-enrollments.update', $enrollment) }}" method="POST" class="mb-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-warning btn-sm w-100">
                                    <i class="bi bi-pause-circle me-2"></i>Suspend
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-warning btn-sm w-100" disabled>
                                <i class="bi bi-pause-circle me-2"></i>Suspended
                            </button>
                        @endif
                    </div>

                    <hr>

                    <!-- Delete Button -->
                    <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash me-2"></i>Delete Enrollment
                    </button>

                    <hr>

                    <div class="d-grid">
                        <a href="{{ route('admin.course-enrollments.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-list me-2"></i>View All Enrollments
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Metadata</h5>
                </div>
                <div class="card-body">
                    <label class="form-label text-muted">Created</label>
                    <div class="mb-3">{{ $enrollment->created_at->format('M d, Y H:i') }}</div>

                    <label class="form-label text-muted">Last Updated</label>
                    <div>{{ $enrollment->updated_at->format('M d, Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Enrollment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        <strong>Are you sure you want to delete this enrollment?</strong>
                    </p>
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        This action will delete the enrollment for <strong>{{ $enrollment->user->name }}</strong> in <strong>{{ $enrollment->course->title }}</strong>. 
                        This cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.course-enrollments.destroy', $enrollment) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Enrollment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Manage Course Enrollments')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Course Enrollments</h3>
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
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Enrollments</h5>
                    <p class="text-muted mb-0">Total: {{ $enrollments->total() }} enrollments</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Enrollment Date</th>
                                <th>Status</th>
                                <th>Payment Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <strong>{{ $enrollment->user?->name ?? 'Unknown' }}</strong><br>
                                        <small class="text-muted">{{ $enrollment->user?->email ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.courses.show', $enrollment->course) }}">
                                            {{ $enrollment->course->title ?? 'Deleted' }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $enrollment->created_at?->format('M d, Y') }}
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'active' => 'success',
                                                'completed' => 'info',
                                                'cancelled' => 'danger'
                                            ];
                                            $status = $enrollment->status ?? 'pending';
                                            $color = $statusColors[$status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ ucfirst($status) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $paymentStatusColors = [
                                                'pending' => 'warning',
                                                'completed' => 'success',
                                                'failed' => 'danger'
                                            ];
                                            $paymentStatus = $enrollment->payment_status ?? 'pending';
                                            $paymentColor = $paymentStatusColors[$paymentStatus] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $paymentColor }}">{{ ucfirst($paymentStatus) }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.course-enrollments.show', $enrollment) }}" class="btn btn-outline-info" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" title="Delete Enrollment" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $enrollment->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $enrollment->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Enrollment?</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mb-0">
                                                    Are you sure you want to delete this enrollment for <strong>{{ $enrollment->user->name }}</strong> in <strong>{{ $enrollment->course->title }}</strong>?
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.course-enrollments.destroy', $enrollment) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                        <p class="mt-2">No enrollments found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($enrollments->hasPages())
                    <div class="card-footer">
                        {{ $enrollments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

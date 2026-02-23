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
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <strong>{{ $enrollment->student->user->name ?? 'Unknown' }}</strong><br>
                                        <small class="text-muted">{{ $enrollment->student->user->email ?? '-' }}</small>
                                    </td>
                                    <td>
                                        {{ $enrollment->course->title ?? 'Deleted' }}
                                    </td>
                                    <td>
                                        {{ $enrollment->created_at?->format('M d, Y') }}
                                    </td>
                                    <td>
                                        {{ $enrollment->start_date?->format('M d, Y') }}
                                    </td>
                                    <td>
                                        {{ $enrollment->end_date?->format('M d, Y') }}
                                    </td>
                                    <td>
                                        @if($enrollment->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="#" class="btn btn-outline-info" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
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

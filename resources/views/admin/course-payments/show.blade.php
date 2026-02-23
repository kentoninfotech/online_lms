@extends('layouts.app')

@section('title', 'Review Course Payment')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Review Course Payment</h3>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.course-payments.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Student & Payment Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Payment & Student Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Student Name</h6>
                            <p class="h6">{{ $payment->user->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Email</h6>
                            <p><a href="mailto:{{ $payment->user->email }}">{{ $payment->user->email }}</a></p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Course</h6>
                            <p class="h6"><a href="{{ route('admin.courses.show', $payment->course) }}">{{ $payment->course->title }}</a></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Amount Due</h6>
                            <p class="h6">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</p>
                        </div>
                    </div>

                    @if($payment->payer_name)
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Payer Name</h6>
                                <p class="h6">{{ $payment->payer_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Amount Paid</h6>
                                <p class="h6">{{ $payment->currency }} {{ number_format($payment->payment_evidence_amount, 2) }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Evidence -->
            @if($payment->payment_evidence_path)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Payment Evidence</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="text-muted">Uploaded: {{ $payment->created_at->format('M d, Y H:i A') }}</p>
                            @if(pathinfo($payment->payment_evidence_path, PATHINFO_EXTENSION) === 'pdf')
                                <iframe src="{{ asset($payment->payment_evidence_path) }}" width="100%" height="600px" style="border: 1px solid #ddd; border-radius: 4px;"></iframe>
                            @else
                                <img src="{{ asset($payment->payment_evidence_path) }}" class="img-fluid rounded" style="max-height: 600px; border: 1px solid #ddd;">
                            @endif
                            <div class="mt-3">
                                <a href="{{ asset($payment->payment_evidence_path) }}" download class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-download me-2"></i>Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Approval Actions -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title text-white mb-0">Approval Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Approve Button -->
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bi bi-check-circle me-2"></i>Approve Payment
                            </button>
                        </div>
                        <!-- Reject Button -->
                        <div class="col-md-6">
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-2"></i>Reject Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Approval Status</small>
                        <p>
                            @if($payment->approval_status === 'pending')
                                <span class="badge bg-warning">Pending Review</span>
                            @elseif($payment->approval_status === 'approved')
                                <span class="badge bg-success">Approved</span>
                                <p class="mt-2 mb-0"><small class="text-muted">Approved by</small></p>
                                <p>{{ $payment->approver->name ?? 'System' }}</p>
                            @elseif($payment->approval_status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </p>
                    </div>

                    @if($payment->approval_notes)
                        <div class="alert alert-info">
                            <small><strong>Notes:</strong></small>
                            <p class="mb-0">{{ $payment->approval_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Enrollment Details -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Enrollment Details</h5>
                </div>
                <div class="card-body">
                    <small>
                        <p class="text-muted mb-2"><strong>Enrollment Status:</strong> {{ ucfirst($payment->enrollment->status) }}</p>
                        <p class="text-muted mb-2"><strong>Date:</strong> {{ $payment->enrollment->created_at->format('M d, Y H:i') }}</p>
                        <p class="text-muted mb-2"><strong>Course Date:</strong> {{ $payment->enrollment->courseDate->date_label ?? 'N/A' }}</p>
                        <p class="text-muted mb-0"><strong>Venue:</strong> {{ $payment->enrollment->venue->venue_name ?? 'N/A' }}</p>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.course-payments.approve', $payment) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Approve Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>You are about to approve this payment for {{ $payment->user->name }}.</p>
                    <div class="alert alert-success">
                        <strong>Student will immediately have access to the course content.</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Approval Notes (Optional)</label>
                        <textarea name="approval_notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.course-payments.reject', $payment) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>You are about to reject this payment.</p>
                    <div class="alert alert-danger">
                        <strong>Student will be notified and can resubmit their payment.</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="approval_notes" class="form-control" rows="4" required placeholder="Explain why the payment is being rejected..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@extends('layouts.app')

@section('title', 'Course Payment Approvals')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Course Payment Approvals</h3>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Pending Approvals</h6>
                    <h2 class="text-warning">{{ $payments->total() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Approved</h6>
                    <h2 class="text-success">{{ $approvedPayments->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">Rejected</h6>
                    <h2 class="text-danger">{{ $rejectedPayments->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Payments Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Pending Payment Approvals</h5>
        </div>
        <div class="table-responsive">
            @if($payments->count() > 0)
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Evidence</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>
                                    <strong>{{ $payment->user->name }}</strong><br>
                                    <small class="text-muted">{{ $payment->user->email }}</small>
                                </td>
                                <td>{{ Str::limit($payment->course->title, 30) }}</td>
                                <td>
                                    <h6 class="mb-0">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</h6>
                                    <small class="text-muted">Paid: {{ $payment->payment_evidence_amount ?? '-' }}</small>
                                </td>
                                <td>
                                    @if($payment->payment_method === 'bank')
                                        <span class="badge bg-info">Bank Transfer</span>
                                    @else
                                        <span class="badge bg-primary">Paystack</span>
                                    @endif
                                </td>
                                <td>
                                    @if($payment->payment_evidence_path)
                                        <a href="{{ asset('storage/' . $payment->payment_evidence_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-file-pdf"></i> View
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $payment->created_at->format('M d, Y H:i') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.course-payments.show', $payment) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Review
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-inbox h2" style="opacity: 0.5;"></i>
                    <p class="mt-3">No pending payments for approval</p>
                </div>
            @endif
        </div>
        @if($payments->count() > 0)
            <div class="card-footer">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <!-- Recent Approved Payments -->
    @if($approvedPayments->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Recently Approved</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Amount</th>
                            <th>Approved By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvedPayments as $payment)
                            <tr>
                                <td>{{ $payment->user->name }}</td>
                                <td>{{ Str::limit($payment->course->title, 30) }}</td>
                                <td>{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->approver->name ?? 'System' }}</td>
                                <td>{{ $payment->approved_at?->format('M d, Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@extends('layouts.landing')

@section('title', 'Payment Pending - Approval')

@section('content')
<div style="padding: 40px 0;">
    <div class="container" style="max-width: 600px;">

        <div class="card shadow-lg">
            <div class="card-body p-5 text-center">
                <div style="margin-bottom: 30px;">
                    <div style="display: inline-block; width: 100px; height: 100px; background: linear-gradient(135deg, #FFC107 0%, #FFA500 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-hourglass-split text-white" style="font-size: 50px;"></i>
                    </div>
                </div>

                <h2 class="h2 fw-bold mb-2">Payment Pending</h2>
                <p class="text-muted mb-4">Your payment evidence is being reviewed</p>

                <!-- Course Details -->
                <div class="card bg-light mb-4">
                    <div class="card-body text-start">
                        <h6 class="text-muted mb-2">Course</h6>
                        <h5 class="mb-3">{{ $payment->course->title }}</h5>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">Amount</small>
                                <h6>{{ $payment->currency }} {{ number_format($payment->payment_evidence_amount, 2) }}</h6>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Payer Name</small>
                                <h6>{{ $payment->payer_name }}</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Timeline -->
                <div class="p-4 bg-light rounded mb-4" style="text-align: left;">
                    <h6 class="fw-bold mb-3">Approval Timeline:</h6>
                    <div style="margin-left: 20px; border-left: 2px solid #28a745; padding-left: 20px;">
                        <div class="mb-3">
                            <span class="badge bg-success" style="margin-left: -40px;">✓</span>
                            <strong>Payment Evidence Submitted</strong>
                            <br>
                            <small class="text-muted">{{ $payment->created_at->format('M d, Y H:i A') }}</small>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-warning" style="margin-left: -40px;">⏳</span>
                            <strong>Under Review</strong>
                            <br>
                            <small class="text-muted">Our admin team is reviewing your payment</small>
                        </div>
                        <div>
                            <span class="badge bg-secondary" style="margin-left: -40px;">→</span>
                            <strong>Approval Decision</strong>
                            <br>
                            <small class="text-muted">You'll receive an email notification</small>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-grid gap-2">
                    <a href="{{ route('courses.show', $payment->course) }}" class="btn btn-outline-primary">
                        <i class="bi bi-book me-2"></i>Back to Course
                    </a>
                    <a href="{{ route('courses.my-enrollments') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-list me-2"></i>My Enrollments
                    </a>
                </div>

                <div class="mt-4 p-3 bg-light rounded">
                    <small class="text-muted">
                        💡 <strong>Typical Review Time:</strong> Your payment will be reviewed within 24 hours. You'll receive an email notification once approved.
                    </small>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

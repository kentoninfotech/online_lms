@extends('layouts.landing')

@section('title', 'Select Payment Method')

@section('content')
<div style="padding: 40px 0;">
    <div class="container" style="max-width: 600px;">
        
        <div class="mb-4">
            <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>

        <div class="card shadow-lg">
            <div class="card-body p-5">
                <h2 class="h3 fw-bold mb-2">Complete Your Payment</h2>
                <p class="text-muted mb-4">Choose your preferred payment method</p>

                <!-- Course Details -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Course</h6>
                        <h5 class="mb-3">{{ $payment->course->title }}</h5>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">Amount Due</small>
                                <h4 class="text-primary">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</h4>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Status</small>
                                <h6><span class="badge bg-warning">Pending Payment</span></h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="row g-3">
                    <!-- Paystack Payment -->
                    <div class="col-12">
                        <a href="{{ route('course.payment.paystack', $payment) }}" class="card payment-method-card h-100" style="text-decoration: none; border: 2px solid #f0f0f0; transition: all 0.3s;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #007AFF 0%, #0051BA 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-credit-card text-white h4 mb-0"></i>
                                        </div>
                                    </div>
                                    <div style="flex: 1;">
                                        <h6 class="mb-1">Paystack</h6>
                                        <small class="text-muted">Card, Bank Transfer, Mobile Money</small>
                                    </div>
                                    <div>
                                        <i class="bi bi-chevron-right text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Bank Transfer -->
                    <div class="col-12">
                        <a href="{{ route('course.payment.bank', $payment) }}" class="card payment-method-card h-100" style="text-decoration: none; border: 2px solid #f0f0f0; transition: all 0.3s;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #25C997 0%, #0FA54D 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-bank2 text-white h4 mb-0"></i>
                                        </div>
                                    </div>
                                    <div style="flex: 1;">
                                        <h6 class="mb-1">Bank Transfer</h6>
                                        <small class="text-muted">Direct bank account transfer</small>
                                    </div>
                                    <div>
                                        <i class="bi bi-chevron-right text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded">
                    <small class="text-muted">
                        💡 <strong>Tip:</strong> Paystack allows card payments. Bank Transfer requires you to provide evidence of payment for admin approval.
                    </small>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .payment-method-card:hover {
        border-color: #007AFF !important;
        box-shadow: 0 4px 12px rgba(0, 122, 255, 0.15) !important;
    }
</style>
@endsection

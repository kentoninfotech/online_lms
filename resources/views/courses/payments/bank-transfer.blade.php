@extends('layouts.landing')

@section('title', 'Bank Transfer Payment')

@section('content')
<div style="padding: 40px 0;">
    <div class="container" style="max-width: 700px;">

        <div class="mb-4">
            <a href="{{ route('course.payment.show', $payment) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Payment Methods
            </a>
        </div>

        <div class="card shadow-lg">
            <div class="card-body p-5">
                <h2 class="h3 fw-bold mb-2">Pay by Bank Transfer</h2>
                <p class="text-muted mb-4">Transfer funds to our account and provide proof below</p>

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
                                <small class="text-muted d-block">Reference</small>
                                <code style="font-size: 12px;">{{ $payment->reference_id }}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Details Section -->
                <div class="alert alert-info mb-4">
                    <h6 class="fw-bold mb-3">📌 Our Bank Account Details:</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Bank Name</small>
                            <strong>{{ env('BANK_NAME', 'First Bank of Nigeria') }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Account Name</small>
                            <strong>{{ env('BANK_ACCOUNT_NAME') ?: \App\Models\HomepageSetting::getSetting('branding', 'site_name') ?: 'Learning Management System Inc' }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Account Number</small>
                            <strong>{{ env('BANK_ACCOUNT_NUMBER', '3017934851') }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Sort Code</small>
                            <strong>{{ env('BANK_SORT_CODE', '011') }}</strong>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted">
                            <strong>⚠️ Important:</strong> Include the reference <code>{{ $payment->reference_id }}</code> in the transfer description so we can identify your payment.
                        </small>
                    </div>
                </div>

                <!-- Upload Evidence Form -->
                <div class="card border-secondary mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Upload Payment Evidence</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('course.payment.upload-evidence', $payment) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Amount Paid <span class="text-danger">*</span></label>
                                <input type="number" name="payment_evidence_amount" class="form-control @error('payment_evidence_amount') is-invalid @enderror" 
                                    step="0.01" min="0" value="{{ old('payment_evidence_amount', $payment->amount) }}" required>
                                @error('payment_evidence_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Amount that was transferred</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Payer's Name <span class="text-danger">*</span></label>
                                <input type="text" name="payer_name" class="form-control @error('payer_name') is-invalid @enderror" 
                                    value="{{ old('payer_name', Auth::user()->name) }}" required>
                                @error('payer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Name on the bank account that made the transfer</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Payment Evidence <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="file" name="payment_evidence_path" class="form-control @error('payment_evidence_path') is-invalid @enderror" 
                                        accept="image/png,image/jpeg,application/pdf" required>
                                    <label class="input-group-text">Choose File</label>
                                </div>
                                @error('payment_evidence_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-2">Upload bank transfer receipt or payment proof (PDF, JPG, PNG - Max 5MB)</small>
                            </div>

                            <input type="hidden" name="payment_method" value="bank">

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-cloud-upload me-2"></i>Submit Payment Evidence
                            </button>
                        </form>
                    </div>
                </div>

                <div class="p-3 bg-light rounded">
                    <small class="text-muted">
                        ✅ <strong>After Upload:</strong> Your evidence will be reviewed by our admin team. You'll receive an email once it's approved.
                    </small>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .alert-info {
        background-color: #e7f3ff;
        border-color: #b3d9ff;
    }
    code {
        background-color: #f5f5f5;
        padding: 2px 6px;
        border-radius: 3px;
    }
</style>
@endsection

@extends('layouts.landing')

@section('title', 'Paystack Payment')

@section('content')
<div style="padding: 40px 0;">
    <div class="container" style="max-width: 600px;">

        <div class="mb-4">
            <a href="{{ route('course.payment.show', $payment) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Payment Methods
            </a>
        </div>

        <div class="card shadow-lg">
            <div class="card-body p-5">
                <h2 class="h3 fw-bold mb-2">Pay with Paystack</h2>
                <p class="text-muted mb-4">Secure payment powered by Paystack</p>

                <!-- Course Details -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Course</h6>
                        <h5 class="mb-3">{{ $payment->course->title }}</h5>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">Amount</small>
                                <h4 class="text-primary">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</h4>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Reference</small>
                                <code style="font-size: 12px;">{{ $payment->reference_id }}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form action="https://api.paystack.co/transaction/initialize" method="POST" class="paystack-form">
                    <input type="hidden" name="email" value="{{ $payment->user->email }}">
                    <input type="hidden" name="amount" value="{{ (int)($payment->amount * 100) }}">
                    <input type="hidden" name="reference" value="{{ $payment->reference_id }}">
                    <input type="hidden" name="bearer" value="account">
                    
                    <button type="button" class="btn btn-primary w-100 py-2" onclick="handlePaystackPayment()">
                        <i class="bi bi-credit-card me-2"></i>Pay {{ $payment->currency }} {{ number_format($payment->amount, 2) }} Now
                    </button>
                </form>

                <div class="mt-4 p-3 bg-light rounded">
                    <small class="text-muted">
                        🔒 <strong>Secure:</strong> Your payment is secured by Paystack, a leading payment processor in Africa.
                    </small>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
    function handlePaystackPayment() {
        const reference = '{{ $payment->reference_id }}';
        const handler = PaystackPop.setup({
            key: '{{ env("PAYSTACK_PUBLIC_KEY") }}',
            email: '{{ $payment->user->email }}',
            amount: {{ (int)($payment->amount * 100) }},
            ref: reference,
            currency: '{{ $payment->currency }}',
            onClose: function() {
                alert('Payment window closed.');
            },
            onSuccess: function(response) {
                // Verify payment on server
                fetch('{{ route("course.payment.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        reference: reference,
                        payment_id: {{ $payment->id }}
                    })
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        window.location.href = '{{ route("course.payment.success") }}?payment=' + {{ $payment->id }};
                    } else {
                        alert('Payment verification failed');
                    }
                });
            }
        });
        handler.openIframe();
    }
</script>
@endsection

@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
        <div class="card-body">
            <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title border-bottom pb-2 mb-2">
                    <a href="{{ route(auth()->user()->user_type .'.payments') }}" class="btn btn-sm btn-primary float-end">
                        ← Back to Payments
                    </a>
                     <h4 class="mb-0">Payment</h4>
                </div>
            </div>
            <div class="col-md-12">
                <ul class="breadcrumb">
                <li class="breadcrumb-item"
                    ><a href="{{ route( auth()->user()->user_type .'.dashboard') }}"><i class="ph ph-house"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Payment #{{ $payment->id }}</li>
                </ul>
            </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->

<div class="container my-4 my-md-5">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="mb-2 mb-md-0">
                <h5 class="fw-bold mb-1">Payment Details</h5>
                <small class="text-muted">Date: <x-format-time :date="$payment->created_at" format="d M Y" /></small>
            </div>
            <span class="badge bg-{{ $payment->status === 'approved' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}
               px-3 py-2 rounded-pill align-self-start align-self-md-center">
                {{ ucfirst($payment->status) }}
            </span>
        </div>

        <div class="card-body">
            

            <div class="mb-4">
                <h3 class="fw-bold text-virtual mb-0 fs-2 fs-md-1">₦{{ number_format($payment->amount, 2) ?? '-' }}</h3>
            </div>

            <div class="border-top pt-3 mt-2">
                <h6 class="fw-semibold mb-3">Payment Information</h6>

                <dl class="row mb-0 small">
                    <dt class="col-5 col-md-3 text-muted">Subscription</dt>
                    <dd class="col-7 col-md-9">{{ $payment->subscription->plan->name ?? 'N/A' }}</dd>
                    
                    <dt class="col-5 col-md-3 text-muted">Payment by</dt>
                    <dd class="col-7 col-md-9">{{ $payment->parent->name }}</dd>

                    <dt class="col-5 col-md-3 text-muted">Student</dt>
                    <dd class="col-7 col-md-9">{{ $payment->subscription->student->name }}</dd>

                    <dt class="col-5 col-md-3 text-muted">Payment method</dt>
                    <dd class="col-7 col-md-9">{{ $payment->method ?? 'N/A' }}</dd>

                    <dt class="col-5 col-md-3 text-muted">Status</dt>
                    <dd class="col-7 col-md-9">
                        <span class="badge bg-{{ $payment->status === 'approved' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </dd>
                    
                    @if ($payment->decision_reason)
                    <dt class="col-5 col-md-3 text-muted ">Reason</dt>
                    <dd class="col-7 col-md-9 p-2 bg-light rounded border d-inline-block">{{ $payment->decision_reason }}</dd>
                    @endif

                    <dt class="col-5 col-md-3 text-muted">Proof of payment</dt>
                    <dd class="col-7 col-md-9">
                        @php
                            $receipt = $payment->file_path ?? null;
                            $extension = $receipt ? strtolower(pathinfo($receipt, PATHINFO_EXTENSION)) : null;
                        @endphp

                        @if ($receipt)
                            <div class="border rounded p-2 bg-light">
                                {{-- Image Preview --}}
                                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <a href="{{ asset('storage/'. $receipt) }}" target="_blank">
                                        <img src="{{ asset('storage/'. $receipt) }}" alt="Proof of payment" class="img-fluid rounded" style="max-width: 200px;">
                                    </a>

                                {{-- PDF Preview --}}
                                @elseif ($extension === 'pdf')
                                    <iframe src="{{ asset('storage/'. $receipt) }}" width="100%" height="400" class="rounded border"></iframe>
                                    <a href="{{ asset('storage/'. $receipt) }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2">
                                        View Full PDF
                                    </a>

                                {{-- DOC or DOCX File --}}
                                @elseif (in_array($extension, ['doc', 'docx']))
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-word text-primary fs-3 me-2"></i>
                                        <div>
                                            <p class="mb-1 small">{{ basename($receipt) }}</p>
                                            <a href="{{ asset('storage/'. $receipt) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                View Document
                                            </a>
                                        </div>
                                    </div>

                                {{-- Other File Types --}}
                                @else
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-text fs-3 me-2 text-muted"></i>
                                        <div>
                                            <p class="mb-1 small">{{ basename($receipt) }}</p>
                                            <a href="{{ asset('storage/'. $receipt) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                @endif
                                <small class="text-muted">Uploaded by {{ $payment->parent->name }}</small>
                            </div>
                        @else
                            <small class="text-muted">No Proof of payment uploaded.</small>
                        @endif
                    </dd>
                </dl>
            </div>

        </div>
        @can('approve', $payment)
           <div class="card-footer">
                <form class="d-inline" action="{{ route('payments.approve', $payment) }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this payment, approving this payment will activate the subscription plan?');">
                        <i class="ph ph-check-square"></i> Approve Payment
                    </button>
                </form>

                <button type="submit" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="ph ph-x"></i> Reject Payment
                </button>
            </div>
        @endcan
    </div>
</div>


<!-- Reject Payment Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('payments.reject', $payment) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Please provide a reason:</p>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <p><strong>Whoops! Something went wrong.</strong></p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <textarea name="decision_reason" class="form-control" rows="3" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

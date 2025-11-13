@extends('layouts.app')

@section('title', 'Payments')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
            <!-- Button trigger modal -->
            <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal" data-bs-target="#createPaymentModal">
                <i class="ph ph-plus"></i> Make Payment
            </button>
            <h4 class="mb-0">Payment History</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('parent.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Payments</li>
            </ul>
        </div>
        </div>
    </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->

<!-- Students -->
<div class="card">
    <div class="card-body">
        <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Plan</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse( $payments as $pay )
                        <tr>
                            <td>{{ $pay->subscription->plan->name ?? '-' }}</td>
                            <td>₦ {{ number_format($pay->amount, 2) ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $pay->status == 'approved' ? 'success' : ($pay->status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($pay->status) }}
                                </span>
                            </td>
                            <td>{{ $pay->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('payments.show', $pay) }}" class="btn btn-sm bg-virtual text-white">
                                    <i class="ph ph-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No payments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    </div>
    <div class="mt-3">
        {{ $payments->links() }}
    </div>
</div>
        
 

<!-- Create Lesson Modal -->
<div class="modal fade" id="createPaymentModal" tabindex="-1" aria-labelledby="createPaymentLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('payment.upload') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createPaymentLabel">Make Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body row g-3">
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
            <!-- parent id -->
            <input type="hidden" name="parent" value="{{ auth()->user()->parent->id }}" > 

            <!-- Student -->
            <div class="col-md-12">
                    <label class="form-label">Subscription</label>
                    <select name="subscription_id" class="form-select" required>
                        <option value="">Select Subscription</option>
                    @foreach($subscriptions as $sub)
                        <option value="{{ $sub->id }}" {{ old('subscription_id') == $sub->id ? 'selected' : '' }}>
                            {{ $sub->student->name }} — {{ $sub->plan->name }} ({{ ucfirst($sub->status) }})  Amount: ₦{{ number_format($sub->plan->price, 2) }}
                        </option>
                    @endforeach
                    </select>
            </div>
            <!-- Subject -->
            <div class="col-md-6">
                    <label class="form-label">Amount</label>
                    <input type="text" name="amount" value="{{ old('amount') }}" class="form-control" required>
            </div>

            <!-- Start time -->
            <div class="col-md-6">
                <label class="form-label">Evidence Payment</label>
                <input type="file" name="file_path" class="form-control" required>
            </div>

        </div> <!-- modal-body -->

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Submit Payment</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection
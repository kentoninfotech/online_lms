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
            <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal" data-bs-target="#createLessonModal">
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
                        <th>Reason</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $pay)
                        <tr>
                            <td>{{ $pay->subscription->plan->name ?? '-' }}</td>
                            <td>₦ {{ number_format($pay->amount, 2) ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $pay->status == 'approved' ? 'success' : ($pay->status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($pay->status) }}
                                </span>
                            </td>
                            <td>{{ $pay->decision_reason ?? '-' }}</td>
                            <td>{{ $pay->created_at->format('d M Y') }}</td>
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
        
 


@endsection
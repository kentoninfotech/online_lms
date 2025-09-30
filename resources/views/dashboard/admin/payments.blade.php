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
            <h4 class="mb-0">Payments</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('admin.dashboard') }}"><i class="ph ph-house"></i></a>
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
        <table class="table table-striped table-bordered">
        <thead class="table-light">
            <tr>
                <th>Student</th>
                <th>Parent</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $pay)
                <tr>
                    <td>
                        <!-- <a href="{{ route('show.student', $pay->subscription->student) }}" class="text-decoration-none"> -->
                            {{ $pay->subscription->student->name ?? '-' }}
                        <!-- </a> -->
                    </td>
                    <td>
                        <!-- <a href="{{ route('show.parent', $pay->parent) }}" class="text-decoration-none"> -->
                            {{ $pay->parent->name ?? '-' }}
                        <!-- </a> -->
                    </td>
                    <td>₦ {{ number_format($pay->amount, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $pay->status === 'approved' ? 'success' : ($pay->status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($pay->status) }}
                        </span>
                    </td>
                    <td>{{ $pay->created_at->format('d M Y h:i A') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">No payments found.</td></tr>
            @endforelse
        </tbody>
    </table>
    
    
    </div>
    <div class="mt-3">
        {{ $payments->links() }}
    </div>
</div>
        
 


@endsection
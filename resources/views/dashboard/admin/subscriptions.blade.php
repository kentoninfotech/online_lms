@extends('layouts.app')

@section('title', 'Subscription')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
            <h4 class="mb-0">Subscription</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('admin.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Subscription</li>
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
                <th>Plan</th>
                <th>Status</th>
                <th>Started</th>
                <th>Ends</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscriptions as $sub)
                <tr>
                    <td>{{ $sub->student->name }}</td>
                    <td>{{ $sub->plan->name }}</td>
                    <td>
                        <span class="badge bg-{{ $sub->status === 'active' ? 'success' : ($sub->status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($sub->status) }}
                        </span>
                    </td>
                    <td>{{ $sub->start_date->format('d M Y') }}</td>
                    <td>{{ $sub->end_date?->format('d M Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">No subscriptions found.</td></tr>
            @endforelse
        </tbody>
    </table>
    
    
    
    </div>
    <div class="mt-3">
        {{ $subscriptions->links() }}
    </div>
</div>
        
 


@endsection
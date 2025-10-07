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
                <th>Action</th>
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
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                                    <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                                </svg>
                            </button>
                            <ul class="dropdown-menu text-center">
                                <li>
                                    <!-- <div class="dropdown-divider"></div> -->
                                    <form class="d-inline" action="{{ route('subscriptions.activate', $sub) }}" method="post">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-success" onclick="return confirm('Are you sure you want to activate this subscription, only activate plan with valid and verified payment?');">
                                          <i class="ph ph-check-square"></i> Activate Subscription
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form class="d-inline" action="{{ route('subscriptions.cancel', $sub) }}" method="post">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to cancel this subscription?');">
                                          <i class="ph ph-x"></i> Deactivate Subscription
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
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
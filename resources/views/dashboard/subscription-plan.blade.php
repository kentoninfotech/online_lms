@extends('layouts.app')

@section('title', 'Subscription Plan')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
        <div class="card-body">
            <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title border-bottom pb-2 mb-2">
                     <h4 class="mb-0">Subscription Plan</h4>
                </div>
            </div>
            <div class="col-md-12">
                <ul class="breadcrumb">
                <li class="breadcrumb-item"
                    ><a href="{{ route( auth()->user()->user_type .'.dashboard') }}"><i class="ph ph-house"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Subscription Plan</li>
                </ul>
            </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->

    <div class="container mt-4">
        <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
            @forelse ($plans as $plan)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 rounded-3">
                        <div class="card-header text-white text-center py-4 rounded-top bg-primary">
                            <h3 class="h2 fw-bold text-white mb-1">₦{{ number_format($plan->price, 2) }}</h3>
                            <p class="mb-0">{{ $plan->name }}</p>
                        </div>

                        <div class="card-body p-4 d-flex flex-column">
                            <ul class="list-unstyled flex-grow-1 mb-4">
                                    <li class="mb-2 d-flex align-items-start">
                                        <i class="ph ph-check"></i>
                                        Reschedule Limit: <span class="badge bg-info text-start"> {{ $plan->reschedule_limit ?? 'N/A' }}</span>
                                    </li>
                                    <li class="mb-2 d-flex align-items-start">
                                        <i class="ph ph-check"></i>
                                        Payment Grace: <span class="badge bg-info text-start">  {{ $plan->payment_grace_days ?? 'N/A' }}</span>
                                    </li>
                            </ul>

                            <div class="mt-auto pt-3">
                                <form action="{{ route('subscription.store', [$student, $plan] ) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-primary btn-lg w-100 fw-bold py-3">
                                        SELECT PLAN
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p>No Plan yet.</p>
            @endforelse
        </div>
    </div>

@endsection

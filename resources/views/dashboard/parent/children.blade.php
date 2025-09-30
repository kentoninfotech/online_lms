@extends('layouts.app')

@section('title', 'My Children')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
            <h4 class="mb-0">My Children</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('parent.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">My Children</li>
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
        <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Subscription</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($children as $child)
                <tr>
                    <td>{{ $child->name }}</td>
                    <td>{{ $child->email }}</td>
                    <td>{{ $child->subscription->plan->name ?? '—' }}</td>
                    <td>
                        <span class="badge bg-{{ $child->subscription?->status === 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($child->subscription?->status ?? 'none') }}
                        </span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                                    <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                                </svg>
                            </button>
                            <ul class="dropdown-menu text-center">
                                <li>
                                    <a class="dropdown-item" href="{{ route('show.student', $child) }}"><i class="ph ph-user"></i> View Child</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('show.student', $child) }}#lessons"><i class="ph ph-book"></i> View Lessons</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('show.student', $child) }}#attendance"><i class="ph ph-check-square"></i> View Attendance</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No children linked yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    </div>
</div>
        
 


@endsection
@extends('layouts.app')

@section('title', 'Reschedule Requests')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
            <h4 class="mb-0">Reschedule Requests</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('parent.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Reschedule Requests</li>
            </ul>
        </div>
        </div>
    </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->

<div class="card shadow-sm p-3">
    <div class="card-body">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Child</th>
                    <th>Instructor</th>
                    <th>Original Time</th>
                    <th>Proposed Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Decision Reason</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td>{{ $req->occurrence->lesson->student->name }}</td>
                        <td>{{ $req->occurrence->lesson->instructor->name }}</td>
                        <td><x-format-time :date="$req->occurrence->scheduled_start" /></td>
                        <td><x-format-time :date="$req->proposed_start" /></td>
                        <td>{{ $req->reason }}</td>
                        <td>
                            <span class="badge bg-{{ $req->status === 'approved' ? 'success' : ($req->status === 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td>{{ $req->decision_reason ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No reschedule requests yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $requests->links() }}
        </div>
    </div>
</div>

@endsection



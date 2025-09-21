@extends('layouts.app')

@section('title', 'Student/Parent Reschedule Requests')

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
                ><a href="{{ route('admin.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Student/Parent Reschedule Requests</li>
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
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Lesson</th>
                    <th>Student</th>
                    <th>Original Time</th>
                    <th>Proposed Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Requested By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td>{{ $req->occurrence->lesson->subject }}</td>
                        <td>{{ $req->occurrence->lesson->student->name }}</td>
                        <td>{{ $req->occurrence->scheduled_start->format('d M Y h:i A') }}</td>
                        <td>{{ $req->proposed_start->format('d M Y h:i A') }}</td>
                        <td>{{ $req->reason }}</td>
                        <td>
                            <span class="badge 
                                @if($req->status == 'approved') bg-success 
                                @elseif($req->status == 'pending') bg-warning 
                                @else bg-danger @endif">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td>{{ $req->requester?->name ?? 'Unknown' }}</td>
                        <td>
                            @if($req->status === 'pending')
                                <form action="{{ route('reschedule.approve', $req) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>

                                <!-- Reject button opens modal -->
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">
                                    Reject
                                </button>
                                <!-- Modal -->
                                <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('reschedule.reject', $req) }}">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reject Reschedule Request</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Please provide a reason:</p>
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
                            @else
                                <em>No actions</em>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No reschedule requests yet.</td>
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



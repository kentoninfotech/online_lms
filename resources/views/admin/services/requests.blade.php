@extends('layouts.app')

@section('content')

<style>
    .requests-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }

    .requests-header a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }

    .requests-header a:hover {
        color: white;
    }

    .requests-header h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
    }

    .table-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .table thead th {
        font-weight: 600;
        color: #333;
        padding: 1rem;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        color: #555;
    }

    .table tbody tr {
        border-bottom: 1px solid #ee2e2e;
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .status-badge {
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        display: inline-block;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .status-contacted {
        background-color: #cfe2ff;
        color: #084298;
        border: 1px solid #b6d4fe;
    }

    .status-completed {
        background-color: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
    }

    .message-view {
        color: #667eea;
        cursor: pointer;
        text-decoration: none;
        font-weight: 500;
    }

    .message-view:hover {
        text-decoration: underline;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
    }

    .empty-state i {
        font-size: 3rem;
        color: #adb5bd;
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: #6c757d;
        margin: 0;
    }

    .success-alert {
        background-color: #d1e7dd;
        border-color: #badbcc;
        color: #0f5132;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .pagination {
        display: flex;
        justify-content: flex-end;
        padding: 1rem;
        border-top: 1px solid #dee2e6;
        gap: 0.5rem;
    }

    .pagination a, .pagination span {
        padding: 0.5rem 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        text-decoration: none;
        color: #667eea;
    }

    .pagination a:hover {
        background-color: #667eea;
        color: white;
    }

    .pagination .active span {
        background-color: #667eea;
        color: white;
        border-color: #667eea;
    }
</style>

<div class="requests-header">
    <div class="container-lg">
        <a href="{{ route('admin.services.index') }}">
            <i class="bi bi-arrow-left"></i>Back to Services
        </a>
        <h1>
            <i class="bi bi-chat-dots me-2"></i>Service Requests
        </h1>
        <p class="text-white-50 mb-0">{{ $service->title }}</p>
    </div>
</div>

<div class="container-lg">
    @if (session('success'))
        <div class="success-alert">
            <i class="bi bi-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="table-wrapper">
        @if($requests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>
                                <i class="bi bi-person-circle me-2"></i>Name
                            </th>
                            <th>
                                <i class="bi bi-envelope me-2"></i>Email
                            </th>
                            <th>
                                <i class="bi bi-telephone me-2"></i>Phone
                            </th>
                            <th>
                                <i class="bi bi-tag me-2"></i>Status
                            </th>
                            <th>
                                <i class="bi bi-chat-left-text me-2"></i>Message
                            </th>
                            <th>
                                <i class="bi bi-calendar-event me-2"></i>Date Requested
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td>
                                    <strong>{{ $request->name }}</strong>
                                </td>
                                <td>
                                    <a href="mailto:{{ $request->email }}" class="text-decoration-none">
                                        {{ $request->email }}
                                    </a>
                                </td>
                                <td>
                                    @if($request->phone)
                                        <a href="tel:{{ $request->phone }}" class="text-decoration-none">
                                            {{ $request->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $request->status }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($request->message)
                                        <details class="custom-details">
                                            <summary class="message-view">
                                                <i class="bi bi-eye me-1"></i>View Message
                                            </summary>
                                            <div class="mt-2 p-2 bg-light rounded">
                                                <p class="mb-0 text-muted" style="font-size: 0.9rem; line-height: 1.5;">{{ $request->message }}</p>
                                            </div>
                                        </details>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $request->created_at->format('M d, Y') }}
                                        <br>
                                        <span style="font-size: 0.8rem;">{{ $request->created_at->format('g:i A') }}</span>
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($requests->hasPages())
                <div class="pagination">
                    {{ $requests->links('pagination::bootstrap-4') }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h5>No Service Requests</h5>
                <p>There are no service requests yet for <strong>{{ $service->title }}</strong>.</p>
            </div>
        @endif
    </div>
</div>

@endsection

@extends('layouts.app')

@section('title', 'Feedback & Messages')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">
                    <i class="bi bi-chat-dots me-2"></i>Feedback & Contact Messages
                </h3>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary bg-opacity-10 border-0">
                <div class="card-body">
                    <h6 class="card-title text-primary">Total Messages</h6>
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning bg-opacity-10 border-0">
                <div class="card-body">
                    <h6 class="card-title text-warning">Unread</h6>
                    <h3 class="mb-0">{{ $stats['unread'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info bg-opacity-10 border-0">
                <div class="card-body">
                    <h6 class="card-title text-info">Read</h6>
                    <h3 class="mb-0">{{ $stats['read'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success bg-opacity-10 border-0">
                <div class="card-body">
                    <h6 class="card-title text-success">Replied</h6>
                    <h3 class="mb-0">{{ $stats['replied'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-3">
        <div class="btn-group" role="group">
            <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline-primary {{ !$status ? 'active' : '' }}">
                All Messages
            </a>
            <a href="{{ route('admin.feedback.index', ['status' => 'unread']) }}" class="btn btn-outline-warning {{ $status === 'unread' ? 'active' : '' }}">
                Unread <span class="badge bg-warning ms-2">{{ $stats['unread'] }}</span>
            </a>
            <a href="{{ route('admin.feedback.index', ['status' => 'read']) }}" class="btn btn-outline-info {{ $status === 'read' ? 'active' : '' }}">
                Read
            </a>
            <a href="{{ route('admin.feedback.index', ['status' => 'replied']) }}" class="btn btn-outline-success {{ $status === 'replied' ? 'active' : '' }}">
                Replied
            </a>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">Status</th>
                        <th>Name & Email</th>
                        <th>Subject</th>
                        <th>Message Preview</th>
                        <th>Received</th>
                        <th style="width: 120px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $message)
                    <tr class="{{ $message->status === 'unread' ? 'table-light' : '' }}">
                        <td>
                            @if($message->status === 'unread')
                                <span class="badge bg-warning">
                                    <i class="bi bi-exclamation-circle me-1"></i>New
                                </span>
                            @elseif($message->status === 'replied')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Replied
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-eye me-1"></i>Read
                                </span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <strong>{{ $message->name }}</strong><br>
                                <small class="text-muted">{{ $message->email }}</small>
                                @if($message->phone)
                                    <br><small class="text-muted">{{ $message->phone }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="text-truncate d-block" style="max-width: 200px;">
                                {{ $message->subject ?? '(No subject)' }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted text-truncate d-block" style="max-width: 300px;">
                                {{ Str::limit($message->message, 100) }}
                            </small>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ $message->created_at->format('M d, Y') }}
                            </small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.feedback.show', $message) }}" class="btn btn-sm btn-primary" title="View & Reply">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('admin.feedback.destroy', $message) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this message?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3">No messages found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($messages->hasPages())
    <div class="mt-4">
        {{ $messages->links() }}
    </div>
    @endif
</div>
@endsection

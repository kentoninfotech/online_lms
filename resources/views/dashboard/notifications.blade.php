@extends('layouts.app')

@section('title', 'My Notification')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
        <div class="card-body">
            <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title border-bottom pb-2 mb-2">
                     <h4 class="mb-0">My Notification</h4>
                </div>
            </div>
            <div class="col-md-12">
                <ul class="breadcrumb">
                <li class="breadcrumb-item"
                    ><a href="{{ route( auth()->user()->user_type .'.dashboard') }}"><i class="ph ph-house"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">My Notification</li>
                </ul>
            </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->

    <div class="card mt-3 shadow-sm">
        <div class="mt-3 float-end">
            <form action="{{ route('notifications.read.all') }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-primary">Mark All as Read</button>
            </form>
        </div>
        @forelse($grouped as $category => $notes)
                <div class="card-header d-flex align-items-center">
                    @if($category === 'Payments')
                        <span class="me-2">💰</span>
                    @elseif($category === 'Reschedules')
                        <span class="me-2">📅</span>
                    @elseif($category === 'Classes')
                        <span class="me-2">🎓</span>
                    @else
                        <span class="me-2">🔔</span>
                    @endif
                    <strong>{{ $category }}</strong>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($notes as $note)
                        <li class="list-group-item d-flex justify-content-between align-items-center 
                            {{ $note->read_at ? '' : 'bg-light' }}">
                            
                            <div>
                                <div>{{ $note->data['message'] ?? $note->type }}</div>
                                <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
                            </div>

                            <div>
                                @if(!$note->read_at)
                                    <form action="{{ route('notifications.read', $note->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success">Mark Read</button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            
        @empty
            <p>No notifications yet.</p>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>

@endsection

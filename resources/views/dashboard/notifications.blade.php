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
                                {{-- Display Title/Header (use title if available, otherwise fallback) --}}
                                <strong class="{{ $category === 'Payments' ? 'text-danger' : 'text-primary' }}">
                                    {{ $note->data['title'] ?? 'Notification' }}
                                </strong>
                                
                                {{-- Optional: Greeting line (can be customized per notification if needed) --}}
                                <p class="mb-1 mt-1 text-muted">Hello,</p>
                                
                                {{-- Display Message Lines (supports all notification types with a standard structure) --}}
                                @if (isset($note->data['message_lines']))
                                    {{-- Render the structured lines for mail-like presentation --}}
                                    @foreach ($note->data['message_lines'] as $line)
                                        <p class="mb-1 text-dark">{{ $line }}</p>
                                    @endforeach
                                @else
                                    {{-- Fallback for simple/legacy notifications that just have a 'message' key --}}
                                    <p class="mb-1 text-dark">{{ $note->data['message'] ?? 'Check your account for details.' }}</p>
                                @endif

                                {{-- Display Action Button (Resolving the Route) --}}
                                @if (isset($note->data['action']['route']['name']))
                                    @php
                                        // Safely resolve the URL using the stored route name and parameters
                                        $routeName = $note->data['action']['route']['name'];
                                        $routeParams = $note->data['action']['route']['params'] ?? [];
                                        $actionUrl = route($routeName, $routeParams);
                                    @endphp
                                    <a href="{{ $actionUrl }}" class="btn btn-sm btn-outline-primary mt-2">
                                        {{ $note->data['action']['text'] ?? 'View Details' }}
                                    </a>
                                @endif

                                <small class="text-muted d-block mt-1">{{ $note->created_at->diffForHumans() }}</small>
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

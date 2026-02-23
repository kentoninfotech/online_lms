@extends('layouts.landing')

@section('title', 'Announcement Details')

@section('content')
<div style="padding: 40px 0;">
    <div class="container">

        <div class="mb-4">
            <a href="{{ route('course.announcement.history', $message->course) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to History
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ $message->subject }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <small class="text-muted d-block">From</small>
                            <p class="mb-0"><strong>{{ $message->sender->name }}</strong></p>
                        </div>
                        <div class="mb-4">
                            <small class="text-muted d-block">Course</small>
                            <p class="mb-0"><strong>{{ $message->course->title }}</strong></p>
                        </div>
                        <div class="border-top pt-4">
                            <div class="message-content">
                                {!! nl2br(e($message->message)) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Methods -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Delivery Methods</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($message->methods as $method)
                                <div class="col-md-6 mb-3">
                                    @if($method === 'email')
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-envelope rounded-circle p-2" style="background: #e7f3ff; color: #007bff;"></i>
                                            <div class="ms-2">
                                                <small class="text-muted d-block">Email</small>
                                                <strong>{{ $message->total_recipients }} recipients</strong>
                                            </div>
                                        </div>
                                    @elseif($method === 'sms')
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-chat-dots rounded-circle p-2" style="background: #fff3e0; color: #ff9800;"></i>
                                            <div class="ms-2">
                                                <small class="text-muted d-block">SMS</small>
                                                <strong>{{ $message->total_recipients }} recipients</strong>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Recipients Status -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Recipient Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h4 class="text-success">{{ $sentCount }}</h4>
                                <small class="text-muted">Delivered</small>
                            </div>
                            <div class="col-md-4">
                                <h4 class="text-warning">{{ $message->total_recipients - $sentCount - $failedCount }}</h4>
                                <small class="text-muted">Pending</small>
                            </div>
                            <div class="col-md-4">
                                <h4 class="text-danger">{{ $failedCount }}</h4>
                                <small class="text-muted">Failed</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Status Card -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Status</small>
                            @if($message->status === 'sent')
                                <span class="badge bg-success">Sent</span>
                            @elseif($message->status === 'scheduled')
                                <span class="badge bg-warning">Scheduled</span>
                            @else
                                <span class="badge bg-secondary">Draft</span>
                            @endif
                        </div>
                        <hr>
                        <div class="mb-3">
                            <small class="text-muted d-block">Total Recipients</small>
                            <h6>{{ $message->total_recipients }}</h6>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Sent</small>
                            <h6>{{ $sentCount }}</h6>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Failed</small>
                            <h6>{{ $failedCount }}</h6>
                        </div>
                        <hr>
                        <small class="text-muted d-block">Created</small>
                        <p class="mb-2">{{ $message->created_at->format('M d, Y H:i A') }}</p>
                        @if($message->scheduled_at)
                            <small class="text-muted d-block">Scheduled For</small>
                            <p>{{ $message->scheduled_at->format('M d, Y H:i A') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

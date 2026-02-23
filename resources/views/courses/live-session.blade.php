@extends('layouts.app')

@section('title', 'Live Session - ' . $course->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Main Session Area -->
        <div class="col-lg-8">
            <!-- Session Header -->
            <div class="card shadow-lg mb-4">
                <div class="card-header bg-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">{{ $session->title }}</h3>
                            <small>
                                @if($session->isLive())
                                    <span class="badge bg-danger">
                                        <i class="bi bi-circle-fill me-1"></i>LIVE NOW
                                    </span>
                                @elseif($session->scheduled_start && $session->scheduled_start->isFuture())
                                    <span class="badge bg-info">
                                        {{ $session->scheduled_start->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Completed</span>
                                @endif
                            </small>
                        </div>
                        <div class="text-white text-end">
                            <div class="fs-5">Online: {{ $session->attendances()->where('attendance_status', 'present')->count() }}</div>
                            <small>Total Participants</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jitsi Video Conference -->
            @if($session->jitsi_room_name)
                <div class="card shadow-lg mb-4">
                    <div class="card-body p-0">
                        <div id="meet" style="height: 500px; width: 100%;">
                            <!-- Jitsi will load here -->
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Session Not Ready:</strong> The facilitator has not configured this session yet. Please check back later.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Session Description -->
            @if($session->description)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">About This Session</h5>
                    </div>
                    <div class="card-body">
                        {!! $session->description !!}
                    </div>
                </div>
            @endif

            <!-- Facilitator Info -->
            @if($session->facilitator)
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Facilitator</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <span class="text-white fs-5">{{ substr($session->facilitator->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $session->facilitator->name }}</h6>
                                <small class="text-muted">{{ $session->facilitator->email }}</small>
                                <div class="mt-2">
                                    <span class="badge bg-success">
                                        <i class="bi bi-circle-fill me-1"></i>Online
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Session Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Session Info
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-6">Course:</dt>
                        <dd class="col-6">
                            <a href="{{ route('courses.show', $course) }}" class="text-decoration-none">
                                {{ Str::limit($course->title, 25) }}
                            </a>
                        </dd>

                        @if($session->scheduled_start)
                            <dt class="col-6">Scheduled:</dt>
                            <dd class="col-6">
                                <i class="bi bi-calendar me-1"></i>{{ $session->scheduled_start->format('M d, Y') }}
                                <br>
                                <small class="text-muted">{{ $session->scheduled_start->format('H:i') }}</small>
                            </dd>
                        @endif

                        @if($session->duration_minutes)
                            <dt class="col-6">Duration:</dt>
                            <dd class="col-6">
                                <i class="bi bi-clock me-1"></i>{{ $session->duration_minutes }} minutes
                            </dd>
                        @endif

                        <dt class="col-6">Type:</dt>
                        <dd class="col-6">
                            @if($session->is_compulsory)
                                <span class="badge bg-danger">Compulsory</span>
                            @else
                                <span class="badge bg-secondary">Optional</span>
                            @endif
                        </dd>

                        @if($session->max_points > 0)
                            <dt class="col-6">Points:</dt>
                            <dd class="col-6">
                                <i class="bi bi-star-fill text-warning me-1"></i>{{ $session->max_points }} pts
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Online Participants -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i>Online Participants
                        <span class="badge bg-light text-success float-end">
                            {{ $attendees->where('attendance_status', 'present')->count() }}
                        </span>
                    </h5>
                </div>
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @forelse($attendees->where('attendance_status', 'present') as $attendee)
                        <div class="list-group-item d-flex align-items-center gap-2">
                            <span class="badge bg-success rounded-circle" style="width: 8px; height: 8px;"></span>
                            <div style="flex: 1;">
                                <small class="fw-bold d-block">{{ $attendee->user->name ?? 'Unknown' }}</small>
                                <small class="text-muted">Online now</small>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-muted text-center py-3">
                            <small>Waiting for participants...</small>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Chat Section (if enabled) -->
            @if($session->chat_enabled)
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-dots me-2"></i>Session Chat
                        </h5>
                    </div>
                    <div class="card-body" style="height: 300px; overflow-y: auto;" id="chatMessages">
                        <div class="text-muted text-center py-4">
                            <small>Chat messages will appear here</small>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <form id="chatForm" class="d-flex gap-2">
                            <input type="text" id="chatInput" class="form-control form-control-sm" placeholder="Type a message..." autocomplete="off">
                            <button type="submit" class="btn btn-sm btn-info">
                                <i class="bi bi-send"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Jitsi Meet Script -->
@if($session->jitsi_room_name)
    <script src='https://meet.jitsi/external_api.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var domain = "{{ config('services.jitsi.domain', 'meet.jitsi') }}";
            var options = {
                roomName: "{{ $session->jitsi_room_name }}",
                width: '100%',
                height: 500,
                parentNode: document.querySelector('#meet'),
                userInfo: {
                    displayName: "{{ Auth::user()->name }}"
                },
                configOverwrite: {
                    startWithAudioMuted: true,
                    startWithVideoMuted: false,
                    disableSimulcast: false,
                    enableLipSync: true,
                    disableAudioLevels: false,
                    constraints: {
                        video: {
                            height: {
                                ideal: 720,
                                max: 720,
                                min: 240
                            }
                        }
                    }
                },
                interfaceConfigOverwrite: {
                    TOOLBAR_BUTTONS: [
                        'microphone',
                        'camera',
                        'videoquality',
                        'fullscreen',
                        'fodeveryoneelsemute',
                        'stats',
                        'profile',
                        'settings',
                        'raisehand',
                        'help'
                    ],
                    DEFAULT_REMOTE_DISPLAY_NAME: 'Fellow Participant',
                    DEFAULT_LOCAL_DISPLAY_NAME: 'Me',
                    SHOW_JITSI_WATERMARK: true,
                }
            };

            var api = new JitsiMeetExternalAPI(domain, options);

            // Listen for display name changes
            api.addEventListener('displayNameChange', function(event) {
                console.log('Display name changed:', event.displayname);
            });

            // Track when user joins
            api.addEventListener('videoConferenceJoined', function() {
                console.log('User joined the conference');
                updateOnlineCount();
            });

            // Track when user leaves
            api.addEventListener('videoConferenceLeft', function() {
                console.log('User left the conference');
            });
        });

        function updateOnlineCount() {
            // This would be called via AJAX to update participant count
            // Implementation depends on your backend
        }
    </script>
@endif

@endsection

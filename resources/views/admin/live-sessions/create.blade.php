@extends('layouts.app')

@section('title', 'Schedule Live Session')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h3 class="page-title mb-0">
                <i class="bi bi-calendar-event me-2"></i>Schedule Live Session
            </h3>
            <p class="text-muted mt-1">{{ $course->title }}</p>
        </div>
        <div class="col-auto">
            @php
                $backRoute = auth()->user()->user_type === 'instructor'
                    ? route('tutor.courses.show', $course)
                    : route('admin.courses.show', $course);
            @endphp
            <a href="{{ $backRoute }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Course
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            @php
                $storeRoute = auth()->user()->user_type === 'instructor'
                    ? route('tutor.live-sessions.store', $course)
                    : route('admin.live-sessions.store', $course);
            @endphp
            <form action="{{ $storeRoute }}" method="POST" class="card shadow-sm">
                @csrf

                <!-- Basic Information -->
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Session Title *</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="facilitator_id" class="form-label">Facilitator *</label>
                        <select class="form-select @error('facilitator_id') is-invalid @enderror" 
                                id="facilitator_id" name="facilitator_id" required>
                            <option value="">-- Select Facilitator --</option>
                            @foreach($facilitators as $facilitator)
                                <option value="{{ $facilitator->id }}" {{ old('facilitator_id') == $facilitator->id ? 'selected' : '' }}>
                                    {{ $facilitator->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('facilitator_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Schedule -->
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="scheduled_start" class="form-label">Start Date & Time *</label>
                            <input type="datetime-local" class="form-control @error('scheduled_start') is-invalid @enderror" 
                                   id="scheduled_start" name="scheduled_start" value="{{ old('scheduled_start') }}" required>
                            @error('scheduled_start')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="scheduled_end" class="form-label">End Date & Time *</label>
                            <input type="datetime-local" class="form-control @error('scheduled_end') is-invalid @enderror" 
                                   id="scheduled_end" name="scheduled_end" value="{{ old('scheduled_end') }}" required>
                            @error('scheduled_end')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                        <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" 
                               id="duration_minutes" name="duration_minutes" min="15" max="480" 
                               value="{{ old('duration_minutes') }}" placeholder="e.g., 60">
                        <small class="text-muted">Auto-calculated from start/end time if not specified</small>
                        @error('duration_minutes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Session Settings -->
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Session Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_compulsory" 
                                       name="is_compulsory" value="1" {{ old('is_compulsory') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_compulsory">
                                    <i class="bi bi-exclamation-circle me-1"></i>Make Compulsory
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2">Students must attend this session</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="chat_enabled" 
                                       name="chat_enabled" value="1" {{ old('chat_enabled', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="chat_enabled">
                                    <i class="bi bi-chat-dots me-1"></i>Enable Chat
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2">Allow students to chat during session</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="max_points" class="form-label">Maximum Points/Score</label>
                        <input type="number" class="form-control @error('max_points') is-invalid @enderror" 
                               id="max_points" name="max_points" min="0" max="100" 
                               value="{{ old('max_points', 0) }}" placeholder="e.g., 10">
                        <small class="text-muted">Points awarded for attending this session (0 = no points)</small>
                        @error('max_points')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Jitsi Configuration -->
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-camera-video me-2"></i>Jitsi Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> This platform uses Jitsi Meet for video conferencing. 
                        You can use the cloud version (meet.jitsi) or your own self-hosted Jitsi server.
                    </div>

                    <div class="mb-3">
                        <label for="session_type" class="form-label">Session Type *</label>
                        <select class="form-select @error('session_type') is-invalid @enderror" 
                                id="session_type" name="session_type" required>
                            <option value="">-- Select Type --</option>
                            <option value="jitsi" {{ old('session_type') == 'jitsi' ? 'selected' : '' }}>
                                Jitsi Meet (Recommended)
                            </option>
                            <option value="zoom" {{ old('session_type') == 'zoom' ? 'selected' : '' }}>
                                Zoom
                            </option>
                            <option value="meet" {{ old('session_type') == 'meet' ? 'selected' : '' }}>
                                Google Meet
                            </option>
                            <option value="teams" {{ old('session_type') == 'teams' ? 'selected' : '' }}>
                                Microsoft Teams
                            </option>
                            <option value="other" {{ old('session_type') == 'other' ? 'selected' : '' }}>
                                Other
                            </option>
                        </select>
                        @error('session_type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jitsi_room_name" class="form-label">Jitsi Room Name</label>
                        <input type="text" class="form-control @error('jitsi_room_name') is-invalid @enderror" 
                               id="jitsi_room_name" name="jitsi_room_name" value="{{ old('jitsi_room_name') }}" 
                               placeholder="Leave empty for auto-generation">
                        <small class="text-muted">
                            Room identifier for Jitsi. Use only alphanumeric characters, hyphens, and underscores. 
                            Leave empty to auto-generate.
                        </small>
                        @error('jitsi_room_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="meeting_link" class="form-label">External Meeting Link (Optional)</label>
                        <input type="url" class="form-control @error('meeting_link') is-invalid @enderror" 
                               id="meeting_link" name="meeting_link" value="{{ old('meeting_link') }}" 
                               placeholder="https://...">
                        <small class="text-muted">If using external service like Zoom or Teams, paste the link here</small>
                        @error('meeting_link')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="card-footer bg-light">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Schedule Session
                    </button>
                    <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-secondary">
                        <i class="bi bi-x me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Help Panel -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>Help
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-2">Session Types</h6>
                    <div class="mb-3">
                        <small>
                            <strong>Jitsi Meet:</strong> Open-source, free, no registration required<br>
                            <strong>Zoom:</strong> Professional, scalable, requires account<br>
                            <strong>Google Meet:</strong> Google Workspace integrated<br>
                            <strong>Teams:</strong> Microsoft Outlook integrated
                        </small>
                    </div>

                    <hr>

                    <h6 class="mb-2">Best Practices</h6>
                    <ul class="small mb-0">
                        <li>Schedule sessions at least 24 hours in advance</li>
                        <li>Set reasonable duration (15 mins to 8 hours)</li>
                        <li>Test your video/audio before going live</li>
                        <li>Send reminders to students before the session</li>
                        <li>Enable chat for Q&A and engagement</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lightbulb me-2"></i>Jitsi Advantage
                    </h5>
                </div>
                <div class="card-body small">
                    <ul class="mb-0">
                        <li>✓ No installation needed</li>
                        <li>✓ End-to-end encrypted</li>
                        <li>✓ Support up to 100+ participants</li>
                        <li>✓ Screen sharing & recording</li>
                        <li>✓ Chat and emojis</li>
                        <li>✓ Virtual backgrounds</li>
                        <li>✓ Self-hosting available</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-calculate duration when dates change
    document.getElementById('scheduled_start').addEventListener('change', calculateDuration);
    document.getElementById('scheduled_end').addEventListener('change', calculateDuration);

    function calculateDuration() {
        const start = document.getElementById('scheduled_start').value;
        const end = document.getElementById('scheduled_end').value;
        
        if (start && end) {
            const startTime = new Date(start);
            const endTime = new Date(end);
            const duration = Math.round((endTime - startTime) / (1000 * 60));
            
            if (duration > 0) {
                document.getElementById('duration_minutes').value = duration;
            }
        }
    }

    // Auto-generate jitsi room name
    document.getElementById('title').addEventListener('change', function() {
        const jitsiInput = document.getElementById('jitsi_room_name');
        if (!jitsiInput.value) {
            // Only auto-generate if empty
            const roomName = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]/g, '-')
                .replace(/-+/g, '-')
                .substring(0, 40);
            jitsiInput.placeholder = roomName || 'auto-generated-' + Date.now();
        }
    });
</script>

@endsection

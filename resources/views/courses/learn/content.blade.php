@extends('layouts.app')

@section('title', 'Learning - ' . $course->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Course Content</h5>
                </div>
                <div class="list-group list-group-flush">
                    @if($courseContents && $courseContents->count() > 0)
                        @foreach($courseContents as $c)
                            <a href="{{ route('courses.learn.content', [$course, $c]) }}" 
                               class="list-group-item list-group-item-action {{ $c->id === $content->id ? 'active' : '' }}">
                                <div class="d-flex align-items-center">
                                    <i class="bi {{ $c->content_type === 'video' ? 'bi-play-circle' : 'bi-file-text' }} me-2"></i>
                                    <div class="flex-grow-1">
                                        <small>{{ Str::limit($c->title, 40) }}</small>
                                    </div>
                                    @if($c->is_published)
                                        <i class="bi bi-check-circle text-success"></i>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Progress</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 30px;">
                        @php
                            $progress = $enrollment->progress_percentage ?? 0;
                        @endphp
                        <div class="progress-bar bg-{{ $progress >= 75 ? 'success' : ($progress >= 50 ? 'info' : ($progress >= 25 ? 'warning' : 'danger')) }}" 
                             role="progressbar" 
                             style="width: {{ $progress }}%" 
                             aria-valuenow="{{ $progress }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ $progress }}%
                        </div>
                    </div>
                    <small class="text-muted">Keep learning to progress through the course!</small>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ $content->title }}</h4>
                        @if($completion && $completion->completed_at)
                            <span class="badge bg-success">Completed</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($content->content_type === 'video' && $content->content_url)
                        <div class="mb-4">
                            <div class="ratio ratio-16x9">
                                <iframe src="{{ $content->content_url }}" title="{{ $content->title }}" allowfullscreen></iframe>
                            </div>
                        </div>
                    @endif

                    <div class="content-body">
                        {!! $content->description !!}
                    </div>

                    @if($content->content_type === 'document' && $content->content_url)
                        <div class="mt-4">
                            <a href="{{ $content->content_url }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-download me-2"></i>Download Document
                            </a>
                        </div>
                    @endif

                    @if($content->content_type === 'link' && $content->content_url)
                        <div class="mt-4">
                            <a href="{{ $content->content_url }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-arrow-up-right me-2"></i>Open Link
                            </a>
                        </div>
                    @endif

                    @if(!($completion && $completion->completed_at))
                        <div class="mt-4">
                            @php
                                $minReadingTime = $content->min_reading_time_minutes ?? 0;
                            @endphp

                            @if($minReadingTime > 0)
                                <!-- Minimum Time Warning -->
                                <div class="alert alert-info mb-3" id="minTimeAlert" role="alert">
                                    <i class="bi bi-clock-history me-2"></i>
                                    <strong>Minimum Reading/Viewing Time Required:</strong>
                                    <span id="requiredTimeDisplay">{{ $minReadingTime }} minute(s)</span>
                                    <br>
                                    <small>You must spend at least {{ $minReadingTime }} minute(s) reviewing this content before you can mark it as complete.</small>
                                    <div class="mt-2">
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                 id="minTimeProgress" 
                                                 role="progressbar" 
                                                 style="width: 0%;" 
                                                 aria-valuenow="0" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="{{ $minReadingTime }}">
                                                <span id="timeDisplay">0 / {{ $minReadingTime }} min</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden input to send time spent -->
                                <input type="hidden" id="timeSpentInput" name="time_spent_minutes" value="0">

                                <!-- Mark Complete Form -->
                                <form action="{{ route('courses.learn.content.complete', [$course, $content]) }}" 
                                      method="POST" 
                                      id="markCompleteForm">
                                    @csrf
                                    <button type="button" 
                                            id="markCompleteBtn" 
                                            class="btn btn-success btn-lg" 
                                            disabled
                                            onclick="submitCompleteForm()">
                                        <i class="bi bi-check-circle me-2"></i>Mark as Complete
                                    </button>
                                    <button type="button" 
                                            id="disabledMessageBtn" 
                                            class="btn btn-secondary btn-lg ms-2" 
                                            disabled
                                            style="display: none;">
                                        <i class="bi bi-hourglass me-2"></i>Please wait...
                                    </button>
                                </form>
                            @else
                                <!-- No minimum time requirement -->
                                <form action="{{ route('courses.learn.content.complete', [$course, $content]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="time_spent_minutes" value="0">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-check-circle me-2"></i>Mark as Complete
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .content-body {
        line-height: 1.8;
        font-size: 1.1rem;
    }

    .content-body h3 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        border-bottom: 2px solid #007bff;
        padding-bottom: 0.5rem;
    }

    .content-body img {
        max-width: 100%;
        height: auto;
        margin: 1rem 0;
    }

    #minTimeProgress {
        font-weight: bold;
        font-size: 0.9rem;
    }
</style>

<script>
    // Track time spent on the page
    let startTime = Date.now();
    let minReadingTime = {{ $minReadingTime ?? 0 }};
    let contentId = {{ $content->id }};
    let isFocused = true;

    // Track page visibility
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            isFocused = false;
        } else {
            isFocused = true;
        }
    });

    // Track focus changes
    window.addEventListener('focus', function() {
        isFocused = true;
    });

    window.addEventListener('blur', function() {
        isFocused = false;
    });

    // Update time tracking every second
    if (minReadingTime > 0) {
        setInterval(function() {
            if (isFocused) {
                let elapsedTime = Math.floor((Date.now() - startTime) / 1000 / 60); // Convert to minutes
                let timeSpent = Math.min(elapsedTime, minReadingTime + 5); // Cap at 5 minutes over
                
                // Update hidden input
                document.getElementById('timeSpentInput').value = elapsedTime;
                
                // Update display
                document.getElementById('timeDisplay').textContent = elapsedTime + ' / ' + minReadingTime + ' min';
                
                // Update progress bar
                let progressPercent = Math.min((elapsedTime / minReadingTime) * 100, 100);
                let progressBar = document.getElementById('minTimeProgress');
                progressBar.style.width = progressPercent + '%';
                progressBar.setAttribute('aria-valuenow', elapsedTime);
                
                // Enable button when minimum time is reached
                if (elapsedTime >= minReadingTime) {
                    document.getElementById('markCompleteBtn').disabled = false;
                    document.getElementById('minTimeAlert').classList.remove('alert-info');
                    document.getElementById('minTimeAlert').classList.add('alert-success');
                    document.getElementById('minTimeAlert').innerHTML = `
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Great! You've met the minimum viewing time.</strong>
                        <br>
                        <small>You can now mark this content as complete.</small>
                    `;
                }
            }
        }, 1000);
    }

    // Submit form with time spent
    function submitCompleteForm() {
        let timeSpent = parseInt(document.getElementById('timeSpentInput').value) || 0;
        
        if (minReadingTime > 0 && timeSpent < minReadingTime) {
            alert('You must spend at least ' + minReadingTime + ' minute(s) on this content. Please wait.');
            return;
        }

        // Disable button and show loading state
        let btn = document.getElementById('markCompleteBtn');
        let disabledBtn = document.getElementById('disabledMessageBtn');
        btn.style.display = 'none';
        disabledBtn.style.display = 'inline-block';

        // Submit the form via AJAX
        let form = document.getElementById('markCompleteForm');
        let formData = new FormData(form);
        formData.append('time_spent_minutes', timeSpent);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert(data.message);
                // Reload the page to show completion status
                window.location.reload();
            } else {
                // Show error message
                alert(data.message || 'Error marking content as complete');
                // Re-enable button
                btn.style.display = 'inline-block';
                disabledBtn.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            // Re-enable button
            btn.style.display = 'inline-block';
            disabledBtn.style.display = 'none';
        });
    }
</script>
@endsection

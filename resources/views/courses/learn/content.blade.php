@extends('layouts.app')

@section('title', 'Learning - ' . $course->title . ' - ' . $content->title)

@section('content')
<style>
    .content-container {
        display: flex;
        gap: 2rem;
        position: relative;
    }
    
    .sidebar-fixed {
        position: fixed;
        top: 150px;
        right: 20px;
        width: 320px;
        max-height: 80vh;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    
    .main-content {
        flex: 1;
        width: 100%;
        margin-right: 0;
    }
    
    @media (max-width: 991.98px) {
        .content-container {
            flex-direction: column;
        }
        
        .sidebar-fixed {
            position: static;
            width: 100%;
            max-height: none;
            box-shadow: none;
            right: auto;
        }
        
        .main-content {
            margin-right: 0;
        }
    }
</style>

<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $content->title }}</li>
        </ol>
    </nav>

    <!-- Sidebar Toggle Button (visible on all sizes) -->
    <div class="mb-3">
        <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#courseContentSidebar" aria-expanded="false" aria-controls="courseContentSidebar">
            <i class="bi bi-list me-2"></i><span id="toggleText">View Course Content</span>
        </button>
    </div>

    <!-- Content Wrapper -->
    <div class="content-container">
        <!-- Main Content Section (Left side) -->
        <div class="main-content">
            <div class="card shadow-sm mb-4">
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

                    <!-- Main Content Section -->
                    @if($content->content_type === 'text' && $content->content)
                        <div class="mb-4">
                            <h5 class="mb-3">Content & Files</h5>
                            <div class="content-body">
                                {!! $content->content !!}
                            </div>
                        </div>
                    @elseif($content->content_type === 'html' || ($content->content_type === 'text' && !$content->content))
                        <div class="mb-4">
                            <h5 class="mb-3">Content & Files</h5>
                            <div class="content-body">
                                {!! $content->content !!}
                            </div>
                        </div>
                    @endif

                    <!-- Description (if not shown above) -->
                    @if(!empty($content->description) && !($content->content_type === 'text' && $content->content))
                        <div class="content-description">
                            {!! $content->description !!}
                        </div>
                    @endif

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
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-check-circle me-2"></i>Mark as Complete
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Resources Section -->
            @if($content->publishedResources()->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-paperclip me-2"></i>Course Resources
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Resource</th>
                                        <th class="text-end">Size</th>
                                        <th class="text-end">Downloads</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($content->publishedResources()->get() as $resource)
                                        <tr>
                                            <td>
                                                <div>
                                                    <i class="bi {{ $resource->getIconClass() }} me-2"></i>
                                                    <strong>{{ $resource->title }}</strong>
                                                    @if($resource->description)
                                                        <br>
                                                        <small class="text-muted">{{ $resource->description }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <small class="text-muted">{{ $resource->getHumanReadableSize() }}</small>
                                            </td>
                                            <td class="text-end">
                                                <small class="text-muted">{{ $resource->download_count }}</small>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('course-content-resources.download', $resource) }}" class="btn btn-sm btn-primary" title="Download {{ $resource->title }}">
                                                    <i class="bi bi-download me-1"></i>Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Collapsible Sidebar (Right side - Floats on top when expanded) -->
        <div class="collapse" id="courseContentSidebar" style="width: 100%;">
            <div class="card shadow-sm mb-4" id="sidebarContent">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Course Content</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-toggle="collapse" data-bs-target="#courseContentSidebar" aria-label="Close"></button>
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
    </div>
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
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-check-circle me-2"></i>Mark as Complete
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Resources Section -->
            @if($content->publishedResources()->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-paperclip me-2"></i>Course Resources
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Resource</th>
                                        <th class="text-end">Size</th>
                                        <th class="text-end">Downloads</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($content->publishedResources()->get() as $resource)
                                        <tr>
                                            <td>
                                                <div>
                                                    <i class="bi {{ $resource->getIconClass() }} me-2"></i>
                                                    <strong>{{ $resource->title }}</strong>
                                                    @if($resource->description)
                                                        <br>
                                                        <small class="text-muted">{{ $resource->description }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <small class="text-muted">{{ $resource->getHumanReadableSize() }}</small>
                                            </td>
                                            <td class="text-end">
                                                <small class="text-muted">{{ $resource->download_count }}</small>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('course-content-resources.download', $resource) }}" class="btn btn-sm btn-primary" title="Download {{ $resource->title }}">
                                                    <i class="bi bi-download me-1"></i>Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const minTimeMinutes = {{ $minReadingTime ?? 0 }};
    let timeSpentSeconds = 0;

    if (minTimeMinutes > 0) {
        const markCompleteBtn = document.getElementById('markCompleteBtn');
        const timeDisplay = document.getElementById('timeDisplay');
        const timeSpentInput = document.getElementById('timeSpentInput');
        const minTimeProgress = document.getElementById('minTimeProgress');

        // Update timer every second
        const timer = setInterval(function() {
            timeSpentSeconds++;
            const timeSpentMinutes = Math.floor(timeSpentSeconds / 60);

            // Update progress bar
            const percentage = Math.min((timeSpentMinutes / minTimeMinutes) * 100, 100);
            minTimeProgress.style.width = percentage + '%';
            minTimeProgress.setAttribute('aria-valuenow', timeSpentMinutes);

            // Update time display
            timeDisplay.textContent = timeSpentMinutes + ' / ' + minTimeMinutes + ' min';

            // Enable button if minimum time reached
            if (timeSpentMinutes >= minTimeMinutes) {
                markCompleteBtn.disabled = false;
                clearInterval(timer);
            }

            // Update hidden input
            timeSpentInput.value = timeSpentMinutes;
        }, 1000);
    }

    // Handle sidebar toggle button text and positioning
    const sidebarToggle = document.getElementById('courseContentSidebar');
    const toggleBtn = document.querySelector('[data-bs-toggle="collapse"][data-bs-target="#courseContentSidebar"]');
    const toggleText = document.getElementById('toggleText');
    const sidebarContent = document.getElementById('sidebarContent');

    if (sidebarToggle && toggleBtn && toggleText) {
        // Update text on show
        sidebarToggle.addEventListener('show.bs.collapse', function() {
            toggleText.textContent = 'Hide Course Content';
            
            // Apply fixed positioning on larger screens
            if (window.innerWidth >= 992) {
                if (sidebarContent) {
                    sidebarContent.classList.add('sidebar-fixed');
                }
            }
        });

        // Update text on hide and remove fixed positioning
        sidebarToggle.addEventListener('hide.bs.collapse', function() {
            toggleText.textContent = 'View Course Content';
            if (sidebarContent) {
                sidebarContent.classList.remove('sidebar-fixed');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (sidebarToggle.classList.contains('show') || sidebarToggle.style.display !== 'none') {
                if (window.innerWidth < 992 && sidebarContent) {
                    sidebarContent.classList.remove('sidebar-fixed');
                } else if (window.innerWidth >= 992 && sidebarContent && toggleBtn.getAttribute('aria-expanded') === 'true') {
                    sidebarContent.classList.add('sidebar-fixed');
                }
            }
        });
    }
});

function submitCompleteForm() {
    const timeSpentInput = document.getElementById('timeSpentInput');
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'time_spent_minutes';
    hiddenInput.value = timeSpentInput.value;

    const form = document.getElementById('markCompleteForm');
    form.appendChild(hiddenInput);
    form.submit();
}
</script>

@endsection

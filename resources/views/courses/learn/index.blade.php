@extends('layouts.app')

@section('title', 'Learning - ' . $course->title)

@section('content')
<style>
    .description-preview {
        line-height: 1.5;
        font-size: 0.95rem;
        color: #333;
    }
    
    .description-preview h1,
    .description-preview h2,
    .description-preview h3,
    .description-preview h4,
    .description-preview h5,
    .description-preview h6 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0.5rem 0 0.25rem 0;
        line-height: 1.4;
        color: #333;
    }

    .description-preview h1:first-child,
    .description-preview h2:first-child,
    .description-preview h3:first-child,
    .description-preview h4:first-child,
    .description-preview h5:first-child,
    .description-preview h6:first-child {
        margin-top: 0;
    }
    
    .description-preview p {
        margin: 0.5rem 0;
        font-size: 0.95rem;
    }
    
    .description-preview ul,
    .description-preview ol {
        margin: 0.5rem 0 0.5rem 1.5rem;
        padding-left: 0;
        font-size: 0.95rem;
    }

    .description-preview li {
        margin: 0.25rem 0;
    }

    .description-preview strong {
        color: var(--bs-primary);
        font-weight: 700;
    }

    /* Table styling */
    .content-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .content-row:hover {
        background-color: #f8f9fa;
    }

    .table-hover tbody tr.content-row:hover {
        background-color: #f0f7ff;
    }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2>{{ $course->title }}</h2>
            <p class="text-muted">Course Learning Hub</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Course
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Learning Modules</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action active">
                        <i class="bi bi-book me-2"></i>Course Content
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="bi bi-clipboard-check me-2"></i>Quizzes
                    </a>
                    <a href="{{ route('courses.discussions.index', $course) }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-chat-dots me-2"></i>Discussions
                    </a>
                    <a href="{{ route('courses.upcoming-sessions') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-video me-2"></i>Live Sessions
                    </a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>Your Progress
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $progress = $enrollment->calculateProgressPercentage();
                        $isComplete = $enrollment->isCourseComplete();
                        $hasCertificate = $enrollment->hasCertificate();
                        $progressColor = $progress >= 75 ? 'success' : ($progress >= 50 ? 'info' : ($progress >= 25 ? 'warning' : 'danger'));
                    @endphp

                    <!-- Progress Bar -->
                    <div class="progress mb-3" style="height: 35px;">
                        <div class="progress-bar bg-{{ $progressColor }} progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: {{ $progress }}%" 
                             aria-valuenow="{{ $progress }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <strong>{{ $progress }}%</strong>
                        </div>
                    </div>

                    <!-- Completion Status -->
                    @if($isComplete)
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <strong>Congratulations!</strong> You've completed all course materials.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        <!-- Certificate Section -->
                        @if($hasCertificate)
                            <div class="alert alert-info">
                                <i class="bi bi-award me-2"></i>
                                <strong>Your Certificate:</strong>
                                <br>
                                <small>Your certificate has been issued. Click below to download it.</small>
                            </div>
                            <a href="{{ route('courses.certificate.download', [$course, $enrollment]) }}" 
                               class="btn btn-success w-100 mb-2">
                                <i class="bi bi-download me-2"></i>Download Certificate (PDF)
                            </a>
                        @else
                            <a href="{{ route('courses.certificate.generate', [$course, $enrollment]) }}" 
                               class="btn btn-success w-100 mb-2">
                                <i class="bi bi-award me-2"></i>Generate & Download Certificate
                            </a>
                        @endif
                    @else
                        <p class="text-muted mb-0">
                            <i class="bi bi-hourglass-split me-2"></i>
                            Keep learning! Complete all required content and quizzes to earn your certificate.
                        </p>
                        <hr>
                        <small class="text-muted">
                            <strong>Remaining:</strong> 
                            {{ 100 - $progress }}% of course material
                        </small>
                    @endif
                </div>
            </div>

            <!-- Live Sessions Badge -->
            <div class="card shadow-sm mt-3 border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-camera-video text-primary" style="font-size: 1.5rem;"></i>
                    <h6 class="mt-2 mb-1">Live Sessions</h6>
                    <p class="text-muted small mb-2">Interact with instructors & classmates</p>
                    <a href="{{ route('courses.upcoming-sessions') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-play-circle me-1"></i>Join Session
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Course Content</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Select a topic from the learning modules to begin.</p>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Getting Started:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Review the course materials in order</li>
                            <li>Complete all quizzes to test your knowledge</li>
                            <li>Participate in discussions with other students</li>
                            <li>Attend live sessions for direct interaction with facilitators</li>
                        </ul>
                    </div>

                    <h5 class="mt-4">Available Content</h5>
                    
                    @if(isset($courseContents) && $courseContents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 50%;">Title</th>
                                        <th style="width: 15%;">Type</th>
                                        <th style="width: 15%;">Reading Time</th>
                                        <th style="width: 15%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courseContents as $index => $content)
                                        <tr class="content-row cursor-pointer" data-content-id="{{ $content->id }}" onclick="toggleDescriptionRow(this, event)">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi {{ $content->content_type === 'video' ? 'bi-play-circle' : 'bi-file-text' }} me-2" style="color: var(--bs-primary);"></i>
                                                    <span style="color: var(--bs-primary); font-weight: 600;">{{ $content->title }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $content->content_type === 'video' ? 'bg-info' : 'bg-secondary' }}">
                                                    {{ ucfirst($content->content_type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $content->min_reading_time_minutes ?? 'N/A' }} min
                                                </small>
                                            </td>
                                            <td>
                                                <a href="{{ route('courses.learn.content', [$course, $content]) }}" class="btn btn-sm btn-primary" onclick="event.stopPropagation()">
                                                    <i class="bi bi-eye me-1"></i>View
                                                </a>
                                            </td>
                                        </tr>
                                        <!-- Description row (hidden by default) -->
                                        <tr class="description-row" id="desc-{{ $content->id }}" style="display: none;">
                                            <td colspan="5">
                                                <div class="p-3 bg-light rounded">
                                                    <strong>Description:</strong>
                                                    <div class="mt-2 description-preview">
                                                        {!! $content->description ?? '<em>No description available</em>' !!}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            No content has been added to this course yet. Please check back later.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDescriptionRow(rowElement, event) {
    // Don't toggle if clicking the View button
    if (event.target.closest('.btn-primary')) {
        return;
    }

    const contentId = rowElement.dataset.contentId;
    const descriptionRow = document.getElementById('desc-' + contentId);
    
    if (descriptionRow) {
        const isHidden = descriptionRow.style.display === 'none';
        
        // Hide all other open description rows
        document.querySelectorAll('.description-row').forEach(row => {
            row.style.display = 'none';
        });
        
        // Toggle current row
        if (isHidden) {
            descriptionRow.style.display = 'table-row';
        }
    }
}

// Close description rows when clicking outside table
document.addEventListener('click', function(event) {
    if (!event.target.closest('.table')) {
        document.querySelectorAll('.description-row').forEach(row => {
            row.style.display = 'none';
        });
    }
});
</script>
@endsection

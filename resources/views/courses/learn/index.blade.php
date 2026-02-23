@extends('layouts.app')

@section('title', 'Learning - ' . $course->title)

@section('content')
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
                        <div class="row">
                            @foreach($courseContents as $content)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="bi {{ $content->content_type === 'video' ? 'bi-play-circle' : 'bi-file-text' }} me-2"></i>
                                                {{ $content->title }}
                                            </h6>
                                            <p class="card-text small text-muted">{{ Str::limit($content->description ?? 'No description', 80) }}</p>
                                            <a href="{{ route('courses.learn.content', [$course, $content]) }}" class="btn btn-sm btn-primary">
                                                Start Learning
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
@endsection

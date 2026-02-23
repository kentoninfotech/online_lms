@extends('layouts.app')

@section('title', $discussion->title)

@section('content')
<div class="container py-5">
    <!-- Course Title -->
    <div class="mb-4">
        <h3 class="text-primary mb-1">
            <i class="bi bi-book me-2"></i>{{ $course->title }}
        </h3>
        <p class="text-muted mb-0">
            <i class="bi bi-chat-dots me-1"></i>Course Discussion
        </p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="mb-1">{{ $discussion->title }}</h4>
                            @if($discussion->is_locked)
                                <span class="badge bg-danger">
                                    <i class="bi bi-lock-fill me-1"></i>Locked
                                </span>
                            @endif
                            @if($discussion->is_pinned)
                                <span class="badge bg-warning">
                                    <i class="bi bi-pin-fill me-1"></i>Pinned
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <strong>{{ $discussion->author?->name ?? 'Deleted User' }}</strong>
                            <br>
                            <small class="text-muted">{{ $discussion->created_at->format('M d, Y \a\t H:i') }}</small>
                        </div>
                    </div>

                    <div class="content-body border-top pt-3">
                        {!! nl2br(e($discussion->message)) !!}
                    </div>
                </div>
            </div>

            <!-- Disqus Comments Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-dots me-2"></i>Community Discussion
                    </h5>
                </div>
                <div class="card-body">
                    <div id="disqus_thread"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Floating Discussion Info -->
            <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Discussion Info
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0" style="font-size: 0.95rem;">
                        <dt class="col-12 mb-2">
                            <strong>Started:</strong>
                        </dt>
                        <dd class="col-12 mb-3 ms-0">
                            <i class="bi bi-calendar me-1"></i>{{ $discussion->created_at->format('M d, Y') }}
                        </dd>

                        <dt class="col-12 mb-2">
                            <strong>By:</strong>
                        </dt>
                        <dd class="col-12 mb-3 ms-0">
                            <i class="bi bi-person me-1"></i>{{ $discussion->author?->name ?? 'Deleted User' }}
                        </dd>

                        <dt class="col-12 mb-2">
                            <strong>Course:</strong>
                        </dt>
                        <dd class="col-12 ms-0">
                            <i class="bi bi-book me-1"></i>{{ $course->title }}
                        </dd>
                    </dl>

                    @if($discussion->is_locked)
                        <div class="alert alert-warning alert-sm mt-3 mb-0">
                            <i class="bi bi-lock-fill me-1"></i>
                            <small>This discussion is locked.</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Course Announcements -->
            @if($announcements->isNotEmpty())
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-megaphone me-2"></i>Recent Announcements
                        </h5>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @foreach($announcements as $announcement)
                            <div class="mb-3 pb-3 border-bottom" style="font-size: 0.9rem;">
                                <h6 class="mb-1 text-truncate" title="{{ $announcement->subject }}">
                                    {{ $announcement->subject }}
                                </h6>
                                <p class="text-muted mb-2" style="font-size: 0.85rem;">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    {{ $announcement->created_at->format('M d, Y') }}
                                </p>
                                <p class="text-truncate mb-2" style="font-size: 0.85rem;">
                                    {!! \Illuminate\Support\Str::limit(strip_tags($announcement->message), 100) !!}
                                </p>
                                <a href="{{ route('course.announcement.show', $announcement) }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-arrow-right me-1"></i>View
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <a href="{{ route('courses.discussions.index', $course) }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left me-2"></i>Back to Discussions
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .content-body {
        line-height: 1.8;
        font-size: 1rem;
    }

    .content-body h3, .content-body h4, .content-body h5 {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }

    .content-body img {
        max-width: 100%;
        height: auto;
        margin: 1rem 0;
    }

    .sticky-top {
        z-index: 1020;
    }

    @media (max-width: 991.98px) {
        .sticky-top {
            position: relative !important;
            top: 0 !important;
        }
    }
</style>

<!-- Disqus Comments Script -->
<script>
    var disqus_config = function () {
        this.page.url = "{{ route('courses.discussions.show', [$course, $discussion]) }}";
        this.page.identifier = "discussion-{{ $discussion->id }}";
        this.page.title = "{{ $discussion->title }}";
        
        @if(Auth::check())
            this.page.remote_auth_s3 = "{{ config('services.disqus.sso_secret') ?? '' }}";
        @endif
    };

    (function() {
        var d = document, s = d.createElement('script');
        s.src = 'https://{{ config('services.disqus.shortname') }}.disqus.com/embed.js';
        s.setAttribute('data-timestamp', +new Date());
        (d.head || d.body).appendChild(s);
    })();
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
@endsection


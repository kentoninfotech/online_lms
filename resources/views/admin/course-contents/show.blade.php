@extends('layouts.app')

@section('title', 'View Content - ' . $content->title)

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">{{ $content->title }}</h3>
                <p class="text-muted">{{ $course->title }}</p>
            </div>
            <div class="col-auto">
                @php
                    $isInstructor = auth()->user()->user_type === 'instructor';
                    $editRoute = $isInstructor ? route('tutor.course-contents.edit', [$course, $content]) : route('admin.course-contents.edit', [$course, $content]);
                    $backRoute = $isInstructor ? route('tutor.course-contents.index', $course) : route('admin.course-contents.index', $course);
                @endphp
                <a href="{{ $editRoute }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ $backRoute }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Content Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Content Type</label>
                            <div>
                                @if($content->content_type === 'video')
                                    <span class="badge bg-danger"><i class="bi bi-play-fill me-1"></i>Video</span>
                                @elseif($content->content_type === 'document')
                                    <span class="badge bg-info"><i class="bi bi-file-earmark me-1"></i>Document</span>
                                @elseif($content->content_type === 'link')
                                    <span class="badge bg-warning"><i class="bi bi-link-45deg me-1"></i>Link</span>
                                @else
                                    <span class="badge bg-secondary">{{ $content->content_type }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Status</label>
                            <div>
                                <span class="badge {{ $content->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $content->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Description</label>
                        <div class="border rounded p-3" style="background-color: #f8f9fa;">
                            {!! $content->description ?? '<em class="text-muted">No description</em>' !!}
                        </div>
                    </div>

                    @if($content->content_type === 'video' && $content->content_url)
                        <div class="mb-3">
                            <label class="form-label text-muted">Video URL</label>
                            <div>
                                <a href="{{ $content->content_url }}" target="_blank" class="text-break">
                                    {{ $content->content_url }}
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($content->content_type === 'document' && $content->content_url)
                        <div class="mb-3">
                            <label class="form-label text-muted">Document</label>
                            <div>
                                <a href="{{ $content->content_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($content->content_type === 'link' && $content->content_url)
                        <div class="mb-3">
                            <label class="form-label text-muted">Link</label>
                            <div>
                                <a href="{{ $content->content_url }}" target="_blank" class="text-break">
                                    {{ $content->content_url }}
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Duration (minutes)</label>
                            <div>{{ $content->duration_minutes ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Order</label>
                            <div>{{ $content->order ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Created</label>
                        <div>{{ $content->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Last Updated</label>
                        <div>{{ $content->updated_at->format('M d, Y H:i') }}</div>
                    </div>
                    
                    <hr>

                    <div class="d-grid gap-2">
                        @php
                            $isInstructor = auth()->user()->user_type === 'instructor';
                            $editRoute = $isInstructor ? route('tutor.course-contents.edit', [$course, $content]) : route('admin.course-contents.edit', [$course, $content]);
                            $destroyRoute = $isInstructor ? route('tutor.course-contents.destroy', [$course, $content]) : route('admin.course-contents.destroy', [$course, $content]);
                        @endphp
                        <a href="{{ $editRoute }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Content
                        </a>
                        <form action="{{ $destroyRoute }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this content?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

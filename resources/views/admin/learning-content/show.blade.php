@extends('layouts.app')

@section('title', 'View Content - ' . $content->title)

@section('content')

<div class="pc-container" style="margin-top: -2rem;">
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.learning-content.index') }}">Learning Content</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($content->title, 30) }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="pc-container" style="margin-top: 0.5rem;">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ $content->title }}</h5>
                    <div>
                        <a href="{{ route('admin.course-contents.edit', [$content->course, $content]) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.course-contents.destroy', [$content->course, $content]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted">Course</h6>
                            <p><a href="{{ route('admin.courses.show', $content->course) }}">{{ $content->course->title }}</a></p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Content Type</h6>
                            <p><span class="badge badge-light-secondary">{{ ucfirst($content->content_type) }}</span></p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted">Sequence</h6>
                            <p>#{{ $content->sequence }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Duration</h6>
                            <p>{{ $content->duration_minutes ?? 'Not specified' }} minutes</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted">Status</h6>
                            <p>
                                @if($content->is_published)
                                    <span class="badge badge-light-success">Published</span>
                                @else
                                    <span class="badge badge-light-warning">Draft</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Required</h6>
                            <p>
                                @if($content->is_required)
                                    <span class="badge badge-light-danger">Required</span>
                                @else
                                    <span class="badge badge-light-info">Optional</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($content->description)
                        <div class="mb-4">
                            <h6 class="text-muted">Description</h6>
                            <p>{{ $content->description }}</p>
                        </div>
                    @endif

                    @if($content->content)
                        <div class="mb-4">
                            <h6 class="text-muted">Content</h6>
                            <div class="bg-light p-3 rounded">
                                {!! $content->content !!}
                            </div>
                        </div>
                    @endif

                    @if($content->file_path)
                        <div class="mb-4">
                            <h6 class="text-muted">Attached File</h6>
                            <p><a href="{{ asset($content->file_path) }}" class="btn btn-sm btn-primary" download>
                                <i class="bi bi-download"></i> Download File
                            </a></p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <small class="text-muted">Created: {{ $content->created_at->format('M d, Y H:i') }}
                            | Last Updated: {{ $content->updated_at->format('M d, Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted">Course</h6>
                        <p class="mb-0">{{ $content->course->title }}</p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-muted">Total Students Accessed</h6>
                        <p class="mb-0"><strong>{{ $content->completions()->count() }}</strong></p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-muted">Completed</h6>
                        <p class="mb-0"><strong>{{ $content->completions()->where('completed_at', '!=', null)->count() }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

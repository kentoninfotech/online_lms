@extends('layouts.app')

@section('title', 'Course Contents')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Course Contents</h3>
                <p class="text-muted">Course: <strong>{{ $course->title }}</strong></p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.course-contents.create', $course) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add Content
                </a>
                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Course
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($contents->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Timeline</th>
                                    <th>Prerequisites</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contents as $index => $content)
                                    <tr>
                                        <td class="fw-bold">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <div class="fw-bold">{{ $content->title }}</div>
                                                    @if($content->description)
                                                        <small class="text-muted d-block" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ Str::limit($content->description, 50) }}
                                                        </small>
                                                    @endif
                                                    @if($content->min_reading_time_minutes > 0)
                                                        <small class="badge badge-info ms-1">
                                                            <i class="bi bi-clock me-1"></i>{{ $content->min_reading_time_minutes }} min
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @switch($content->content_type)
                                                @case('text')
                                                    <span class="badge bg-info"><i class="bi bi-file-text me-1"></i>Text</span>
                                                    @break
                                                @case('video')
                                                    <span class="badge bg-danger"><i class="bi bi-play-circle me-1"></i>Video</span>
                                                    @break
                                                @case('pdf')
                                                    <span class="badge bg-warning"><i class="bi bi-file-pdf me-1"></i>PDF</span>
                                                    @break
                                                @case('word')
                                                    <span class="badge bg-primary"><i class="bi bi-file-word me-1"></i>Word</span>
                                                    @break
                                                @case('powerpoint')
                                                    <span class="badge bg-success"><i class="bi bi-file-slides me-1"></i>PPT</span>
                                                    @break
                                                @case('excel')
                                                    <span class="badge bg-success"><i class="bi bi-file-spreadsheet me-1"></i>Excel</span>
                                                    @break
                                                @case('image')
                                                    <span class="badge bg-secondary"><i class="bi bi-image me-1"></i>Image</span>
                                                    @break
                                                @case('link')
                                                    <span class="badge bg-secondary"><i class="bi bi-link-45deg me-1"></i>Link</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $content->content_type }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div>
                                                @if($content->is_published)
                                                    <span class="badge bg-success"><i class="bi bi-eye me-1"></i>Published</span>
                                                @else
                                                    <span class="badge bg-secondary"><i class="bi bi-eye-slash me-1"></i>Draft</span>
                                                @endif
                                                @if($content->is_required)
                                                    <span class="badge bg-warning"><i class="bi bi-exclamation-circle me-1"></i>Required</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <small>
                                                @if($content->available_from && $content->available_until)
                                                    <strong>From:</strong> {{ $content->available_from->format('M d, Y H:i') }}<br>
                                                    <strong>Until:</strong> {{ $content->available_until->format('M d, Y H:i') }}
                                                @elseif($content->available_from)
                                                    <strong>From:</strong> {{ $content->available_from->format('M d, Y H:i') }}<br>
                                                    <em class="text-success">No end date</em>
                                                @elseif($content->available_until)
                                                    <strong>Until:</strong> {{ $content->available_until->format('M d, Y H:i') }}<br>
                                                    <em class="text-success">Available now</em>
                                                @else
                                                    <em class="text-success">Always available</em>
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            @if($content->prerequisite_content_id)
                                                @php
                                                    $prerequisite = $contents->find($content->prerequisite_content_id);
                                                @endphp
                                                @if($prerequisite)
                                                    <small><strong>Requires:</strong> {{ $prerequisite->title }}</small>
                                                @else
                                                    <small class="text-danger"><strong>Requires:</strong> (Deleted)</small>
                                                @endif
                                            @else
                                                <small class="text-muted">No prerequisites</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.course-contents.show', [$course, $content]) }}" 
                                                    class="btn btn-outline-primary" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.course-contents.edit', [$course, $content]) }}" 
                                                    class="btn btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal{{ $content->id }}" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $content->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Delete Content</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to delete <strong>{{ $content->title }}</strong>?</p>
                                                            <p class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>This action cannot be undone.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <form action="{{ route('admin.course-contents.destroy', [$course, $content]) }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        </div>
                        <h5 class="text-muted">No Course Contents Yet</h5>
                        <p class="text-muted mb-3">Start creating course content to build your curriculum.</p>
                        <a href="{{ route('admin.course-contents.create', $course) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create First Content
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}
</style>

@endsection

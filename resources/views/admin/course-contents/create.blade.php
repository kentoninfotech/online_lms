@extends('layouts.app')

@section('title', 'Add Course Content')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Add Course Content</h3>
                <p class="text-muted">Course: <strong>{{ $course->title }}</strong></p>
            </div>
            <div class="col-auto">
                @php
                    $backRoute = auth()->user()->user_type === 'instructor'
                        ? route('tutor.course-contents.index', $course)
                        : route('admin.course-contents.index', $course);
                @endphp
                <a href="{{ $backRoute }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Contents
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="mb-3"><i class="bi bi-exclamation-circle me-2"></i>Validation Errors</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-10">
            @php
                // Use tutor route if user is instructor, otherwise use admin route
                $storeRoute = auth()->user()->user_type === 'instructor'
                    ? route('tutor.course-contents.store', $course)
                    : route('admin.course-contents.store', $course);
            @endphp
            <form action="{{ $storeRoute }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Basic Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Content Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                        id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sequence" class="form-label">Sequence <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('sequence') is-invalid @enderror" 
                                        id="sequence" name="sequence" value="{{ old('sequence', 0) }}" required>
                                    <small class="text-muted">Display order in course</small>
                                    @error('sequence') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="content_type" class="form-label">Content Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('content_type') is-invalid @enderror" 
                                        id="content_type" name="content_type" required>
                                        <option value="">-- Select Type --</option>
                                        <option value="text" {{ old('content_type') == 'text' ? 'selected' : '' }}>Text/HTML</option>
                                        <option value="video" {{ old('content_type') == 'video' ? 'selected' : '' }}>Video</option>
                                        <option value="pdf" {{ old('content_type') == 'pdf' ? 'selected' : '' }}>PDF Document</option>
                                        <option value="word" {{ old('content_type') == 'word' ? 'selected' : '' }}>Word Document</option>
                                        <option value="powerpoint" {{ old('content_type') == 'powerpoint' ? 'selected' : '' }}>PowerPoint</option>
                                        <option value="excel" {{ old('content_type') == 'excel' ? 'selected' : '' }}>Excel Sheet</option>
                                        <option value="image" {{ old('content_type') == 'image' ? 'selected' : '' }}>Image</option>
                                        <option value="link" {{ old('content_type') == 'link' ? 'selected' : '' }}>External Link</option>
                                    </select>
                                    @error('content_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                        id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', 0) }}" min="0">
                                    <small class="text-muted">Estimated time to complete</small>
                                    @error('duration_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content & File Upload -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-file-earmark me-2"></i>Content & Files
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" id="text-content-section">
                            <label for="content" class="form-label">Content (for text/HTML type)</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                id="content" name="content" rows="5">{{ old('content') }}</textarea>
                            <small class="text-muted">You can use HTML tags</small>
                            @error('content') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3" id="file-upload-section" style="display: none;">
                            <label for="file" class="form-label">Upload File</label>
                            <div class="input-group">
                                <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                    id="file" name="file">
                                <button class="btn btn-outline-secondary" type="button" id="fileHelp">
                                    <i class="bi bi-question-circle me-1"></i>Help
                                </button>
                            </div>
                            <div id="file-help-text" style="display: none;" class="mt-2 alert alert-info small">
                                <strong>Accepted formats:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>PDF: .pdf</li>
                                    <li>Word: .doc, .docx</li>
                                    <li>PowerPoint: .ppt, .pptx</li>
                                    <li>Excel: .xls, .xlsx</li>
                                    <li>Images: .jpg, .jpeg, .png, .gif, .webp</li>
                                </ul>
                            </div>
                            @error('file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Timeline & Availability -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-event me-2"></i>Timeline & Availability
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Set when this content becomes available to students.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="available_from" class="form-label">Available From</label>
                                    <input type="datetime-local" class="form-control @error('available_from') is-invalid @enderror" 
                                        id="available_from" name="available_from" value="{{ old('available_from') }}">
                                    <small class="text-muted">Students cannot access before this date</small>
                                    @error('available_from') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="available_until" class="form-label">Available Until</label>
                                    <input type="datetime-local" class="form-control @error('available_until') is-invalid @enderror" 
                                        id="available_until" name="available_until" value="{{ old('available_until') }}">
                                    <small class="text-muted">Students cannot access after this date</small>
                                    @error('available_until') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="min_reading_time_minutes" class="form-label">Minimum Reading/Viewing Time (minutes)</label>
                            <input type="number" class="form-control @error('min_reading_time_minutes') is-invalid @enderror" 
                                id="min_reading_time_minutes" name="min_reading_time_minutes" 
                                value="{{ old('min_reading_time_minutes', 0) }}" min="0" max="1440">
                            <small class="text-muted">Student must spend at least this time on content before marking complete</small>
                            @error('min_reading_time_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Prerequisites -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-diagram-2 me-2"></i>Prerequisites
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Optionally require students to complete another content first.</p>
                        <div class="mb-3">
                            <label for="prerequisite_content_id" class="form-label">Prerequisite Content</label>
                            <select class="form-select @error('prerequisite_content_id') is-invalid @enderror" 
                                id="prerequisite_content_id" name="prerequisite_content_id">
                                <option value="">-- None (No prerequisite) --</option>
                                @foreach($courseContents as $content)
                                    <option value="{{ $content->id }}" {{ old('prerequisite_content_id') == $content->id ? 'selected' : '' }}>
                                        {{ $content->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('prerequisite_content_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Display & Tracking Options -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-gear me-2"></i>Display & Tracking Options
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="embed_type" class="form-label">How to Display Content <span class="text-danger">*</span></label>
                                    <select class="form-select @error('embed_type') is-invalid @enderror" 
                                        id="embed_type" name="embed_type" required>
                                        <option value="default" {{ old('embed_type', 'default') == 'default' ? 'selected' : '' }}>Default (Normal Page)</option>
                                        <option value="iframe" {{ old('embed_type') == 'iframe' ? 'selected' : '' }}>Embedded in Frame</option>
                                        <option value="popup" {{ old('embed_type') == 'popup' ? 'selected' : '' }}>Popup Window</option>
                                        <option value="fullscreen" {{ old('embed_type') == 'fullscreen' ? 'selected' : '' }}>Full Screen</option>
                                        <option value="modal" {{ old('embed_type') == 'modal' ? 'selected' : '' }}>Modal Dialog</option>
                                    </select>
                                    @error('embed_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tracking Options</label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="track_viewing" 
                                            name="track_viewing" value="1" {{ old('track_viewing') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="track_viewing">
                                            Track Student Viewing Time
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="allow_download" 
                                            name="allow_download" value="1" {{ old('allow_download') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allow_download">
                                            Allow Students to Download
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status & Requirements -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-flag me-2"></i>Status & Requirements
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_published" 
                                name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">
                                Publish this content (make visible to students)
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_required" 
                                name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_required">
                                Mark as required (students must complete)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card">
                    <div class="card-body d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Create Content
                        </button>
                        <a href="{{ route('admin.course-contents.index', $course) }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTypeSelect = document.getElementById('content_type');
    const textSection = document.getElementById('text-content-section');
    const fileSection = document.getElementById('file-upload-section');
    const fileHelpBtn = document.getElementById('fileHelp');
    const fileHelpText = document.getElementById('file-help-text');

    function updateContentDisplay() {
        const selectedType = contentTypeSelect.value;
        if (selectedType === 'text') {
            textSection.style.display = 'block';
            fileSection.style.display = 'none';
        } else if (['video', 'pdf', 'word', 'powerpoint', 'excel', 'image', 'link'].includes(selectedType)) {
            textSection.style.display = 'none';
            fileSection.style.display = 'block';
        } else {
            textSection.style.display = 'block';
            fileSection.style.display = 'block';
        }
    }

    contentTypeSelect.addEventListener('change', updateContentDisplay);
    updateContentDisplay();

    fileHelpBtn.addEventListener('click', function() {
        fileHelpText.style.display = fileHelpText.style.display === 'none' ? 'block' : 'none';
    });
});
</script>

@endsection

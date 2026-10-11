@extends('layouts.app')

@section('title', 'Edit Lesson')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Edit Lesson</h3>
                <p class="text-muted">Course: <strong>{{ $course->title }}</strong></p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.course-lessons.show', [$course, $lesson]) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lesson Details</h5>
                </div>
                <div class="card-body">
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

                    <form action="{{ route('admin.course-lessons.update', [$course, $lesson]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="topic" class="form-label">Topic <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('topic') is-invalid @enderror"
                                id="topic" name="topic" value="{{ old('topic', $lesson->topic) }}" required>
                            @error('topic')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description" rows="3">{{ old('description', $lesson->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scheduled_date" class="form-label">Scheduled Date</label>
                                    <input type="date" class="form-control @error('scheduled_date') is-invalid @enderror"
                                        id="scheduled_date" name="scheduled_date" 
                                        value="{{ old('scheduled_date', $lesson->scheduled_date?->format('Y-m-d')) }}">
                                    @error('scheduled_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror"
                                        id="start_time" name="start_time" 
                                        value="{{ old('start_time', $lesson->start_time?->format('Y-m-d\TH:i')) }}" required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror"
                                        id="end_time" name="end_time" 
                                        value="{{ old('end_time', $lesson->end_time?->format('Y-m-d\TH:i')) }}" required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="instructor_id" class="form-label">Instructor <span class="text-danger">*</span></label>
                                    <select class="form-select @error('instructor_id') is-invalid @enderror"
                                        id="instructor_id" name="instructor_id" required>
                                        <option value="">-- Select Instructor --</option>
                                        @foreach ($instructors as $instructor)
                                            <option value="{{ $instructor->id }}" 
                                                {{ old('instructor_id', $lesson->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Capacity (Students)</label>
                                    <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                                        id="capacity" name="capacity" value="{{ old('capacity', $lesson->capacity) }}" min="1">
                                    @error('capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                                        id="location" name="location" placeholder="e.g., Room 101" 
                                        value="{{ old('location', $lesson->location) }}">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="meeting_link" class="form-label">Meeting Link (Zoom, etc.)</label>
                                    <input type="url" class="form-control @error('meeting_link') is-invalid @enderror"
                                        id="meeting_link" name="meeting_link" value="{{ old('meeting_link', $lesson->meeting_link) }}">
                                    @error('meeting_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                id="notes" name="notes" rows="2">{{ old('notes', $lesson->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_published" name="is_published"
                                value="1" {{ old('is_published', $lesson->is_published) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">
                                Publish this lesson (make visible to enrolled students)
                            </label>
                        </div>

                        <div class="form-footer">
                            <a href="{{ route('admin.course-lessons.show', [$course, $lesson]) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

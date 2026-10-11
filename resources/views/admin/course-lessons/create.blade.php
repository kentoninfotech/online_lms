@extends('layouts.app')

@section('title', 'Create Lesson')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Create New Lesson</h3>
                <p class="text-muted">Course: <strong>{{ $course->title }}</strong></p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.course-lessons.index', $course) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Lessons
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

                    <form action="{{ route('admin.course-lessons.store', $course) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="topic" class="form-label">Topic <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('topic') is-invalid @enderror"
                                id="topic" name="topic" value="{{ old('topic') }}" required>
                            @error('topic')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scheduled_date" class="form-label">Scheduled Date</label>
                                    <input type="date" class="form-control @error('scheduled_date') is-invalid @enderror"
                                        id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date') }}">
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
                                    <input type="text" class="form-control @error('start_time') is-invalid @enderror"
                                        id="start_time" name="start_time" value="{{ old('start_time') }}" 
                                        placeholder="DD/MM/YYYY HH:mm" required>
                                    <small class="text-muted">Format: DD/MM/YYYY HH:mm (e.g., 16/10/2026 15:00)</small>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('end_time') is-invalid @enderror"
                                        id="end_time" name="end_time" value="{{ old('end_time') }}" 
                                        placeholder="DD/MM/YYYY HH:mm" required>
                                    <small class="text-muted">Format: DD/MM/YYYY HH:mm (e.g., 16/10/2026 15:00)</small>
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
                                            <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
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
                                        id="capacity" name="capacity" value="{{ old('capacity') }}" min="1">
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
                                        id="location" name="location" placeholder="e.g., Room 101" value="{{ old('location') }}">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="meeting_link" class="form-label">Meeting Link (Zoom, etc.)</label>
                                    <input type="url" class="form-control @error('meeting_link') is-invalid @enderror"
                                        id="meeting_link" name="meeting_link" value="{{ old('meeting_link') }}">
                                    @error('meeting_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_published" name="is_published"
                                value="1" {{ old('is_published') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">
                                Publish this lesson (make visible to enrolled students)
                            </label>
                        </div>

                        <div class="form-footer">
                            <a href="{{ route('admin.course-lessons.index', $course) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Create Lesson
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    function formatDatetoDDMMYYYY(dateString) {
        if (!dateString) return '';
        
        // If already in DD/MM/YYYY HH:mm format, return as-is
        if (/^\d{2}\/\d{2}\/\d{4}\s+\d{2}:\d{2}$/.test(dateString)) {
            return dateString;
        }
        
        // Convert from Y-m-d H:i format to DD/MM/YYYY HH:mm
        if (/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}/.test(dateString)) {
            const [datePart, timePart] = dateString.split(' ');
            const [yyyy, mm, dd] = datePart.split('-');
            return `${dd}/${mm}/${yyyy} ${timePart}`;
        }
        
        // Convert from ISO format to DD/MM/YYYY HH:mm
        if (/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/.test(dateString)) {
            const [datePart, timePart] = dateString.split('T');
            const [yyyy, mm, dd] = datePart.split('-');
            return `${dd}/${mm}/${yyyy} ${timePart}`;
        }
        
        // Try to parse as Date object
        try {
            const date = new Date(dateString);
            if (!isNaN(date.getTime())) {
                const dd = String(date.getDate()).padStart(2, '0');
                const mm = String(date.getMonth() + 1).padStart(2, '0');
                const yyyy = date.getFullYear();
                const hh = String(date.getHours()).padStart(2, '0');
                const mi = String(date.getMinutes()).padStart(2, '0');
                return `${dd}/${mm}/${yyyy} ${hh}:${mi}`;
            }
        } catch (e) {
            console.error('Error parsing date:', e);
        }
        
        return dateString;
    }
    
    function convertToBackendFormat(dateString) {
        if (!dateString) return '';
        
        // Convert DD/MM/YYYY HH:mm to Y-m-d H:i
        const match = dateString.match(/^(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})$/);
        if (match) {
            const [, dd, mm, yyyy, hh, mi] = match;
            return `${yyyy}-${mm}-${dd} ${hh}:${mi}`;
        }
        
        // If already in backend format, return as-is
        if (/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}$/.test(dateString)) {
            return dateString;
        }
        
        return '';
    }
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            
            if (startTimeInput && startTimeInput.value) {
                const converted = convertToBackendFormat(startTimeInput.value);
                if (converted) {
                    startTimeInput.value = converted;
                }
            }
            
            if (endTimeInput && endTimeInput.value) {
                const converted = convertToBackendFormat(endTimeInput.value);
                if (converted) {
                    endTimeInput.value = converted;
                }
            }
        });
    }
});
</script>
@endsection

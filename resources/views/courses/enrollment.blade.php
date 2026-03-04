@extends('layouts.landing')

@section('title', 'Enroll in ' . $course->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">
                        <i class="bi bi-exclamation-circle me-2"></i>Enrollment Error
                    </h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <details class="mt-2">
                        <summary class="cursor-pointer text-muted small">Debug Info</summary>
                        <pre class="small mt-2 bg-light p-2">
Submitted Data:
- course_date_id: {{ old('course_date_id', 'not submitted') }}
- course_venue_id: {{ old('course_venue_id', 'not submitted') }}

Available Dates: {{ $courseDates->count() }}
{{ json_encode($courseDates->map(fn($d) => ['id' => $d->id, 'label' => $d->date_label, 'venues' => $d->venues->count()])->toArray(), JSON_PRETTY_PRINT) }}
                        </pre>
                    </details>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>Course Enrollment
                    </h4>
                </div>
                <div class="card-body">
                    <h5>{{ $course->title }}</h5>
                    <p class="text-muted">{{ $course->subtitle }}</p>

                    <hr>

                    <form action="{{ route('courses.enroll.store', $course) }}" method="POST" id="enrollmentForm" novalidate>
                        @csrf

                        <!-- Date and venue fields are now optional for all courses -->
                        <div class="mb-4">
                            <label for="course_date_id" class="form-label">
                                <strong>Select Course Date (Optional)</strong>
                            </label>
                            <select class="form-select @error('course_date_id') is-invalid @enderror" 
                                    id="course_date_id" 
                                    name="course_date_id" 
                                    onchange="updateVenues()">
                                <option value="">-- No preference --</option>
                                @if($courseDates && $courseDates->count() > 0)
                                    @foreach($courseDates as $date)
                                        <option value="{{ $date->id }}" data-delivery="{{ $date->delivery_method ?? 'offline' }}" data-venues="{{ json_encode($date->venues->pluck('id', 'venue_name')->toArray()) }}" @selected(old('course_date_id') == $date->id)>
                                            {{ $date->date_label ?? $date->start_date->format('M d, Y') . ' - ' . $date->end_date->format('M d, Y') }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('course_date_id')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-info-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="course_venue_id" class="form-label">
                                <strong>Select Venue (Optional)</strong>
                            </label>
                            <select class="form-select @error('course_venue_id') is-invalid @enderror" 
                                    id="course_venue_id" 
                                    name="course_venue_id">
                                <option value="">-- No preference --</option>
                                @if($course->is_online && $course->is_offline)
                                    <option value="online-na">Online (Venue N/A)</option>
                                @endif
                            </select>
                            @error('course_venue_id')
                                <div class="invalid-feedback d-block">
                                    <i class="bi bi-info-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Course Fee:</strong> 
                            <span class="h5">{{ $course->currency ?? 'NGN' }} {{ number_format($course->fee ?? 0, 2) }}</span>
                        </div>

                        <div id="submitStatus" style="display: none;" class="alert alert-info">
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Processing...</span>
                            </div>
                            <span>Processing your enrollment... Please wait</span>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>Complete Enrollment
                            </button>
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Course Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Category:</dt>
                        <dd class="col-sm-7">{{ $course->category->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-5">Facilitator:</dt>
                        <dd class="col-sm-7">{{ $course->facilitator->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-5">Duration:</dt>
                        <dd class="col-sm-7">{{ $course->course_hours ?? 'N/A' }} hours</dd>

                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            @if($course->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateVenues() {
    const dateSelect = document.getElementById('course_date_id');
    const venueSelect = document.getElementById('course_venue_id');
    
    if (!dateSelect || !venueSelect) return;
    
    const selectedOption = dateSelect.options[dateSelect.selectedIndex];
    
    console.log('updateVenues called, selected date:', selectedOption.value);
    
    // Keep the "No preference" option
    venueSelect.innerHTML = '<option value="">-- No preference --</option>';
    
    // Add online option for hybrid courses
    const onlineOption = document.createElement('option');
    onlineOption.value = 'online-na';
    onlineOption.text = 'Online (Venue N/A)';
    venueSelect.appendChild(onlineOption);
    
    if (selectedOption.value) {
        try {
            const venuesJson = selectedOption.getAttribute('data-venues');
            console.log('Venues JSON:', venuesJson);
            const venues = JSON.parse(venuesJson);
            console.log('Parsed venues:', venues);
            
            if (Object.keys(venues).length > 0) {
                Object.entries(venues).forEach(([name, id]) => {
                    const option = document.createElement('option');
                    option.value = id;
                    option.text = name;
                    venueSelect.appendChild(option);
                    console.log('Added venue option:', name, '=', id);
                });
            }
        } catch (e) {
            console.error('Error parsing venues:', e);
        }
    }
}

// Handle form submission - no validation needed, users can enroll without date/venue
const enrollmentForm = document.getElementById('enrollmentForm');
if (enrollmentForm) {
    enrollmentForm.addEventListener('submit', function(e) {
        console.log('Form submitted - no validation required, date and venue are optional');
        
        // Show processing status
        document.getElementById('submitStatus').style.display = 'block';
        document.getElementById('submitBtn').disabled = true;
        
        // Let the form submit normally
        return true;
    });
}

// Pre-fill selections if there's old data
window.addEventListener('load', function() {
    const dateSelect = document.getElementById('course_date_id');
    const venueSelect = document.getElementById('course_venue_id');
    
    if (!dateSelect || !venueSelect) return;
    
    console.log('Page loaded');
    @if(old('course_date_id'))
        console.log('Old course_date_id found:', "{{ old('course_date_id') }}");
        dateSelect.value = "{{ old('course_date_id') }}";
        updateVenues();
        @if(old('course_venue_id'))
            console.log('Old course_venue_id found:', "{{ old('course_venue_id') }}");
            venueSelect.value = "{{ old('course_venue_id') }}";
        @endif
    @endif
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'Create Course')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    .select2-container--default .select2-selection--multiple:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Create New Course</h3>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Courses
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Course Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Course Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="subtitle" class="form-label">Subtitle</label>
                                <input type="text" class="form-control @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" value="{{ old('subtitle') }}">
                                @error('subtitle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="facilitator_ids" class="form-label">Tutors <small class="text-muted">(Searchable, Multiple)</small></label>
                                <select class="form-select select2-facilitators @error('facilitator_ids') is-invalid @enderror" id="facilitator_ids" name="facilitator_ids[]" multiple>
                                    @foreach($facilitators as $facilitator)
                                        <option value="{{ $facilitator->id }}" @selected(in_array($facilitator->id, old('facilitator_ids', [])))>
                                            {{ $facilitator->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('facilitator_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Select one or more tutors assigned to this course</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="course_hours" class="form-label">Course Hours</label>
                                <input type="number" class="form-control @error('course_hours') is-invalid @enderror" id="course_hours" name="course_hours" value="{{ old('course_hours') }}" min="1">
                                @error('course_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-muted">(Rich Text)</span></label>
                            
                            <!-- AI Content Generator Section -->
                            <div class="card bg-light mb-3 border-info">
                                <div class="card-body p-3">
                                    <div class="row align-items-end g-2">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold mb-2">
                                                <i class="bi bi-robot text-info"></i> AI Content Generator
                                            </label>
                                            <select class="form-select form-select-sm" id="llmProvider">
                                                <option value="">Loading LLM providers...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label text-muted small">Modules:</label>
                                            <input type="number" class="form-control form-control-sm" id="numberOfModules" value="5" min="3" max="15">
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-info btn-sm w-100" id="generateContentBtn" onclick="generateAIContent()">
                                                <i class="bi bi-stars me-1"></i>Generate
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2">AI will generate a professional course overview and outline based on the course title and category.</small>
                                </div>
                            </div>

                            <div id="generatingLoader" class="alert alert-info d-none" role="alert">
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Generating...</span>
                                </div>
                                <span>Generating content from AI... Please wait</span>
                            </div>

                            <!-- Error Alert -->
                            <div id="generationError" class="alert alert-danger d-none" role="alert"></div>

                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="fee" class="form-label">Course Fee <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('fee') is-invalid @enderror" id="fee" name="fee" value="{{ old('fee') }}" step="0.01" min="0" required>
                                @error('fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                    <option value="">-- Select Currency --</option>
                                    <option value="NGN" @selected(old('currency') == 'NGN')>NGN (₦)</option>
                                    <option value="USD" @selected(old('currency') == 'USD')>USD ($)</option>
                                    <option value="GBP" @selected(old('currency') == 'GBP')>GBP (£)</option>
                                    <option value="EUR" @selected(old('currency') == 'EUR')>EUR (€)</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="max_enrollees" class="form-label">Max Enrollees</label>
                                <input type="number" class="form-control @error('max_enrollees') is-invalid @enderror" id="max_enrollees" name="max_enrollees" value="{{ old('max_enrollees') }}" min="1">
                                @error('max_enrollees')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="featured_image" class="form-label">Featured Image</label>
                            <div id="imagePreviewContainer" class="mb-3" style="display: none;">
                                <img id="imagePreview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                            <input type="file" class="form-control @error('featured_image') is-invalid @enderror" id="featured_image" name="featured_image" accept="image/*">
                            <small class="form-text text-muted">Max 2MB. Accepted formats: jpg, jpeg, png, gif</small>
                            @error('featured_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_online" name="is_online" value="1" @checked(old('is_online'))>
                                    <label class="form-check-label" for="is_online">
                                        Online Course
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_offline" name="is_offline" value="1" @checked(old('is_offline'))>
                                    <label class="form-check-label" for="is_offline">
                                        Offline Course
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured'))>
                                    <label class="form-check-label" for="is_featured">
                                        Featured
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', true))>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-check form-switch p-3 bg-light border rounded">
                                    <input class="form-check-input" type="checkbox" id="is_free" name="is_free" value="1" @checked(old('is_free')) onchange="toggleFeeFields()">
                                    <label class="form-check-label fw-bold" for="is_free">
                                        <i class="bi bi-gift text-success me-2"></i>Free Course (Requires Manual Approval)
                                    </label>
                                    <small class="d-block text-muted mt-2">If checked, students can enroll without payment. Admin must manually approve enrollment before student can access course content.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Course Dates Section -->
                        <div class="card mb-4 border-primary">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-calendar-event text-primary"></i> Course Dates
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="courseDatesContainer">
                                    @if(old('course_dates'))
                                        @foreach(old('course_dates') as $index => $date)
                                            @include('admin.courses.partials.date-form', ['index' => $index, 'date' => $date])
                                        @endforeach
                                    @else
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i> No course dates added yet. Click "Add Date" to add one.
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addDateBtn">
                                    <i class="bi bi-plus-circle me-1"></i>Add Date
                                </button>
                            </div>
                        </div>

                        <div class="form-footer">
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Create Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/{{ config('services.tinymce.api_key') }}/tinymce/7/tinymce.min.js"></script>
<!-- jQuery for Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Toggle fee and currency fields based on is_free checkbox
    function toggleFeeFields() {
        const isFreeCheckbox = document.getElementById('is_free');
        const feeInput = document.getElementById('fee');
        const currencySelect = document.getElementById('currency');
        
        if (isFreeCheckbox.checked) {
            feeInput.disabled = true;
            feeInput.value = '0';
            currencySelect.disabled = true;
        } else {
            feeInput.disabled = false;
            currencySelect.disabled = false;
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleFeeFields();
    });
    
    // Global error handler  
    window.addEventListener('error', function(e) {
        console.error('Global JS Error:', e.message, e.filename, e.lineno);
    });

    tinymce.init({
        selector: '#description',
        height: 400,
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
    });

    // Load LLM Providers on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadLLMProviders();
    });

    function loadLLMProviders() {
        fetch('/admin/ai-content-generator/providers', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('llmProvider');
            select.innerHTML = '';
            data.providers.forEach(provider => {
                const option = document.createElement('option');
                option.value = provider.id;
                option.textContent = provider.name + (provider.configured ? ' ✓' : ' (Not configured)');
                option.disabled = !provider.configured;
                if (provider.id === data.current_provider) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading providers:', error));
    }

    function generateAIContent() {
        const courseTitle = document.getElementById('title').value;
        const courseDescription = document.getElementById('subtitle').value;
        const numberOfModules = document.getElementById('numberOfModules').value;
        const provider = document.getElementById('llmProvider').value;

        if (!courseTitle) {
            showError('Please enter a course title first');
            return;
        }

        if (!provider) {
            showError('Please select an LLM provider');
            return;
        }

        showGenerating(true);
        hideError();

        fetch('/admin/ai-content-generator/generate-content', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                course_title: courseTitle,
                course_description: courseDescription,
                number_of_modules: numberOfModules,
                provider: provider
            })
        })
        .then(response => response.json())
        .then(data => {
            showGenerating(false);
            
            if (data.success) {
                // Combine overview and outline
                const fullContent = '<h2>Course Overview</h2>' + data.overview + 
                                   '<h2>Course Outline</h2>' + data.outline;

                // Set in TinyMCE
                tinymce.get('description').setContent(fullContent);
                
                // Show success message
                showSuccess('Content generated successfully! Review and edit as needed.');
            } else {
                showError(data.error || 'Failed to generate content');
            }
        })
        .catch(error => {
            showGenerating(false);
            showError('Error: ' + error.message);
        });
    }

    function showGenerating(show) {
        const loader = document.getElementById('generatingLoader');
        const btn = document.getElementById('generateContentBtn');
        
        if (show) {
            loader.classList.remove('d-none');
            btn.disabled = true;
        } else {
            loader.classList.add('d-none');
            btn.disabled = false;
        }
    }

    function showError(message) {
        const errorDiv = document.getElementById('generationError');
        errorDiv.textContent = message;
        errorDiv.classList.remove('d-none');
    }

    function hideError() {
        document.getElementById('generationError').classList.add('d-none');
    }

    function showSuccess(message) {
        // You can enhance this with a toast notification
        alert(message);
    }

    // Image Preview
    document.getElementById('featured_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('imagePreview');
                const container = document.getElementById('imagePreviewContainer');
                preview.src = event.target.result;
                container.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Initialize Select2 for facilitators
    document.addEventListener('DOMContentLoaded', function() {
        $('.select2-facilitators').select2({
            placeholder: 'Search and select tutors...',
            allowClear: true,
            width: '100%'
        });
    });

    // Course Dates Management
    let dateIndex = {{ old('course_dates') ? count(old('course_dates')) : 0 }};
    let venueIndex = {};

    // Initialize venueIndex for old() dates
    @if(old('course_dates'))
        @foreach(old('course_dates') as $index => $date)
            venueIndex[{{ $index }}] = {{ isset($date['venues']) ? count($date['venues']) : 0 }};
        @endforeach
    @endif

    // Attach click handler to Add Date button
    function setupDateButton() {
        const addDateBtn = document.getElementById('addDateBtn');
        console.log('setupDateButton called, button found:', addDateBtn);
        if (addDateBtn) {
            addDateBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Add Date button clicked');
                addCourseDate();
                return false;
            });
        } else {
            console.error('Add Date button not found!');
        }
    }

    // Setup button when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupDateButton);
    } else {
        setupDateButton();
    }

    function addCourseDate() {
        const container = document.getElementById('courseDatesContainer');
        
        // Clear empty state message if exists
        const emptyAlert = container.querySelector('.alert-info');
        if (emptyAlert && container.children.length === 1) {
            emptyAlert.remove();
        }

        const html = `
            <div class="card mb-3 border-secondary" id="dateCard-${dateIndex}">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #f8f9fa;">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-calendar"></i> Date ${dateIndex + 1}
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDate(${dateIndex})">
                        <i class="bi bi-trash me-1"></i>Remove
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="course_dates[${dateIndex}][start_date]" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="course_dates[${dateIndex}][end_date]" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Date Label (Optional)</label>
                            <input type="text" class="form-control" name="course_dates[${dateIndex}][date_label]" placeholder="e.g., Cohort 1, Batch A">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Notes (Optional)</label>
                            <input type="text" class="form-control" name="course_dates[${dateIndex}][notes]" placeholder="Additional information">
                        </div>
                    </div>

                    <!-- Venues for this date -->
                    <div class="mt-4">
                        <h6 class="mb-3">
                            <i class="bi bi-geo-alt"></i> Venues
                        </h6>
                        <div id="venuesContainer-${dateIndex}" class="mb-3">
                            <div class="alert alert-light border">
                                <i class="bi bi-info-circle"></i> No venues added. Click "Add Venue" to add one.
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addVenue(${dateIndex}); return false;">
                            <i class="bi bi-plus-circle me-1"></i>Add Venue
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', html);
        
        venueIndex[dateIndex] = 0;
        dateIndex++;
    }

    function removeDate(index) {
        const card = document.getElementById(`dateCard-${index}`);
        if(confirm('Are you sure you want to remove this date?')) {
            card.remove();
            
            // Show empty message if no dates left
            const container = document.getElementById('courseDatesContainer');
            if(container.children.length === 0) {
                container.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle"></i> No course dates added yet. Click "Add Date" to add one.</div>';
            }
        }
    }

    function addVenue(dateIndex) {
        console.log('addVenue called with dateIndex:', dateIndex);
        console.log('venueIndex object:', venueIndex);
        
        const container = document.getElementById(`venuesContainer-${dateIndex}`);
        console.log('Container found:', container);
        
        if (!container) {
            alert('Error: Could not find venues container for date ' + dateIndex);
            return;
        }
        
        const idx = venueIndex[dateIndex] || 0;
        console.log('Current venue index:', idx);
        
        // Remove empty state if exists
        const emptyAlert = container.querySelector('.alert-light');
        if(emptyAlert) {
            emptyAlert.remove();
        }

        const html = `<div class="card border-light mb-2" id="venueCard-${dateIndex}-${idx}">
<div class="card-body p-3">
<div class="row align-items-end">
<div class="col-md-4 mb-2">
<label class="form-label mb-1">Venue Name <span class="text-danger">*</span></label>
<input type="text" class="form-control form-control-sm" name="course_dates[${dateIndex}][venues][${idx}][venue_name]" placeholder="e.g., Main Campus" required>
</div>
<div class="col-md-4 mb-2">
<label class="form-label mb-1">Address</label>
<input type="text" class="form-control form-control-sm" name="course_dates[${dateIndex}][venues][${idx}][address]" placeholder="Street address">
</div>
<div class="col-md-2 mb-2">
<label class="form-label mb-1">City</label>
<input type="text" class="form-control form-control-sm" name="course_dates[${dateIndex}][venues][${idx}][city]" placeholder="City">
</div>
<div class="col-md-2 mb-2">
<button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeVenue(${dateIndex}, ${idx})">
<i class="bi bi-trash me-1"></i>Remove
</button>
</div>
</div>
<div class="row">
<div class="col-md-3">
<label class="form-label mb-1">State/Province</label>
<input type="text" class="form-control form-control-sm" name="course_dates[${dateIndex}][venues][${idx}][state]" placeholder="State">
</div>
<div class="col-md-3">
<label class="form-label mb-1">Country</label>
<input type="text" class="form-control form-control-sm" name="course_dates[${dateIndex}][venues][${idx}][country]" placeholder="Country">
</div>
<div class="col-md-3">
<label class="form-label mb-1">Capacity</label>
<input type="number" class="form-control form-control-sm" name="course_dates[${dateIndex}][venues][${idx}][capacity]" placeholder="Number of seats" min="1">
</div>
<div class="col-md-3">
<label class="form-label mb-1">Notes</label>
<input type="text" class="form-control form-control-sm" name="course_dates[${dateIndex}][venues][${idx}][notes]" placeholder="Optional notes">
</div>
</div>
</div>
</div>`;

        container.insertAdjacentHTML('beforeend', html);

        venueIndex[dateIndex] = idx + 1;
    }

    function removeVenue(dateIndex, venueIdx) {
        const card = document.getElementById(`venueCard-${dateIndex}-${venueIdx}`);
        if(confirm('Are you sure you want to remove this venue?')) {
            card.remove();
            
            // Show empty message if no venues left for this date
            const container = document.getElementById(`venuesContainer-${dateIndex}`);
            if(container.children.length === 0) {
                container.innerHTML = '<div class="alert alert-light border"><i class="bi bi-info-circle"></i> No venues added. Click "Add Venue" to add one.</div>';
            }
        }
    }
</script>
@endsection

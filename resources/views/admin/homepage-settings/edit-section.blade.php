@extends('layouts.app')

@section('title', 'Edit ' . $sectionLabel . ' - Homepage Settings')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.homepage-settings.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
                <div>
                    <h1 class="h2 mb-0">{{ $sectionLabel }}</h1>
                    <p class="text-muted mt-1">Edit all content for this section</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Settings Cards -->
    <div class="row g-4">
        @if($settings->count() > 0)
            @foreach($settings as $key => $setting)
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form action="{{ route('admin.homepage-settings.update-setting', [$section, $key]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3">
                                            <i class="fa fa-edit"></i> {{ ucfirst(str_replace('_', ' ', $key)) }}
                                        </h5>
                                        <hr class="my-2">
                                    </div>
                                </div>

                                <!-- Title Field -->
                                @if($setting->title)
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Title</label>
                                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                            value="{{ old('title', $setting->title) }}" placeholder="Enter title">
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endif

                                <!-- Description Field -->
                                @if($setting->description)
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Description</label>
                                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" 
                                            placeholder="Enter description">{{ old('description', $setting->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endif

                                <!-- Value Field -->
                                @if($setting->data_type === 'textarea')
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Content</label>
                                        <textarea name="value" rows="5" class="form-control @error('value') is-invalid @enderror" 
                                            placeholder="Enter content">{{ old('value', $setting->value) }}</textarea>
                                        @error('value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @elseif($setting->data_type === 'text')
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Text Content</label>
                                        <input type="text" name="value" class="form-control @error('value') is-invalid @enderror" 
                                            value="{{ old('value', $setting->value) }}" placeholder="Enter text">
                                        @error('value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endif

                                <!-- Image Field -->
                                @if($setting->data_type === 'image' || $setting->image_path)
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Image</label>
                                        @if($setting->image_path)
                                            <div class="mb-3">
                                                <img src="{{ asset($setting->image_path) }}" alt="Current image" 
                                                    class="img-fluid rounded" style="max-width: 300px; max-height: 200px;">
                                            </div>
                                        @endif
                                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                            accept="image/*">
                                        <small class="form-text text-muted">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF, WebP</small>
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endif

                                <!-- Button Fields -->
                                @if($setting->button_text || $setting->button_link)
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Button Text</label>
                                        <input type="text" name="button_text" class="form-control @error('button_text') is-invalid @enderror" 
                                            value="{{ old('button_text', $setting->button_text) }}" placeholder="e.g., Learn More">
                                        @error('button_text')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Button Link</label>
                                        <input type="text" name="button_link" class="form-control @error('button_link') is-invalid @enderror" 
                                            value="{{ old('button_link', $setting->button_link) }}" placeholder="e.g., /courses or #section">
                                        @error('button_link')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endif

                                <!-- Active Status -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="is_active" value="0">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" 
                                                {{old('is_active', $setting->is_active) ? 'checked' : ''}}>
                                            <label class="form-check-label fw-semibold" for="is_active">
                                                This item is active and visible on the homepage
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-save"></i> Save Changes
                                            </button>
                                            <a href="{{ route('admin.homepage-settings.edit-section', $section) }}" class="btn btn-secondary">
                                                Cancel
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <!-- Delete Form (Outside of save form) -->
                            <form action="{{ route('admin.homepage-settings.destroy', [$section, $key]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fa fa-plus-circle"></i> Create Settings for {{ $sectionLabel }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">No settings found for this section yet. Use the form below to create your first entry, or initialize default fields:</p>
                        
                        <!-- Quick Initialize Button -->
                        <form action="{{ route('admin.homepage-settings.initialize-defaults') }}" method="POST" style="display: inline;" class="mb-4">
                            @csrf
                            <input type="hidden" name="section" value="{{ $section }}">
                            <button type="submit" class="btn btn-outline-info">
                                <i class="fa fa-magic"></i> Initialize Default Fields
                            </button>
                        </form>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3">Or manually create a new entry:</h6>

                        <!-- Manual Entry Form -->
                        <form method="POST" action="{{ route('admin.homepage-settings.update-setting', [$section, 'manual_entry_' . time()]) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Field Key *</label>
                                    <input type="text" name="field_key" class="form-control @error('field_key') is-invalid @enderror" 
                                        placeholder="e.g., title, description, email_value" required>
                                    <small class="form-text text-muted">Identifier for this field</small>
                                    @error('field_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Field Type *</label>
                                    <select name="field_type" class="form-select @error('field_type') is-invalid @enderror" required id="fieldTypeSelect">
                                        <option value="">-- Select Type --</option>
                                        <option value="text">Text</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="image">Image</option>
                                    </select>
                                    @error('field_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Conditional Fields -->
                            <div class="row mb-3" id="valueField" style="display: none;">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Value</label>
                                    <input type="text" name="value" class="form-control" placeholder="Enter value">
                                </div>
                            </div>

                            <div class="row mb-3" id="textareaField" style="display: none;">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Content</label>
                                    <textarea name="textarea_value" rows="5" class="form-control" placeholder="Enter content"></textarea>
                                </div>
                            </div>

                            <div class="row mb-3" id="imageField" style="display: none;">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <small class="form-text text-muted">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF, WebP</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Button Text (Optional)</label>
                                    <input type="text" name="button_text" class="form-control" placeholder="e.g., Learn More">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Button Link (Optional)</label>
                                    <input type="text" name="button_link" class="form-control" placeholder="e.g., /courses or #section">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                                        <label class="form-check-label fw-semibold" for="is_active">
                                            This item is active and visible on the homepage
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-plus"></i> Create Entry
                                        </button>
                                        <a href="{{ route('admin.homepage-settings.index') }}" class="btn btn-secondary">
                                            Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <hr class="my-4">

                        <h6 class="fw-bold text-muted">Section Reference:</h6>
                        <div class="alert alert-light">
                            <small>
                                <strong>Section:</strong> <code>{{ $section }}</code><br>
                                <strong>Example Fields:</strong> 
                                @if($section === 'contact')
                                    <code>title</code>, <code>subtitle</code>, <code>email_icon</code>, <code>email_label</code>, <code>email_value</code>, <code>phone_icon</code>, <code>phone_label</code>, <code>phone_value</code>, <code>address_icon</code>, <code>address_label</code>, <code>address_value</code>, <code>hours_icon</code>, <code>hours_label</code>, <code>hours_value</code>, <code>whatsapp_link</code>, <code>form_title</code>, <code>form_name_label</code>, <code>form_email_label</code>, <code>form_phone_label</code>, <code>form_subject_label</code>, <code>form_message_label</code>, <code>form_submit_text</code>
                                @elseif($section === 'hero')
                                    <code>title</code>, <code>description</code>, <code>button_text</code>, <code>button_link</code>, <code>stat1_value</code>, <code>stat1_label</code>, <code>stat2_value</code>, <code>stat2_label</code>, <code>stat3_value</code>, <code>stat3_label</code>
                                @elseif($section === 'about')
                                    <code>title</code>, <code>content</code>, <code>content_2</code>, <code>stat1_value</code>, <code>stat1_label</code>, <code>stat2_value</code>, <code>stat2_label</code>, <code>stat3_value</code>, <code>stat3_label</code>, <code>stat4_value</code>, <code>stat4_label</code>
                                @elseif($section === 'features')
                                    <code>section_title</code>, <code>section_subtitle</code>, <code>feature1_icon</code>, <code>feature1_title</code>, <code>feature1_desc</code>, ... <code>feature8_icon</code>, <code>feature8_title</code>, <code>feature8_desc</code>
                                @elseif($section === 'stats')
                                    <code>stat1_value</code>, <code>stat1_label</code>, <code>stat2_value</code>, <code>stat2_label</code>, <code>stat3_value</code>, <code>stat3_label</code>, <code>stat4_value</code>, <code>stat4_label</code>
                                @elseif($section === 'cta')
                                    <code>title</code>, <code>description</code>, <code>button_text</code>, <code>button_link</code>
                                @else
                                    Create fields based on your section requirements
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .form-control:focus {
        border-color: #2563EB;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }
</style>

<script>
    // Dynamic field type handling for new entry form
    document.addEventListener('DOMContentLoaded', function() {
        const fieldTypeSelect = document.getElementById('fieldTypeSelect');
        const valueField = document.getElementById('valueField');
        const textareaField = document.getElementById('textareaField');
        const imageField = document.getElementById('imageField');

        if (fieldTypeSelect) {
            fieldTypeSelect.addEventListener('change', function() {
                // Hide all fields first
                valueField.style.display = 'none';
                textareaField.style.display = 'none';
                imageField.style.display = 'none';

                // Show selected field
                if (this.value === 'text') {
                    valueField.style.display = 'block';
                } else if (this.value === 'textarea') {
                    textareaField.style.display = 'block';
                } else if (this.value === 'image') {
                    imageField.style.display = 'block';
                }
            });
        }
    });
</script>
@endsection

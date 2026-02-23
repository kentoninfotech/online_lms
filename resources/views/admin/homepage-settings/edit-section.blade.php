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
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> No settings found for this section. 
                    <form action="{{ route('admin.homepage-settings.initialize-defaults') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-link p-0" style="vertical-align: baseline; text-decoration: underline;">Initialize defaults</button>
                    </form> to get started.
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
@endsection

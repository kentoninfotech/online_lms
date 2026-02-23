@extends('layouts.app')

@section('content')
<style>
    .form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 8px;
    }
    
    .form-header h1 {
        margin: 0;
        font-weight: 700;
    }
    
    .form-section {
        margin-bottom: 2rem;
    }
    
    .form-section-title {
        font-weight: 700;
        color: #333;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #667eea;
    }
    
    .image-upload-area {
        border: 2px dashed #667eea;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        background: rgba(102, 126, 234, 0.05);
        transition: all 0.3s ease;
    }
    
    .image-upload-area:hover {
        background: rgba(102, 126, 234, 0.1);
        border-color: #764ba2;
    }
    
    .image-preview-container {
        border-radius: 8px;
        overflow: hidden;
        background: #f8f9fa;
        padding: 1rem;
    }
    
    .image-preview-container img {
        max-width: 100%;
        max-height: 300px;
        border-radius: 6px;
        display: block;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 12px 28px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.75rem;
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="form-header mb-4 shadow-sm">
        <div class="container">
            <h1>
                <i class="bi bi-plus-circle me-2"></i>Create New Service
            </h1>
            <small class="text-white-50">Add a new service to your offerings</small>
        </div>
    </div>

    <div class="container">
        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                <strong>Validation Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Form Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Basic Information Section -->
                    <div class="form-section">
                        <h5 class="form-section-title">
                            <i class="bi bi-info-circle me-2"></i>Service Information
                        </h5>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Service Title *</label>
                                <input 
                                    type="text" 
                                    id="title" 
                                    name="title" 
                                    value="{{ old('title') }}" 
                                    required 
                                    class="form-control @error('title') is-invalid @enderror"
                                    placeholder="Enter service title">
                                @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="subtitle" class="form-label">Service Subtitle *</label>
                                <input 
                                    type="text" 
                                    id="subtitle" 
                                    name="subtitle" 
                                    value="{{ old('subtitle') }}" 
                                    required 
                                    class="form-control @error('subtitle') is-invalid @enderror"
                                    placeholder="Enter service subtitle">
                                @error('subtitle')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="body" class="form-label">Service Description *</label>
                            <textarea 
                                id="body" 
                                name="body" 
                                required 
                                class="form-control @error('body') is-invalid @enderror"
                                rows="8"
                                placeholder="Enter detailed service description...">{{ old('body') }}</textarea>
                            @error('body')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            <small class="text-muted">Rich text editor enabled</small>
                            <script>
                                if (typeof CKEDITOR !== 'undefined') {
                                    CKEDITOR.replace('body', {
                                        height: 300,
                                        toolbar: 'basic'
                                    });
                                }
                            </script>
                        </div>

                        <div class="mb-4">
                            <label for="published" class="form-label d-block">Publication Status</label>
                            <div class="form-check form-switch">
                                <input 
                                    type="checkbox" 
                                    id="published" 
                                    name="published" 
                                    value="1" 
                                    {{ old('published') ? 'checked' : '' }} 
                                    class="form-check-input"
                                    style="width: 3rem; height: 1.5rem;">
                                <label class="form-check-label" for="published">
                                    Publish this service immediately
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Featured Image Section -->
                    <div class="form-section">
                        <h5 class="form-section-title">
                            <i class="bi bi-image me-2"></i>Featured Image
                        </h5>

                        <div class="mb-4">
                            <label for="featured_image" class="form-label d-block mb-3">Upload Image *</label>
                            <div class="image-upload-area">
                                <i class="bi bi-cloud-arrow-up" style="font-size: 2.5rem; color: #667eea;"></i>
                                <p class="mt-2 mb-2">
                                    <strong>Click to browse or drag and drop</strong>
                                </p>
                                <small class="text-muted d-block mb-3">
                                    PNG, JPG, GIF up to 2MB. Best dimensions: 600x400px
                                </small>
                                <input 
                                    type="file" 
                                    id="featured_image" 
                                    name="featured_image" 
                                    accept="image/*" 
                                    class="form-control d-none"
                                    required>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('featured_image').click()">
                                    <i class="bi bi-folder-plus me-2"></i>Select Image
                                </button>
                            </div>
                        </div>

                        <!-- Image Preview -->
                        <div id="image-preview" class="image-preview-container d-none">
                            <img id="preview-image" src="" alt="Preview">
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 pt-4 border-top">
                        <button type="submit" class="btn btn-submit">
                            <i class="bi bi-check-lg me-2"></i>Create Service
                        </button>
                        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Handle image preview
document.getElementById('featured_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            const preview = document.getElementById('image-preview');
            const previewImage = document.getElementById('preview-image');
            previewImage.src = evt.target.result;
            preview.classList.remove('d-none');
        }
        reader.readAsDataURL(file);
    }
});

// Drag and drop
const uploadArea = document.querySelector('.image-upload-area');
if (uploadArea) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.style.background = 'rgba(102, 126, 234, 0.15)';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.style.background = 'rgba(102, 126, 234, 0.05)';
        }, false);
    });

    uploadArea.addEventListener('drop', (e) => {
        document.getElementById('featured_image').files = e.dataTransfer.files;
        const event = new Event('change', { bubbles: true });
        document.getElementById('featured_image').dispatchEvent(event);
    }, false);
}
</script>
@endsection

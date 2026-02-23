@extends('layouts.app')

@section('content')
<style>
    .form-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 8px;
    }
    
    .form-header h1 {
        margin: 0;
        font-weight: 700;
    }
    
    .image-upload-area {
        border: 2px dashed #f5576c;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        background: rgba(245, 87, 108, 0.05);
        transition: all 0.3s ease;
    }
    
    .image-upload-area:hover {
        background: rgba(245, 87, 108, 0.1);
        border-color: #f093fb;
    }
    
    .image-preview {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        background: #f8f9fa;
    }
    
    .image-preview img {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }
    
    .gallery-image-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .gallery-image-item:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    
    .gallery-image-item img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        display: block;
    }
    
    .delete-btn-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .gallery-image-item:hover .delete-btn-overlay {
        opacity: 1;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        color: white;
        padding: 10px 24px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
        color: white;
        text-decoration: none;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="form-header mb-4 shadow-sm">
        <div class="container">
            <h1>
                <i class="bi bi-pencil-square me-2"></i>Edit Gallery: {{ $gallery->title }}
            </h1>
            <small class="text-white-50">Update gallery information and manage images</small>
        </div>
    </div>

    <div class="container">
        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                <strong>Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Edit Form Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.galleries.update', $gallery) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information Section -->
                    <h5 class="card-title fw-bold mb-4">
                        <i class="bi bi-info-circle me-2"></i>Gallery Information
                    </h5>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="title" class="form-label fw-600">Gallery Title *</label>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                value="{{ old('title', $gallery->title) }}" 
                                required 
                                class="form-control @error('title') is-invalid @enderror"
                                placeholder="Enter gallery title">
                            @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="event_name" class="form-label fw-600">Event Name</label>
                            <input 
                                type="text" 
                                id="event_name" 
                                name="event_name" 
                                value="{{ old('event_name', $gallery->event_name) }}" 
                                class="form-control @error('event_name') is-invalid @enderror"
                                placeholder="(Optional) Event name">
                            @error('event_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="event_date" class="form-label fw-600">Event Date</label>
                            <input 
                                type="datetime-local" 
                                id="event_date" 
                                name="event_date" 
                                value="{{ old('event_date', $gallery->event_date?->format('Y-m-d\TH:i')) }}" 
                                class="form-control @error('event_date') is-invalid @enderror">
                            @error('event_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="published" class="form-label fw-600 d-block">Publication Status</label>
                            <div class="form-check form-switch">
                                <input 
                                    type="checkbox" 
                                    id="published" 
                                    name="published" 
                                    value="1" 
                                    {{ old('published', $gallery->published) ? 'checked' : '' }} 
                                    class="form-check-input"
                                    style="width: 3rem; height: 1.5rem;">
                                <label class="form-check-label" for="published">
                                    Publish gallery
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-600">Description</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="4"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Enter gallery description...">{{ old('description', $gallery->description) }}</textarea>
                        @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <hr class="my-4">

                    <!-- Images Upload Section -->
                    <h5 class="card-title fw-bold mb-4">
                        <i class="bi bi-cloud-arrow-up me-2"></i>Add More Images
                    </h5>

                    <div class="mb-4">
                        <label for="images" class="form-label fw-600 d-block mb-3">Upload Additional Images</label>
                        <div class="image-upload-area">
                            <i class="bi bi-cloud-arrow-up" style="font-size: 2.5rem; color: #f5576c;"></i>
                            <p class="mt-2 mb-2">
                                <strong>Click to browse or drag and drop</strong>
                            </p>
                            <small class="text-muted d-block mb-3">
                                PNG, JPG, GIF up to 5MB each. Multiple images supported.
                            </small>
                            <input 
                                type="file" 
                                id="images" 
                                name="images[]" 
                                multiple 
                                accept="image/*" 
                                class="form-control d-none">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="document.getElementById('images').click()">
                                <i class="bi bi-folder-plus me-2"></i>Select Images
                            </button>
                        </div>
                    </div>

                    <!-- Image Preview for New Images -->
                    <div id="image-preview" class="row g-3 mb-4"></div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2 pt-4 border-top">
                        <button type="submit" class="btn btn-submit">
                            <i class="bi bi-check-lg me-2"></i>Update Gallery
                        </button>
                        <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to List
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Current Images Section -->
        @if($gallery->images()->count() > 0)
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">
                        <i class="bi bi-image me-2"></i>Gallery Images ({{ $gallery->images()->count() }})
                    </h5>

                    <div class="row g-3">
                        @foreach($gallery->images as $image)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="gallery-image-item">
                                    <img src="{{ asset($image->image_path) }}" alt="Gallery image">
                                    <a href="#" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#deleteImageModal{{ $image->id }}"
                                       class="delete-btn-overlay text-decoration-none">
                                        <button type="button" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </a>
                                </div>
                            </div>

                            <!-- Delete Image Modal -->
                            <div class="modal fade" id="deleteImageModal{{ $image->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header border-bottom-0">
                                            <h5 class="modal-title">
                                                <i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete Image
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="{{ asset($image->image_path) }}" alt="Image to delete" class="img-fluid mb-3" style="max-height: 200px;">
                                            <p>Are you sure you want to delete this image?</p>
                                            <p class="text-muted small mb-0">This action cannot be undone.</p>
                                        </div>
                                        <div class="modal-footer border-top-0">
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form method="POST" action="{{ route('admin.galleries.delete-image', $image) }}" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete Image</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>No images in this gallery yet. Upload some images above!
            </div>
        @endif
    </div>
</div>

<script>
// Handle image preview for new images
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(evt) {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3';
            col.innerHTML = `
                <div class="image-preview">
                    <img src="${evt.target.result}" alt="Preview ${index + 1}">
                </div>
            `;
            preview.appendChild(col);
        }
        reader.readAsDataURL(file);
    });
});

// Drag and drop
const uploadArea = document.querySelector('.image-upload-area');
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    uploadArea.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    uploadArea.addEventListener(eventName, () => {
        uploadArea.style.background = 'rgba(245, 87, 108, 0.15)';
    }, false);
});

['dragleave', 'drop'].forEach(eventName => {
    uploadArea.addEventListener(eventName, () => {
        uploadArea.style.background = 'rgba(245, 87, 108, 0.05)';
    }, false);
});

uploadArea.addEventListener('drop', (e) => {
    document.getElementById('images').files = e.dataTransfer.files;
    const event = new Event('change', { bubbles: true });
    document.getElementById('images').dispatchEvent(event);
}, false);
</script>
@endsection

@extends('layouts.app')

@section('title', 'Carousel Management - Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0">Carousel Management</h1>
                    <p class="text-muted mt-1">Manage carousel slides for your homepage banner</p>
                </div>
                <a href="{{ route('admin.homepage-settings.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Homepage Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Errors found:</strong>
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
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Upload New Slide -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-cloud-upload-alt me-2"></i> Upload New Carousel Slide
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.carousel.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slide Image <span class="text-danger">*</span></label>
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
                                <small class="form-text text-muted">Recommended: 1920x600px, Max 5MB</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slide Title</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="e.g., Learn New Skills" value="{{ old('title') }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Slide Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Optional description for this slide">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Button Text (Optional)</label>
                                <input type="text" name="button_text" class="form-control" placeholder="e.g., Explore Courses" value="{{ old('button_text') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Button Link (Optional)</label>
                                <input type="url" name="button_link" class="form-control @error('button_link') is-invalid @enderror" placeholder="https://example.com" value="{{ old('button_link') }}">
                                @error('button_link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-upload me-2"></i> Upload Slide
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Carousel Slides -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-images me-2"></i> Carousel Slides ({{ $carousel->count() }})
                    </h5>
                    @if($carousel->count() > 1)
                        <small class="text-muted mt-2 d-block">
                            <i class="fa fa-info-circle me-1"></i> Drag slides to reorder them
                        </small>
                    @endif
                </div>
                <div class="card-body">
                    @if($carousel->count() > 0)
                        <div id="carouselSortable">
                            @foreach($carousel as $index => $slide)
                                <div class="card mb-3 carousel-slide-item" data-id="{{ $slide->id }}">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Drag Handle -->
                                            <div class="col-auto align-self-center">
                                                <div class="carousel-drag-handle" title="Drag to reorder">
                                                    <i class="fa fa-grip-vertical fa-lg text-muted"></i>
                                                </div>
                                            </div>

                                            <!-- Order Number -->
                                            <div class="col-auto align-self-center">
                                                <span class="badge bg-secondary carousel-order-badge">{{ $index + 1 }}</span>
                                            </div>

                                            <!-- Image -->
                                            <div class="col-md-2">
                                                @if($slide->image_path)
                                                    <img src="{{ asset($slide->image_path) }}" alt="{{ $slide->value }}" class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                                        <i class="fa fa-image fa-3x text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Content -->
                                            <div class="col-md-5">
                                                <h6 class="card-title">
                                                    {{ $slide->value ?? 'Untitled Slide' }}
                                                </h6>
                                                @if($slide->description)
                                                    <p class="card-text text-muted small mb-2">
                                                        {{ Str::limit($slide->description, 100) }}
                                                    </p>
                                                @endif
                                                @if($slide->button_text)
                                                    <small class="badge bg-info">
                                                        <i class="fa fa-link me-1"></i> {{ $slide->button_text }}
                                                    </small>
                                                @endif
                                                <div class="mt-2">
                                                    <small class="text-muted d-block">
                                                        <i class="fa fa-calendar-alt me-1"></i>
                                                        Uploaded {{ $slide->created_at->format('M d, Y') }}
                                                    </small>
                                                </div>
                                            </div>

                                            <!-- Controls -->
                                            <div class="col-md-3 text-end">
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input carousel-toggle" type="checkbox" {{ $slide->is_active ? 'checked' : '' }} data-id="{{ $slide->id }}">
                                                    <label class="form-check-label">Active</label>
                                                </div>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editSlideModal{{ $slide->id }}">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-carousel-slide" data-id="{{ $slide->id }}">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editSlideModal{{ $slide->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Carousel Slide</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.carousel.update', $slide->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Slide Image</label>
                                                        <div class="mb-2">
                                                            @if($slide->image_path)
                                                                <img src="{{ asset($slide->image_path) }}" alt="{{ $slide->value }}" class="img-fluid rounded" style="max-height: 200px;">
                                                            @endif
                                                        </div>
                                                        <input type="file" name="image" class="form-control" accept="image/*">
                                                        <small class="text-muted">Leave empty to keep current image</small>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Slide Title</label>
                                                        <input type="text" name="title" class="form-control" value="{{ $slide->value }}">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Slide Description</label>
                                                        <textarea name="description" class="form-control" rows="3">{{ $slide->description }}</textarea>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Button Text</label>
                                                        <input type="text" name="button_text" class="form-control" value="{{ $slide->button_text }}">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Button Link</label>
                                                        <input type="url" name="button_link" class="form-control" value="{{ $slide->button_link }}">
                                                    </div>

                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active" {{ $slide->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label">Active</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- SortableJS Library -->
                        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

                        <script>
                            // Initialize Sortable for carousel reordering
                            const carouselSortable = document.getElementById('carouselSortable');
                            Sortable.create(carouselSortable, {
                                handle: '.carousel-drag-handle',
                                animation: 150,
                                ghostClass: 'carousel-ghost',
                                dragClass: 'carousel-drag',
                                onEnd: function(evt) {
                                    // Get the new order
                                    const items = document.querySelectorAll('.carousel-slide-item');
                                    const order = [];
                                    let newIndex = 1;

                                    items.forEach((item, index) => {
                                        const id = item.dataset.id;
                                        order.push(id);
                                        // Update visual order badge
                                        item.querySelector('.carousel-order-badge').textContent = newIndex++;
                                    });

                                    // Send new order to backend
                                    fetch('{{ route("admin.carousel.reorder") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        },
                                        body: JSON.stringify({ order: order })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            console.log('Carousel order updated successfully');
                                        }
                                    })
                                    .catch(error => console.error('Error:', error));
                                }
                            });

                            // Delete carousel items
                            document.querySelectorAll('.delete-carousel-slide').forEach(btn => {
                                btn.addEventListener('click', function() {
                                    if (confirm('Are you sure you want to delete this slide?')) {
                                        const form = document.createElement('form');
                                        form.method = 'POST';
                                        form.action = '{{ route("admin.carousel.destroy", ":id") }}'.replace(':id', this.dataset.id);
                                        form.innerHTML = '@csrf @method("DELETE")';
                                        document.body.appendChild(form);
                                        form.submit();
                                    }
                                });
                            });

                            // Toggle active status
                            document.querySelectorAll('.carousel-toggle').forEach(toggle => {
                                toggle.addEventListener('change', function() {
                                    const form = document.createElement('form');
                                    form.method = 'POST';
                                    form.action = '{{ route("admin.carousel.update", ":id") }}'.replace(':id', this.dataset.id);
                                    form.innerHTML = '@csrf @method("PUT")<input type="hidden" name="is_active" value="' + (this.checked ? 1 : 0) + '">';
                                    document.body.appendChild(form);
                                    form.submit();
                                });
                            });
                        </script>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-images fa-4x text-muted mb-3"></i>
                            <p class="text-muted">No carousel slides yet. Upload your first slide to get started!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .carousel-slide-item {
        border-left: 4px solid #0d6efd;
        transition: all 0.3s ease;
    }

    .carousel-slide-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .carousel-drag-handle {
        cursor: grab;
        padding: 0.5rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .carousel-drag-handle:hover {
        background-color: #f0f0f0;
        color: #0d6efd !important;
    }

    .carousel-drag-handle:active {
        cursor: grabbing;
    }

    .carousel-ghost {
        opacity: 0.5;
        background-color: #f8f9fa;
    }

    .carousel-drag {
        opacity: 1;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        border-left-color: #0d6efd !important;
    }

    .carousel-order-badge {
        font-size: 1.2rem;
        padding: 0.5rem 0.75rem;
        min-width: 40px;
        text-align: center;
    }
</style>
@endsection

@extends('layouts.app')

@section('content')
<style>
    .gallery-card {
        transition: all 0.3s ease;
        border: 1px solid #e0e0e0;
        position: relative;
    }
    
    .gallery-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-4px);
    }
    
    .gallery-image-container {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        height: 220px;
    }
    
    .gallery-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .gallery-card:hover .gallery-image-container img {
        transform: scale(1.05);
    }
    
    .gallery-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .page-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 8px;
    }
    
    .page-header h1 {
        margin: 0;
        font-weight: 700;
    }
    
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .action-btn-primary {
        background: white;
        color: #f5576c;
    }
    
    .action-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    .image-count-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4 shadow-sm">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="mb-0">
                        <i class="bi bi-images me-2"></i>Galleries Management
                    </h1>
                    <small class="text-white-50">Manage your photo collections</small>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.galleries.create') }}" class="action-btn action-btn-primary">
                        <i class="bi bi-plus-lg"></i> Add New Gallery
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Alert Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Galleries Grid -->
        @if($galleries->count() > 0)
            <div class="row g-4">
                @foreach($galleries as $gallery)
                    <div class="col-md-6 col-lg-4">
                        <div class="card gallery-card h-100 shadow-sm">
                            <!-- Gallery Image with Badge -->
                            <div class="gallery-image-container">
                                @if($gallery->images()->first())
                                    <img src="{{ asset($gallery->images()->first()->image_path) }}" 
                                         alt="{{ $gallery->title }}" 
                                         class="img-fluid">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 text-white opacity-50">
                                        <i class="bi bi-images" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                
                                <div class="image-count-badge">
                                    <i class="bi bi-file-image me-1"></i>{{ $gallery->images()->count() }} Images
                                </div>
                            </div>

                            <!-- Gallery Content -->
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold text-dark">{{ $gallery->title }}</h5>
                                
                                @if($gallery->description)
                                    <p class="card-text text-muted small mb-3">
                                        {{ Str::limit($gallery->description, 60) }}
                                    </p>
                                @endif

                                <!-- Status & Date -->
                                <div class="mb-3">
                                    <div class="mb-2">
                                        @if($gallery->published)
                                            <span class="gallery-badge bg-success-light text-success">
                                                <i class="bi bi-check-circle me-1"></i> Published
                                            </span>
                                        @else
                                            <span class="gallery-badge bg-warning-light text-warning">
                                                <i class="bi bi-clock me-1"></i> Draft
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if($gallery->event_date)
                                        <small class="text-muted d-block">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            {{ $gallery->event_date->format('M d, Y') }}
                                        </small>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2 mt-auto">
                                    <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-pencil me-1"></i> Edit Gallery
                                    </a>
                                    <div class="d-flex gap-2">
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-sm flex-grow-1"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $gallery->id }}">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal{{ $gallery->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header border-bottom-0">
                                    <h5 class="modal-title">
                                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete Gallery
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete <strong>{{ $gallery->title }}</strong>?</p>
                                    <p class="text-muted small mb-0">This action cannot be undone.</p>
                                </div>
                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form method="POST" action="{{ route('admin.galleries.destroy', $gallery) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete Gallery</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($galleries->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $galleries->links('pagination::bootstrap-4') }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-images" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="mt-3 text-muted">No Galleries Found</h4>
                <p class="text-muted mb-4">Create your first gallery to get started</p>
                <a href="{{ route('admin.galleries.create') }}" class="action-btn action-btn-primary" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                    <i class="bi bi-plus-lg"></i> Create First Gallery
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

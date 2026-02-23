@extends('layouts.app')

@section('content')
<style>
    .service-card {
        transition: all 0.3s ease;
        border: 1px solid #e0e0e0;
    }
    
    .service-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-4px);
    }
    
    .service-image-container {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        height: 200px;
    }
    
    .service-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .service-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .action-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    .action-btn-sm {
        padding: 6px 12px;
        font-size: 0.85rem;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4 shadow-sm">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="mb-0">
                        <i class="bi bi-briefcase me-2"></i>Services Management
                    </h1>
                    <small class="text-white-50">Manage your service offerings</small>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.services.create') }}" class="action-btn action-btn-primary">
                        <i class="bi bi-plus-lg"></i> Add New Service
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

        <!-- Services Grid -->
        @if($services->count() > 0)
            <div class="row g-4">
                @foreach($services as $service)
                    <div class="col-md-6 col-lg-4">
                        <div class="card service-card h-100 shadow-sm">
                            <!-- Service Image -->
                            <div class="service-image-container">
                                @if($service->featured_image)
                                    <img src="{{ asset($service->featured_image) }}" alt="{{ $service->title }}" class="img-fluid">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 text-white opacity-50">
                                        <i class="bi bi-image" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Service Content -->
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold text-dark">{{ $service->title }}</h5>
                                
                                <p class="card-text text-muted small mb-3">
                                    {{ Str::limit($service->subtitle, 60) }}
                                </p>

                                <!-- Status Badge -->
                                <div class="mb-3">
                                    @if($service->published)
                                        <span class="service-badge bg-success-light text-success">
                                            <i class="bi bi-check-circle me-1"></i> Published
                                        </span>
                                    @else
                                        <span class="service-badge bg-warning-light text-warning">
                                            <i class="bi bi-clock me-1"></i> Draft
                                        </span>
                                    @endif
                                </div>

                                <!-- Stats -->
                                <div class="row g-2 mb-3 text-center small">
                                    <div class="col">
                                        <div class="p-2 bg-light rounded">
                                            <div class="fw-bold text-primary">{{ $service->requests()->count() }}</div>
                                            <small class="text-muted">Requests</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2 mt-auto">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-pencil me-1"></i> Edit Service
                                    </a>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.services.requests', $service) }}" class="btn btn-outline-info btn-sm flex-grow-1">
                                            <i class="bi bi-chat-dots me-1"></i> View Requests
                                        </a>
                                        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="flex-grow-1" onsubmit="return confirm('Are you sure you want to delete this service?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                <i class="bi bi-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($services->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $services->links('pagination::bootstrap-4') }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-briefcase-na" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="mt-3 text-muted">No Services Found</h4>
                <p class="text-muted mb-4">Create your first service to get started</p>
                <a href="{{ route('admin.services.create') }}" class="action-btn action-btn-primary">
                    <i class="bi bi-plus-lg"></i> Create First Service
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

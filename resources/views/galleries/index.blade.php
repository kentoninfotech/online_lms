@extends('layouts.landing')
@section('content')

<style>
    .galleries-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4rem 0;
        margin-bottom: 3rem;
        text-align: center;
    }

    .galleries-hero h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .galleries-hero p {
        font-size: 1.1rem;
        opacity: 0.95;
        max-width: 600px;
        margin: 0 auto;
    }

    .gallery-card {
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
        height: 100%;
    }

    .gallery-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .gallery-image-container {
        position: relative;
        overflow: hidden;
        background: #f0f0f0;
        height: 240px;
    }

    .gallery-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .gallery-card:hover .gallery-image-container img {
        transform: scale(1.08);
    }

    .gallery-card:hover .gallery-overlay {
        opacity: 1;
    }

    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .image-count-badge {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .gallery-content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .gallery-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.75rem;
        transition: color 0.3s ease;
    }

    .gallery-card:hover .gallery-title {
        color: #667eea;
    }

    .gallery-meta {
        font-size: 0.9rem;
        color: #666;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .gallery-description {
        font-size: 0.95rem;
        color: #666;
        line-height: 1.5;
        flex-grow: 1;
        margin-top: auto;
        margin-bottom: 1rem;
    }

    .view-btn {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        text-align: center;
        border: none;
        width: 100%;
    }

    .view-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        background: #f8f9fa;
        border-radius: 12px;
        border: 2px dashed #dee2e6;
    }

    .empty-state i {
        font-size: 3rem;
        color: #adb5bd;
        margin-bottom: 1rem;
    }

    .empty-state h4 {
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #6c757d;
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .galleries-hero h1 {
            font-size: 2rem;
        }

        .galleries-hero p {
            font-size: 1rem;
        }
    }
</style>

<div class="galleries-hero">
    <div class="container-lg">
        <h1><i class="bi bi-images me-2"></i>Our Galleries</h1>
        <p>Explore our collection of memories and moments from our community</p>
    </div>
</div>

<div class="container-lg mb-5">
    @if($galleries->count() > 0)
        <div class="row g-4">
            @foreach($galleries as $gallery)
                <div class="col-md-6 col-lg-6">
                    <div class="card gallery-card h-100 shadow-sm">
                        <!-- Image Section -->
                        <div class="gallery-image-container">
                            @if($gallery->images()->first())
                                <img src="{{ asset($gallery->images()->first()->image_path) }}" alt="{{ $gallery->title }}">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                    <i class="bi bi-images text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                            
                            <div class="gallery-overlay"></div>
                            
                            @if($gallery->images()->count() > 0)
                                <div class="image-count-badge">
                                    <i class="bi bi-file-image"></i>{{ $gallery->images()->count() }}
                                </div>
                            @endif
                        </div>

                        <!-- Content Section -->
                        <div class="gallery-content">
                            <h5 class="gallery-title">{{ $gallery->title }}</h5>
                            
                            @if($gallery->event_name)
                                <div class="gallery-meta">
                                    <i class="bi bi-geo-alt text-primary"></i>
                                    <span>{{ $gallery->event_name }}</span>
                                </div>
                            @endif
                            
                            @if($gallery->event_date)
                                <div class="gallery-meta">
                                    <i class="bi bi-calendar-event text-primary"></i>
                                    <span>{{ $gallery->event_date->format('M d, Y') }}</span>
                                </div>
                            @endif
                            
                            @if($gallery->description)
                                <p class="gallery-description">{{ Str::limit($gallery->description, 120) }}</p>
                            @endif
                            
                            <a href="{{ route('galleries.show', $gallery) }}" class="view-btn">
                                <i class="bi bi-arrow-right me-2"></i>View Gallery
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-image"></i>
            <h4>No Galleries Available</h4>
            <p>Check back soon for our latest gallery collections</p>
        </div>
    @endif
</div>

@endsection

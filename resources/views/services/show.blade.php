@extends('layouts.landing')

@section('content')

<style>
    .service-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 0;
        margin-bottom: 2rem;
    }

    .service-hero h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .service-hero .breadcrumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 6px;
        padding: 0.5rem 1rem;
        display: inline-block;
    }

    .service-hero .breadcrumb-item {
        color: rgba(255, 255, 255, 0.8);
    }

    .service-hero .breadcrumb-item a {
        color: white;
        text-decoration: none;
    }

    .service-hero .breadcrumb-item a:hover {
        text-decoration: underline;
    }

    .service-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .service-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .service-subtitle {
        font-size: 1.2rem;
        color: #667eea;
        margin-bottom: 2rem;
    }

    .service-content {
        font-size: 1rem;
        line-height: 1.8;
        color: #555;
        margin-bottom: 2rem;
    }

    .service-content p {
        margin-bottom: 1rem;
    }

    .service-content h2 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #333;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    .service-content h3 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #333;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .service-content ul, 
    .service-content ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }

    .service-content li {
        margin-bottom: 0.5rem;
    }

    /* Request Form Sidebar */
    .request-form-sidebar {
        position: sticky;
        top: 30px;
    }

    .request-form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .request-form-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .request-form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
    }

    .request-form-header h3 {
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .request-form-header i {
        font-size: 1.2rem;
    }

    .request-form-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-weight: 600;
        font-size: 0.95rem;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .form-label .required {
        color: #dc3545;
    }

    .form-control, .form-select {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus, 
    .form-select:focus {
        color: #495057;
        background-color: #fff;
        border-color: #667eea;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .form-control::placeholder {
        color: #adb5bd;
        opacity: 1;
    }

    .submit-btn {
        display: block;
        width: 100%;
        padding: 0.875rem 1rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .alert-danger ul {
        margin: 0;
        padding-left: 1.5rem;
    }

    .alert-danger li {
        margin-bottom: 0.25rem;
    }

    /* Related Services */
    .related-services-section {
        margin-top: 4rem;
        padding-top: 3rem;
        border-top: 2px solid #e9ecef;
    }

    .related-services-title {
        font-size: 2rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 2rem;
    }

    .related-service-card {
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
        height: 100%;
    }

    .related-service-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .related-service-image {
        width: 100%;
        height: 160px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .related-service-card:hover .related-service-image {
        transform: scale(1.05);
    }

    .related-service-content {
        padding: 1.25rem;
    }

    .related-service-title {
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .related-service-card:hover .related-service-title {
        color: #667eea;
    }

    .related-service-description {
        font-size: 0.9rem;
        color: #666;
        margin: 0;
    }

    @media (max-width: 992px) {
        .service-hero h1 {
            font-size: 2rem;
        }

        .service-title {
            font-size: 1.8rem;
        }

        .request-form-sidebar {
            margin-top: 2rem;
        }
    }
</style>

<div class="service-hero">
    <div class="container-lg">
        <nav class="breadcrumb">
            <span class="breadcrumb-item">
                <a href="{{ route('services.index') }}">
                    <i class="bi bi-house me-2"></i>Services
                </a>
            </span>
            <span class="breadcrumb-item ms-2">/ {{ $service->title }}</span>
        </nav>
    </div>
</div>

<div class="container-lg">
    <div class="row g-5">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Service Image -->
            @if($service->featured_image)
                <img src="{{ asset($service->featured_image) }}" alt="{{ $service->title }}" class="service-image">
            @endif

            <!-- Service Content -->
            <h1 class="service-title">{{ $service->title }}</h1>
            <p class="service-subtitle">{{ $service->subtitle }}</p>

            <div class="service-content">
                {!! $service->body !!}
            </div>
        </div>

        <!-- Request Form Sidebar -->
        <div class="col-lg-4">
            <div class="request-form-sidebar">
                <div class="request-form-card">
                    <!-- Header -->
                    <div class="request-form-header">
                        <h3>
                            <i class="bi bi-chat-dots"></i>
                            Request This Service
                        </h3>
                    </div>

                    <!-- Body -->
                    <div class="request-form-body">
                        @if (session('success'))
                            <div class="alert-success">
                                <i class="bi bi-check-circle"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert-danger">
                                <p class="mb-2"><strong>Please correct the following:</strong></p>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('service.request.store', $service) }}">
                            @csrf

                            <!-- Name -->
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    Your Name
                                    <span class="required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name', auth()->user()->name ?? '') }}" 
                                    required 
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Enter your full name">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    Email Address
                                    <span class="required">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email', auth()->user()->email ?? '') }}" 
                                    required 
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="Enter your email address">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="form-group">
                                <label for="phone" class="form-label">
                                    Phone Number
                                </label>
                                <input 
                                    type="tel" 
                                    id="phone" 
                                    name="phone" 
                                    value="{{ old('phone') }}" 
                                    class="form-control @error('phone') is-invalid @enderror"
                                    placeholder="Enter your phone number">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Message -->
                            <div class="form-group">
                                <label for="message" class="form-label">
                                    Additional Details
                                </label>
                                <textarea 
                                    id="message" 
                                    name="message" 
                                    rows="4" 
                                    class="form-control @error('message') is-invalid @enderror"
                                    placeholder="Tell us more about your request...">{{ old('message') }}</textarea>
                                @error('message')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="submit-btn">
                                <i class="bi bi-send me-2"></i>Send Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Services -->
    <div class="related-services-section">
        <h2 class="related-services-title">
            <i class="bi bi-stars me-2"></i>Other Services
        </h2>
        <div class="row g-4">
            @foreach(\App\Models\Service::published()->where('id', '!=', $service->id)->limit(3)->get() as $relatedService)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('services.show', $relatedService) }}" class="text-decoration-none">
                        <div class="card related-service-card shadow-sm">
                            @if($relatedService->featured_image)
                                <img src="{{ asset($relatedService->featured_image) }}" alt="{{ $relatedService->title }}" class="related-service-image">
                            @else
                                <div style="width: 100%; height: 160px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-image text-white" style="font-size: 2rem;"></i>
                                </div>
                            @endif
                            <div class="related-service-content">
                                <h4 class="related-service-title">{{ $relatedService->title }}</h4>
                                <p class="related-service-description">{{ Str::limit($relatedService->subtitle, 60) }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

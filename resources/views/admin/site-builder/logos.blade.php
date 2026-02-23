@extends('layouts.app')

@section('title', 'Edit Logos & Branding')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2">
                <i class="fas fa-image me-2"></i>Logos & Branding
            </h1>
            <p class="text-muted">Customize your site's logos and branding text</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">Validation Errors</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.site-builder.update-logos') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <!-- Site Name Section -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-heading me-2"></i>Site Name & Tagline
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="site_name" class="form-label">Site Name</label>
                            <input type="text" class="form-control" id="site_name" name="site_name" 
                                value="{{ $logoSettings['site_name'] ?? '' }}" placeholder="e.g., Online LMS">
                            <small class="text-muted">Your main site title displayed in navigation</small>
                        </div>

                        <div class="mb-0">
                            <label for="site_tagline" class="form-label">Tagline / Motto</label>
                            <input type="text" class="form-control" id="site_tagline" name="site_tagline" 
                                value="{{ $logoSettings['site_tagline'] ?? '' }}" placeholder="e.g., Learn Anytime, Anywhere">
                            <small class="text-muted">A short description shown with your brand</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-eye me-2"></i>Live Preview
                        </h5>
                    </div>
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <h4 id="preview_name" class="mb-1">{{ $logoSettings['site_name'] ?? 'Site Name' }}</h4>
                            <p id="preview_tagline" class="text-muted small mb-0">
                                {{ $logoSettings['site_tagline'] ?? 'Your tagline here' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logo Uploads Section -->
        <div class="row">
            <!-- Light Logo -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-sun me-2"></i>Light Logo
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @if ($logoSettings['logo_light'])
                                <div class="mb-3">
                                    <img src="{{ asset($logoSettings['logo_light']) }}" 
                                        alt="Light Logo" class="img-fluid" style="max-height: 100px;">
                                </div>
                            @endif
                            
                            <label for="logo_light" class="form-label">Upload Logo</label>
                            <input type="file" class="form-control" id="logo_light" name="logo_light" 
                                accept="image/png,image/jpeg,image/gif,image/webp">
                            <small class="text-muted d-block mt-2">PNG, JPG, GIF, or WebP • Max 2MB</small>
                        </div>
                        <small class="text-muted">For light backgrounds</small>
                    </div>
                </div>
            </div>

            <!-- Dark Logo -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-moon me-2"></i>Dark Logo
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @if ($logoSettings['logo_dark'])
                                <div class="mb-3">
                                    <div class="bg-dark p-3 rounded">
                                        <img src="{{ asset($logoSettings['logo_dark']) }}" 
                                            alt="Dark Logo" class="img-fluid" style="max-height: 100px;">
                                    </div>
                                </div>
                            @endif
                            
                            <label for="logo_dark" class="form-label">Upload Logo</label>
                            <input type="file" class="form-control" id="logo_dark" name="logo_dark" 
                                accept="image/png,image/jpeg,image/gif,image/webp">
                            <small class="text-muted d-block mt-2">PNG, JPG, GIF, or WebP • Max 2MB</small>
                        </div>
                        <small class="text-muted">For dark backgrounds</small>
                    </div>
                </div>
            </div>

            <!-- Favicon -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-star me-2"></i>Favicon
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @if ($logoSettings['favicon'])
                                <div class="mb-3">
                                    <img src="{{ asset($logoSettings['favicon']) }}" 
                                        alt="Favicon" class="img-fluid" style="max-height: 50px;">
                                </div>
                            @endif
                            
                            <label for="favicon" class="form-label">Upload Favicon</label>
                            <input type="file" class="form-control" id="favicon" name="favicon" 
                                accept="image/png,image/x-icon">
                            <small class="text-muted d-block mt-2">PNG or ICO • Max 1MB • 64x64px recommended</small>
                        </div>
                        <small class="text-muted">Browser tab icon</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('admin.site-builder.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // Live preview for site name and tagline
    document.getElementById('site_name').addEventListener('input', function() {
        document.getElementById('preview_name').textContent = this.value || 'Site Name';
    });

    document.getElementById('site_tagline').addEventListener('input', function() {
        document.getElementById('preview_tagline').textContent = this.value || 'Your tagline here';
    });
</script>
@endsection

@extends('layouts.app')

@section('title', 'Design & Layout Settings')

@section('content')

<div class="pc-container" style="margin-top: -2rem;">
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.site-builder.index') }}">Site Builder</a></li>
                        <li class="breadcrumb-item active">Design & Layout</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h4 class="m-0">Design & Layout Settings</h4>
                        <p class="text-muted">Customize main element, navbar, and container styles</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="pc-container" style="margin-top: 0.5rem;">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>
            <strong>Validation Errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-palette2 me-2"></i>Design & Layout Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.site-builder.update-design') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Main Element Section -->
                        <h5 class="mb-4 mt-4 fw-bold">
                            <i class="bi bi-layout-text-sidebar me-2 text-primary"></i>Main Element
                        </h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="main_bg_color" class="form-label">Background Color</label>
                                <div class="input-group">
                                    <input type="color" 
                                           class="form-control form-control-color" 
                                           id="main_bg_color" 
                                           name="main_bg_color" 
                                           value="{{ $designSettings['main_bg_color'] ?? '#ffffff' }}"
                                           style="max-width: 70px;">
                                    <input type="text" 
                                           class="form-control" 
                                           id="main_bg_color_text" 
                                           value="{{ $designSettings['main_bg_color'] ?? '#ffffff' }}"
                                           readonly>
                                </div>
                                <small class="text-muted">Set the background color for the main content element</small>
                            </div>

                            <div class="col-md-6">
                                <label for="main_bg_opacity" class="form-label">Background Opacity</label>
                                <div class="input-group">
                                    <input type="range" 
                                           class="form-range" 
                                           id="main_bg_opacity" 
                                           name="main_bg_opacity" 
                                           min="0" 
                                           max="100" 
                                           value="{{ $designSettings['main_bg_opacity'] ?? '100' }}">
                                    <span class="input-group-text w-25 text-center" id="opacity_display">
                                        {{ $designSettings['main_bg_opacity'] ?? '100' }}%
                                    </span>
                                </div>
                                <small class="text-muted">Opacity percentage (0-100)</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="main_bg_image" class="form-label">Background Image</label>
                            <input type="file" 
                                   class="form-control @error('main_bg_image') is-invalid @enderror" 
                                   id="main_bg_image" 
                                   name="main_bg_image"
                                   accept="image/*">
                            @error('main_bg_image')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            <small class="text-muted d-block mt-2">PNG, JPG, GIF up to 5MB. Leave empty to remove background image.</small>
                            
                            @if($designSettings['main_bg_image'])
                                <div class="mt-3">
                                    <p class="text-muted mb-2"><strong>Current Background Image:</strong></p>
                                    <img src="{{ asset($designSettings['main_bg_image']) }}" alt="Background image" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            @endif
                        </div>

                        <hr class="my-4">

                        <!-- Navbar Section -->
                        <h5 class="mb-4 fw-bold">
                            <i class="bi bi-window-dock me-2 text-success"></i>Top Navbar
                        </h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="navbar_bg_color" class="form-label">Navbar Background</label>
                                <input type="text" 
                                       class="form-control @error('navbar_bg_color') is-invalid @enderror" 
                                       id="navbar_bg_color" 
                                       name="navbar_bg_color" 
                                       value="{{ $designSettings['navbar_bg_color'] ?? 'linear-gradient(135deg, #fff 0%, #f8f9fa 100%)' }}"
                                       placeholder="e.g., #ffffff or linear-gradient(135deg, #fff 0%, #f8f9fa 100%)">
                                @error('navbar_bg_color')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                <small class="text-muted d-block mt-2">Use hex color (#fff), rgb, or gradient. Examples:<br>
                                    • Solid: #ffffff<br>
                                    • Gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%)
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label for="navbar_text_color" class="form-label">Navbar Text Color</label>
                                <div class="input-group">
                                    <input type="color" 
                                           class="form-control form-control-color" 
                                           id="navbar_text_color" 
                                           name="navbar_text_color" 
                                           value="{{ $designSettings['navbar_text_color'] ?? '#333333' }}"
                                           style="max-width: 70px;">
                                    <input type="text" 
                                           class="form-control" 
                                           id="navbar_text_color_text" 
                                           value="{{ $designSettings['navbar_text_color'] ?? '#333333' }}"
                                           readonly>
                                </div>
                                <small class="text-muted">Color for navbar links and text</small>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Navbar Preview:</strong> The navbar uses the gradient or color you set above for its background. Links will use the text color you specify.
                        </div>

                        <hr class="my-4">

                        <!-- First Container Section -->
                        <h5 class="mb-4 fw-bold">
                            <i class="bi bi-box me-2 text-warning"></i>First Container
                        </h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="container_bg_color" class="form-label">Container Background Color</label>
                                <div class="input-group">
                                    <input type="color" 
                                           class="form-control form-control-color" 
                                           id="container_bg_color" 
                                           name="container_bg_color" 
                                           value="{{ $designSettings['container_bg_color'] ?? '#f8f9fa' }}"
                                           style="max-width: 70px;">
                                    <input type="text" 
                                           class="form-control" 
                                           id="container_bg_color_text" 
                                           value="{{ $designSettings['container_bg_color'] ?? '#f8f9fa' }}"
                                           readonly>
                                </div>
                                <small class="text-muted">Background color for the first page container/section</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Form Actions -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Save Design Settings
                            </button>
                            <a href="{{ route('admin.site-builder.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Site Builder
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-eye me-2"></i>Live Preview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Live Updates:</strong> After saving, changes will be visible on the public landing page. 
                        <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-info ms-2">
                            <i class="bi bi-box-arrow-up-right"></i> View Live Site
                        </a>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Main Element Preview</h6>
                            <div style="background-color: {{ $designSettings['main_bg_color'] ?? '#ffffff' }}; 
                                        @if($designSettings['main_bg_image']) background-image: url('{{ asset($designSettings['main_bg_image']) }}'); background-size: cover; @endif
                                        opacity: {{ ($designSettings['main_bg_opacity'] ?? 100) / 100 }}; 
                                        padding: 30px; 
                                        border: 1px solid #ddd; 
                                        border-radius: 8px;">
                                <p class="text-muted">This is how your main element background will look</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Navbar Preview</h6>
                            <div style="background: {{ $designSettings['navbar_bg_color'] ?? 'linear-gradient(135deg, #fff 0%, #f8f9fa 100%)' }}; 
                                        padding: 15px 20px; 
                                        border-radius: 8px;
                                        border: 1px solid #ddd;">
                                <a href="#" style="color: {{ $designSettings['navbar_text_color'] ?? '#333' }}; text-decoration: none; margin-right: 20px; font-weight: 500;">Home</a>
                                <a href="#" style="color: {{ $designSettings['navbar_text_color'] ?? '#333' }}; text-decoration: none; margin-right: 20px; font-weight: 500;">Courses</a>
                                <a href="#" style="color: {{ $designSettings['navbar_text_color'] ?? '#333' }}; text-decoration: none; font-weight: 500;">Services</a>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">First Container Preview</h6>
                            <div style="background-color: {{ $designSettings['container_bg_color'] ?? '#f8f9fa' }}; 
                                        padding: 30px; 
                                        border: 1px solid #ddd; 
                                        border-radius: 8px;">
                                <p class="text-muted">This is how your first container will look</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update color inputs and text displays
document.getElementById('main_bg_color').addEventListener('change', function() {
    document.getElementById('main_bg_color_text').value = this.value;
});

document.getElementById('navbar_text_color').addEventListener('change', function() {
    document.getElementById('navbar_text_color_text').value = this.value;
});

document.getElementById('container_bg_color').addEventListener('change', function() {
    document.getElementById('container_bg_color_text').value = this.value;
});

// Update opacity display
document.getElementById('main_bg_opacity').addEventListener('input', function() {
    document.getElementById('opacity_display').textContent = this.value + '%';
});
</script>

@endsection

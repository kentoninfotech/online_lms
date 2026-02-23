@extends('layouts.app')

@section('title', 'Edit Colors')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2">
                <i class="fas fa-palette me-2"></i>Color Theme
            </h1>
            <p class="text-muted">Customize your site's color palette to match your brand</p>
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

    <form action="{{ route('admin.site-builder.update-colors') }}" method="POST">
        @csrf
        
        <div class="row">
            <!-- Primary Colors -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-swatches me-2"></i>Primary Colors
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Primary Color -->
                        <div class="mb-4">
                            <label for="primary_color" class="form-label d-flex align-items-center">
                                <span>Primary Color</span>
                                <div class="ms-3">
                                    <input type="color" id="primary_color" name="primary_color" 
                                        value="{{ $colorSettings['primary_color'] }}" style="width: 60px; height: 50px; border: none; border-radius: 4px; cursor: pointer;">
                                </div>
                            </label>
                            <p class="mb-0">
                                <code class="bg-light px-2 py-1 rounded">{{ $colorSettings['primary_color'] }}</code>
                            </p>
                            <small class="text-muted">Main brand color for buttons, links, and highlights</small>
                        </div>

                        <!-- Secondary Color -->
                        <div class="mb-4">
                            <label for="secondary_color" class="form-label d-flex align-items-center">
                                <span>Secondary Color</span>
                                <div class="ms-3">
                                    <input type="color" id="secondary_color" name="secondary_color" 
                                        value="{{ $colorSettings['secondary_color'] }}" style="width: 60px; height: 50px; border: none; border-radius: 4px; cursor: pointer;">
                                </div>
                            </label>
                            <p class="mb-0">
                                <code class="bg-light px-2 py-1 rounded">{{ $colorSettings['secondary_color'] }}</code>
                            </p>
                            <small class="text-muted">Secondary accent color for complementary UI elements</small>
                        </div>

                        <!-- Background Color -->
                        <div class="mb-0">
                            <label for="background_color" class="form-label d-flex align-items-center">
                                <span>Background Color</span>
                                <div class="ms-3">
                                    <input type="color" id="background_color" name="background_color" 
                                        value="{{ $colorSettings['background_color'] }}" style="width: 60px; height: 50px; border: none; border-radius: 4px; cursor: pointer;">
                                </div>
                            </label>
                            <p class="mb-0">
                                <code class="bg-light px-2 py-1 rounded">{{ $colorSettings['background_color'] }}</code>
                            </p>
                            <small class="text-muted">Page background color</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Semantic Colors -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-traffic-light me-2"></i>Status Colors
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Success Color -->
                        <div class="mb-4">
                            <label for="success_color" class="form-label d-flex align-items-center">
                                <span>Success Color</span>
                                <div class="ms-3">
                                    <input type="color" id="success_color" name="success_color" 
                                        value="{{ $colorSettings['success_color'] }}" style="width: 60px; height: 50px; border: none; border-radius: 4px; cursor: pointer;">
                                </div>
                            </label>
                            <p class="mb-0">
                                <code class="bg-light px-2 py-1 rounded">{{ $colorSettings['success_color'] }}</code>
                            </p>
                            <small class="text-muted">For success messages and positive actions</small>
                        </div>

                        <!-- Warning Color -->
                        <div class="mb-4">
                            <label for="warning_color" class="form-label d-flex align-items-center">
                                <span>Warning Color</span>
                                <div class="ms-3">
                                    <input type="color" id="warning_color" name="warning_color" 
                                        value="{{ $colorSettings['warning_color'] }}" style="width: 60px; height: 50px; border: none; border-radius: 4px; cursor: pointer;">
                                </div>
                            </label>
                            <p class="mb-0">
                                <code class="bg-light px-2 py-1 rounded">{{ $colorSettings['warning_color'] }}</code>
                            </p>
                            <small class="text-muted">For warning messages and caution alerts</small>
                        </div>

                        <!-- Danger Color -->
                        <div class="mb-4">
                            <label for="danger_color" class="form-label d-flex align-items-center">
                                <span>Danger Color</span>
                                <div class="ms-3">
                                    <input type="color" id="danger_color" name="danger_color" 
                                        value="{{ $colorSettings['danger_color'] }}" style="width: 60px; height: 50px; border: none; border-radius: 4px; cursor: pointer;">
                                </div>
                            </label>
                            <p class="mb-0">
                                <code class="bg-light px-2 py-1 rounded">{{ $colorSettings['danger_color'] }}</code>
                            </p>
                            <small class="text-muted">For error messages and destructive actions</small>
                        </div>

                        <!-- Info Color -->
                        <div class="mb-0">
                            <label for="info_color" class="form-label d-flex align-items-center">
                                <span>Info Color</span>
                                <div class="ms-3">
                                    <input type="color" id="info_color" name="info_color" 
                                        value="{{ $colorSettings['info_color'] }}" style="width: 60px; height: 50px; border: none; border-radius: 4px; cursor: pointer;">
                                </div>
                            </label>
                            <p class="mb-0">
                                <code class="bg-light px-2 py-1 rounded">{{ $colorSettings['info_color'] }}</code>
                            </p>
                            <small class="text-muted">For informational messages</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Text Color -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-font me-2"></i>Text Color
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="text_color" class="form-label d-flex align-items-center">
                                <span>Primary Text Color</span>
                                <div class="ms-3">
                                    <input type="color" id="text_color" name="text_color" 
                                        value="{{ $colorSettings['text_color'] }}" style="width: 60px; height: 50px; border: none; border-radius: 4px; cursor: pointer;">
                                </div>
                            </label>
                            <p class="mb-0">
                                <code class="bg-light px-2 py-1 rounded">{{ $colorSettings['text_color'] }}</code>
                            </p>
                            <small class="text-muted">Main text color throughout the site</small>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <small><strong>Tip:</strong> Ensure good contrast between text and background colors for readability</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Color Preview -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-eye me-2"></i>Live Preview
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <button type="button" class="btn w-100" 
                                    style="background-color: {{ $colorSettings['primary_color'] }}; color: white;">
                                    Primary
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn w-100" 
                                    style="background-color: {{ $colorSettings['secondary_color'] }}; color: white;">
                                    Secondary
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-success w-100" 
                                    style="background-color: {{ $colorSettings['success_color'] }}; color: white;">
                                    Success
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn w-100" 
                                    style="background-color: {{ $colorSettings['warning_color'] }}; color: white;">
                                    Warning
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn w-100" 
                                    style="background-color: {{ $colorSettings['danger_color'] }}; color: white;">
                                    Danger
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn w-100" 
                                    style="background-color: {{ $colorSettings['info_color'] }}; color: white;">
                                    Info
                                </button>
                            </div>
                        </div>
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
                    <i class="fas fa-save me-2"></i>Save Colors
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // Update button colors live when color inputs change
    const colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Update preview buttons
            const buttons = document.querySelectorAll('.card-body .btn');
            buttons.forEach((btn, index) => {
                if (index < colorInputs.length) {
                    const colorValue = colorInputs[index].value;
                    // Determine which button to update based on the color
                }
            });
        });
    });
</script>
@endsection

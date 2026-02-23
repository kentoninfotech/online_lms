@extends('layouts.app')

@section('title', 'Edit Typography')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2">
                <i class="fas fa-font me-2"></i>Typography Settings
            </h1>
            <p class="text-muted">Customize fonts and text sizes for your site</p>
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

    <form action="{{ route('admin.site-builder.update-typography') }}" method="POST">
        @csrf
        
        <div class="row">
            <!-- Font Selection -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-heading me-2"></i>Font Families
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Primary Font -->
                        <div class="mb-4">
                            <label for="primary_font" class="form-label">
                                Primary Font
                                <small class="text-muted">(Headings)</small>
                            </label>
                            <select class="form-select" id="primary_font" name="primary_font">
                                @foreach ($googleFonts as $font)
                                    <option value="{{ $font }}" 
                                        @selected($typographySettings['primary_font'] === $font)>
                                        {{ $font }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-2">Used for headings and titles</small>
                        </div>

                        <!-- Secondary Font -->
                        <div class="mb-0">
                            <label for="secondary_font" class="form-label">
                                Secondary Font
                                <small class="text-muted">(Body Text)</small>
                            </label>
                            <select class="form-select" id="secondary_font" name="secondary_font">
                                @foreach ($googleFonts as $font)
                                    <option value="{{ $font }}" 
                                        @selected($typographySettings['secondary_font'] === $font)>
                                        {{ $font }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-2">Used for body text and paragraphs</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Font Size Settings -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-ruler-vertical me-2"></i>Font Sizes
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Heading Size -->
                        <div class="mb-4">
                            <label for="heading_size" class="form-label">
                                Heading Size
                                <span class="badge bg-info">
                                    <span id="heading_size_display">{{ $typographySettings['heading_size'] }}</span>px
                                </span>
                            </label>
                            <input type="range" class="form-range" id="heading_size" name="heading_size" 
                                min="16" max="72" step="1" value="{{ $typographySettings['heading_size'] }}">
                            <small class="text-muted d-block mt-2">For page headings and section titles</small>
                            <div class="mt-3" style="font-size: {{ $typographySettings['heading_size'] }}px; font-family: {{ $typographySettings['primary_font'] }};">
                                Preview Heading
                            </div>
                        </div>

                        <!-- Body Size -->
                        <div class="mb-0">
                            <label for="body_size" class="form-label">
                                Body Text Size
                                <span class="badge bg-info">
                                    <span id="body_size_display">{{ $typographySettings['body_size'] }}</span>px
                                </span>
                            </label>
                            <input type="range" class="form-range" id="body_size" name="body_size" 
                                min="12" max="24" step="1" value="{{ $typographySettings['body_size'] }}">
                            <small class="text-muted d-block mt-2">For paragraphs and regular text</small>
                            <div class="mt-3" style="font-size: {{ $typographySettings['body_size'] }}px; font-family: {{ $typographySettings['secondary_font'] }}; line-height: 1.6;">
                                This is how your body text will look like with the selected font and size.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Preview -->
        <div class="row mt-4">
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-eye me-2"></i>Full Typography Preview
                        </h5>
                    </div>
                    <div class="card-body">
                        <div style="font-family: {{ $typographySettings['primary_font'] }}; padding: 20px; background-color: #f8f9fa; border-radius: 8px;">
                            <h1 id="preview_h1" style="font-size: {{ $typographySettings['heading_size'] }}px; margin-bottom: 1rem;">
                                Main Heading (H1)
                            </h1>
                            <h2 id="preview_h2" style="font-size: {{ $typographySettings['heading_size'] * 0.85 }}px; margin-bottom: 0.5rem;">
                                Subheading (H2)
                            </h2>
                        </div>

                        <div style="font-family: {{ $typographySettings['secondary_font'] }}; padding: 20px; margin-top: 20px; background-color: #ffffff; border: 1px solid #e9ecef; border-radius: 8px;">
                            <p id="preview_body" style="font-size: {{ $typographySettings['body_size'] }}px; line-height: 1.6; margin-bottom: 1rem;">
                                This is how your body text will look. The font family and size can be customized here. 
                                Make sure to choose fonts that are easy to read and match your brand identity. 
                                Good typography enhances the user experience and makes your content more accessible.
                            </p>
                            <p id="preview_body" style="font-size: {{ $typographySettings['body_size'] * 0.9 }}px; line-height: 1.6; color: #6c757d;">
                                Secondary text (slightly smaller) - Perfect for captions and descriptions.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tips & Best Practices -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="alert alert-info" role="alert">
                    <h6 class="alert-heading">
                        <i class="fas fa-lightbulb me-2"></i>Typography Tips
                    </h6>
                    <ul class="mb-0 small">
                        <li>Choose fonts that are web-safe and load quickly</li>
                        <li>Maintain good contrast between heading and body fonts</li>
                        <li>Keep heading sizes between 24px-40px for better readability</li>
                        <li>Body text should be between 14px-18px for comfortable reading</li>
                        <li>Use consistent typography across all pages for a professional look</li>
                        <li>Test your typography on different devices for better user experience</li>
                    </ul>
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
                    <i class="fas fa-save me-2"></i>Save Typography Settings
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // Update heading font live
    document.getElementById('primary_font').addEventListener('change', function() {
        const headings = document.querySelectorAll('#preview_h1, #preview_h2');
        headings.forEach(h => {
            h.style.fontFamily = this.value;
        });
    });

    // Update body font live
    document.getElementById('secondary_font').addEventListener('change', function() {
        const bodyTexts = document.querySelectorAll('#preview_body');
        bodyTexts.forEach(p => {
            p.style.fontFamily = this.value;
        });
    });

    // Update heading size live
    document.getElementById('heading_size').addEventListener('input', function() {
        document.getElementById('heading_size_display').textContent = this.value;
        document.getElementById('preview_h1').style.fontSize = this.value + 'px';
        document.getElementById('preview_h2').style.fontSize = (this.value * 0.85) + 'px';
    });

    // Update body size live
    document.getElementById('body_size').addEventListener('input', function() {
        document.getElementById('body_size_display').textContent = this.value;
        const bodyTexts = document.querySelectorAll('#preview_body');
        bodyTexts.forEach((p, idx) => {
            if (idx === 0) {
                p.style.fontSize = this.value + 'px';
            } else {
                p.style.fontSize = (this.value * 0.9) + 'px';
            }
        });
    });
</script>
@endsection

@extends('layouts.admin')

@section('title', 'Page Titles - Site Builder')

@section('content')

<style>
    .page-title-card {
        border-left: 4px solid #2563EB;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #2563EB;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1D4ED8 0%, #1E40AF 100%);
    }

    .preview-card {
        background: linear-gradient(135deg, #f3f4f6 0%, #f9fafb 100%);
        border-radius: 8px;
        padding: 1.5rem;
        margin-top: 1rem;
    }

    .preview-text {
        color: #333;
        margin: 0.5rem 0;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4" data-aos="fade-up">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h2 fw-bold text-dark">Page Titles & Subtitles</h1>
                    <p class="text-muted">Customize titles and subtitles for your key pages</p>
                </div>
                <a href="{{ route('admin.site-builder.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Site Builder
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Validation Error!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Form -->
    <div class="row">
        <div class="col-lg-8" data-aos="fade-up">
            <form method="POST" action="{{ route('admin.site-builder.update-page-titles') }}" class="needs-validation">
                @csrf

                <!-- Landing Page Section -->
                <div class="card shadow-sm mb-4 page-title-card">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-house-fill text-primary"></i> Landing Page
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Landing Page Title -->
                        <div class="mb-3">
                            <label for="landing_page_title" class="form-label fw-bold">Page Title</label>
                            <input type="text" 
                                   class="form-control @error('landing_page_title') is-invalid @enderror" 
                                   id="landing_page_title" 
                                   name="landing_page_title"
                                   value="{{ old('landing_page_title', \App\Models\HomepageSetting::getSetting('pages', 'landing_page_title') ?? 'LearnSmart - Master Your Future with Expert-Led Courses') }}"
                                   placeholder="Enter page title"
                                   maxlength="255">
                            <small class="form-text text-muted d-block mt-2">This title appears at the top of your landing page</small>
                            @error('landing_page_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Landing Page Subtitle -->
                        <div class="mb-3">
                            <label for="landing_page_subtitle" class="form-label fw-bold">Page Subtitle</label>
                            <textarea class="form-control @error('landing_page_subtitle') is-invalid @enderror" 
                                      id="landing_page_subtitle" 
                                      name="landing_page_subtitle"
                                      rows="3"
                                      placeholder="Enter page subtitle"
                                      maxlength="500">{{ old('landing_page_subtitle', \App\Models\HomepageSetting::getSetting('pages', 'landing_page_subtitle') ?? 'Explore our most popular and highly-rated courses') }}</textarea>
                            <small class="form-text text-muted d-block mt-2">This subtitle appears below the main title</small>
                            @error('landing_page_subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Preview -->
                        <div class="preview-card">
                            <small class="text-muted d-block mb-2">Preview:</small>
                            <h3 class="fw-bold text-dark preview-text" id="landing-title-preview">LearnSmart - Master Your Future with Expert-Led Courses</h3>
                            <p class="text-secondary preview-text" id="landing-subtitle-preview">Explore our most popular and highly-rated courses</p>
                        </div>
                    </div>
                </div>

                <!-- All Courses Page Section -->
                <div class="card shadow-sm mb-4 page-title-card">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-collection-fill text-success"></i> All Courses Page
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- All Courses Page Title -->
                        <div class="mb-3">
                            <label for="all_courses_page_title" class="form-label fw-bold">Page Title</label>
                            <input type="text" 
                                   class="form-control @error('all_courses_page_title') is-invalid @enderror" 
                                   id="all_courses_page_title" 
                                   name="all_courses_page_title"
                                   value="{{ old('all_courses_page_title', \App\Models\HomepageSetting::getSetting('pages', 'all_courses_page_title') ?? 'All Courses') }}"
                                   placeholder="Enter page title"
                                   maxlength="255">
                            <small class="form-text text-muted d-block mt-2">This title appears on the dedicated courses catalog page</small>
                            @error('all_courses_page_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- All Courses Page Subtitle -->
                        <div class="mb-3">
                            <label for="all_courses_page_subtitle" class="form-label fw-bold">Page Subtitle</label>
                            <textarea class="form-control @error('all_courses_page_subtitle') is-invalid @enderror" 
                                      id="all_courses_page_subtitle" 
                                      name="all_courses_page_subtitle"
                                      rows="3"
                                      placeholder="Enter page subtitle"
                                      maxlength="500">{{ old('all_courses_page_subtitle', \App\Models\HomepageSetting::getSetting('pages', 'all_courses_page_subtitle') ?? 'Explore our comprehensive catalog of professional courses') }}</textarea>
                            <small class="form-text text-muted d-block mt-2">This subtitle helps users understand the page content</small>
                            @error('all_courses_page_subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Preview -->
                        <div class="preview-card">
                            <small class="text-muted d-block mb-2">Preview:</small>
                            <h3 class="fw-bold text-dark preview-text" id="courses-title-preview">All Courses</h3>
                            <p class="text-secondary preview-text" id="courses-subtitle-preview">Explore our comprehensive catalog of professional courses</p>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold">
                        <i class="bi bi-check-circle"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4" data-aos="fade-up">
            <!-- Info Card -->
            <div class="card shadow-sm mb-4" style="border-left: 4px solid #4F46E5;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-info-circle-fill text-info"></i> About Page Titles
                    </h6>
                    <p class="small text-muted mb-0">
                        Customize the titles and subtitles that appear on your key pages. These settings help set the tone and improve the user experience on your platform.
                    </p>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card shadow-sm" style="border-left: 4px solid #10B981;">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-lightbulb-fill text-success"></i> Best Practices
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Keep titles <strong>concise and descriptive</strong></li>
                        <li class="mb-2">Use <strong>action-oriented language</strong> to engage users</li>
                        <li class="mb-2">Subtitles should <strong>clarify the page purpose</strong></li>
                        <li class="mb-2">Avoid <strong>overly long titles</strong> (keep under 60 chars)</li>
                        <li>Make titles <strong>SEO-friendly</strong> when possible</li>
                    </ul>
                </div>
            </div>

            <!-- Pages Overview -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-file-text-fill"></i> Pages Affected
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted fw-bold d-block mb-1">Landing Page</small>
                        <a href="{{ route('courses.index') }}" target="_blank" class="btn btn-sm btn-outline-primary btn-block w-100">
                            View Page <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                    <div>
                        <small class="text-muted fw-bold d-block mb-1">All Courses Page</small>
                        <a href="{{ route('courses.all') }}" target="_blank" class="btn btn-sm btn-outline-primary btn-block w-100">
                            View Page <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Live Preview Script -->
<script>
    // Landing Page Title Preview
    const landingTitleInput = document.getElementById('landing_page_title');
    const landingTitlePreview = document.getElementById('landing-title-preview');
    
    landingTitleInput?.addEventListener('input', function() {
        landingTitlePreview.textContent = this.value || 'LearnSmart - Master Your Future with Expert-Led Courses';
    });

    // Landing Page Subtitle Preview
    const landingSubtitleInput = document.getElementById('landing_page_subtitle');
    const landingSubtitlePreview = document.getElementById('landing-subtitle-preview');
    
    landingSubtitleInput?.addEventListener('input', function() {
        landingSubtitlePreview.textContent = this.value || 'Explore our most popular and highly-rated courses';
    });

    // All Courses Title Preview
    const coursesTitleInput = document.getElementById('all_courses_page_title');
    const coursesTitlePreview = document.getElementById('courses-title-preview');
    
    coursesTitleInput?.addEventListener('input', function() {
        coursesTitlePreview.textContent = this.value || 'All Courses';
    });

    // All Courses Subtitle Preview
    const coursesSubtitleInput = document.getElementById('all_courses_page_subtitle');
    const coursesSubtitlePreview = document.getElementById('courses-subtitle-preview');
    
    coursesSubtitleInput?.addEventListener('input', function() {
        coursesSubtitlePreview.textContent = this.value || 'Explore our comprehensive catalog of professional courses';
    });
</script>

@endsection

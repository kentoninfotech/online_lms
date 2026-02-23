@extends('layouts.app')

@section('title', 'Site Builder & Design Manager')

@section('content')

<div class="pc-container">
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Site Builder</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h4 class="m-0">Site Builder & Design Manager</h4>
                        <p class="text-muted">Customize your landing page, colors, logos, and all components</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="pc-container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Branding Section -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-palette"></i> Branding</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.site-builder.logos') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Logos</strong>
                                <small class="d-block text-muted">Site logo & favicon</small>
                            </div>
                            <i class="bi bi-chevron-right text-primary"></i>
                        </a>
                        <a href="{{ route('admin.site-builder.colors') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Colors</strong>
                                <small class="d-block text-muted">Theme & accent colors</small>
                            </div>
                            <i class="bi bi-chevron-right text-primary"></i>
                        </a>
                        <a href="{{ route('admin.site-builder.typography') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Typography</strong>
                                <small class="d-block text-muted">Fonts & text styles</small>
                            </div>
                            <i class="bi bi-chevron-right text-primary"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Homepage Sections -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-images"></i> Homepage</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.homepage-settings.edit-section', 'hero') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Hero Section</strong>
                                <small class="d-block text-muted">Banner & main title</small>
                            </div>
                            <i class="bi bi-chevron-right text-success"></i>
                        </a>
                        <a href="{{ route('admin.homepage-settings.edit-section', 'about') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong>About Section</strong>
                                <small class="d-block text-muted">About us content</small>
                            </div>
                            <i class="bi bi-chevron-right text-success"></i>
                        </a>
                        <a href="{{ route('admin.homepage-settings.edit-section', 'features') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Features</strong>
                                <small class="d-block text-muted">Why choose us section</small>
                            </div>
                            <i class="bi bi-chevron-right text-success"></i>
                        </a>
                        <a href="{{ route('admin.homepage-settings.edit-section', 'featured_courses') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Featured Courses</strong>
                                <small class="d-block text-muted">Showcase top courses</small>
                            </div>
                            <i class="bi bi-chevron-right text-success"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Sections -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-chat-square-text"></i> Content</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.homepage-settings.edit-section', 'testimonials') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Testimonials</strong>
                                <small class="d-block text-muted">Student reviews & quotes</small>
                            </div>
                            <i class="bi bi-chevron-right text-info"></i>
                        </a>
                        <a href="{{ route('admin.homepage-settings.edit-section', 'stats') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Statistics</strong>
                                <small class="d-block text-muted">Success metrics & numbers</small>
                            </div>
                            <i class="bi bi-chevron-right text-info"></i>
                        </a>
                        <a href="{{ route('admin.homepage-settings.edit-section', 'cta') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Call-to-Action</strong>
                                <small class="d-block text-muted">CTA buttons & links</small>
                            </div>
                            <i class="bi bi-chevron-right text-info"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer & Other -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="bi bi-gear"></i> Settings</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.homepage-settings.edit-section', 'contact') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Contact Section</strong>
                                <small class="d-block text-muted">Contact info & form</small>
                            </div>
                            <i class="bi bi-chevron-right text-warning"></i>
                        </a>
                        <a href="{{ route('admin.homepage-settings.edit-section', 'footer') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Footer</strong>
                                <small class="d-block text-muted">Footer content & links</small>
                            </div>
                            <i class="bi bi-chevron-right text-warning"></i>
                        </a>
                        <a href="{{ route('admin.homepage-settings.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong>All Settings</strong>
                                <small class="d-block text-muted">View all page settings</small>
                            </div>
                            <i class="bi bi-chevron-right text-warning"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Design Tips</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="bi bi-lightbulb text-warning"></i> Logo Best Practices</h6>
                            <ul class="text-muted small mb-3">
                                <li>Use PNG format for transparent backgrounds (recommended)</li>
                                <li>Minimum size: 200x100 pixels</li>
                                <li>Keep file size under 500KB</li>
                                <li>Use a favicon (32x32 PNG)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-lightbulb text-warning"></i> Color Guidelines</h6>
                            <ul class="text-muted small mb-3">
                                <li>Primary color: Main brand color</li>
                                <li>Secondary color: Accent & highlights</li>
                                <li>Ensure good contrast for accessibility</li>
                                <li>Test colors on different devices</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Preview Changes</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Live Preview:</strong> Changes made in the design manager will appear on the public landing page immediately. View your website to see live updates:
                        <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-info ms-2">
                            <i class="bi bi-box-arrow-up-right"></i> View Live Site
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

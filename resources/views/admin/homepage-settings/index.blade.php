@extends('layouts.app')

@section('title', 'Homepage Settings - Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0">Homepage Settings</h1>
                    <p class="text-muted mt-1">Manage all content and settings for your homepage</p>
                </div>
                <form action="{{ route('admin.homepage-settings.initialize-defaults') }}" method="POST" style="display: inline;" onsubmit="return confirm('This will reset all homepage content to defaults. Continue?');">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-redo"></i> Reset to Defaults
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Add heading banner if sessions exist -->
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Errors found:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Sections Grid -->
    <div class="row g-4">
        @php
            $sectionIcons = [
                'hero' => 'fa-star',
                'about' => 'fa-info-circle',
                'features' => 'fa-lightbulb',
                'featured_courses' => 'fa-book',
                'testimonials' => 'fa-comments',
                'stats' => 'fa-chart-bar',
                'cta' => 'fa-bullhorn',
                'contact' => 'fa-envelope',
                'footer' => 'fa-square'
            ];
        @endphp

        @foreach($availableSections as $sectionKey => $sectionLabel)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm hover-shadow">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="fs-3 text-primary">
                                <i class="fa {{ $sectionIcons[$sectionKey] ?? 'fa-cog' }}"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">{{ $sectionLabel }}</h5>
                                <small class="text-muted">{{ count($sections[$sectionKey] ?? []) }} items</small>
                            </div>
                        </div>
                        <hr>
                        <p class="card-text text-muted small">
                            Edit all content and settings for this section
                        </p>
                        <a href="{{ route('admin.homepage-settings.edit-section', $sectionKey) }}" class="btn btn-primary btn-sm w-100">
                            <i class="fa fa-edit"></i> Edit Section
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Information section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fa fa-info-circle text-info"></i> How to Use Homepage Settings
                    </h5>
                    <p class="card-text mb-0">
                        Click on any section above to edit its content. You can update titles, descriptions, images, and buttons. 
                        All changes are saved immediately and will be reflected on your homepage. Use the "Reset to Defaults" button 
                        to restore original content if needed.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow {
        transition: box-shadow 0.3s ease;
    }

    .hover-shadow:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endsection

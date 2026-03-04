@extends('layouts.app')

@section('title', 'Page Visibility Settings')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.homepage-settings.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
                <div>
                    <h1 class="h2 mb-0">Page Visibility Settings</h1>
                    <p class="text-muted mt-1">Control which pages are visible to the public</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please fix the following errors:</strong>
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
        <i class="fa fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Visibility Settings -->
    <div class="row g-4">
        <!-- Services Page Visibility -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="fa fa-briefcase fa-2x text-primary"></i>
                        <div>
                            <h5 class="mb-0">Services Page</h5>
                            <small class="text-muted">Show/Hide the public Services page</small>
                        </div>
                    </div>
                    <hr>
                    
                    <form action="{{ route('admin.homepage-settings.update-setting', ['visibility', 'show_services']) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-check form-switch">
                            @php
                                $showServices = \App\Models\HomepageSetting::getSetting('visibility', 'show_services', true);
                            @endphp
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="value" 
                                id="show_services"
                                value="1"
                                {{ $showServices ? 'checked' : '' }}
                                onchange="this.form.submit()"
                            />
                            <label class="form-check-label" for="show_services">
                                {{ $showServices ? 'Services page is VISIBLE' : 'Services page is HIDDEN' }}
                            </label>
                        </div>

                        <small class="d-block text-muted mt-3">
                            <i class="fa fa-info-circle"></i> 
                            When enabled, visitors can access <code>/services</code> to view your services.
                            When disabled, the page will show a 404 error.
                        </small>
                    </form>
                </div>
            </div>
        </div>

        <!-- Galleries Page Visibility -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="fa fa-images fa-2x text-primary"></i>
                        <div>
                            <h5 class="mb-0">Galleries Page</h5>
                            <small class="text-muted">Show/Hide the public Galleries page</small>
                        </div>
                    </div>
                    <hr>
                    
                    <form action="{{ route('admin.homepage-settings.update-setting', ['visibility', 'show_galleries']) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-check form-switch">
                            @php
                                $showGalleries = \App\Models\HomepageSetting::getSetting('visibility', 'show_galleries', true);
                            @endphp
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="value" 
                                id="show_galleries"
                                value="1"
                                {{ $showGalleries ? 'checked' : '' }}
                                onchange="this.form.submit()"
                            />
                            <label class="form-check-label" for="show_galleries">
                                {{ $showGalleries ? 'Galleries page is VISIBLE' : 'Galleries page is HIDDEN' }}
                            </label>
                        </div>

                        <small class="d-block text-muted mt-3">
                            <i class="fa fa-info-circle"></i> 
                            When enabled, visitors can access <code>/galleries</code> to view your image galleries.
                            When disabled, the page will show a 404 error.
                        </small>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                <h5 class="alert-heading"><i class="fa fa-lightbulb"></i> How to Use</h5>
                <p>Toggle the switches above to show or hide pages from your visitors:</p>
                <ul class="mb-0">
                    <li><strong>Services Page:</strong> Enable to display your services at <code>/services</code></li>
                    <li><strong>Galleries Page:</strong> Enable to display your image galleries at <code>/galleries</code></li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

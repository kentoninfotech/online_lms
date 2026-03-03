@extends('layouts.app')

@section('title', 'Course Display Settings - Homepage Settings')

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
                    <h1 class="h2 mb-0"><i class="fa fa-graduation-cap"></i> Course Display Settings</h1>
                    <p class="text-muted mt-1">Manage how courses are displayed on the homepage</p>
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

    <!-- Settings Form -->
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.homepage-settings.update-course-display') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Featured Courses Display Options -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fa fa-book"></i> Featured Courses Display</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="show_featured_courses" value="1" 
                                        id="showFeaturedCourses" {{ $settings['show_featured_courses'] ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="showFeaturedCourses">
                                        Show Featured Courses Section
                                    </label>
                                    <small class="d-block text-muted mt-1">
                                        Display featured courses on the homepage
                                    </small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Course Display Mode -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Display Mode</label>
                                <div class="alert alert-info small" role="alert">
                                    <i class="fa fa-info-circle"></i> Choose how courses should be displayed after the featured courses list
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="course_display_mode" 
                                        value="default" id="displayModeDefault" 
                                        {{ $settings['course_display_mode'] === 'default' || !$settings['course_display_mode'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="displayModeDefault">
                                        <strong>Default View</strong>
                                        <small class="d-block text-muted ms-0">Only show featured courses (no additional options)</small>
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="course_display_mode" 
                                        value="categories_dropdown" id="displayModeCategoriesDropdown"
                                        {{ $settings['course_display_mode'] === 'categories_dropdown' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="displayModeCategoriesDropdown">
                                        <strong>With Category Dropdown</strong>
                                        <small class="d-block text-muted ms-0">Add a dropdown to filter courses by category</small>
                                    </label>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="course_display_mode" 
                                        value="level_tabs" id="displayModeLevelTabs"
                                        {{ $settings['course_display_mode'] === 'level_tabs' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="displayModeLevelTabs">
                                        <strong>With Course Level Tabs</strong>
                                        <small class="d-block text-muted ms-0">Display courses organized by type: Local, International, Diploma</small>
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="course_display_mode" 
                                        value="both" id="displayModeBoth"
                                        {{ $settings['course_display_mode'] === 'both' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="displayModeBoth">
                                        <strong>With Both Options</strong>
                                        <small class="d-block text-muted ms-0">Show both category dropdown and level tabs</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Courses Per Row -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="coursesPerRow" class="form-label fw-semibold">Courses Per Row (Desktop)</label>
                                <select name="courses_per_row" id="coursesPerRow" class="form-select">
                                    <option value="3" {{ $settings['courses_per_row'] == 3 ? 'selected' : '' }}>3 Columns</option>
                                    <option value="4" {{ $settings['courses_per_row'] == 4 ? 'selected' : '' }}>4 Columns</option>
                                    <option value="5" {{ $settings['courses_per_row'] == 5 ? 'selected' : '' }}>5 Columns</option>
                                </select>
                                <small class="form-text text-muted">Number of course cards to display per row</small>
                            </div>
                            <div class="col-md-6">
                                <label for="maxCoursesDisplay" class="form-label fw-semibold">Maximum Courses to Display</label>
                                <input type="number" name="max_courses_display" id="maxCoursesDisplay" class="form-control" 
                                    value="{{ $settings['max_courses_display'] ?? 12 }}" min="1" max="100">
                                <small class="form-text text-muted">Leave empty to show all active courses</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Display Settings -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fa fa-folder"></i> Category Display Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="show_all_categories_option" value="1"
                                        id="showAllCategoriesOption" {{ $settings['show_all_categories_option'] ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="showAllCategoriesOption">
                                        Include "All Courses" option in category dropdown
                                    </label>
                                    <small class="d-block text-muted mt-1">
                                        When category dropdown is enabled, show an option to display all courses
                                    </small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Categories to Display (Dropdown)</label>
                                <div class="alert alert-light small" role="alert">
                                    <i class="fa fa-info-circle"></i> Select which categories should appear in the dropdown filter
                                </div>

                                <div class="row">
                                    @forelse($categories as $category)
                                        <div class="col-md-6 col-lg-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="selected_categories[]" 
                                                    value="{{ $category->id }}" id="category_{{ $category->id }}"
                                                    {{ in_array($category->id, (array)$settings['selected_categories'] ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="category_{{ $category->id }}">
                                                    {{ $category->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="text-muted small mb-0">No categories available. <a href="{{ route('admin.course-categories.index') }}">Create categories first</a>.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Level Display Settings -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fa fa-layer-group"></i> Course Level Display Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Course Levels to Display (Tabs)</label>
                                <div class="alert alert-light small" role="alert">
                                    <i class="fa fa-info-circle"></i> Select which course levels should appear as tabs
                                </div>

                                <div class="row">
                                    @foreach(['Local', 'International', 'Diploma'] as $level)
                                        <div class="col-md-6 col-lg-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="selected_levels[]" 
                                                    value="{{ $level }}" id="level_{{ $level }}"
                                                    {{ in_array($level, (array)$settings['selected_levels'] ?? ['Local', 'International', 'Diploma']) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="level_{{ $level }}">
                                                    <strong>{{ $level }}</strong>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="show_all_levels_option" value="1"
                                        id="showAllLevelsOption" {{ $settings['show_all_levels_option'] ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="showAllLevelsOption">
                                        Include "All Programs" tab
                                    </label>
                                    <small class="d-block text-muted mt-1">
                                        When level tabs are enabled, show a tab to display all course levels
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-save"></i> Save Settings
                            </button>
                            <a href="{{ route('admin.homepage-settings.index') }}" class="btn btn-secondary btn-lg">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Sidebar - Preview Info -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 90px;">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0"><i class="fa fa-eye"></i> Preview</h5>
                </div>
                <div class="card-body small">
                    <div class="mb-3">
                        <p class="fw-semibold mb-2">Selected Display Mode:</p>
                        <div id="previewMode" class="alert alert-light mb-0">
                            Default View
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <p class="fw-semibold mb-2">What users will see:</p>
                        <ul id="previewList" class="list-unstyled small">
                            <li class="mb-1">✓ Featured courses section</li>
                        </ul>
                    </div>

                    <hr>

                    <div class="alert alert-info small mb-0">
                        <i class="fa fa-lightbulb"></i>
                        <strong>Tip:</strong> You can change the display mode anytime. Settings apply immediately to the homepage.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-check-input:checked {
        background-color: #2563EB;
        border-color: #2563EB;
    }

    .form-check-input:focus {
        border-color: #2563EB;
        box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25);
    }

    .form-check-input[type="radio"]:checked {
        background-color: #2563EB;
        border-color: #2563EB;
    }

    .sticky-top {
        z-index: 100;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const displayModes = document.querySelectorAll('input[name="course_display_mode"]');
    const previewMode = document.getElementById('previewMode');
    const previewList = document.getElementById('previewList');

    function updatePreview() {
        const selectedMode = document.querySelector('input[name="course_display_mode"]:checked').value;
        const baseItems = ['<li class="mb-1">✓ Featured courses section</li>'];

        switch(selectedMode) {
            case 'categories_dropdown':
                previewMode.textContent = 'With Category Dropdown';
                previewList.innerHTML = baseItems.join('') + '<li class="mb-1">✓ Category filter dropdown</li>';
                break;
            case 'level_tabs':
                previewMode.textContent = 'With Course Level Tabs';
                previewList.innerHTML = baseItems.join('') + '<li class="mb-1">✓ Level tabs (Local, International, Diploma)</li>';
                break;
            case 'both':
                previewMode.textContent = 'With Both Options';
                previewList.innerHTML = baseItems.join('') + '<li class="mb-1">✓ Category filter dropdown</li><li class="mb-1">✓ Level tabs</li>';
                break;
            default:
                previewMode.textContent = 'Default View';
                previewList.innerHTML = baseItems.join('');
        }
    }

    displayModes.forEach(mode => {
        mode.addEventListener('change', updatePreview);
    });

    // Initial preview update
    updatePreview();
});
</script>
@endsection

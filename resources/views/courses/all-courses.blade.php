@extends('layouts.landing')

@section('title', \App\Models\HomepageSetting::getSetting('pages', 'all_courses_page_title') ?? ('All Courses - ' . (\App\Models\HomepageSetting::getSetting('branding', 'site_name') ?? 'LMS Inc')))

@section('content')

<style>
    /* Scroll margin for sections */
    #all-courses {
        scroll-margin-top: 80px;
    }

    /* Category tabs styling */
    .category-tabs {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        flex-wrap: wrap;
        margin-bottom: 2rem;
        padding-bottom: 0.5rem;
    }

    .category-tab {
        padding: 8px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 25px;
        background: white;
        color: #333;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        white-space: nowrap;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .category-tab:hover {
        border-color: #2563EB;
        color: #2563EB;
    }

    .category-tab.active {
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
        color: white;
        border-color: #2563EB;
    }

    .category-tab i {
        margin-right: 6px;
    }

    /* Course grid - Bootstrap 3 column layout */
    .courses-grid {
        gap: 1.5rem;
    }

    .course-card {
        transition: all 0.3s ease;
    }

    .course-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
    }

    /* Breadcrumb styling */
    .breadcrumb-custom {
        background: linear-gradient(135deg, #f3f4f6 0%, #f9fafb 100%);
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
    }

    .breadcrumb-custom a {
        color: #2563EB;
        text-decoration: none;
    }

    .breadcrumb-custom a:hover {
        text-decoration: underline;
    }
</style>

<!-- Page Header -->
<section class="py-5 py-md-8" style="background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%); color: white;">
    <div class="container-lg">
        <div class="text-center" data-aos="fade-up">
            <h1 class="display-4 fw-bold mb-3">{{ \App\Models\HomepageSetting::getSetting('pages', 'all_courses_page_title') ?? 'All Courses' }}</h1>
            <p class="lead mb-0">{{ \App\Models\HomepageSetting::getSetting('pages', 'all_courses_page_subtitle') ?? 'Explore our comprehensive catalog of professional courses' }}</p>
        </div>
    </div>
</section>

<!-- Main Content -->
<section id="all-courses" class="py-5 py-md-8">
    <div class="container-lg">
        <!-- Breadcrumb -->
        <div class="breadcrumb-custom" data-aos="fade-up">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Courses</li>
                    @if($activeCategory)
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $categories->where('id', $activeCategory)->first()?->name ?? 'Category' }}
                        </li>
                    @endif
                </ol>
            </nav>
        </div>

        <!-- Course Search Bar -->
        <div class="mb-5" style="position: relative; z-index: 10;">
            @include('components.course-search-bar', ['categoryId' => $activeCategory])
        </div>

        <!-- Category Filter Tabs -->
        <div class="mb-5" data-aos="fade-up">
            <h5 class="fw-bold mb-3">Filter by Category</h5>
            <div class="category-tabs">
                <!-- All Courses Tab -->
                <a href="{{ route('courses.all') }}" class="category-tab @if(!$activeCategory) active @endif">
                    <i class="bi bi-collection"></i>All Courses
                </a>

                <!-- Category Tabs -->
                @foreach($categories as $category)
                    <a href="{{ route('courses.all', ['category' => $category->id]) }}" 
                       class="category-tab @if($activeCategory == $category->id) active @endif">
                        {{ $category->name }}
                        <span class="badge bg-light ms-1">{{ $category->activeCourses()->count() }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Courses Grid -->
        <div class="row courses-grid mb-5">
            @forelse($courses as $course)
                <div class="col-lg-4 col-md-6 col-12" data-aos="fade-up">
                    <div class="card shadow-sm h-100 rounded-4 overflow-hidden course-card">
                        <div class="position-relative overflow-hidden" style="height: 220px;">
                            @if($course->featured_image)
                                <img src="{{ asset($course->featured_image) }}" alt="{{ $course->title }}" class="card-img-top h-100 object-fit-cover">
                            @else
                                <div class="w-100 h-100 bg-gradient-primary d-flex align-items-center justify-content-center">
                                    <span style="font-size: 4rem;">📚</span>
                                </div>
                            @endif
                            
                            <!-- Category Badge -->
                            <div class="position-absolute top-0 start-0 p-3">
                                <span class="badge bg-white text-primary" style="max-width: 150px; word-wrap: break-word; white-space: normal; overflow-wrap: break-word;">{{ $course->category->name ?? 'General' }}</span>
                            </div>

                            <!-- Featured Badge (if applicable) -->
                            @if($course->is_featured)
                                <div class="position-absolute top-0 end-0 p-3">
                                    <span class="badge bg-danger">⭐ Featured</span>
                                </div>
                            @endif
                        </div>

                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-2">{{ Str::limit($course->title, 40) }}</h5>
                            
                            @if($course->subtitle)
                                <p class="card-text text-muted small mb-3">{{ Str::limit($course->subtitle, 60) }}</p>
                            @endif

                            <hr>

                            <!-- Instructor Info -->
                            @if($course->facilitator)
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span style="font-size: 1.25rem;">👨‍🏫</span>
                                    <div>
                                        <small class="d-block text-muted">Instructor</small>
                                        <small class="fw-semibold">{{ Str::limit($course->facilitator->name, 25) }}</small>
                                    </div>
                                </div>
                            @endif

                            <!-- Course Details -->
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <small class="d-block text-muted">Duration</small>
                                    <span class="fw-bold">{{ $course->course_hours }}h</span>
                                </div>
                                <div class="col-6">
                                    <small class="d-block text-muted">Level</small>
                                    <span class="fw-bold">Beginner</span>
                                </div>
                            </div>

                            <hr>

                            <h5 class="text-primary fw-bold mb-3">₦{{ number_format($course->fee) }}</h5>

                            <div class="d-grid gap-2">
                                <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-primary btn-sm fw-bold">
                                    View Details
                                </a>
                                @auth
                                    <a href="{{ route('courses.enroll', $course) }}" class="btn btn-success btn-sm fw-bold">
                                        Enroll Now
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-success btn-sm fw-bold">
                                        Enroll
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
                        <h4 class="text-muted">No courses found</h4>
                        <p class="text-muted">Try selecting a different category or check back soon for new courses.</p>
                        <a href="{{ route('courses.all') }}" class="btn btn-primary mt-3">View All Courses</a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($courses->hasPages())
            <nav aria-label="Page navigation" class="d-flex justify-content-center" data-aos="fade-up">
                {{ $courses->links('pagination::bootstrap-5') }}
            </nav>
        @endif

        <!-- Statistics Section -->
        <div class="mt-5 pt-5 border-top" data-aos="fade-up">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div style="font-size: 2rem; margin-bottom: 1rem;">🎓</div>
                            <h3 class="fw-bold text-primary">{{ \App\Models\Course::where('is_active', true)->count() }}</h3>
                            <p class="text-muted mb-0">Total Courses</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div style="font-size: 2rem; margin-bottom: 1rem;">📚</div>
                            <h3 class="fw-bold text-success">{{ $categories->count() }}</h3>
                            <p class="text-muted mb-0">Categories</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div style="font-size: 2rem; margin-bottom: 1rem;">👨‍🏫</div>
                            <h3 class="fw-bold text-info">{{ \App\Models\Facilitator::count() }}</h3>
                            <p class="text-muted mb-0">Expert Instructors</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 py-md-8" style="background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%); color: white;">
    <div class="container-lg text-center" data-aos="fade-up">
        <h2 class="fw-bold mb-3">Start Learning Today</h2>
        <p class="lead mb-4">Join thousands of students already learning on {{ \App\Models\HomepageSetting::getSetting('branding', 'site_name') ?? 'LMS Inc' }}</p>
        @auth
            <a href="{{ route('courses.all') }}" class="btn btn-light btn-lg fw-bold">Browse Courses</a>
        @else
            <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-bold">Create Account & Learn</a>
        @endauth
    </div>
</section>

@endsection

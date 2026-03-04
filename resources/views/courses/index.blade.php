@extends('layouts.landing')

@section('title', \App\Models\HomepageSetting::getSetting('pages', 'landing_page_title') ?? 'LMS - Master Your Future with Expert-Led Courses')

@section('content')

<style>
    /* Remove carousel section margins */
    .carousel-section {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }
    
    /* Add scroll margin top to sections to account for fixed navbar */
    #hero,
    #about,
    #featured-courses,
    #all-courses,
    #testimonials,
    #contact {
        scroll-margin-top: 80px;
    }
</style>

<!-- CAROUSEL Section (Full Width) -->
<section id="carousel-section" class="carousel-section">
    @if($carouselImages->count() > 0)
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" style="height: 500px; overflow: hidden;">
        <div class="carousel-indicators">
            @foreach($carouselImages as $key => $image)
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $key }}" 
                    class="@if($key == 0) active @endif" aria-current="@if($key == 0)true @endif"></button>
            @endforeach
        </div>
        
        <div class="carousel-inner h-100">
            @foreach($carouselImages as $key => $image)
                <div class="carousel-item @if($key == 0) active @endif h-100">
                    <img src="{{ asset($image->image_path) }}" class="d-block w-100 h-100 object-fit-cover" alt="{{ $image->value }}">
                    <div class="carousel-caption d-none d-md-block" style="padding: 2px !important; width: 50% !important; margin: auto; bottom: 50px">
                        <h3 class="fw-bold text-white" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                            {{ $image->value }}
                        </h3>
                        @if($image->description)
                            <p class="lead text-white" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                                {{ $image->description }}
                            </p>
                        @endif
                        @if($image->button_text && $image->button_link)
                            <a href="{{ $image->button_link }}" class="btn btn-primary btn-lg fw-bold">
                                {{ $image->button_text }}
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    @else
    <!-- Empty carousel placeholder - encourage adding carousel images -->
    <div class="bg-secondary" style="height: 500px; display: flex; align-items: center; justify-content: center;">
        <div class="text-center text-white">
            <i class="bi bi-images" style="font-size: 4rem;"></i>
            <h3 class="mt-3">No Carousel Images</h3>
            <p>Add carousel images from the admin panel at <strong>/admin/carousel</strong></p>
        </div>
    </div>
    @endif
</section>

<!-- HERO Section (Content below carousel) -->
<section id="hero" class="hero-section py-5 py-md-8">
    <div class="container-lg">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6 hero-content mb-4 mb-lg-0" data-aos="fade-right">
                <div class="badge bg-light text-primary mb-3 d-inline-block">
                    <i class="bi bi-mortarboard"></i> Professional Learning Platform
                </div>
                <h1 class="display-4 fw-bold mb-4 text-white">
                    {{ $homeSettings['hero']['title']['value'] ?? 'Master Your Future with Expert-Led Courses' }}
                </h1>
                <p class="lead text-white-50 mb-4">
                    {{ $homeSettings['hero']['description']['value'] ?? 'Access world-class training from top facilitators. Learn at your own pace, earn certificates, and advance your career with industry-relevant skills.' }}
                </p>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="{{ $homeSettings['hero']['button_link']['value'] ?? '#featured-courses' }}" class="btn btn-light btn-lg fw-bold">
                        <i class="bi bi-arrow-right"></i> {{ $homeSettings['hero']['button_text']['value'] ?? 'Explore Courses' }}
                    </a>
                    @guest
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg fw-bold">
                        Get Started Free
                    </a>
                    @endguest
                </div>
                <div class="row g-4 mt-5">
                    <div class="col-6 col-sm-4">
                        <h3 class="text-white fw-bold mb-1">{{ $homeSettings['hero']['stat1_value']['value'] ?? '50K+' }}</h3>
                        <p class="text-white-50 small">{{ $homeSettings['hero']['stat1_label']['value'] ?? 'Active Learners' }}</p>
                    </div>
                    <div class="col-6 col-sm-4">
                        <h3 class="text-white fw-bold mb-1">{{ $homeSettings['hero']['stat2_value']['value'] ?? '200+' }}</h3>
                        <p class="text-white-50 small">{{ $homeSettings['hero']['stat2_label']['value'] ?? 'Expert Courses' }}</p>
                    </div>
                    <div class="col-6 col-sm-4">
                        <h3 class="text-white fw-bold mb-1">{{ $homeSettings['hero']['stat3_value']['value'] ?? '95%' }}</h3>
                        <p class="text-white-50 small">{{ $homeSettings['hero']['stat3_label']['value'] ?? 'Satisfaction' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="card shadow-lg-custom rounded-4">
                    <div class="card-body p-4 p-lg-5">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-3 p-3 bg-light-primary rounded-3">
                                    <span style="font-size: 2rem;">🎯</span>
                                    <div>
                                        <h6 class="mb-1 fw-bold">Structured Learning</h6>
                                        <small class="text-muted">Curated content paths</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-3 p-3 bg-success bg-opacity-10 rounded-3">
                                    <span style="font-size: 2rem;">🏆</span>
                                    <div>
                                        <h6 class="mb-1 fw-bold">Earn Certificates</h6>
                                        <small class="text-muted">Recognized credentials</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-3 p-3 bg-warning bg-opacity-10 rounded-3">
                                    <span style="font-size: 2rem;">👥</span>
                                    <div>
                                        <h6 class="mb-1 fw-bold">Community Support</h6>
                                        <small class="text-muted">Learn with peers</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-3 p-3 bg-danger bg-opacity-10 rounded-3">
                                    <span style="font-size: 2rem;">⏰</span>
                                    <div>
                                        <h6 class="mb-1 fw-bold">Learn Anytime</h6>
                                        <small class="text-muted">24/7 Course Access</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ABOUT US Section -->
<section id="about" class="py-5 py-md-8 bg-light">
    <div class="container-lg">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="row g-3">
                    <!-- Stat 1 -->
                    <div class="col-6">
                        <div class="card bg-gradient-primary text-white h-100 d-flex align-items-center justify-content-center rounded-4 shadow" style="min-height: 200px;">
                            <div class="card-body text-center">
                                <h2 class="fw-bold mb-2">{{ $homeSettings['about']['stat1_value']['value'] ?? '200+' }}</h2>
                                <p class="mb-0 small">{{ $homeSettings['about']['stat1_label']['value'] ?? 'Expert Instructors' }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Stat 2 -->
                    <div class="col-6">
                        <div class="card bg-gradient-secondary text-white h-100 d-flex align-items-center justify-content-center rounded-4 shadow" style="min-height: 200px;">
                            <div class="card-body text-center">
                                <h2 class="fw-bold mb-2">{{ $homeSettings['about']['stat2_value']['value'] ?? '50K+' }}</h2>
                                <p class="mb-0 small">{{ $homeSettings['about']['stat2_label']['value'] ?? 'Success Stories' }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Stat 3 -->
                    <div class="col-6">
                        <div class="card bg-success text-white h-100 d-flex align-items-center justify-content-center rounded-4 shadow" style="min-height: 200px;">
                            <div class="card-body text-center">
                                <h2 class="fw-bold mb-2">{{ $homeSettings['about']['stat3_value']['value'] ?? '15+' }}</h2>
                                <p class="mb-0 small">{{ $homeSettings['about']['stat3_label']['value'] ?? 'Years Experience' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card bg-warning text-dark h-100 d-flex align-items-center justify-content-center rounded-4 shadow" style="min-height: 200px;">
                            <div class="card-body text-center">
                                <h2 class="fw-bold mb-2">{{ $homeSettings['about']['stat4_value']['value'] ?? '1M+' }}</h2>
                                <p class="mb-0 small">{{ $homeSettings['about']['stat4_label']['value'] ?? 'Certificates Issued' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="section-title">{{ $homeSettings['about']['title']['value'] ?? 'About COINMAC Academy' }}</h2>
                <p class="text-muted mb-3 text-justify">
                    {{ $homeSettings['about']['content']['value'] ?? 'COINMAC Academy is a leading online learning platform dedicated to transforming careers through world-class education and professional development courses.' }}
                </p>
                <p class="text-muted mb-4 text-justify">
                    {{ $homeSettings['about']['content_2']['value'] ?? 'Founded in 2009, we\'ve helped over 50,000 professionals advance their careers by providing industry-relevant training, expert mentorship, and internationally recognized certifications.' }}
                </p>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle text-success fw-bold" style="font-size: 1.5rem;"></i>
                            <span class="fw-semibold">Flexible Learning</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle text-success fw-bold" style="font-size: 1.5rem;"></i>
                            <span class="fw-semibold">Expert Instructors</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle text-success fw-bold" style="font-size: 1.5rem;"></i>
                            <span class="fw-semibold">Verified Certificates</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle text-success fw-bold" style="font-size: 1.5rem;"></i>
                            <span class="fw-semibold">Lifetime Access</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- WHY CHOOSE US Section -->
<section class="py-5 py-md-8">
    <div class="container-lg">
        <div class="text-center mb-5 mb-md-8" data-aos="fade-up">
            <h2 class="section-title">{{ $homeSettings['features']['section_title']['value'] ?? 'Why Choose COINMAC?' }}</h2>
            <p class="section-subtitle">{{ $homeSettings['features']['section_subtitle']['value'] ?? 'Premium education with professional support' }}</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                <div class="feature-card text-center card shadow-sm h-100">
                    <div class="card-body">
                        <div class="feature-icon">{{ $homeSettings['features']['feature1_icon']['value'] ?? '🎓' }}</div>
                        <h5 class="card-title fw-bold">{{ $homeSettings['features']['feature1_title']['value'] ?? 'Expert Instructors' }}</h5>
                        <p class="card-text text-muted small">{{ $homeSettings['features']['feature1_desc']['value'] ?? 'Learn from industry professionals with 10+ years of experience' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card text-center card shadow-sm h-100">
                    <div class="card-body">
                        <div class="feature-icon">{{ $homeSettings['features']['feature2_icon']['value'] ?? '⏰' }}</div>
                        <h5 class="card-title fw-bold">{{ $homeSettings['features']['feature2_title']['value'] ?? 'Learn Anytime' }}</h5>
                        <p class="card-text text-muted small">{{ $homeSettings['features']['feature2_desc']['value'] ?? 'Access courses 24/7 from anywhere at your own pace' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card text-center card shadow-sm h-100">
                    <div class="card-body">
                        <div class="feature-icon">{{ $homeSettings['features']['feature3_icon']['value'] ?? '🏆' }}</div>
                        <h5 class="card-title fw-bold">{{ $homeSettings['features']['feature3_title']['value'] ?? 'Verified Certificates' }}</h5>
                        <p class="card-text text-muted small">{{ $homeSettings['features']['feature3_desc']['value'] ?? 'Earn industry-recognized certificates upon completion' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card text-center card shadow-sm h-100">
                    <div class="card-body">
                        <div class="feature-icon">{{ $homeSettings['features']['feature4_icon']['value'] ?? '👥' }}</div>
                        <h5 class="card-title fw-bold">{{ $homeSettings['features']['feature4_title']['value'] ?? 'Community Support' }}</h5>
                        <p class="card-text text-muted small">{{ $homeSettings['features']['feature4_desc']['value'] ?? 'Connect with peers, ask questions, and grow together' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-card text-center card shadow-sm h-100">
                    <div class="card-body">
                        <div class="feature-icon">{{ $homeSettings['features']['feature5_icon']['value'] ?? '💻' }}</div>
                        <h5 class="card-title fw-bold">{{ $homeSettings['features']['feature5_title']['value'] ?? 'Interactive Content' }}</h5>
                        <p class="card-text text-muted small">{{ $homeSettings['features']['feature5_desc']['value'] ?? 'Videos, quizzes, projects, and live sessions' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="500">
                <div class="feature-card text-center card shadow-sm h-100">
                    <div class="card-body">
                        <div class="feature-icon">{{ $homeSettings['features']['feature6_icon']['value'] ?? '💰' }}</div>
                        <h5 class="card-title fw-bold">{{ $homeSettings['features']['feature6_title']['value'] ?? 'Affordable Pricing' }}</h5>
                        <p class="card-text text-muted small">{{ $homeSettings['features']['feature6_desc']['value'] ?? 'Get premium education at competitive prices' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="600">
                <div class="feature-card text-center card shadow-sm h-100">
                    <div class="card-body">
                        <div class="feature-icon">{{ $homeSettings['features']['feature7_icon']['value'] ?? '🔐' }}</div>
                        <h5 class="card-title fw-bold">{{ $homeSettings['features']['feature7_title']['value'] ?? 'Lifetime Access' }}</h5>
                        <p class="card-text text-muted small">{{ $homeSettings['features']['feature7_desc']['value'] ?? 'Access course materials forever after enrollment' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="700">
                <div class="feature-card text-center card shadow-sm h-100">
                    <div class="card-body">
                        <div class="feature-icon">{{ $homeSettings['features']['feature8_icon']['value'] ?? '📱' }}</div>
                        <h5 class="card-title fw-bold">{{ $homeSettings['features']['feature8_title']['value'] ?? 'Mobile Friendly' }}</h5>
                        <p class="card-text text-muted small">{{ $homeSettings['features']['feature8_desc']['value'] ?? 'Learn on smartphone, tablet, or computer' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED COURSES Section -->
<section id="featured-courses" class="py-5 py-md-8 bg-light">
    <div class="container-lg">
        <div class="text-center mb-5 mb-md-8" data-aos="fade-up">
            <h2 class="section-title">{{ \App\Models\HomepageSetting::getSetting('pages', 'landing_page_title') ?? 'Featured Courses' }}</h2>
            <p class="section-subtitle">{{ \App\Models\HomepageSetting::getSetting('pages', 'landing_page_subtitle') ?? 'Explore our most popular and highly-rated courses' }}</p>
        </div>

        <!-- Course Search Bar -->
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto">
                @include('components.course-search-bar')
            </div>
        </div>

        <div class="row g-4">
            @forelse($featuredCourses as $course)
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
                            <div class="position-absolute top-0 start-0 p-3">
                                <span class="badge bg-white text-primary" style="max-width: 150px; word-wrap: break-word; white-space: normal; overflow-wrap: break-word;">{{ $course->category->name ?? 'General' }}</span>
                            </div>
                            <div class="position-absolute top-0 end-0 p-3">
                                <span class="badge bg-danger">⭐ Featured</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-2">{{ Str::limit($course->title, 40) }}</h5>
                            @if($course->subtitle)
                                <p class="card-text text-muted small mb-3">{{ Str::limit($course->subtitle, 60) }}</p>
                            @endif
                            
                            <hr>

                            @if($course->facilitator)
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span style="font-size: 1.25rem;">👨‍🏫</span>
                                    <div>
                                        <small class="d-block text-muted">Instructor</small>
                                        <small class="fw-semibold">{{ $course->facilitator->name }}</small>
                                    </div>
                                </div>
                            @endif

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
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No featured courses available yet</p>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('courses.all') }}" class="btn btn-primary btn-lg fw-bold">
                {{ $homeSettings['sections']['featured_button_text']['value'] ?? 'Explore All Courses →' }}
            </a>
        </div>
    </div>
</section>

<!-- COURSES WITH CATEGORY FILTER Section (Optional) -->
@if(in_array($courseDisplaySettings['course_display_mode'], ['categories_dropdown', 'both']))
<section class="py-5 py-md-8">
    <div class="container-lg">
        <div class="text-center mb-5 mb-md-8" data-aos="fade-up">
            <h2 class="section-title">Browse By Category</h2>
            <p class="section-subtitle">Find courses tailored to your interests</p>
        </div>

        <!-- Category Filter Dropdown -->
        <div class="row mb-5">
            <div class="col-lg-4 mx-auto">
                <div class="input-group">
                    <label class="input-group-text" for="categoryFilter">
                        <i class="fa fa-filter"></i> Filter by Category
                    </label>
                    <select id="categoryFilter" class="form-select" onchange="filterCoursesByCategory(this.value)">
                        @if($courseDisplaySettings['show_all_categories_option'])
                            <option value="">All Courses</option>
                        @endif
                        @foreach($categories as $category)
                            @if(empty($courseDisplaySettings['selected_categories']) || in_array($category->id, $courseDisplaySettings['selected_categories']))
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Courses Display Area -->
        <div class="row g-4" id="categoryCoursesContainer">
            @php
                $allActiveCourses = \App\Models\Course::where('is_active', true)
                    ->orderBy('created_at', 'desc')
                    ->limit($courseDisplaySettings['max_courses_display'] ?? 12)
                    ->get();
            @endphp

            @forelse($allActiveCourses as $course)
                <div class="col-lg-{{ $courseDisplaySettings['courses_per_row'] }} col-md-6 col-12 course-item" data-category="{{ $course->category_id ?? '' }}">
                    <div class="card shadow-sm h-100 rounded-4 overflow-hidden course-card">
                        <div class="position-relative overflow-hidden" style="height: 220px;">
                            @if($course->featured_image)
                                <img src="{{ asset($course->featured_image) }}" alt="{{ $course->title }}" class="card-img-top h-100 object-fit-cover">
                            @else
                                <div class="w-100 h-100 bg-gradient-primary d-flex align-items-center justify-content-center">
                                    <span style="font-size: 4rem;">📚</span>
                                </div>
                            @endif
                            <div class="position-absolute top-0 start-0 p-3">
                                <span class="badge bg-white text-primary" style="max-width: 150px; word-wrap: break-word; white-space: normal; overflow-wrap: break-word;">{{ $course->category->name ?? 'General' }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-2">{{ Str::limit($course->title, 40) }}</h5>
                            @if($course->subtitle)
                                <p class="card-text text-muted small mb-3">{{ Str::limit($course->subtitle, 60) }}</p>
                            @endif
                            
                            <hr>

                            @if($course->facilitator)
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span style="font-size: 1.25rem;">👨‍🏫</span>
                                    <div>
                                        <small class="d-block text-muted">Instructor</small>
                                        <small class="fw-semibold">{{ $course->facilitator->name }}</small>
                                    </div>
                                </div>
                            @endif

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <small class="d-block text-muted">Duration</small>
                                    <span class="fw-bold">{{ $course->course_hours }}h</span>
                                </div>
                                <div class="col-6">
                                    <small class="d-block text-muted">Level</small>
                                    <span class="fw-bold">{{ $course->level ?? 'Beginner' }}</span>
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
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No courses available in this category</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endif

<!-- COURSES WITH LEVEL TABS Section (Optional) -->
@if(in_array($courseDisplaySettings['course_display_mode'], ['level_tabs', 'both']))
<section class="py-5 py-md-8 bg-light">
    <div class="container-lg">
        <div class="text-center mb-5 mb-md-8" data-aos="fade-up">
            <h2 class="section-title">Browse By Program Type</h2>
            <p class="section-subtitle">Explore courses organized by program level</p>
        </div>

        <!-- Level Tab Navigation -->
        <ul class="nav nav-tabs mb-5 justify-content-center border-bottom-0" id="levelTabs" role="tablist">
            @if($courseDisplaySettings['show_all_levels_option'])
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-levels-tab" data-bs-toggle="tab" data-bs-target="#all-levels-pane" type="button" role="tab" aria-controls="all-levels-pane" aria-selected="true">
                    <i class="fa fa-book"></i> All Programs
                </button>
            </li>
            @endif

            @foreach(['Local', 'International', 'Diploma'] as $level)
                @if(in_array($level, $courseDisplaySettings['selected_levels']))
                <li class="nav-item" role="presentation">
                    <button class="nav-link{{ $courseDisplaySettings['show_all_levels_option'] ? '' : ' active' }}" 
                        id="{{ strtolower($level) }}-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#{{ strtolower($level) }}-pane" 
                        type="button" 
                        role="tab" 
                        aria-controls="{{ strtolower($level) }}-pane" 
                        aria-selected="false">
                        <i class="fa fa-graduation-cap"></i> {{ $level }}
                    </button>
                </li>
                @endif
            @endforeach
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="levelTabsContent">
            @if($courseDisplaySettings['show_all_levels_option'])
            <div class="tab-pane fade show active" id="all-levels-pane" role="tabpanel" aria-labelledby="all-levels-tab">
                <div class="row g-4">
                    @php
                        $allLevelCourses = \App\Models\Course::where('is_active', true)
                            ->orderBy('level')
                            ->orderBy('created_at', 'desc')
                            ->limit($courseDisplaySettings['max_courses_display'] ?? 12)
                            ->get();
                    @endphp

                    @forelse($allLevelCourses as $course)
                        <div class="col-lg-{{ $courseDisplaySettings['courses_per_row'] }} col-md-6 col-12">
                            <div class="card shadow-sm h-100 rounded-4 overflow-hidden course-card">
                                <div class="position-relative overflow-hidden" style="height: 220px;">
                                    @if($course->featured_image)
                                        <img src="{{ asset($course->featured_image) }}" alt="{{ $course->title }}" class="card-img-top h-100 object-fit-cover">
                                    @else
                                        <div class="w-100 h-100 bg-gradient-secondary d-flex align-items-center justify-content-center">
                                            <span style="font-size: 4rem;">📚</span>
                                        </div>
                                    @endif
                                    <div class="position-absolute top-0 start-0 p-3">
                                        <span class="badge bg-info text-white">{{ $course->level ?? 'General' }}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title fw-bold mb-2">{{ Str::limit($course->title, 40) }}</h5>
                                    @if($course->subtitle)
                                        <p class="card-text text-muted small mb-3">{{ Str::limit($course->subtitle, 60) }}</p>
                                    @endif
                                    
                                    <hr>

                                    @if($course->facilitator)
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span style="font-size: 1.25rem;">👨‍🏫</span>
                                            <div>
                                                <small class="d-block text-muted">Instructor</small>
                                                <small class="fw-semibold">{{ $course->facilitator->name }}</small>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <small class="d-block text-muted">Duration</small>
                                            <span class="fw-bold">{{ $course->course_hours }}h</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="d-block text-muted">Category</small>
                                            <span class="fw-bold text-truncate">{{ $course->category->name ?? 'General' }}</span>
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
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">No courses available</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @endif

            @foreach(['Local', 'International', 'Diploma'] as $level)
                @if(in_array($level, $courseDisplaySettings['selected_levels']))
                <div class="tab-pane fade{{ $courseDisplaySettings['show_all_levels_option'] ? '' : ' show active' }}" id="{{ strtolower($level) }}-pane" role="tabpanel" aria-labelledby="{{ strtolower($level) }}-tab">
                    <div class="row g-4">
                        @php
                            $levelCourses = \App\Models\Course::where('is_active', true)
                                ->where('level', $level)
                                ->orderBy('created_at', 'desc')
                                ->limit($courseDisplaySettings['max_courses_display'] ?? 12)
                                ->get();
                        @endphp

                        @forelse($levelCourses as $course)
                            <div class="col-lg-{{ $courseDisplaySettings['courses_per_row'] }} col-md-6 col-12">
                                <div class="card shadow-sm h-100 rounded-4 overflow-hidden course-card">
                                    <div class="position-relative overflow-hidden" style="height: 220px;">
                                        @if($course->featured_image)
                                            <img src="{{ asset($course->featured_image) }}" alt="{{ $course->title }}" class="card-img-top h-100 object-fit-cover">
                                        @else
                                            <div class="w-100 h-100 bg-gradient-success d-flex align-items-center justify-content-center">
                                                <span style="font-size: 4rem;">📚</span>
                                            </div>
                                        @endif
                                        <div class="position-absolute top-0 start-0 p-3">
                                            <span class="badge bg-white text-primary">{{ $course->category->name ?? 'General' }}</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold mb-2">{{ Str::limit($course->title, 40) }}</h5>
                                        @if($course->subtitle)
                                            <p class="card-text text-muted small mb-3">{{ Str::limit($course->subtitle, 60) }}</p>
                                        @endif
                                        
                                        <hr>

                                        @if($course->facilitator)
                                            <div class="d-flex align-items-center gap-2 mb-3">
                                                <span style="font-size: 1.25rem;">👨‍🏫</span>
                                                <div>
                                                    <small class="d-block text-muted">Instructor</small>
                                                    <small class="fw-semibold">{{ $course->facilitator->name }}</small>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="row g-3 mb-3">
                                            <div class="col-6">
                                                <small class="d-block text-muted">Duration</small>
                                                <span class="fw-bold">{{ $course->course_hours }}h</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="d-block text-muted">Category</small>
                                                <span class="fw-bold text-truncate">{{ $course->category->name ?? 'General' }}</span>
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
                            <div class="col-12 text-center py-5">
                                <p class="text-muted">No {{ strtolower($level) }} courses available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- TESTIMONIALS Section -->
<section id="testimonials" class="py-5 py-md-8 bg-light">
    <div class="container-lg">
        <div class="text-center mb-5 mb-md-8" data-aos="fade-up">
            <h2 class="section-title">{{ $homeSettings['testimonials']['section_title']['value'] ?? 'What Our Students Say' }}</h2>
            <p class="section-subtitle">{{ $homeSettings['testimonials']['section_subtitle']['value'] ?? 'Join thousands of satisfied learners' }}</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4" data-aos="fade-up">
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="testimonial-avatar bg-gradient-primary">AJ</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Adekunle Okafor</h6>
                            <small class="text-muted">Software Engineer</small>
                        </div>
                    </div>
                    <div class="stars mb-3">
                        ⭐⭐⭐⭐⭐
                    </div>
                    <p class="text-muted fst-italic">
                        "{{ \App\Models\HomepageSetting::getSetting('branding', 'site_name') ?? 'LMS' }}'s courses helped me transition into a new role within 6 months. The instructors are world-class!"
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="testimonial-avatar bg-success">CB</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Chisom Benson</h6>
                            <small class="text-muted">Marketing Manager</small>
                        </div>
                    </div>
                    <div class="stars mb-3">
                        ⭐⭐⭐⭐⭐
                    </div>
                    <p class="text-muted fst-italic">
                        "The flexibility to learn at my own pace was perfect. I completed my certification while working full-time!"
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="testimonial-avatar bg-danger">OE</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Oluwaseun Ekaette</h6>
                            <small class="text-muted">Data Analyst</small>
                        </div>
                    </div>
                    <div class="stars mb-3">
                        ⭐⭐⭐⭐⭐
                    </div>
                    <p class="text-muted fst-italic">
                        "I got a salary increase 3 months after completing. The practical projects were invaluable!"
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATS Section -->
<section class="py-5 py-md-8 bg-gradient-primary text-white">
    <div class="container-lg">
        <div class="row g-4 text-center">
            <div class="col-sm-6 col-lg-3" data-aos="zoom-in">
                <h2 class="stat-number">{{ $homeSettings['stats']['stat1_value']['value'] ?? '50K+' }}</h2>
                <p class="stat-label">{{ $homeSettings['stats']['stat1_label']['value'] ?? 'Active Learners' }}</p>
            </div>
            <div class="col-sm-6 col-lg-3" data-aos="zoom-in" data-aos-delay="100">
                <h2 class="stat-number">{{ $homeSettings['stats']['stat2_value']['value'] ?? '200+' }}</h2>
                <p class="stat-label">{{ $homeSettings['stats']['stat2_label']['value'] ?? 'Expert Courses' }}</p>
            </div>
            <div class="col-sm-6 col-lg-3" data-aos="zoom-in" data-aos-delay="200">
                <h2 class="stat-number">{{ $homeSettings['stats']['stat3_value']['value'] ?? '95%' }}</h2>
                <p class="stat-label">{{ $homeSettings['stats']['stat3_label']['value'] ?? 'Satisfaction Rate' }}</p>
            </div>
            <div class="col-sm-6 col-lg-3" data-aos="zoom-in" data-aos-delay="300">
                <h2 class="stat-number">{{ $homeSettings['stats']['stat4_value']['value'] ?? '1M+' }}</h2>
                <p class="stat-label">{{ $homeSettings['stats']['stat4_label']['value'] ?? 'Certificates Issued' }}</p>
            </div>
        </div>
    </div>
</section>

<!-- CALL-TO-ACTION Section -->
<section class="py-5 py-md-8 bg-gradient-secondary text-white">
    <div class="container-lg text-center" data-aos="fade-up">
        <h2 class="display-5 fw-bold mb-3">{{ $homeSettings['cta']['title']['value'] ?? 'Ready to Transform Your Career?' }}</h2>
        <p class="lead mb-5">{{ $homeSettings['cta']['description']['value'] ?? 'Start learning today and join thousands of professionals who\'ve achieved their goals.' }}</p>
        <div class="d-flex flex-wrap gap-3 justify-content-center">
            @guest
            <a href="{{ $homeSettings['cta']['button_link']['value'] ?? route('register') }}" class="btn btn-light btn-lg fw-bold">
                <i class="bi bi-pencil-square"></i> {{ $homeSettings['cta']['button_text']['value'] ?? 'Sign Up Free' }}
            </a>
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg fw-bold">
                Already a Member? Login
            </a>
            @else
            <a href="#all-courses" class="btn btn-light btn-lg fw-bold">
                <i class="bi bi-book"></i> Explore Courses Now
            </a>
            @endguest
        </div>
    </div>
</section>

<!-- CONTACT Section -->
<section id="contact" class="py-5 py-md-8 bg-light">
    <div class="container-lg">
        <div class="row g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <h2 class="section-title mb-4">{{ ($homeSettings['contact']['title']['value'] ?? null) ?? 'Get in Touch' }}</h2>
                <p class="text-muted mb-4">{{ ($homeSettings['contact']['subtitle']['value'] ?? null) ?? 'Have questions? Our support team is always ready to help.' }}</p>
                
                <div class="row g-4">
                    @if(isset($homeSettings['contact']) && count($homeSettings['contact']) > 0)
                        <div class="col-12">
                            <div class="d-flex gap-3">
                                <span style="font-size: 1.75rem;">{{ $homeSettings['contact']['email_icon']['value'] ?? '📧' }}</span>
                                <div>
                                    <h6 class="fw-bold">{{ $homeSettings['contact']['email_label']['value'] ?? 'Email' }}</h6>
                                    <p class="text-muted">{{ $homeSettings['contact']['email_value']['value'] ?? 'info@coinmac.org' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-3">
                                <span style="font-size: 1.75rem;">{{ $homeSettings['contact']['phone_icon']['value'] ?? '📞' }}</span>
                                <div>
                                    <h6 class="fw-bold">{{ $homeSettings['contact']['phone_label']['value'] ?? 'Phone' }}</h6>
                                    <p class="text-muted mb-2">{{ $homeSettings['contact']['phone_value']['value'] ?? '+234 (0) 806 563 2882' }}</p>
                                    @if(isset($homeSettings['contact']['whatsapp_link']) && $homeSettings['contact']['whatsapp_link']['value'])
                                        <a href="{{ $homeSettings['contact']['whatsapp_link']['value'] }}" target="_blank" class="btn btn-sm btn-success">
                                            <i class="bi bi-whatsapp" style="font-size: 1rem;"></i> Chat on WhatsApp
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-3">
                                <span style="font-size: 1.75rem;">{{ $homeSettings['contact']['address_icon']['value'] ?? '📍' }}</span>
                                <div>
                                    <h6 class="fw-bold">{{ $homeSettings['contact']['address_label']['value'] ?? 'Address' }}</h6>
                                    <p class="text-muted">{!! $homeSettings['contact']['address_value']['value'] ?? 'Lagos, Nigeria <br> Available Worldwide' !!}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-3">
                                <span style="font-size: 1.75rem;">{{ $homeSettings['contact']['hours_icon']['value'] ?? '⏰' }}</span>
                                <div>
                                    <h6 class="fw-bold">{{ $homeSettings['contact']['hours_label']['value'] ?? 'Support Hours' }}</h6>
                                    <p class="text-muted">{!! $homeSettings['contact']['hours_value']['value'] ?? 'Monday - Friday: 9am - 6pm<br>Weekend: 10am - 4pm (Nigeria Time)' !!}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-12">
                            <div class="d-flex gap-3">
                                <span style="font-size: 1.75rem;">📧</span>
                                <div>
                                    <h6 class="fw-bold">Email</h6>
                                    <p class="text-muted">{{ \App\Models\HomepageSetting::getSetting('contact', 'email_value') ?? 'info@example.org' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-3">
                                <span style="font-size: 1.75rem;">📞</span>
                                <div>
                                    <h6 class="fw-bold">Phone</h6>
                                    <p class="text-muted mb-2">{{ \App\Models\HomepageSetting::getSetting('contact', 'phone_value') ?? '+234 (0) 806 563 2882' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-3">
                                <span style="font-size: 1.75rem;">📍</span>
                                <div>
                                    <h6 class="fw-bold">Address</h6>
                                    <p class="text-muted">Lagos, Nigeria<br>Available Worldwide</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-3">
                                <span style="font-size: 1.75rem;">⏰</span>
                                <div>
                                    <h6 class="fw-bold">Support Hours</h6>
                                    <p class="text-muted">Monday - Friday: 9am - 6pm<br>Weekend: 10am - 4pm (Nigeria Time)</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-left">
                <div class="card shadow-sm rounded-4 p-5">
                    <h4 class="fw-bold mb-4">{{ $homeSettings['contact']['form_title']['value'] ?? 'Send us a Message' }}</h4>
                    <form method="POST" action="{{ route('contact.store') }}" class="space-y-4">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">{{ $homeSettings['contact']['form_name_label']['value'] ?? 'Full Name' }}</label>
                            <input type="text" id="name" name="name" required class="form-control form-control-lg" placeholder="Your Name" value="{{ old('name') }}">
                        </div>
                        <div class="mb-3">
                            <label for="email_contact" class="form-label fw-semibold">{{ $homeSettings['contact']['form_email_label']['value'] ?? 'Email' }}</label>
                            <input type="email" id="email_contact" name="email" required class="form-control form-control-lg" placeholder="your@email.com" value="{{ old('email') }}">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-semibold">{{ $homeSettings['contact']['form_phone_label']['value'] ?? 'Phone (Optional)' }}</label>
                            <input type="tel" id="phone" name="phone" class="form-control form-control-lg" placeholder="+234..." value="{{ old('phone') }}">
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-semibold">{{ $homeSettings['contact']['form_subject_label']['value'] ?? 'Subject (Optional)' }}</label>
                            <input type="text" id="subject" name="subject" class="form-control form-control-lg" placeholder="Message subject" value="{{ old('subject') }}">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label fw-semibold">{{ $homeSettings['contact']['form_message_label']['value'] ?? 'Message' }}</label>
                            <textarea id="message" name="message" rows="4" required class="form-control form-control-lg" placeholder="Your message...">{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                            <i class="bi bi-send"></i> {{ $homeSettings['contact']['form_submit_text']['value'] ?? 'Send Message' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Category filter functionality
function filterCoursesByCategory(categoryId) {
    const courseItems = document.querySelectorAll('.course-item');
    
    if (categoryId === '') {
        // Show all courses
        courseItems.forEach(item => {
            item.style.display = 'block';
        });
    } else {
        // Filter by category
        courseItems.forEach(item => {
            if (item.getAttribute('data-category') === categoryId) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
}
</script>


<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        // Fetch design settings from database
        $designSettings = [
            'main_bg_color' => \App\Models\HomepageSetting::getSetting('design', 'main_bg_color') ?? '#ffffff',
            'main_bg_image' => \App\Models\HomepageSetting::getSetting('design', 'main_bg_image'),
            'main_bg_opacity' => \App\Models\HomepageSetting::getSetting('design', 'main_bg_opacity') ?? '100',
            'navbar_bg_color' => \App\Models\HomepageSetting::getSetting('design', 'navbar_bg_color') ?? 'linear-gradient(135deg, #fff 0%, #f8f9fa 100%)',
            'navbar_text_color' => \App\Models\HomepageSetting::getSetting('design', 'navbar_text_color') ?? '#333',
            'container_bg_color' => \App\Models\HomepageSetting::getSetting('design', 'container_bg_color') ?? '#f8f9fa',
        ];
        
        // Fetch branding settings from database
        $brandingSettings = [
            'site_name' => \App\Models\HomepageSetting::getSetting('branding', 'site_name') ?? 'LMS',
            'site_tagline' => \App\Models\HomepageSetting::getSetting('branding', 'site_tagline'),
            'logo_light' => \App\Models\HomepageSetting::getImagePath('branding', 'logo_light'),
            'logo_dark' => \App\Models\HomepageSetting::getImagePath('branding', 'logo_dark'),
            'logo_height' => \App\Models\HomepageSetting::getSetting('branding', 'logo_height') ?? '50',
            'show_logo' => \App\Models\HomepageSetting::getSetting('branding', 'show_logo') ?? '1',
            'show_site_name' => \App\Models\HomepageSetting::getSetting('branding', 'show_site_name') ?? '1',
            'show_site_tagline' => \App\Models\HomepageSetting::getSetting('branding', 'show_site_tagline') ?? '1',
        ];
    @endphp
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $brandingSettings['site_name'] }} - Professional Online Learning Platform. Access world-class courses from expert instructors."/>
    <meta name="keywords" content="online learning, courses, education, professional development, training"/>
    <meta name="author" content="{{ $brandingSettings['site_name'] }}"/>

<title>@yield('title', $brandingSettings['site_name'] . ' - Professional Online Learning Platform')</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AOS (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', 'Inter', sans-serif;
            color: #333;
            background-color: #fff;
            padding-top: 70px;
        }

        /* Navbar Styles */
        .navbar {
            background: {{ $designSettings['navbar_bg_color'] }};
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            padding: 1rem 0;
            transition: all 0.3s ease;
            z-index: 9999 !important;
            top: 0 !important;
            position: fixed !important;
            width: 100% !important;
            left: 0 !important;
            right: 0 !important;
            overflow: visible !important;
        }

        .navbar.scrolled {
            box-shadow: 0 5px 25px rgba(0,0,0,0.12);
            padding: 0.5rem 0;
        }

        .navbar-brand {
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            z-index: 10000;
        }

        .navbar-brand img {
            max-height: {{ $brandingSettings['logo_height'] }}px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.1));
        }

        .navbar-brand-text {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .navbar-brand-name {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            whitespace: nowrap;
        }

        .navbar-brand-tagline {
            font-size: 0.65rem;
            font-weight: 500;
            color: #666;
            letter-spacing: 0.5px;
        }

        .navbar-toggler {
            border: none;
            padding: 0.25rem 0.5rem;
            margin-left: auto;
        }

        .navbar-toggler:focus {
            box-shadow: none;
            outline: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%232563EB' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .navbar-collapse {
            /* Default Bootstrap behavior */
            flex-basis: 100%;
        }

        .navbar-nav {
            padding-top: 0.5rem;
        }

        .nav-link {
            font-weight: 500;
            color: {{ $designSettings['navbar_text_color'] }} !important;
            margin: 0 0.5rem;
            padding: 0.5rem 0 !important;
            transition: all 0.3s ease;
            position: relative;
            display: inline-block;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%);
            transition: width 0.3s ease;
        }

        .nav-link:hover {
            color: #2563EB !important;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link.active {
            color: #2563EB !important;
        }

        .nav-link.active::after {
            width: 100%;
        }

        .dropdown-menu {
            background: white;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-radius: 8px;
            animation: slideDown 0.2s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item {
            color: #333;
            transition: all 0.2s ease;
            padding: 0.75rem 1.25rem;
        }

        .dropdown-item:hover {
            background-color: #f3f4f6;
            color: #2563EB;
            padding-left: 1.5rem;
        }

        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu > .dropdown-menu {
            display: none;
            position: absolute;
            left: 100%;
            top: 0;
            margin-top: 0;
            border-radius: 8px;
            min-width: 200px;
        }

        .dropdown-submenu:hover > .dropdown-menu {
            display: block;
        }

        .dropdown-submenu > a::after {
            content: '\f054';
            font-family: 'bootstrap-icons';
            margin-left: auto;
            float: right;
        }

        /* Mobile Dropdown Fix - Align dropdowns to left on small screens */
        @media (max-width: 991px) {
            .dropdown-submenu > .dropdown-menu {
                position: static !important;
                display: none !important;
                left: 0 !important;
                top: auto !important;
                border-radius: 8px;
                margin-top: 0;
                background: #f3f4f6;
                border: none;
                box-shadow: none;
                padding: 0.5rem 0 0.5rem 1.5rem;
            }

            .dropdown-submenu:hover > .dropdown-menu,
            .dropdown-submenu.show > .dropdown-menu {
                display: block !important;
            }

            .dropdown-submenu > a::after {
                content: '\f285';
                font-family: 'bootstrap-icons';
                margin-left: auto;
                float: right;
                transform: rotate(0deg);
            }

            .dropdown-submenu.show > a::after {
                transform: rotate(90deg);
            }
        }

        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            border: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
            background: linear-gradient(135deg, #1D4ED8 0%, #1e40af 100%);
        }

        .btn-outline-primary {
            color: #2563EB;
            border-color: #2563EB;
            font-weight: 600;
        }

        .btn-outline-primary:hover {
            background: #2563EB;
            border-color: #2563EB;
        }

        /* Section Styles */
        section {
            padding: 4rem 0;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #1a1a1a 0%, #555 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.15);
        }

        /* Gradient Backgrounds */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%);
        }

        .bg-gradient-secondary {
            background: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%);
        }

        .bg-gradient-light {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.95) 0%, rgba(79, 70, 229, 0.95) 100%),
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="1200" height="800" fill="url(%23grid)"/></svg>');
            background-attachment: fixed;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        /* Feature Grid */
        .feature-card {
            text-align: center;
            padding: 2rem;
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: inline-block;
        }

        /* Stats Section */
        .stat-card {
            text-align: center;
            padding: 2rem;
            color: white;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Testimonial Cards */
        .testimonial-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .testimonial-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        .stars {
            color: #fbbf24;
            margin-bottom: 1rem;
        }

        /* Footer Styles */
        footer {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            padding: 3rem 0 1rem;
        }

        footer a {
            color: #d1d5db;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #2563EB;
        }

        /* Utilities */
        .text-gradient {
            background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .shadow-lg-custom {
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        .animate-slide-in-right {
            animation: slideInRight 0.6s ease-out;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: white;
                margin-top: 0.5rem;
                padding: 1rem;
                border-radius: 0 0 8px 8px;
                border-top: 2px solid #f3f4f6;
                max-height: calc(100vh - 80px);
                overflow-y: auto;
                overflow-x: hidden;
            }

            .navbar-nav {
                display: flex;
                flex-direction: column;
                gap: 0 !important;
            }

            .nav-item {
                width: 100%;
            }

            .nav-link {
                display: block;
                width: 100%;
                margin: 0;
                padding: 0.75rem 0 !important;
            }

            .nav-link::after {
                display: none;
            }

            .dropdown-menu {
                background: #f9fafb;
                border: none;
                box-shadow: none;
                width: auto;
                position: static;
                margin: 0;
                padding: 0.5rem 0 0.5rem 1.5rem;
            }

            .dropdown-item {
                padding-left: 0;
                font-size: 0.9rem;
            }
        }

        @media (min-width: 992px) {
            .navbar-collapse {
                background: transparent;
                margin-top: 0;
                padding: 0;
                border: none;
                border-radius: 0;
            }

            .navbar-nav {
                display: flex;
                flex-direction: row;
                gap: 0.5rem !important;
                align-items: center;
            }

            .nav-item {
                width: auto;
            }

            .nav-link {
                display: inline-block;
                width: auto;
                margin: 0 0.5rem;
                padding: 0.5rem 0 !important;
            }

            .nav-link::after {
                display: block;
            }

            .dropdown-menu {
                background: white;
                border: 1px solid #e5e7eb;
                box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                position: absolute;
                margin-top: 0.5rem;
                padding: 0.5rem 0;
            }

            .dropdown-item {
                padding-left: 1rem;
            }
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 1.75rem;
            }

            section {
                padding: 2rem 0;
            }

            .navbar-brand {
                font-size: 1.35rem;
            }

            .nav-link {
                font-size: 0.95rem;
            }
        }

        /* Scroll behavior */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #2563EB;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #1D4ED8;
        }

        /* Carousel Styles */
        .carousel-section {
            position: relative;
            z-index: 1;
            /* margin-top: 70px; */
        }

        #heroCarousel {
            background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);
        }

        .carousel-item {
            position: relative;
        }

        .carousel-item::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            pointer-events: none;
        }

        .carousel-item img {
            object-fit: cover;
            object-position: center;
        }

        .carousel-caption {
            bottom: 25%;
            background: rgba(0, 0, 0, 0.4);
            padding: 3rem 2rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .carousel-caption h1 {
            margin-bottom: 1rem;
            font-size: 3rem;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
        }

        .carousel-caption p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: auto;
            height: auto;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            background: rgba(0, 0, 0, 0.8);
        }

        .carousel-indicators button {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
        }

        .carousel-indicators button.active {
            background: white;
            width: 20px;
            height: 20px;
        }

        @media (max-width: 768px) {
            .carousel-caption {
                bottom: 15%;
                padding: 1.5rem 1rem;
            }

            .carousel-caption h1 {
                font-size: 1.75rem;
            }

            .carousel-caption p {
                font-size: 1rem;
            }

            #heroCarousel {
                height: 350px !important;
            }
        }
    </style>

    <!-- Page Specific Styles -->
    @stack('styles')

    <!-- Timezone Detector -->
    <script>
        @include('partials.timezone-detector-inline')
    </script>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-light" id="mainNavbar">
        <div class="container-lg">
            <a class="navbar-brand" href="{{ route('courses.index') }}">
                @if($brandingSettings['show_logo'] && $brandingSettings['logo_light'])
                    <img src="{{ $brandingSettings['logo_light'] }}" alt="{{ $brandingSettings['site_name'] }}">
                @elseif($brandingSettings['show_logo'])
                    <i class="bi bi-book" style="font-size: 2rem; margin-right: 0.5rem;"></i>
                @endif
                <div class="navbar-brand-text">
                    @if($brandingSettings['show_site_name'])
                        <span class="navbar-brand-name">{{ $brandingSettings['site_name'] }}</span>
                    @endif
                    @if($brandingSettings['show_site_tagline'] && $brandingSettings['site_tagline'])
                        <span class="navbar-brand-tagline">{{ $brandingSettings['site_tagline'] }}</span>
                    @endif
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-2">
                    <li class="nav-item">
                        <a class="nav-link" href="/#hero">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#about">About</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="{{ route('courses.index') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-book-half"></i> Courses
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <div class="px-3 py-2" style="position: relative;">
                                    <input type="text" id="navSearchInput" class="form-control form-control-sm" placeholder="🔍 Search courses...">
                                    <div id="navSearchResults" class="position-absolute bg-white rounded shadow-lg" style="width: 320px; min-width: 100%; display: none; max-height: 350px; overflow-y: auto; overflow-x: visible; z-index: 10001; top: 45px; left: 12px; border: 1px solid #e5e7eb;"></div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('courses.all') }}"><strong>All Courses</strong></a></li>
                            <li><hr class="dropdown-divider"></li>
                            
                            <!-- Course Levels Dropdown -->
                            @php
                                $levels = ['Local', 'International', 'Diploma'];
                            @endphp
                            @foreach($levels as $level)
                            <li class="dropdown-submenu">
                                <a class="dropdown-item dropdown-toggle" href="#">{{ $level }}</a>
                                <ul class="dropdown-menu">
                                    @php
                                        $levelCategories = \App\Models\CourseCategory::where('is_active', true)
                                            ->whereIn('id', \App\Models\Course::where('level', $level)->pluck('category_id')->toArray())
                                            ->orderBy('sort_order')
                                            ->distinct()
                                            ->get();
                                    @endphp
                                    @forelse($levelCategories as $category)
                                    <li><a class="dropdown-item" href="{{ route('courses.by-level-category', ['level' => $level, 'category' => $category]) }}">{{ $category->name }}</a></li>
                                    @empty
                                    <li><a class="dropdown-item disabled">No categories</a></li>
                                    @endforelse
                                </ul>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="{{ route('services.index') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Services
                        </a>
                        <ul class="dropdown-menu">
                            @php
                                $services = \App\Models\Service::where('published', true)->limit(8)->get();
                            @endphp
                            @forelse($services as $service)
                            <li><a class="dropdown-item" href="{{ route('services.show', $service) }}">{{ $service->title }}</a></li>
                            @empty
                            <li><a class="dropdown-item disabled">No services available</a></li>
                            @endforelse
                            @if($services->count() > 0)
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item fw-bold text-primary" href="{{ route('services.index') }}">View All Services</a></li>
                            @endif
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('galleries.index') }}">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#contact">Contact</a>
                    </li>
                    @auth
                    
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('courses.my-enrollments') }}">My Courses</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('my.profile') }}">Profile</a></li>
                            @if(Auth::user()->hasRole('admin'))
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>

                            <li><a class="dropdown-item" href="{{ route('admin.courses.index') }}">Manage Courses</a></li>
                            @endif
                            @if(Auth::user()->hasRole('instructor'))
                            <li><a class="dropdown-item" href="{{ route('instructor.dashboard') }}">My Dashboard</a></li>

                            @endif
                            @if(Auth::user()->hasRole('student'))
                            <li><a class="dropdown-item" href="{{ route('student.dashboard') }}">My Dashboard</a></li>

                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Register</a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main style="margin-top: 60px; 
                 background-color: {{ $designSettings['main_bg_color'] }};
                 @if($designSettings['main_bg_image']) 
                 background-image: url('{{ asset($designSettings['main_bg_image']) }}');
                 background-size: cover;
                 background-attachment: fixed;
                 @endif
                 opacity: {{ ($designSettings['main_bg_opacity'] ?? 100) / 100 }};">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container-lg py-5 border-bottom border-secondary">
            <div class="row g-4">
                <div class="col-lg-3">
                    @if($brandingSettings['show_site_name'])
                    <h5 class="mb-3"><i class="bi bi-book text-primary"></i> {{ $brandingSettings['site_name'] }}</h5>
                    @endif
                    <p class="text-muted small">Empowering professionals worldwide with quality education and skill development.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('courses.index') }}">Home</a></li>
                        <li class="mb-2"><a href="#featured-courses">Courses</a></li>
                        <li class="mb-2"><a href="#about">About Us</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="mb-3">Courses</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('courses.index') }}"><strong>All Courses</strong></a></li>
                        @foreach(\App\Models\CourseCategory::where('is_active', true)->orderBy('sort_order')->take(5)->get() as $cat)
                        <li class="mb-2"><a href="{{ route('courses.by-category', $cat) }}">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="mb-3">Support</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="#contact">Contact Us</a></li>
                        <li class="mb-2"><a href="#">Privacy Policy</a></li>
                        <li class="mb-2"><a href="#">Terms of Service</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="container-lg py-3 text-center text-muted small">
            <p>&copy; 2026 @if($brandingSettings['show_site_name']){{ $brandingSettings['site_name'] }} . @endif All rights reserved. | Empowering Professionals Globally</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Page Specific Scripts -->
    @stack('scripts')

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Navbar search functionality
        const navSearchInput = document.getElementById('navSearchInput');
        const navSearchResults = document.getElementById('navSearchResults');
        let navSearchTimeout;

        if (navSearchInput) {
            navSearchInput.addEventListener('input', function() {
                clearTimeout(navSearchTimeout);
                const query = this.value.trim();

                if (query.length < 2) {
                    navSearchResults.style.display = 'none';
                    return;
                }

                navSearchResults.innerHTML = '<div style="padding: 8px 12px; color: #666; font-size: 12px;">Searching...</div>';
                navSearchResults.style.display = 'block';

                navSearchTimeout = setTimeout(() => {
                    fetch(`{{ route('courses.search') }}?q=${encodeURIComponent(query)}&limit=5`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data.length > 0) {
                                navSearchResults.innerHTML = data.data.map(course => `
                                    <a href="${course.url}" class="dropdown-item" style="border-bottom: 1px solid #f0f0f0; padding: 8px 12px; white-space: normal; display: flex; gap: 8px; align-items: flex-start;">
                                        ${course.featured_image ? `<img src="${course.featured_image}" alt="" style="width: 40px; height: 40px; min-width: 40px; object-fit: cover; border-radius: 4px; flex-shrink: 0;">` : '<span style="width: 40px; height: 40px; min-width: 40px; background: #e9ecef; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">📚</span>'}
                                        <div style="flex: 1; width: 100%; min-width: 0;">
                                            <div style="font-weight: 500; color: #2c3e50; font-size: 13px; word-wrap: break-word; overflow-wrap: break-word; white-space: normal; line-height: 1.3;">${course.title}</div>
                                            <div style="font-size: 11px; color: #7f8c8d; word-wrap: break-word; overflow-wrap: break-word; white-space: normal; line-height: 1.3;">${course.category}</div>
                                        </div>
                                    </a>
                                `).join('');
                            } else {
                                navSearchResults.innerHTML = '<div style="padding: 8px 12px; color: #999; font-size: 12px;">No courses found</div>';
                            }
                        });
                }, 300);
            });
        }

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scroll for navbar links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && document.querySelector(href)) {
                    e.preventDefault();
                    document.querySelector(href).scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Mobile submenu toggle functionality
        const isSmallScreen = () => window.innerWidth <= 991;

        document.querySelectorAll('.dropdown-submenu > a').forEach(submenuLink => {
            submenuLink.addEventListener('click', function(e) {
                if (isSmallScreen()) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const submenuItem = this.closest('.dropdown-submenu');
                    const submenuList = submenuItem.querySelector('.dropdown-menu');
                    
                    // Close other open submenus in the same parent
                    document.querySelectorAll('.dropdown-submenu').forEach(item => {
                        if (item !== submenuItem) {
                            item.classList.remove('show');
                            const menu = item.querySelector('.dropdown-menu');
                            if (menu) menu.style.display = 'none';
                        }
                    });
                    
                    // Toggle current submenu
                    if (submenuItem.classList.contains('show')) {
                        submenuItem.classList.remove('show');
                        submenuList.style.display = 'none';
                    } else {
                        submenuItem.classList.add('show');
                        submenuList.style.display = 'block';
                    }
                }
            });
        });

        // Close submenus when clicking outside
        document.addEventListener('click', function(e) {
            if (isSmallScreen() && !e.target.closest('.dropdown-submenu')) {
                document.querySelectorAll('.dropdown-submenu.show').forEach(item => {
                    item.classList.remove('show');
                    const menu = item.querySelector('.dropdown-menu');
                    if (menu) menu.style.display = 'none';
                });
            }
        });

        // Handle window resize to close submenus when switching from mobile to desktop
        window.addEventListener('resize', function() {
            if (!isSmallScreen()) {
                document.querySelectorAll('.dropdown-submenu.show').forEach(item => {
                    item.classList.remove('show');
                });
            }
        });
    </script>

    <!-- Tawk.to Chat Widget -->
    <script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/51b7d41e286dc8be5be7287f400443803a5ac4ee/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
    </script>
    <!-- End Tawk.to Chat Widget -->

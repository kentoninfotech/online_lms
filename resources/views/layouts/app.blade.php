<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- [Head] start -->
<head>
    <title>@yield('title', 'Dashboard')</title>
    <!-- [Meta] -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Online Class Management System"/>
    <meta name="keywords" content="Online Class Management System, Learning Management System"/>
    <meta name="author" content="Online Class Management System"/>

    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon" />
    
    <!-- [Bootstrap] icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- map-vector css -->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/jsvectormap.min.css') }}" />

    <!-- [Google Font : Poppins] -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />

    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />

    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />

    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />

    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />

    <!-- Page Specific Stylesheet -->
    @stack('styles')

    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />


</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-header="header-1" data-pc-preset="preset-1" data-pc-sidebar-theme="light" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
    <nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
        <a href="/" class="b-brand text-primary">
            <!-- ========   Change your logo from here   ============ -->
            <img src="../assets/images/logo.svg" alt="logo image" class="logo-lg" />
        </a>
        </div>
        <div class="navbar-content">

        {{-- Sidebar List --}}
        @auth
            @if(auth()->user()->user_type === 'admin')
                @include('layouts.partials.sidebars.admin')
            @elseif(auth()->user()->user_type === 'instructor')
                @include('layouts.partials.sidebars.instructor')
            @elseif(auth()->user()->user_type === 'parent')
                @include('layouts.partials.sidebars.parent')
            @elseif(auth()->user()->user_type === 'student')
                @include('layouts.partials.sidebars.student')
            @endif
        @endauth
        {{-- End Sidebar List --}}
        
        </div>
    </div>
    </nav>
    <!-- [ Sidebar Menu ] end -->

    <!-- [ Header Topbar ] start -->
    <header class="pc-header">
     @include('layouts.partials.header')
    </header>
    <!-- [ Header ] end -->



        <!-- [ Main Content ] start -->
        <div class="pc-container">
            <div class="pc-content">
                
            {{-- Toast Notification --}}
            @include('components.toast')
            {{-- End Toast Notification --}}

                <!-- [ Main Content ] start -->
                @yield('content')
                <!-- [ Main Content ] end -->
            </div>
        </div>
      <!-- [ Main Content ] end -->

<!-- [ Footer ] start -->    
{{-- Footer --}}
@include('layouts.partials.footer')
<!-- [footer] end -->
    
</body>
<!-- [Body] end -->
</html>

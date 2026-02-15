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

    <!-- [Styles : Scripts : Icons : Fonts] -->

    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon" />

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

    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    <!-- [Timezone Detector] - Load early to detect timezone before login -->
    <script>
        @include('partials.timezone-detector-inline')
    </script>
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

    <!-- [ Main Content ] start -->
    @yield('content')
    <!-- [ Main Content ] end -->
    

  </body>
  <!-- [Body] end -->
</html>

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
    @php
        $favicon = \App\Models\HomepageSetting::getImagePath('branding', 'favicon') ?? asset('assets/images/favicon.png');
    @endphp
    <link rel="icon" href="{{ $favicon }}" type="image/x-icon" />
    
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

    <!-- [Flatpickr] Date/Time Picker Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/themes/light.css">

    <!-- [Flatpickr Z-Index Fix] Ensure calendar is always visible -->
    <style>
        .flatpickr-calendar {
            z-index: 99999 !important;
            box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.1) !important;
        }
        .flatpickr-input {
            cursor: pointer;
        }
    </style>

    <!-- [Timezone Detector] - Load early to detect timezone before page interactions -->
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
    <!-- [ Sidebar Menu ] start -->
    <nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
        <a href="/" class="b-brand text-primary d-flex align-items-center gap-2">
            <!-- ========   Admin uploaded logo from Site Settings   ============ -->
            @php
                $logo = \App\Models\HomepageSetting::getImagePath('branding', 'logo_light', asset('assets/images/logo.svg'));
                $siteName = \App\Models\HomepageSetting::getSetting('branding', 'site_name', 'COINMAC');
            @endphp
            @if($logo && $logo !== asset('assets/images/logo.svg'))
                <img src="{{ $logo }}" alt="logo" class="logo-lg" style="height: 32px; width: auto;" />
            @else
                <img src="{{ asset('assets/images/logo.svg') }}" alt="logo" class="logo-lg" />
            @endif
            <span class="d-none d-md-inline" style="font-weight: 600; font-size: 0.9rem;">{{ $siteName }}</span>
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

@include('layouts.partials.whatsapp-chat-widget')

<!-- [Flatpickr] Date/Time Picker Script - Load with defer to ensure it's available -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js" defer></script>

<!-- [Global DateTime Picker Initialization] - Wait for Flatpickr to load -->
<script>
    function initializeDatePickers() {
        // Check if flatpickr is loaded
        if (typeof flatpickr === 'undefined') {
            // If not loaded, retry after 100ms
            setTimeout(initializeDatePickers, 100);
            return;
        }

        // Configuration for all datetime pickers
        const datetimeConfig = {
            enableTime: true,
            dateFormat: "d/m/Y H:i",
            time_24hr: true,
            minuteIncrement: 1,
            altInput: false,
            allowInput: true,
            clickOpens: true,
            closeOnSelect: true,
            static: false,
            position: 'auto',
            appendTo: document.body,
            onClose: function(selectedDates, dateStr, instance) {
                const hiddenInput = instance.element.parentElement?.querySelector('input[type="hidden"]');
                if (hiddenInput && dateStr) {
                    hiddenInput.value = dateStr;
                    const event = new Event('change', { bubbles: true });
                    hiddenInput.dispatchEvent(event);
                }
            }
        };
        
        const dateConfig = {
            enableTime: false,
            dateFormat: "d/m/Y",
            altInput: false,
            allowInput: true,
            clickOpens: true,
            closeOnSelect: true,
            static: false,
            position: 'auto',
            appendTo: document.body
        };
        
        // Initialize datetime-local inputs with Flatpickr
        document.querySelectorAll('input[type="datetime-local"]').forEach(function(elem) {
            if (!elem.hasAttribute('data-flatpickr-initialized')) {
                try {
                    flatpickr(elem, datetimeConfig);
                    elem.setAttribute('data-flatpickr-initialized', 'true');
                    elem.setAttribute('placeholder', 'DD/MM/YYYY HH:mm');
                } catch(e) {
                    console.error('Error initializing datetime picker:', e);
                }
            }
        });

        // Initialize date inputs with Flatpickr
        document.querySelectorAll('input[type="date"]').forEach(function(elem) {
            if (!elem.hasAttribute('data-flatpickr-initialized')) {
                try {
                    flatpickr(elem, dateConfig);
                    elem.setAttribute('data-flatpickr-initialized', 'true');
                    elem.setAttribute('placeholder', 'DD/MM/YYYY');
                } catch(e) {
                    console.error('Error initializing date picker:', e);
                }
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeDatePickers);
    } else {
        initializeDatePickers();
    }

    // Re-initialize pickers when new forms are dynamically added
    window.initializeNewDatePickers = function() {
        if (typeof flatpickr === 'undefined') {
            console.warn('Flatpickr not loaded yet');
            return;
        }

        const datetimeConfig = {
            enableTime: true,
            dateFormat: "d/m/Y H:i",
            time_24hr: true,
            minuteIncrement: 1,
            altInput: false,
            allowInput: true,
            clickOpens: true,
            closeOnSelect: true,
            static: false,
            position: 'auto',
            appendTo: document.body,
            onClose: function(selectedDates, dateStr, instance) {
                const hiddenInput = instance.element.parentElement?.querySelector('input[type="hidden"]');
                if (hiddenInput && dateStr) {
                    hiddenInput.value = dateStr;
                    const event = new Event('change', { bubbles: true });
                    hiddenInput.dispatchEvent(event);
                }
            }
        };
        
        const dateConfig = {
            enableTime: false,
            dateFormat: "d/m/Y",
            altInput: false,
            allowInput: true,
            clickOpens: true,
            closeOnSelect: true,
            static: false,
            position: 'auto',
            appendTo: document.body
        };
        
        document.querySelectorAll('input[type="datetime-local"]:not([data-flatpickr-initialized])').forEach(function(elem) {
            try {
                flatpickr(elem, datetimeConfig);
                elem.setAttribute('data-flatpickr-initialized', 'true');
                elem.setAttribute('placeholder', 'DD/MM/YYYY HH:mm');
            } catch(e) {
                console.error('Error initializing datetime picker:', e);
            }
        });

        document.querySelectorAll('input[type="date"]:not([data-flatpickr-initialized])').forEach(function(elem) {
            try {
                flatpickr(elem, dateConfig);
                elem.setAttribute('data-flatpickr-initialized', 'true');
                elem.setAttribute('placeholder', 'DD/MM/YYYY');
            } catch(e) {
                console.error('Error initializing date picker:', e);
            }
        });
    };
</script>
    
</body>
<!-- [Body] end -->
</html>

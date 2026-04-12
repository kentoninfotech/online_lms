<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <!-- [Head] start -->
  <head>
    <title>@yield('title', 'Login')</title>
    <!-- [Meta] -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ \App\Models\HomepageSetting::getSetting('branding', 'site_name') ?? 'LMS' }} - {{ \App\Models\HomepageSetting::getSetting('branding', 'site_tagline') ?? 'Professional Online Learning Platform' }}"/>
    <meta name="keywords" content="online learning, courses, education, professional development"/>
    <meta name="author" content="{{ \App\Models\HomepageSetting::getSetting('branding', 'site_name') ?? 'LMS Inc' }}"/>

    <!-- [Favicon] icon -->
    @php
        $favicon = \App\Models\HomepageSetting::getImagePath('branding', 'favicon') ?? asset('assets/images/favicon.png');
    @endphp
    <link rel="icon" href="{{ $favicon }}" type="image/x-icon" />

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
            background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 500px;
        }

        .auth-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            padding: 2rem;
            overflow: hidden;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 1.5rem;
        }

        .auth-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            color: #666;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .form-check-input {
            width: 1.25rem;
            height: 1.25rem;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #2563EB;
            border-color: #2563EB;
        }

        .form-check-label {
            cursor: pointer;
            user-select: none;
            color: #666;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 1rem;
        }

        .forgot-password a {
            color: #2563EB;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #1D4ED8;
        }

        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f3f4f6;
        }

        .auth-footer p {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .auth-footer a {
            color: #2563EB;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            color: #1D4ED8;
        }

        .demo-credentials {
            background: linear-gradient(135deg, #f0f9ff 0%, #f3f4f6 100%);
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .demo-credentials h6 {
            color: #1e40af;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .demo-credentials h6 i {
            font-size: 1.2rem;
        }

        .demo-group {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dbeafe;
        }

        .demo-group:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .demo-label {
            color: #1e40af;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
        }

        .demo-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .demo-item i {
            color: #2563EB;
            width: 20px;
        }

        .password-field {
            position: relative;
        }

        .btn-password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px 8px;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
            z-index: 10;
        }

        .btn-password-toggle:hover {
            color: #495057;
        }

        #passwordInput {
            padding-right: 40px;
        }

        .alert {
            border-radius: 8px;
        }

        @media (max-width: 576px) {
            .auth-card {
                padding: 1.5rem;
            }

            .auth-header h2 {
                font-size: 1.5rem;
            }

            .demo-credentials {
                padding: 1rem;
            }
        }
    </style>
    
    <!-- Timezone Detector -->
    <script>
        @include('partials.timezone-detector-inline')
    </script>
  </head>
  <!-- [Head] end -->

  <!-- [Body] Start -->
<body>
    <!-- [ Main Content ] start -->
    @yield('content')
    <!-- [ Main Content ] end -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
  <!-- [Body] end -->
</html>

@extends('layouts.auth')

@section('title', 'Session Expired')

@section('content')
<div class="auth-main v1 bg-grd-primary">
  <div class="auth-wrapper">
    <div class="auth-form">
      <div class="card my-5">
        <div class="card-body">
          <div class="text-center">
            <img src="{{ asset('assets/images/logo.png') }}" alt="images" width="90px" class="img-fluid mb-4" />
            
            <!-- Error Icon -->
            <div class="mb-4">
              <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto">
                <circle cx="40" cy="40" r="40" fill="#FEE4E2"/>
                <circle cx="40" cy="40" r="36" fill="none" stroke="#F04438" stroke-width="2"/>
                <path d="M40 24V40M40 48H40.01M40 12C24.536 12 12 24.536 12 40C12 55.464 24.536 68 40 68C55.464 68 68 55.464 68 40C68 24.536 55.464 12 40 12Z" stroke="#F04438" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>

            <h3 class="mb-2" style="color: #1F2937;">Session Expired</h3>
            <p class="text-muted mb-4 f-w-400">
              Your session has expired due to inactivity or you have been logged out. 
              For your security, please log in again to continue.
            </p>

            <div class="alert alert-info alert-dismissible fade show mb-4" role="alert" style="background-color: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46;">
              <i class="feather icon-info me-2"></i>
              <strong>Tip:</strong> Always keep your session active for a seamless experience.
            </div>

            <div class="d-grid mt-4 mb-3">
              <a href="{{ route('login') }}" class="btn btn-primary">
                <i class="feather icon-arrow-right me-2"></i>Go to Login Page
              </a>
            </div>

            <p class="mb-0 text-muted f-w-400">
              Need help? <a href="#" class="link-primary text-decoration-none">Contact Support</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  body {
    overflow: hidden;
  }
</style>
@endsection

@extends('layouts.auth')

@php
    $siteName = \App\Models\HomepageSetting::getSetting('branding', 'site_name') ?? 'LMS';
@endphp

@section('title', 'Email Verification - ' . $siteName)

@section('content')

<div class="auth-container">
    <div class="auth-card">
        <!-- Header Section -->
        <div class="auth-header">
            <div style="font-size: 3rem; margin-bottom: 1rem;">✉️</div>
            <h2>Verify Your Email</h2>
            <p>One last step to complete your registration</p>
        </div>

        <!-- Warning/Info Message -->
        @if (session('resent'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-left: 5px solid #28a745;">
                <i class="bi bi-check-circle-fill me-2" style="font-size: 1.2rem; color: #28a745;"></i>
                <strong style="font-size: 1.05rem;">Email Sent Successfully! ✓</strong><br>
                <span style="font-size: 0.95rem;">A fresh verification link has been sent to your email address. Please check your inbox and spam folder.</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                <strong>Verification Required</strong><br>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main Content -->
        <div class="verification-content">
            <div class="mb-4">
                <p style="font-size: 1rem; line-height: 1.6; color: #555;">
                    <strong>We've sent a verification link to:</strong><br>
                    <code style="background: #f0f0f0; padding: 0.5rem; border-radius: 4px; display: inline-block; margin-top: 0.5rem;">{{ session('email') ?? auth()->user()->email }}</code>
                </p>
            </div>

            <div class="mb-4" style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #2563EB;">
                <h5 style="margin-bottom: 1rem; font-weight: 600;">What to do next?</h5>
                <ol style="margin: 0; padding-left: 1.5rem;">
                    <li style="margin-bottom: 0.8rem;">Check your email inbox (and spam folder) for a message from us</li>
                    <li style="margin-bottom: 0.8rem;">Click the verification link in the email</li>
                    <li style="margin-bottom: 0;">Return here and log in to access your dashboard</li>
                </ol>
            </div>

            <!-- Resend Button -->
            <div class="mb-4">
                <form method="POST" action="{{ route('verification.resend') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-arrow-clockwise me-2"></i>Resend Verification Email
                    </button>
                </form>
            </div>

            <!-- Help Text -->
            <div style="text-align: center; padding: 1.5rem; background: #f0f7ff; border-radius: 8px; margin-bottom: 1.5rem;">
                <p style="margin: 0; font-size: 0.95rem; color: #555;">
                    <strong>Didn't receive the email?</strong><br>
                    <small style="color: #666;">Check your spam folder or click the button above to request a new verification link.</small>
                </p>
            </div>

            <!-- Login Link -->
            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Return to Login
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="auth-footer">
            <p>Already verified?</p>
            <a href="{{ route('login') }}">Log in to your account</a>
        </div>
    </div>
</div>

<style>
    .verification-content ol li {
        line-height: 1.6;
        color: #555;
    }

    .verification-content code {
        color: #2563EB;
        font-family: 'Courier New', monospace;
    }
</style>

@endsection

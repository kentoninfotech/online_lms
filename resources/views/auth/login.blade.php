@extends('layouts.auth')

@section('title', 'Login - COINMAC')

@section('content')

<div class="auth-container">
    <div class="auth-card">
        <!-- Header Section -->
        <div class="auth-header">
            <h2><i class="bi bi-book"></i> COINMAC Inc</h2>
            <p>Login to your account to access all courses</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->has('error') || session()->has('error'))
            <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                <strong>Oops!</strong><br>
                {{ $errors->first('error') ?? session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Field -->
            <div class="form-group">
                <label for="email">Email Address <span style="color: #dc3545;">*</span></label>
                <input 
                    type="email" 
                    name="email" 
                    id="email"
                    class="form-control @error('email') is-invalid @enderror" 
                    placeholder="you@example.com"
                    value="{{ old('email') }}"
                    required
                />
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="form-group">
                <label for="password">Password <span style="color: #dc3545;">*</span></label>
                <div class="password-field">
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        class="form-control @error('password') is-invalid @enderror" 
                        placeholder="Enter your password"
                        required
                    />
                    <button type="button" class="btn-password-toggle" id="togglePassword">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Remember & Forgot Password -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        name="remember"
                        id="remember" 
                        {{ old('remember') ? 'checked' : '' }}
                    />
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>
                @if (Route::has('password.request'))
                    <div class="forgot-password">
                        <a href="{{ route('password.request') }}">Forgot Password?</a>
                    </div>
                @endif
            </div>

            <!-- Login Button -->
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login
            </button>
        </form>

        <!-- Footer -->
        <div class="auth-footer">
            <p>Don't have an account?</p>
            <a href="{{ route('register') }}">Create a new account</a>
        </div>

        
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Toggle the type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle the icon
                if (type === 'text') {
                    eyeIcon.classList.remove('bi-eye');
                    eyeIcon.classList.add('bi-eye-slash');
                } else {
                    eyeIcon.classList.remove('bi-eye-slash');
                    eyeIcon.classList.add('bi-eye');
                }
            });
        }
    });
</script>

@endsection
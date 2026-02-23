@extends('layouts.auth')

@section('title', 'Register - COINMAC')

@section('content')

<div class="auth-container">
    <div class="auth-card">
        <!-- Header Section -->
        <div class="auth-header">
            <h2><i class="bi bi-pencil-square"></i> Create Account</h2>
            <p>Join COINMAC and start your learning journey</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                <strong>Registration Failed!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Register Form -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Full Name -->
            <div class="form-group">
                <label for="name">Full Name <span style="color: #dc3545;">*</span></label>
                <input 
                    type="text" 
                    name="name" 
                    id="name"
                    class="form-control @error('name') is-invalid @enderror" 
                    placeholder="John Doe"
                    value="{{ old('name') }}"
                    required
                    autofocus
                />
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
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

            <!-- User Type Selection -->
            <div class="form-group">
                <label for="user_type">I am a <span style="color: #dc3545;">*</span></label>
                <select 
                    id="user_type" 
                    name="user_type" 
                    class="form-control @error('user_type') is-invalid @enderror"
                    required
                >
                    <option value="">-- Select Role --</option>
                    <option value="student" {{ old('user_type') === 'student' ? 'selected' : '' }}>Student (Learner)</option>
                    <option value="instructor" {{ old('user_type') === 'instructor' ? 'selected' : '' }}>Instructor (Tutor)</option>
                    <option value="parent" {{ old('user_type') === 'parent' ? 'selected' : '' }}>Parent</option>
                </select>
                @error('user_type')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password <span style="color: #dc3545;">*</span></label>
                <div class="password-field">
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        class="form-control @error('password') is-invalid @enderror" 
                        placeholder="Enter a strong password"
                        required
                        autocomplete="new-password"
                    />
                    <button type="button" class="btn-password-toggle" id="togglePassword">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation">Confirm Password <span style="color: #dc3545;">*</span></label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    id="password_confirmation"
                    class="form-control" 
                    placeholder="Re-enter your password"
                    required
                    autocomplete="new-password"
                />
            </div>

            <!-- Terms Agreement -->
            <div class="form-check mb-3">
                <input 
                    type="checkbox" 
                    class="form-check-input @error('terms') is-invalid @enderror"
                    id="terms" 
                    name="terms" 
                    required
                />
                <label class="form-check-label" for="terms">
                    I agree to the Terms & Conditions and Privacy Policy <span style="color: #dc3545;">*</span>
                </label>
                @error('terms')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Register Button -->
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i>Create Account
            </button>
        </form>

        <!-- Footer -->
        <div class="auth-footer">
            <p>Already have an account?</p>
            <a href="{{ route('login') }}">Sign in here</a>
        </div>
    </div>
</div>

<!-- Password Toggle Script -->
<script>
    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const passwordField = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });
</script>

@endsection
@extends('layouts.auth')

@section('title', 'Register - ' . (\App\Models\HomepageSetting::getSetting('branding', 'site_name') ?? 'LMS'))

@section('content')

<div class="auth-container">
    <div class="auth-card">
        <!-- Header Section -->
        <div class="auth-header">
            <h2><i class="bi bi-pencil-square"></i> Create Account</h2>
            <p>Join {{ \App\Models\HomepageSetting::getSetting('branding', 'site_name') ?? 'LMS' }} and start your learning journey</p>
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

                <!-- Password Requirements Checker -->
                <div class="password-requirements mt-2 p-3 bg-light rounded" id="passwordRequirements" style="display: none;">
                    <small class="d-block mb-2 fw-bold text-dark">Password must contain:</small>
                    <ul class="mb-0 ps-3 small list-unstyled">
                        <li id="req-uppercase" class="text-danger mb-1">
                            <i class="bi bi-x-circle me-1"></i>At least one uppercase letter (A-Z)
                        </li>
                        <li id="req-lowercase" class="text-danger mb-1">
                            <i class="bi bi-x-circle me-1"></i>At least one lowercase letter (a-z)
                        </li>
                        <li id="req-number" class="text-danger mb-1">
                            <i class="bi bi-x-circle me-1"></i>At least one number (0-9)
                        </li>
                        <li id="req-length" class="text-danger mb-1">
                            <i class="bi bi-x-circle me-1"></i>At least 8 characters long
                        </li>
                        <li id="req-symbol" class="text-muted mb-1">
                            <i class="bi bi-check-circle me-1"></i>Symbols (@$!%*?&) are optional
                        </li>
                    </ul>
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

<!-- Password Validation & Toggle Script -->
<script>
    const passwordInput = document.getElementById('password');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');
    const requirementsDiv = document.getElementById('passwordRequirements');

    // Show/Hide password
    togglePasswordBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });

    // Real-time password validation
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        // Show requirements box when user starts typing
        if (password.length > 0) {
            requirementsDiv.style.display = 'block';
        } else {
            requirementsDiv.style.display = 'none';
        }

        // Check uppercase
        const hasUppercase = /[A-Z]/.test(password);
        updateValidation('req-uppercase', hasUppercase, 'At least one uppercase letter (A-Z)');

        // Check lowercase
        const hasLowercase = /[a-z]/.test(password);
        updateValidation('req-lowercase', hasLowercase, 'At least one lowercase letter (a-z)');

        // Check number
        const hasNumber = /\d/.test(password);
        updateValidation('req-number', hasNumber, 'At least one number (0-9)');

        // Check length with character count
        const hasLength = password.length >= 8;
        const lengthText = hasLength ? 'At least 8 characters long (currently ' + password.length + ')' : 'At least 8 characters long (currently ' + password.length + ')';
        updateValidation('req-length', hasLength, lengthText);
    });

    function updateValidation(elementId, isValid, text) {
        const element = document.getElementById(elementId);
        if (isValid) {
            element.classList.remove('text-danger');
            element.classList.add('text-success');
            element.innerHTML = '<i class="bi bi-check-circle me-1"></i>' + text;
        } else {
            element.classList.remove('text-success');
            element.classList.add('text-danger');
            element.innerHTML = '<i class="bi bi-x-circle me-1"></i>' + text;
        }
    }

    // Listen for form submission to validate all rules
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const password = passwordInput.value;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /\d/.test(password);
        const hasLength = password.length >= 8;

        if (!hasUppercase || !hasLowercase || !hasNumber || !hasLength) {
            e.preventDefault();
            passwordInput.classList.add('is-invalid');
            requirementsDiv.style.display = 'block';
            
            // Scroll to password field
            passwordInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>

@endsection
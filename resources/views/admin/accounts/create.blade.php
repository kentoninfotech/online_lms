@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold text-dark">Create New Admin Account</h2>
            <p class="text-muted">Add a new administrator to your system</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Admin Account Details</h5>
                </div>
                <div class="card-body p-4">
                    <!-- Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>Validation Errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.accounts.store') }}" method="POST" class="needs-validation">
                        @csrf

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="e.g., John Doe" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   placeholder="e.g., admin@example.com" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter secure password" 
                                   required>
                            
                            <!-- Password Requirements -->
                            <div class="mt-2 p-3 bg-light rounded">
                                <small class="d-block mb-2 fw-bold">Password must contain:</small>
                                <ul class="mb-0 ps-3 small list-unstyled">
                                    <li id="req-uppercase" class="mb-1">
                                        <i class="bi bi-x-circle text-danger me-1"></i>At least one uppercase letter (A-Z)
                                    </li>
                                    <li id="req-lowercase" class="mb-1">
                                        <i class="bi bi-x-circle text-danger me-1"></i>At least one lowercase letter (a-z)
                                    </li>
                                    <li id="req-number" class="mb-1">
                                        <i class="bi bi-x-circle text-danger me-1"></i>At least one number (0-9)
                                    </li>
                                    <li id="req-length" class="mb-1">
                                        <i class="bi bi-x-circle text-danger me-1"></i>At least 8 characters long
                                    </li>
                                    <li id="req-symbol" class="text-muted mb-1">
                                        <i class="bi bi-check-circle text-success me-1"></i>Symbols (@$!%*?&) are optional
                                    </li>
                                </ul>
                            </div>
                            
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   placeholder="Re-enter password" 
                                   required>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info Alert -->
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <small>This admin will be automatically verified and can login immediately after creation.</small>
                        </div>

                        <!-- Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Create Admin Account
                            </button>
                            <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Admin List
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Real-time password validation feedback
const passwordInput = document.getElementById('password');

passwordInput.addEventListener('input', function() {
    const password = this.value;
    
    if (password.length === 0) {
        // Reset if empty
        resetValidation('req-uppercase', 'At least one uppercase letter (A-Z)');
        resetValidation('req-lowercase', 'At least one lowercase letter (a-z)');
        resetValidation('req-number', 'At least one number (0-9)');
        resetValidation('req-length', 'At least 8 characters long');
        return;
    }
    
    // Check and update requirements
    updateValidation('req-uppercase', /[A-Z]/.test(password), 'At least one uppercase letter (A-Z)');
    updateValidation('req-lowercase', /[a-z]/.test(password), 'At least one lowercase letter (a-z)');
    updateValidation('req-number', /\d/.test(password), 'At least one number (0-9)');
    updateValidation('req-length', password.length >= 8, 'At least 8 characters long (currently ' + password.length + ')');
});

function updateValidation(elementId, isValid, text) {
    const element = document.getElementById(elementId);
    const icon = element.querySelector('i');
    
    if (isValid) {
        element.classList.remove('text-danger');
        element.classList.add('text-success');
        icon.classList.remove('bi-x-circle');
        icon.classList.add('bi-check-circle');
        icon.classList.remove('text-danger');
        icon.classList.add('text-success');
    } else {
        element.classList.remove('text-success');
        element.classList.add('text-danger');
        icon.classList.remove('bi-check-circle');
        icon.classList.add('bi-x-circle');
        icon.classList.remove('text-success');
        icon.classList.add('text-danger');
    }
    
    element.innerHTML = icon.outerHTML + text;
}

function resetValidation(elementId, text) {
    const element = document.getElementById(elementId);
    const icon = element.querySelector('i');
    
    element.classList.remove('text-success', 'text-danger');
    icon.classList.remove('bi-check-circle', 'bi-x-circle');
    icon.classList.add('bi-x-circle');
    icon.classList.add('text-danger');
    
    element.innerHTML = icon.outerHTML + text;
}

// Form submission validation
const form = document.querySelector('form');
form.addEventListener('submit', function(e) {
    const password = passwordInput.value;
    
    // Password is required for creation
    if (password.length === 0) {
        e.preventDefault();
        alert('Password is required');
        passwordInput.focus();
        return;
    }
    
    const isValid = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/.test(password);
    
    if (!isValid) {
        e.preventDefault();
        passwordInput.classList.add('is-invalid');
        
        // Scroll to password field
        passwordInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
@endsection

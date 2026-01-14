@extends('layouts.auth')

@section('title', 'Login')

@section('content')

<div class="auth-main v1 bg-grd-primary">
  <div class="auth-wrapper">
    <div class="auth-form">
      <div class="card my-5">
            <div class="card-body">
                <div class="text-center">
                    <img src="../assets/images/logo.png" alt="images" width="90px" class="img-fluid mb-4" />
                    <h4 class="f-w-500 mb-1">Login with your email</h4>
                    <!-- <p class="mb-4">Don't have an Account? <a href="{{ route('register') }}" class="link-primary ms-1">Create Account</a></p> -->
                </div>

                @if ($errors->has('error') || session()->has('error'))
                    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert" style="background-color: #FFFBEB; border: 1px solid #FCD34D; color: #92400E;">
                        <i class="feather icon-alert-circle me-2"></i>
                        <strong>Session Expired!</strong><br>
                        {{ $errors->first('error') ?? session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="floatingInput" placeholder="Email Address" />
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <div class="password-field position-relative">
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="passwordInput" placeholder="Password" />
                                <button type="button" class="btn-password-toggle" id="togglePassword" title="Show/Hide Password">
                                    <i class="feather icon-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="d-flex mt-1 justify-content-between align-items-center">
                            <div class="form-check">
                            <input name="remember" class="form-check-input input-primary" type="checkbox" id="remember" {{ old('remember') ? 'checked' : '' }} />
                            <label class="form-check-label text-muted" for="customCheckc1">Remember me?</label>
                            </div>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}">
                                <h6 class="f-w-400 mb-0">
                                Forgot Your Password?
                                </h6>
                                </a>
                            @endif
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                        <div class="saprator my-3">
                            <!-- <span>Or continue with</span> -->
                        </div>
                        <div class="text-center">
                                <!-- <ul class="list-inline mx-auto mt-3 mb-0">
                                    <li class="list-inline-item">
                                        <a href="https://www.facebook.com/" class="avtar avtar-s rounded-circle bg-facebook" target="_blank">
                                        <i class="fab fa-facebook-f text-white"></i>
                                        </a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a href="https://twitter.com/" class="avtar avtar-s rounded-circle bg-twitter" target="_blank">
                                        <i class="fab fa-twitter text-white"></i>
                                        </a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a href="https://myaccount.google.com/" class="avtar avtar-s rounded-circle bg-googleplus" target="_blank">
                                        <i class="fab fa-google text-white"></i>
                                        </a>
                                    </li>
                                </ul> -->
                        </div>
                    </form>   
            </div>
      </div>
    </div>
  </div>
</div>

<style>
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

    .btn-password-toggle:focus {
        outline: none;
    }

    #passwordInput {
        padding-right: 40px;
    }

    #passwordInput.is-invalid + .btn-password-toggle {
        right: 30px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('passwordInput');
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
                    eyeIcon.classList.remove('icon-eye');
                    eyeIcon.classList.add('icon-eye-off');
                } else {
                    eyeIcon.classList.remove('icon-eye-off');
                    eyeIcon.classList.add('icon-eye');
                }
            });
        }
    });
</script>
  
@endsection
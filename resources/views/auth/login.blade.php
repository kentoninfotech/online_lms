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
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="floatingInput1" placeholder="Password" />
                            @error('password')
                                <span class="invalid-feedback" role="alert">
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
                    </from>   
            </div>
      </div>
    </div>
  </div>
</div>
  
@endsection
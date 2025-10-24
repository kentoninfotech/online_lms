@extends('layouts.auth')

@section('title', 'Register ')

@section('content')

<div class="auth-main v1 bg-grd-primary">
  <div class="auth-wrapper">
    <div class="auth-form">
      <div class="card my-5">
        <div class="card-body">
            <div class="text-center">
              <img src="../assets/images/logo.png" alt="images" width="90px" class="img-fluid mb-4" />
              <h4 class="f-w-500 mb-1">Register with your email</h4>
              <p class="mb-4">Already have an Account? <a href="{{ route('login') }}" class="link-primary">Log in</a></p>
            </div>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group mb-3">
                        <input type="text" class="form-control" placeholder="First Name" />
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group mb-3">
                        <input type="text" name="first_name" class="form-control" placeholder="Last Name" />
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                      </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-3">
                      <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-3">
                  <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>
                <div class="d-flex mt-1 justify-content-between">
                  <div class="form-check">
                    <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" checked="" />
                    <label class="form-check-label text-muted" for="customCheckc1">I agree to all the Terms & Condition</label>
                  </div>
                </div>
                <div class="d-grid mt-4">
                  <button type="button" class="btn btn-primary">Create Account</button>
                </div>
                <div class="saprator my-3">
                  <span>Or continue with</span>
                </div>
                <div class="text-center">
                    <ul class="list-inline mx-auto mt-3 mb-0">
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
                    </ul>
                </div>
              </form>
          </div>
      </div>
    </div>
  </div>
</div>

@endsection
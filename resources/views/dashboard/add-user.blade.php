@extends('layouts.app')

@section('title', 'Add ' . ucfirst($role))

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Add {{ ucfirst($role) }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store', $role) }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <p><strong>Whoops! Something went wrong.</strong></p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Common fields -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password </label>
                        <p class="text-muted">If not set default: <em><b>password123</b></em></p>
                        <input type="password" name="password" class="form-control">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="number" class="form-control" value="{{ old('number') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                    </div>
                </div>

                <!-- Role-specific fields -->
                @if($role === 'student')
                    <div class="mb-3">
                        <!-- <label class="form-label">Enrollment Number</label>
                        <input type="text" name="enrollment_no" class="form-control" value="{{ old('enrollment_no') }}"> -->
                    </div>
                @elseif($role === 'parent')
                    <div class="mb-3">
                        <!-- <label class="form-label">Emergency Contact</label>
                        <input type="text" name="emergency_contact" class="form-control" value="{{ old('emergency_contact') }}"> -->
                    </div>
                @elseif($role === 'instructor')
                    <div class="mb-3">
                        <!-- <label class="form-label">Bio / Qualification</label>
                        <textarea name="bio" class="form-control">{{ old('bio') }}</textarea> -->
                    </div>
                @endif

                <button type="submit" class="btn btn-primary w-100">Create {{ ucfirst($role) }}</button>
            </form>
        </div>
    </div>
</div>
@endsection

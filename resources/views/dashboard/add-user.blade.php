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
                    <div class="form-group row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" 
                                value="{{ old('dob') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Sex</label>
                            <select name="gender" id="gender" class="form-control">
                                <option value="">--Select Gender--</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>
                    <h5>Student Parent</h5>
                    <div class="col-md-6 m-3">
                        <label class="form-label">Student Parent</label>
                        <select name="parent_id" id="parent_id" class="form-control">
                            <option value="">Select Parent</option>
                            @foreach($parent_list as $parent)
                            <option value="{{ $parent->id }}" 
                                {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                @elseif($role === 'parent')
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Occupation</label>
                        <input type="text" name="occupation" class="form-control" value="{{ old('occupation') }}">
                    </div>
                @elseif($role === 'instructor')
                    <div class="form-group row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Specialization</label>
                            <input type="text" name="specialization" class="form-control" value="{{ old('specialization') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" class="form-control">{{ old('bio') }}</textarea>
                        </div>
                    </div>
                @endif

                <button type="submit" class="btn btn-primary w-100">Create {{ ucfirst($role) }}</button>
            </form>
        </div>
    </div>
</div>
@endsection

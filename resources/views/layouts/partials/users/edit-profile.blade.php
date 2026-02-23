<!-- Modern Profile Card -->
<div class="card shadow-sm border-0 rounded-3 overflow-hidden">
    <!-- Banner -->
    <div class="w-100" style="background: linear-gradient(90deg, #f0c221 0%, #f0c221 100%); height: 140px;">
    </div>

    <div class="card-body position-relative">
        <!-- Avatar & Basic Info -->
        <div class="d-flex align-items-center">
            <img src="{{ $user->profile ? asset($user->profile) : asset('images/default-profile.png') ?? 'https://ui-avatars.com/api/?name='.$user->name }}" 
                    alt="{{ $user->name }}"
                    class="rounded-circle border border-3 border-white shadow"
                    style="width: 110px; height: 110px; margin-top:-80px;">

            <div class="ms-3">
                <h4 class="mb-0">{{ $user->name }}</h4>
                <p class="text-muted mb-0">{{ $user->email }}</p>
                <span class="badge bg-primary">{{ ucfirst($role) }}</span>
            </div>
        </div>

        <!-- Edit Form -->
        <form method="POST" action="{{ route('users.update', [$user->id, $role]) }}" class="mt-4">
            @csrf
            @method('PUT')

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
            
            <input type="hidden" name="_tab" value="profile">

            <!-- Common Fields -->
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                </div>
            </div>

            <!-- Role-specific Fields -->
            @if($role === 'student')
                @include('layouts.partials.users.student', ['student' => $user->student])
            @elseif($role === 'parent')
                @include('layouts.partials.users.parent', ['parent' => $user->parent])
            @elseif($role === 'instructor')
                @include('layouts.partials.users.instructor', ['instructor' => $user->instructor])
            @endif

            <!-- Save Button -->
            <div class="mt-3">
                <button class="btn btn-primary">Save Changes</button>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
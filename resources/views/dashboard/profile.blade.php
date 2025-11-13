@extends('layouts.app')

@section('title', 'My Profile')

@section('content')

<!-- Profile Card -->
<div class="card shadow-sm border-0 rounded-3 overflow-hidden">
    <!-- Banner -->
    <div class="w-100" 
            style="background: linear-gradient(90deg, #f0c221 0%, #f0c221 100%); height: 140px;">
    </div>

    <div class="card-body position-relative">
        <!-- Avatar & Basic Info -->
        <div class="d-flex align-items-center">
            <img src="{{ $user->profile ? asset('storage/'. $user->profile) : asset('storage/profiles/profile.png') ?? 'https://ui-avatars.com/api/?name='.$user->name }}" 
                    alt="{{ $user->name }}"
                    class="rounded-circle border border-3 border-white shadow"
                    style="width: 110px; height: 110px; margin-top:-80px;">

            <div class="ms-3">
                <h4 class="mb-0">{{ $user->name }}</h4>
                <p class="text-muted mb-0">{{ $user->email }}</p>
                <span class="badge bg-primary text-capitalize">{{ $roleType }}</span>
            </div>
            <div class="ms-auto">
                <!-- Upload Profile Picture Button -->
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadPictureModal">
                    <i class="bi bi-camera"></i> Change Picture
                </button>

                <!-- Change Password Button -->
                <button class="btn btn-outline-secondary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="bi bi-key"></i> Change Password
                </button>
                <a href="{{ route('users.edit', ['user' => $user, 'role' => $user->user_type]) }}" 
                    class="btn btn-sm btn-primary">Edit</a>
            </div>
        </div>

        <!-- Read-only Details -->
        <div class="row mt-4 g-3">
            <div class="col-md-6">
                <label class="form-label small text-muted">Full Name</label>
                <input type="text" class="form-control bg-light" value="{{ $user->name }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted">Email</label>
                <input type="text" class="form-control bg-light" value="{{ $user->email }}" readonly>
            </div>

            @if($roleType !== 'admin')
                <div class="col-md-6">
                    <label class="form-label small text-muted">Phone</label>
                    <input type="text" class="form-control bg-light" 
                            value="{{ $roleData->number ?? 'N/A' }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">Address</label>
                    <input type="text" class="form-control bg-light" 
                            value="{{ $roleData->address ?? 'N/A' }}" readonly>
                </div>
            @endif
        </div>

        <!-- Quick Stats (for non-admins) -->
        @if($roleType === 'student')
            <div class="mt-4 d-flex gap-4">
                <div class="text-center flex-fill p-3 bg-light rounded">
                    <h5 class="mb-0">{{ $roleData->lessons?->count() ?? 0 }}</h5>
                    <small class="text-muted">Lessons</small>
                </div>
                <div class="text-center flex-fill p-3 bg-light rounded">
                    <h5 class="mb-0">{{ $roleData->attendances?->count() ?? 0 }}</h5>
                    <small class="text-muted">Attendances</small>
                </div>
            </div>
        @elseif($roleType === 'instructor')
            <div class="mt-4 d-flex gap-4">
                <div class="text-center flex-fill p-3 bg-light rounded">
                    <h5 class="mb-0">{{ $roleData->lessons?->count() ?? 0 }}</h5>
                    <small class="text-muted">Lessons</small>
                </div>
                <div class="text-center flex-fill p-3 bg-light rounded">
                    <h5 class="mb-0">{{ $roleData->attendances?->count() ?? 0 }}</h5>
                    <small class="text-muted">Attendances</small>
                </div>
            </div>
        @elseif($roleType === 'parent')
            <div class="mt-4 d-flex gap-4">
                <div class="text-center flex-fill p-3 bg-light rounded">
                    <h5 class="mb-0">{{ $roleData->students?->count() ?? 0 }}</h5>
                    <small class="text-muted">Children</small>
                </div>
                <div class="text-center flex-fill p-3 bg-light rounded">
                    <h5 class="mb-0">{{ $roleData->payments?->count() ?? 0 }}</h5>
                    <small class="text-muted">Payments</small>
                </div>
            </div>
        @endif
    </div>
</div>


<!-- [Change Password Modal] -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('profile.change.password', $user) }}" class="modal-content">
        @csrf
        @method('PUT')
        <div class="modal-header">
            <h5 class="modal-title" id="changePasswordLabel">Change Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Update Password</button>
        </div>
    </form>
  </div>
</div>

<!-- [Upload Picture Modal] -->
<div class="modal fade" id="uploadPictureModal" tabindex="-1" aria-labelledby="uploadPictureLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('profile.upload.picture', $user) }}" enctype="multipart/form-data" class="modal-content">
        @csrf
        @method('PUT')
        <div class="modal-header">
            <h5 class="modal-title" id="uploadPictureLabel">Upload Profile Picture</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body text-center">
            <!-- Preview Avatar -->
            <img id="profilePreview"
                 src="{{ $user->profile ? asset('storage/'.$user->profile) : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}"
                 class="rounded-circle border shadow mb-3"
                 style="width: 120px; height: 120px; object-fit: cover; display:block; margin:0 auto;">
            
            <div class="mt-3">
                <input type="file" name="profile" id="profileInput" class="form-control" accept="image/*">
                <small class="text-muted d-block mt-2">Max size: 2MB, formats: JPG/PNG</small>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Upload</button>
        </div>
    </form>
  </div>
</div>



@endsection


<script>
/**
 * The main initialization function executed when the DOM is ready.
 * It finds the profile input and attaches the change listener.
 */
const initializeProfileUploader = function () {
    const profileInput = document.getElementById('profileInput');
    
    profileInput?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const profilePreview = document.getElementById('profilePreview');

        if (file && profilePreview) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                profilePreview.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
};

// Pass the variable function to the DOMContentLoaded listener
document.addEventListener("DOMContentLoaded", initializeProfileUploader);

// document.addEventListener("DOMContentLoaded", function () {
//     document.getElementById('profileInput')?.addEventListener('change', function(e) {
//         const file = e.target.files[0];
//         if (file) {
//             const reader = new FileReader();
//             reader.onload = function(ev) {
//                 document.getElementById('profilePreview').src = ev.target.result;
//             }
//             reader.readAsDataURL(file);
//         }
//     });
// });


</script>

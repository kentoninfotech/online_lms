@extends('layouts.app')

@section('title', 'Edit ' . ucfirst($role) . ' Profile')

@section('content')

<div class="container bg-white mt-4 pt-4 pb-4">
    <div class="row">
        <div class="col-md-3">
            <!-- Tabs -->
            <ul class="nav flex-column nav-pills me-3" id="profileTabs" role="tablist">
                <li class="nav-item mb-2">
                    <button class="nav-link {{ request()->get('tab') === 'profile' ? 'active' : '' }}"
                            id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile"
                            type="button" role="tab">
                        <i class="ph ph-person me-2"></i> Profile
                    </button>
                </li>
                <li class="nav-item mb-2">
                    <button class="nav-link {{ request()->get('tab') === 'password' ? 'active' : '' }}"
                            id="password-tab" data-bs-toggle="pill" data-bs-target="#password"
                            type="button" role="tab">
                        <i class="ph ph-lock me-2"></i> Password
                    </button>
                </li>
                <li class="nav-item mb-2">
                    <button class="nav-link {{ request()->get('tab') === 'picture' ? 'active' : '' }}"
                            id="picture-tab" data-bs-toggle="pill" data-bs-target="#picture"
                            type="button" role="tab">
                        <i class="ph ph-image me-2"></i> Profile Picture
                    </button>
                </li>
            </ul>
        </div>

        <div class="col-md-9">
            <div class="tab-content mt-3">
                <!-- Profile Info Tab -->
                <div class="tab-pane fade {{ request()->get('tab') === 'profile' || !request()->has('tab') ? 'show active' : '' }}" id="profile" role="tabpanel">
                    @include('layouts.partials.users.edit-profile', ['user' => $user, 'role' => $role])
                </div>

                <!-- Change Password Tab -->
                <div class="tab-pane fade {{ request()->get('tab') === 'password' ? 'show active' : '' }}" id="password" role="tabpanel">
                    @include('layouts.partials.users.change-password', ['user' => $user])
                </div>

                <!-- Profile Picture Tab -->
                <div class="tab-pane fade {{ request()->get('tab') === 'picture' ? 'show active' : '' }}" id="picture" role="tabpanel">
                    @include('layouts.partials.users.change-picture', ['user' => $user])
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Preserve active tab in hash
    const triggerTabList = [].slice.call(document.querySelectorAll('#profileTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        triggerEl.addEventListener('shown.bs.tab', function (event) {
            let tabId = event.target.getAttribute('data-bs-target').substring(1);
            history.replaceState(null, null, '?tab=' + tabId);
        });
    });

    // Live preview for profile picture
    const fileInput = document.querySelector('input[name="profile"]');
    if (fileInput) {
        fileInput.addEventListener("change", function (event) {
            const [file] = event.target.files;
            if (file) {
                const previewImg = document.getElementById("profile-preview");
                if (previewImg) {
                    previewImg.src = URL.createObjectURL(file);
                }
            }
        });
    }
});
</script>
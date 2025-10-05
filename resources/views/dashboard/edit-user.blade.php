@extends('layouts.app')

@section('title', 'Edit ' . ucfirst($role) . ' Profile')

@section('content')

@php
    $activeTab = old('_tab') ?? (request()->get('_tab') ?? 'profile');
@endphp

<div class="container mt-4">
    <div class="row">
        <!-- Sidebar Tabs -->
        <div class="col-md-3">
            <ul class="nav flex-column nav-pills me-3 profile-nav" id="profileTabs" role="tablist">
                <li class="nav-item mb-2">
                    <button class="nav-link @if($activeTab === 'profile') active @endif"
                            id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile"
                            type="button" role="tab">
                        <i class="ph ph-person me-2"></i> Profile
                    </button>
                </li>
                <li class="nav-item mb-2">
                    <button class="nav-link @if($activeTab === 'password') active @endif"
                            id="password-tab" data-bs-toggle="pill" data-bs-target="#password"
                            type="button" role="tab">
                        <i class="ph ph-lock me-2"></i> Password
                    </button>
                </li>
                <li class="nav-item mb-2">
                    <button class="nav-link @if($activeTab === 'picture') active @endif"
                            id="picture-tab" data-bs-toggle="pill" data-bs-target="#picture"
                            type="button" role="tab">
                        <i class="ph ph-image me-2"></i> Picture
                    </button>
                </li>

                @role('parent')
                <li class="nav-item mb-2">
                    <button class="nav-link @if($activeTab === 'link-child') active @endif"
                            id="link-child-tab" data-bs-toggle="pill" data-bs-target="#link-child"
                            type="button" role="tab">
                        <i class="ph ph-link me-2"></i> Link a Child
                    </button>
                </li>
                @endrole

                @role('student')
                <li class="nav-item mb-2">
                    <button class="nav-link @if($activeTab === 'link-code') active @endif"
                            id="link-code-tab" data-bs-toggle="pill" data-bs-target="#link-code"
                            type="button" role="tab">
                        <i class="ph ph-key me-2"></i> Link Code
                    </button>
                </li>
                @endrole
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="col-md-9">
            <div class="tab-content">

                <!-- Profile -->
                <div class="tab-pane fade @if($activeTab === 'profile') show active @endif"
                     id="profile" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold">Edit Profile</div>
                        <div class="card-body">
                            @include('layouts.partials.users.edit-profile', ['user' => $user, 'role' => $role])
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="tab-pane fade @if($activeTab === 'password') show active @endif" 
                     id="password" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold">Change Password</div>
                        <div class="card-body">
                            @include('layouts.partials.users.change-password', ['user' => $user])
                        </div>
                    </div>
                </div>

                <!-- Picture -->
                <div class="tab-pane fade @if($activeTab === 'picture') show active @endif" 
                     id="picture" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light fw-bold">Update Profile Picture</div>
                        <div class="card-body">
                            @include('layouts.partials.users.change-picture', ['user' => $user])
                        </div>
                    </div>
                </div>

                @role('parent')
                <div class="tab-pane fade @if($activeTab === 'link-child') show active @endif" 
                     id="link-child" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold">Link a Child</div>
                        <div class="card-body">
                            @include('layouts.partials.users.link-child')
                        </div>
                    </div>
                </div>
                @endrole

                @role('student')
                <div class="tab-pane fade @if($activeTab === 'link-code') show active @endif" 
                     id="link-code" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light fw-bold">Link Code</div>
                        <div class="card-body text-center">
                            @include('layouts.partials.users.link-code', ['user' => $user])
                        </div>
                    </div>
                </div>
                @endrole

            </div>
        </div>
    </div>
</div>
@endsection


<script>
document.addEventListener("DOMContentLoaded", function () {
    // If there's a hash, show that tab
    const hash = window.location.hash;
    if (hash) {
        const trigger = document.querySelector(`button[data-bs-target="${hash}"]`);
        if (trigger) new bootstrap.Tab(trigger).show();
    }

    // When user clicks tabs, update the URL fragment (so success redirect with fragment is preserved)
    document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function (e) {
            // e.target.dataset.bsTarget is like "#password"
            history.replaceState(null, null, e.target.dataset.bsTarget);
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

<div class="m-header">
        <a href="/" class="b-brand text-primary">
        <!-- ========   Change your logo from here   ============ -->
        <img src="../assets/images/logo.png" width="120" alt="logo image" class="logo-lg" />
        <!-- <img src="../assets/images/logo-white.svg" alt="logo image" class="logo-lg" /> -->
        </a>
    </div>
    <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
    <div class="me-auto pc-mob-drp">
    <ul class="list-unstyled">
        <!-- ======= Menu collapse Icon ===== -->
        <li class="pc-h-item pc-sidebar-collapse">
        <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
            <i class="ph ph-list"></i>
        </a>
        </li>
        <li class="pc-h-item pc-sidebar-popup">
        <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
            <i class="ph ph-list"></i>
        </a>
        </li>
        <li class="dropdown pc-h-item">
        <a
            class="pc-head-link dropdown-toggle arrow-none m-0"
            data-bs-toggle="dropdown"
            href="#"
            role="button"
            aria-haspopup="false"
            aria-expanded="false"
        >
            <i class="ph ph-magnifying-glass"></i>
        </a>
        <!-- <div class="dropdown-menu pc-h-dropdown drp-search">
            <form class="px-3">
            <div class="form-group mb-0 d-flex align-items-center">
                <input type="search" class="form-control border-0 shadow-none" placeholder="Search here. . ." />
                <button class="btn btn-light-secondary btn-search">Search</button>
            </div>
            </form>
        </div> -->
        </li>
    </ul>
    </div>
    <!-- [Mobile Media Block end] -->
    <div class="ms-auto">
    <ul class="list-unstyled">
        <li class="dropdown pc-h-item header-user-profile">
        
        @auth
         <a class="pc-head-link dropdown-toggle arrow-none me-0"
            data-bs-toggle="dropdown"
            href="#"
            role="button"
            aria-haspopup="false"
            data-bs-auto-close="outside"
            aria-expanded="false">
            <img src="{{ Auth::user()->profile ? asset('storage/'. Auth::user()->profile) : asset('storage/profiles/profile.png') ?? 'https://ui-avatars.com/api/?name='. Auth::user()->name }}" 
                alt="{{ Auth::user()->name }}" class="user-avtar" />
         </a>
        @endauth
        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-body">
            <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                <ul class="list-group list-group-flush w-100">
                <li class="list-group-item">
                    <a href="{{ route('my.profile') }}" class="dropdown-item">
                    <span class="d-flex align-items-center">
                        <i class="ph ph-user-circle"></i>
                        <span>My profile</span>
                    </span>
                    </a>
                    <a href="{{ route('notifications') }}" class="dropdown-item">
                        <span class="d-flex align-items-center">
                            <i class="ph ph-bell"></i>
                            <span>Notifications</span>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="badge bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
                            @endif
                        </span>
                    </a>

                    {{-- For Admin --}}
                    @role('admin')
                    <a href="{{ route('settings.index') }}" class="dropdown-item">
                    <span class="d-flex align-items-center">
                        <i class="ph ph-gear-six"></i>
                        <span>System Settings</span>
                    </span>
                    </a>
                    @endrole
                </li>
                <li class="list-group-item">
                    <span class="d-flex align-items-center">
                        <i class="ph ph-power"></i>
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                           <button type="submit" class="btn btn-basic">Logout</button>
                        </form>
                    </span>
                </li>
                </ul>
            </div>
            </div>
        </div>
        </li>
    </ul>
    </div>
</div>
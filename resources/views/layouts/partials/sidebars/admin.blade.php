<ul class="pc-navbar">
    <li class="pc-item pc-caption">
    <label>Navigation</label>
    </li>
    <li class="pc-item">
        <a href="{{ route('admin.dashboard') }}" class="pc-link">
            <span class="pc-micon"> <i class="ph ph-gauge"></i></span><span class="pc-mtext">Dashboard</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.students') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-text-aa"></i></span>
            <span class="pc-mtext">Students</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.instructors') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-text-aa"></i></span>
            <span class="pc-mtext">Instructors</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.parents') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-text-aa"></i></span>
            <span class="pc-mtext">Parents/Guardian</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('admin.lessons') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-text-aa"></i></span>
            <span class="pc-mtext">Lessons</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('admin.reschedules') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-palette"></i></span>
            <span class="pc-mtext">Reschedule Request</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('admin.subscriptions') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-palette"></i></span>
            <span class="pc-mtext">Subscriptions</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.payments') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-palette"></i></span>
            <span class="pc-mtext">Payments</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.plans') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-palette"></i></span>
            <span class="pc-mtext">Plans</span>
        </a>
    </li>

    <li class="pc-item pc-caption">
    <label>Pages</label>
    <i class="ph ph-devices"></i>
    </li>
    <li class="pc-item">
        <a href="{{ route('notifications') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-bell"></i></span>
            <span class="pc-mtext">Notification</span>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="badge bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
            @endif
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('users.edit', ['user' => auth()->user(), 'role' => auth()->user()->user_type]) }}" 
            target="_blank" class="pc-link">
            <span class="pc-micon"><i class="ph ph-user-circle-plus"></i></span>
            <span class="pc-mtext">Settings</span>
        </a>
    </li>
    <li class="pc-item">
    <a href="{{ route('settings.index') }}" target="_blank" class="pc-link">
        <span class="pc-micon"><i class="ph ph-user-circle-plus"></i></span>
        <span class="pc-mtext">System Settings</span>
    </a>
    </li>

</ul>
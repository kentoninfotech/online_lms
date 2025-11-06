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
            <span class="pc-micon"><i class="bi bi-people-fill"></i></span>
            <span class="pc-mtext">Students</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.instructors') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-users"></i></span>
            <span class="pc-mtext">Instructors</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.parents') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-people-fill"></i></span>
            <span class="pc-mtext">Parents/Guardian</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('admin.lessons') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-book"></i></span>
            <span class="pc-mtext">Lessons</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('admin.reschedules') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-calendar-week-fill"></i></span>
            <span class="pc-mtext">Reschedule Request</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('bulk-messages.index') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-megaphone"></i></span>
            <span class="pc-mtext">Broadcast</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('admin.subscriptions') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-briefcase-fill"></i></span>
            <span class="pc-mtext">Subscriptions</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.payments') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-credit-card"></i></span>
            <span class="pc-mtext">Payments</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.plans') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-package"></i></span>
            <span class="pc-mtext">Plans</span>
        </a>
    </li>

    <li class="pc-item pc-caption">
    <label>Pages</label>
    <i class="ph ph-devices"></i>
    </li>
    <li class="pc-item">
        <a href="{{ route('notifications') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-bell-fill"></i></span>
            <span class="pc-mtext">Notification</span>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="badge bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
            @endif
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('users.edit', ['user' => auth()->user(), 'role' => auth()->user()->user_type]) }}" 
            target="_blank" class="pc-link">
            <span class="pc-micon"><i class="bi bi-gear"></i></span>
            <span class="pc-mtext">Settings</span>
        </a>
    </li>
    <li class="pc-item">
    <a href="{{ route('settings.index') }}" target="_blank" class="pc-link">
        <span class="pc-micon"><i class="bi bi-sliders"></i></span>
        <span class="pc-mtext">System Settings</span>
    </a>
    </li>

</ul>
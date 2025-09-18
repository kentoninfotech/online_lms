<ul class="pc-navbar">
    <li class="pc-item pc-caption">
    <label>Navigation</label>
    </li>
    <li class="pc-item">
        <a href="{{ route('student.dashboard') }}" class="pc-link">
            <span class="pc-micon"> <i class="ph ph-gauge"></i></span><span class="pc-mtext">Dashboard</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('student.lessons') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-text-aa"></i></span>
            <span class="pc-mtext">My Lessons</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('student.attendance') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-palette"></i></span>
            <span class="pc-mtext">Attendance</span>
        </a>
    </li>

    <li class="pc-item pc-caption">
    <label>Pages</label>
    <i class="ph ph-devices"></i>
    </li>
    <li class="pc-item">
        <a href="{{ route('student.notifications') }}" target="_blank" class="pc-link">
            <span class="pc-micon"><i class="ph ph-bell"></i></span>
            <span class="pc-mtext">Notification</span>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="badge bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
            @endif
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('student.settings') }}" target="_blank" class="pc-link">
            <span class="pc-micon"><i class="ph ph-user-circle-plus"></i></span>
            <span class="pc-mtext">Settings</span>
        </a>
    </li>

</ul>
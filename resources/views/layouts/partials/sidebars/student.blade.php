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
            <span class="pc-micon"><i class="bi bi-book-half"></i></span>
            <span class="pc-mtext">My Lessons</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('student.attendance') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-calendar2-check"></i></span>
            <span class="pc-mtext">Attendance</span>
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

</ul>
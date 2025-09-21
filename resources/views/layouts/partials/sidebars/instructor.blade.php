<ul class="pc-navbar">
    <li class="pc-item pc-caption">
    <label>Navigation</label>
    </li>
    <li class="pc-item">
        <a href="{{ route('instructor.dashboard') }}" class="pc-link">
            <span class="pc-micon"> <i class="ph ph-gauge"></i></span><span class="pc-mtext">Dashboard</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('instructor.students') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-text-aa"></i></span>
            <span class="pc-mtext">Students</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('instructor.lessons') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-text-aa"></i></span>
            <span class="pc-mtext">Lessons</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('instructor.reschedules') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-palette"></i></span>
            <span class="pc-mtext">Reschedule Request</span>
        </a>
    </li>

    <li class="pc-item pc-caption">
    <label>Pages</label>
    <i class="ph ph-devices"></i>
    </li>
    <li class="pc-item">
        <a href="{{ route('instructor.notifications') }}" target="_blank" class="pc-link">
            <span class="pc-micon"><i class="ph ph-bell"></i></span>
            <span class="pc-mtext">Notification</span>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="badge bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
            @endif
        </a>
    </li>
    <!-- <li class="pc-item">
    <a href="{{ route('instructor.settings') }}" target="_blank" class="pc-link">
        <span class="pc-micon"><i class="ph ph-user-circle-plus"></i></span>
        <span class="pc-mtext">Settings</span>
    </a>
    </li> -->

</ul>
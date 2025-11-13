<ul class="pc-navbar">
    <li class="pc-item pc-caption">
    <label>Navigation</label>
    </li>
    <li class="pc-item">
        <a href="{{ route('instructor.dashboard') }}" class="pc-link">
            <span class="pc-micon"> <i class="ph ph-gauge" style="color: #f0c221;"></i></span><span class="pc-mtext">Dashboard</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('instructor.students') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-people-fill" style="color: #330952;"></i></span>
            <span class="pc-mtext">Students</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('instructor.lessons') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-book" style="color: #f0c221;"></i></span>
            <span class="pc-mtext">Lessons</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('instructor.reschedules') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-calendar-week-fill" style="color: #330952;"></i></span>
            <span class="pc-mtext">Reschedule Request</span>
        </a>
    </li>

    <li class="pc-item pc-caption">
    <label>Pages</label>
    <i class="ph ph-devices" style="color: #f0c221;"></i>
    </li>
    <li class="pc-item">
        <a href="{{ route('notifications') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-bell" style="color: #f0c221;"></i></span>
            <span class="pc-mtext">Notification</span>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="badge bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
            @endif
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('users.edit', ['user' => auth()->user(), 'role' => auth()->user()->user_type]) }}" 
            target="_blank" class="pc-link">
            <span class="pc-micon"><i class="bi bi-gear-fill" style="color: #330952;"></i></span>
            <span class="pc-mtext">Settings</span>
        </a>
    </li>

</ul>
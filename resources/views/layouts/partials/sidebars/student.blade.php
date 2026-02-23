<ul class="pc-navbar">
    <li class="pc-item pc-caption">
    <label>Navigation</label>
    </li>
    <li class="pc-item">
        <a href="{{ route('student.dashboard') }}" class="pc-link">
            <span class="pc-micon"> <i class="ph ph-gauge" style="color: #f0c221;"></i></span><span class="pc-mtext">Dashboard</span>
        </a>
    </li>
    <li class="pc-item pc-hasmenu">
        <a href="#" class="pc-link">
            <span class="pc-micon"><i class="bi bi-book-fill" style="color: #330952;"></i></span>
            <span class="pc-mtext">Courses</span>
            <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
        </a>
        <ul class="pc-submenu">
            <li class="pc-item">
                <a href="{{ route('courses.my-enrollments') }}" class="pc-link">My Courses</a>
            </li>
            <li class="pc-item">
                <a href="{{ route('courses.all') }}" class="pc-link">Browse Courses</a>
            </li>
            <!-- <li class="pc-item">
                <a href="{{ route('courses.index') }}#featured-courses" class="pc-link">Featured Courses</a>
            </li> -->
        </ul>
    </li>
    <li class="pc-item">
        <a href="{{ route('student.lessons') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-book-half" style="color: #330952;"></i></span>
            <span class="pc-mtext">My Lessons</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('student.attendance') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-calendar2-check" style="color: #f0c221;"></i></span>
            <span class="pc-mtext">Attendance</span>
        </a>
    </li>

    <li class="pc-item pc-caption">
    <label>Pages</label>
    <i class="ph ph-devices" style="color: #f0c221;"></i>
    </li>
    <li class="pc-item">
        <a href="{{ route('notifications') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-bell-fill" style="color: #330952;"></i></span>
            <span class="pc-mtext">Notification</span>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="badge bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
            @endif
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('users.edit', ['user' => auth()->user(), 'role' => auth()->user()->user_type]) }}" 
            target="_blank" class="pc-link">
            <span class="pc-micon"><i class="bi bi-gear" style="color: #f0c221;"></i></span>
            <span class="pc-mtext">Settings</span>
        </a>
    </li>

    <!-- User Timezone Display -->
    <li class="pc-item" style="padding: 12px 16px; margin-top: 10px; background-color: #f5f5f5; border-radius: 6px;">
        <div style="font-size: 12px; color: #666; font-weight: 500; margin-bottom: 6px;">
            <i class="ph ph-globe-simple" style="color: #f0c221; margin-right: 6px;"></i>Your Timezone
        </div>
        <div style="font-size: 13px; color: #333; font-weight: 600; margin-bottom: 8px;">
            {{ session('user_timezone', config('app.timezone')) }}
        </div>
        <div style="font-size: 11px; color: #666; font-weight: 500;">
            <i class="ph ph-clock" style="color: #f0c221; margin-right: 4px;"></i>Local Time
        </div>
        <div style="font-size: 14px; color: #333; font-weight: 700;" id="user-local-time"></div>
        <script>
            (function() {
                const timezone = '{{ session("user_timezone", config("app.timezone")) }}';
                
                function updateLocalTime() {
                    const now = new Date();
                    const formatter = new Intl.DateTimeFormat('en-US', {
                        timeZone: timezone,
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: true
                    });
                    document.getElementById('user-local-time').textContent = formatter.format(now);
                }
                
                updateLocalTime();
                setInterval(updateLocalTime, 1000);
            })();
        </script>
    </li>

</ul>
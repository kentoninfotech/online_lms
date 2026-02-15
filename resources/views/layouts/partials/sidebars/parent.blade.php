<ul class="pc-navbar">
    <li class="pc-item pc-caption">
    <label>Navigation</label>
    </li>
    <li class="pc-item">
        <a href="{{ route('parent.dashboard') }}" class="pc-link">
            <span class="pc-micon"> <i class="ph ph-gauge" style="color: #f0c221;"></i></span><span class="pc-mtext">Dashboard</span>
        </a>
    </li>

    <li class="pc-item">
    <a href="{{ route('parent.children') }}" class="pc-link">
        <span class="pc-micon"><i class="bi bi-people-fill" style="color: #330952;"></i></span>
        <span class="pc-mtext">My Children</span>
    </a>
    </li>

    <li class="pc-item">
    <a href="{{ route('parent.lessons') }}" class="pc-link">
        <span class="pc-micon"><i class="ph ph-book" style="color: #f0c221;"></i></span>
        <span class="pc-mtext">Upcoming Lessons</span>
    </a>
    </li>

    <li class="pc-item">
    <a href="{{ route('parent.reschedules') }}" class="pc-link">
        <span class="pc-micon"><i class="bi bi-calendar-week-fill" style="color: #330952;"></i></span>
        <span class="pc-mtext">Reschedule Requests</span>
    </a>
    </li>

    <li class="pc-item">
    <a href="{{ route('parent.payments') }}" class="pc-link">
        <span class="pc-micon"><i class="bi bi-credit-card" style="color: #f0c221;"></i></span>
        <span class="pc-mtext">Payments</span>
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
            <span class="pc-micon"><i class="ph ph-gear" style="color: #f0c221;"></i></span>
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
<ul class="pc-navbar">
    <li class="pc-item pc-caption">
    <label>Navigation</label>
    </li>
    <li class="pc-item">
        <a href="{{ route('admin.dashboard') }}" class="pc-link">
            <span class="pc-micon"> <i class="ph ph-gauge" style="color: #f0c221;"></i></span><span class="pc-mtext">Dashboard</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.students') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-people-fill" style="color: #330952;"></i></span>
            <span class="pc-mtext">Students</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.instructors') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-users" style="color: #f0c221;"></i></span>
            <span class="pc-mtext">Instructors</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.parents') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-people-fill" style="color: #330952;"></i></span>
            <span class="pc-mtext">Parents/Guardian</span>
        </a>
    </li>
    <li class="pc-item pc-hasmenu">
        <a href="#" class="pc-link">
            <span class="pc-micon"><i class="bi bi-book-fill" style="color: #f0c221;"></i></span>
            <span class="pc-mtext">Courses Management</span>
            <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
        </a>
        <ul class="pc-submenu">
            <li class="pc-item">
                <a href="{{ route('admin.courses.index') }}" class="pc-link">All Courses</a>
            </li>
            <li class="pc-item">
                <a href="{{ route('admin.courses.create') }}" class="pc-link">Create Course</a>
            </li>
            <li class="pc-item">
                <a href="{{ route('admin.course-categories.index') }}" class="pc-link">Categories</a>
            </li>
            <li class="pc-item">
                <a href="{{ route('admin.courses.import') }}" class="pc-link">Import Courses</a>
            </li>
        </ul>
    </li>

    <li class="pc-item pc-hasmenu">
        <a href="#" class="pc-link">
            <span class="pc-micon"><i class="bi bi-file-text" style="color: #330952;"></i></span>
            <span class="pc-mtext">Learning Content</span>
            <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
        </a>
        <ul class="pc-submenu">
            <li class="pc-item">
                <a href="{{ route('admin.learning-content.index') }}" class="pc-link">All Content</a>
            </li>
        </ul>
    </li>

    <li class="pc-item pc-hasmenu">
        <a href="#" class="pc-link">
            <span class="pc-micon"><i class="bi bi-question-circle" style="color: #f0c221;"></i></span>
            <span class="pc-mtext">Quizzes</span>
            <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
        </a>
        <ul class="pc-submenu">
            <li class="pc-item">
                <a href="{{ route('admin.quizzes.index') }}" class="pc-link">All Quizzes</a>
            </li>
            <li class="pc-item">
                <a href="{{ route('admin.quiz-submissions.index') }}" class="pc-link">Submissions</a>
            </li>
        </ul>
    </li>

    <li class="pc-item pc-hasmenu">
        <a href="#" class="pc-link">
            <span class="pc-micon"><i class="bi bi-chat-dots" style="color: #330952;"></i></span>
            <span class="pc-mtext">Discussions</span>
            <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
        </a>
        <ul class="pc-submenu">
            <li class="pc-item">
                <a href="{{ route('admin.discussions.index') }}" class="pc-link">All Discussions</a>
            </li>
        </ul>
    </li>

    <li class="pc-item pc-hasmenu">
        <a href="#" class="pc-link">
            <span class="pc-micon"><i class="bi bi-broadcast" style="color: #f0c221;"></i></span>
            <span class="pc-mtext">Live Sessions</span>
            <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
        </a>
        <ul class="pc-submenu">
            <li class="pc-item">
                <a href="{{ route('admin.live-sessions-all.index') }}" class="pc-link">All Sessions</a>
            </li>
        </ul>
    </li>

    <li class="pc-item pc-hasmenu">
        <a href="#" class="pc-link">
            <span class="pc-micon"><i class="bi bi-person-check" style="color: #330952;"></i></span>
            <span class="pc-mtext">Enrollments</span>
            <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
        </a>
        <ul class="pc-submenu">
            <li class="pc-item">
                <a href="{{ route('admin.course-enrollments.index') }}" class="pc-link">All Enrollments</a>
            </li>
        </ul>
    </li>

    <li class="pc-item pc-hasmenu">
        <a href="#" class="pc-link">
            <span class="pc-micon"><i class="bi bi-laptop" style="color: #f0c221;"></i></span>
            <span class="pc-mtext">Online Tutors</span>
            <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
        </a>
        <ul class="pc-submenu">
            <li class="pc-item">
                <a href="{{ route('admin.facilitators.index') }}" class="pc-link">All Tutors</a>
            </li>
            <li class="pc-item">
                <a href="{{ route('admin.facilitators.create') }}" class="pc-link">Add Tutor</a>
            </li>
            <li class="pc-item">
                <a href="{{ route('admin.facilitators.index') }}" class="pc-link">Tutor Ratings</a>
            </li>
        </ul>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.lessons') }}" class="pc-link">
            <span class="pc-micon"><i class="ph ph-book" style="color: #f0c221;"></i></span>
            <span class="pc-mtext">Lessons</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('admin.reschedules') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-calendar-week-fill" style="color: #330952;"></i></span>
            <span class="pc-mtext">Reschedule Request</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('bulk-messages.index') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-megaphone" style="color: #f0c221;"></i></span>
            <span class="pc-mtext">Broadcast</span>
        </a>
    </li>
    <li class="pc-item">
        <a href="{{ route('admin.subscriptions') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-briefcase-fill" style="color: #330952;"></i></span>
            <span class="pc-mtext">Subscriptions</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.payments') }}" class="pc-link"> <!-- Subscription Payments -->
            <span class="pc-micon"><i class="bi bi-credit-card" style="color: #f0c221;"></i></span>
            <span class="pc-mtext">Subscription Payments</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.course-payments.index') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-credit-card-fill" style="color: #5c61f2;"></i></span>
            <span class="pc-mtext">Course Payments</span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.feedback.index') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-chat-dots-fill" style="color: #ff6c6c;"></i></span>
            <span class="pc-mtext">Feedback & Messages
                @php
                    $unreadCount = \App\Helpers\FeedbackHelper::getUnreadCount();
                @endphp
                @if($unreadCount > 0)
                    <span class="badge bg-danger ms-2" style="font-size: 0.7rem;">{{ $unreadCount }}</span>
                @endif
            </span>
        </a>
    </li>

    <li class="pc-item">
        <a href="{{ route('admin.plans') }}" class="pc-link">
            <span class="pc-micon"><i class="fa fa-clipboard-list" style="color: #330952;"></i></span>
            <span class="pc-mtext">Plans</span>
        </a>
    </li>

    <li class="pc-item pc-hasmenu">
        <a href="#" class="pc-link">
            <span class="pc-micon"><i class="bi bi-palette-fill" style="color: #f0c221;"></i></span>
            <span class="pc-mtext">Site Builder</span>
            <span class="pc-arrow"><i class="ph ph-caret-down"></i></span>
        </a>
        <ul class="pc-submenu">
            <li class="pc-item">
                <a href="{{ route('admin.site-builder.index') }}" class="pc-link">Dashboard</a>
            </li>
            <li class="pc-item">
                <a href="{{ route('admin.site-builder.logos') }}" class="pc-link">Logos & Branding</a>
            </li>
            <li class="pc-item">
                <a href="{{ route('admin.site-builder.colors') }}" class="pc-link">Colors</a>
            </li>
            <li class="pc-item">
                <a href="{{ route('admin.site-builder.typography') }}" class="pc-link">Typography</a>
            </li>
            <li class="pc-item">
                <a href="{{ route('admin.homepage-settings.index') }}" class="pc-link">Homepage Sections</a>
            </li>
        </ul>
    </li>

    <li class="pc-item pc-caption">
    <label>Pages</label>
    <i class="ph ph-devices" style="color: #f0c221;"></i>
    </li>
    <li class="pc-item">
        <a href="{{ route('notifications') }}" class="pc-link">
            <span class="pc-micon"><i class="bi bi-bell-fill" style="color: #f0c221;"></i></span>
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
    <li class="pc-item">
    <a href="{{ route('settings.index') }}" target="_blank" class="pc-link">
        <span class="pc-micon"><i class="bi bi-sliders" style="color: #f0c221;"></i></span>
        <span class="pc-mtext">System Settings</span>
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
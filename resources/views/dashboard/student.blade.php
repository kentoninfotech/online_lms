@extends('layouts.app')

@section('title', $student->name ."'s". ' Profile')

@section('content')

<!-- [Student Details] start -->
<div class="card shadow-sm border-0 rounded-3 overflow-hidden">
    <!-- Banner -->
    <div class="w-100" 
         style="background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%); height: 140px;">
    </div>

    <div class="card-body position-relative">
        <!-- Avatar & Basic Info -->
        <div class="d-flex align-items-center">
            <img src="{{ $student->user->profile ? asset('storage/'. $student->user->profile) : asset('storage/profiles/profile.png') ?? 'https://ui-avatars.com/api/?name='.$student->name }}" 
                 alt="{{ $student->name }}"
                 class="rounded-circle border border-3 border-white shadow"
                 style="width: 110px; height: 110px; margin-top:-80px;">

            <div class="ms-3">
                <h4 class="mb-0">{{ $student->name }}</h4>
                <p class="text-muted mb-0">{{ $student->email }}</p>
                <span class="badge bg-success">Student</span>
            </div>

            @role('admin')
               <div class="ms-auto">
                   <a href="{{ route('users.edit', ['user' => $student->user, 'role' => 'student']) }}" 
                       class="btn btn-sm btn-primary">Edit</a>
               </div>
            @endrole
        </div>

        <!-- Read-only Details -->
        <div class="row mt-4 g-3">
            <div class="col-md-6">
                <label class="form-label small text-muted">Full Name</label>
                <input type="text" class="form-control bg-light" value="{{ $student->name }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted">Email</label>
                <input type="text" class="form-control bg-light" 
                       value="{{ $student->email }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted">Phone</label>
                <input type="text" class="form-control bg-light" 
                       value="{{ $student->number ?? 'N/A' }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted">Address</label>
                <input type="text" class="form-control bg-light" 
                       value="{{ $student->address ?? 'N/A' }}" readonly>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="mt-4 d-flex gap-4">
            <div class="text-center flex-fill p-3 bg-light rounded">
                <h5 class="mb-0">{{ $student->lessons?->count() ?? 0 }}</h5>
                <small class="text-muted">Lessons</small>
            </div>
            <div class="text-center flex-fill p-3 bg-light rounded">
                <h5 class="mb-0">{{ $attendances->count() ?? 0 }}</h5>
                <small class="text-muted">Attendance</small>
            </div>
            <div class="text-center flex-fill p-3 bg-light rounded">
                <h5 class="mb-0">
                    <span class="badge 
                        @if($student->subscription->status === 'active') bg-success 
                        @elseif($student->subscription->status === 'expired') bg-danger 
                        @elseif($student->subscription->status === 'pending') bg-warning 
                        @else bg-info @endif">
                        {{ Str::headline($student->subscription->status) ?? 'N/A' }} 
                    </span>
                </h5>
                <small class="text-muted">Subscription</small>
            </div>
        </div>
    </div>
</div>
<!-- [Student Details] end -->


<!-- Tabs for Lessons and Attendance -->
<div class="container bg-white mt-4 p-3">
    <!-- Tabs -->
    <ul class="nav nav-tabs" id="studentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="lessons-tab" data-bs-toggle="tab" href="#lessons" data-bs-target="#lessons"
                type="button" role="tab">Lessons</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" href="#attendance" data-bs-target="#attendance"
                type="button" role="tab">Attendance</button>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <!-- Lessons Tab -->
        <div class="tab-pane fade show active" id="lessons" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Lessons
                    <!-- Filter -->
                    <form method="GET" class="row g-2 mt-2">
                        <div class="col-md-4">
                            <input type="text" name="subject" class="form-control"
                                value="{{ request('subject') }}" placeholder="Filter by Subject">
                        </div>
                        <div class="col-md-4">
                            <select name="instructor" class="form-select">
                                <option value="">All Instructors</option>
                                @foreach($instructors as $id => $name)
                                    <option value="{{ $id }}" @selected(request('instructor') == $id)>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('show.student', $student->id) }}" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </form>
                    <!-- Export Buttons -->
                    <div>
                        <a href="{{ route('students.lessons.export', [$student, 'format' => 'csv']) }}" class="btn btn-sm btn-outline-secondary">Export CSV</a>
                        <a href="{{ route('students.lessons.export', [$student, 'format' => 'pdf']) }}" class="btn btn-sm btn-outline-primary">Export PDF</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Instructor</th>
                                <th>Start Time</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lessons as $lesson)
                                @foreach($lesson->occurrences as $occurrence)
                                <tr>
                                    <td>{{ $lesson->subject }}</td>
                                    <td>{{ $lesson->instructor->name ?? '-' }}</td>
                                    <td>{{ $occurrence->scheduled_start->format('d M Y h:i A') }}</td>
                                    <td>{{ $occurrence->duration_minutes }} mins</td>
                                </tr>
                                @endforeach
                            @empty
                                <tr><td colspan="4" class="text-center">No lessons found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $lessons->withQueryString()->links() }}
                </div>
            </div>
        </div>

        <!-- Attendance Tab -->
        <div class="tab-pane fade" id="attendance" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Attendance
                    <!-- Filter -->
                    <form method="GET" class="row g-2 mt-2">
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="present" @selected(request('status') == 'present')>Present</option>
                                <option value="late" @selected(request('status') == 'late')>Late</option>
                                <option value="absent" @selected(request('status') == 'absent')>Absent</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex">
                            <button class="btn btn-primary me-2">Filter</button>
                            <a href="{{ route('show.student', $student->id) }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                    <!-- Export Buttons -->
                    <!-- <div>
                        <a href="{{ route('students.attendance.export', [$student->id, 'format' => 'csv']) }}" class="btn btn-sm btn-outline-secondary">Export CSV</a>
                        <a href="{{ route('students.attendance.export', [$student->id, 'format' => 'pdf']) }}" class="btn btn-sm btn-outline-primary">Export PDF</a>
                    </div> -->
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Lesson</th>
                                <th>Status</th>
                                <th>Joined</th>
                                @role('admin|instructor')
                                    <th>Leave</th>
                                    <th>Duration</th>
                                @endrole
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->occurrence->lesson->subject ?? '-' }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($attendance->status === 'present') bg-success 
                                            @elseif($attendance->status === 'late') bg-warning
                                            @else bg-danger @endif">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $attendance->join_time?->format('h:i A') ?? '-' }}</td>
                                    @role('admin|instructor')
                                        <td>{{ $attendance->leave_time?->format('h:i A') ?? '-' }}</td>
                                        <td>{{ $attendance->duration_minutes ?? '-' }} mins</td>
                                    @endrole
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">No attendance records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $attendances->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


<script>
document.addEventListener("DOMContentLoaded", function () {
    const defaultTab = "#lessons";
    const hash = window.location.hash || defaultTab;

    // Show correct tab on load
    const activeTab = document.querySelector(`#studentTabs button[data-bs-target="${hash}"]`);
    if (activeTab) new bootstrap.Tab(activeTab).show();

    // Update hash when switching tabs
    document.querySelectorAll('#studentTabs button[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener("shown.bs.tab", function (e) {
            const target = e.target.getAttribute("data-bs-target");
            history.replaceState(null, null, target);
        });
    });

    // Keep hash in pagination links
    document.querySelectorAll(".pagination a").forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            let url = this.getAttribute("href");
            window.location.href = url + window.location.hash;
        });
    });

    // Ensure filter forms append current hash, based on their tab
    document.querySelectorAll(".tab-pane form").forEach(form => {
        form.addEventListener("submit", function () {
            const tabId = this.closest(".tab-pane").id; // lessons or attendance
            this.action = this.action.split('#')[0] + "#" + tabId;
        });
    });
});
</script>



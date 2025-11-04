@extends('layouts.app')

@section('title', $instructor->name ."'s". ' Profile')

@section('content')

<!-- [Instructor Details] start -->
<div class="card shadow-sm border-0 rounded-3 overflow-hidden">
    <!-- Banner -->
    <div class="w-100" 
         style="background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%); height: 140px;">
    </div>

    <div class="card-body position-relative">
        <!-- Avatar & Basic Info -->
        <div class="d-flex align-items-center">
            <img src="{{ $instructor->user->profile ? asset('storage/'. $instructor->user->profile) : asset('storage/profiles/profile.png') ?? 'https://ui-avatars.com/api/?name='.$instructor->name }}" 
                 alt="{{ $instructor->name }}"
                 class="rounded-circle border border-3 border-white shadow"
                 style="width: 110px; height: 110px; margin-top:-80px;">

            <div class="ms-3">
                <h4 class="mb-0">{{ $instructor->name }}</h4>
                <p class="text-muted mb-0">{{ $instructor->email }}</p>
                <span class="badge bg-primary">Instructor</span>
            </div>

            @role('admin')
               <div class="ms-auto">
                   <a href="{{ route('users.edit', ['user' => $instructor->user, 'role' => 'instructor']) }}" 
                       class="btn btn-sm btn-primary">Edit</a>
               </div>
            @endrole
        </div>

        <!-- Read-only Details -->
        <div class="row mt-4 g-3">
            <div class="col-md-6">
                <label class="form-label small text-muted">Full Name</label>
                <input type="text" class="form-control bg-light" value="{{ $instructor->name }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted">Email</label>
                <input type="text" class="form-control bg-light" 
                       value="{{ $instructor->email }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted">Phone</label>
                <input type="text" class="form-control bg-light" 
                       value="{{ $instructor->number ?? 'N/A' }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted">Address</label>
                <input type="text" class="form-control bg-light" 
                       value="{{ $instructor->address ?? 'N/A' }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted">Specialization</label>
                <input type="text" class="form-control bg-light" 
                       value="{{ $instructor->specialization ?? 'N/A' }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted">Bio</label>
                <textarea class="form-control bg-light" 
                     readonly>{{ $instructor->bio ?? 'N/A' }}
                </textarea>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="mt-4 d-flex gap-4">
            <div class="text-center flex-fill p-3 bg-light rounded">
                <h5 class="mb-0">{{ $instructor->lessons?->count() ?? 0 }}</h5>
                <small class="text-muted">Lessons</small>
            </div>
            <div class="text-center flex-fill p-3 bg-light rounded">
                <h5 class="mb-0">{{ $attendances->count() ?? 0 }}</h5>
                <small class="text-muted">Attendances</small>
            </div>
        </div>
    </div>
</div>
<!-- [Instructor Details] end -->


<!-- Tabs for Lessons and Attendance -->
<div class="container bg-white mt-4 p-3">
    <!-- Tabs -->
    <ul class="nav nav-tabs" id="instructorTabs" role="tablist">
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
                        <div class="col-md-6">
                            <input type="text" name="subject" class="form-control"
                                value="{{ request('subject') }}" placeholder="Filter by Subject">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary">Filter</button>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('show.instructor', $instructor->id) }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Student</th>
                                <th>Start Time</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lessons as $lesson)
                                @foreach($lesson->occurrences as $occurrence)
                                <tr>
                                    <td>{{ $lesson->subject }}</td>
                                    <td>
                                        <a href="{{ route('show.student', $lesson->student->id) }}">
                                            {{ $lesson->student->name }}
                                        </a>
                                    </td>
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
                            <a href="{{ route('show.instructor', $instructor->id) }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Lesson</th>
                                <th>Student</th>
                                <th>Status</th>
                                <th>Join</th>
                                <th>Leave</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->occurrence->lesson->subject ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('show.student', $attendance->occurrence->lesson->student->id) }}">
                                            {{ $attendance->occurrence->lesson->student->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($attendance->status === 'present') bg-success 
                                            @elseif($attendance->status === 'late') bg-warning
                                            @else bg-danger @endif">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $attendance->join_time?->format('h:i A') ?? '-' }}</td>
                                    <td>{{ $attendance->leave_time?->format('h:i A') ?? '-' }}</td>
                                    <td>{{ $attendance->duration_minutes ?? '-' }} mins</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No attendance records found.</td></tr>
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
    const tabLinks = document.querySelectorAll('#instructorTabs button[data-bs-toggle="tab"]');

    // Restore active tab from URL hash
    if (window.location.hash) {
        const tabTrigger = document.querySelector(`#instructorTabs button[data-bs-target="${window.location.hash}"]`);
        if (tabTrigger) {
            new bootstrap.Tab(tabTrigger).show();
        }
    }

    // Update hash when switching tabs
    tabLinks.forEach(link => {
        link.addEventListener('shown.bs.tab', function (event) {
            const target = event.target.getAttribute("data-bs-target");
            history.replaceState(null, null, target);
        });
    });

    // Make pagination links preserve hash
    const paginationLinks = document.querySelectorAll(".pagination a");
    paginationLinks.forEach(link => {
        link.addEventListener("click", function (e) {
            const hash = window.location.hash;
            if (hash) {
                e.preventDefault();
                window.location = this.href + hash;
            }
        });
    });
});
</script>




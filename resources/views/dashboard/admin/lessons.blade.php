@extends('layouts.app')

@section('title', 'Lessons')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
                <a href="{{ route('lesson.create') }}" class="btn btn-sm btn-primary float-end">
                     <i class="ph ph-plus"></i> New Lesson
                </a>
            <h4 class="mb-0">Lessons</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('admin.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Lessons</li>
            </ul>
        </div>
        </div>
    </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->

<!-- Today’s Schedule -->
<div class="card mb-3">
    <div class="card-header">Today’s Classes</div>
    <div class="card-body">
        @forelse($todayLessons as $class)
            <p>
                {{ $class->scheduled_start->format('h:i A') }} — {{ $class->lesson->student->name }}
            </p>
        @empty
            <p>No classes scheduled today.</p>
        @endforelse
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Lesson</th>
                    <th>Student</th>
                    <th>Instructor</th>
                    <th>Next Class</th>
                    <th>Status</th>
                    <!-- <th>Action</th> -->
                </tr>
            </thead>
            <tbody>
                @forelse($lessons as $lesson)
                    <tr>
                        <td>{{ $lesson->subject }}</td>
                        <td>{{ $lesson->student->name ?? 'N/A' }}</td>
                        <td>{{ $lesson->instructor->name ?? 'N/A' }}</td>
                        <td>
                            @php
                                $nextOccurrence = $lesson->occurrences->first();
                            @endphp
                            @if($nextOccurrence)
                                {{ $nextOccurrence->scheduled_start->format('d M Y h:i A') }}
                            @else
                                <span class="text-muted">No upcoming class</span>
                            @endif
                        </td>
                        <td>
                            @if($nextOccurrence?->status === 'scheduled')
                                <span class="badge bg-virtual">{{ Str::headline($nextOccurrence?->status) ?? 'N/A' }}</span>
                            @elseif($nextOccurrence?->status === 'completed')
                                <span class="badge virtual-secondary">{{ Str::headline($nextOccurrence?->status) ?? 'N/A' }}</span>
                            @else
                                <span class="badge bg-success">{{ Str::headline($nextOccurrence?->status) ?? 'N/A' }}</span>
                            @endif
                        </td>
                        <td>

                           <div class="dropdown">
                                <button class="btn btn-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                                        <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                                    </svg>
                                </button>
                                <ul class="dropdown-menu text-center">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('lesson.edit', $lesson) }}"><i class="ph ph-pen"></i> Edit Lesson</a>
                                    </li>
                                    <li>
                                        <form class="d-inline" action="{{ route('lesson.delete', $lesson) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this lesson, deleting this lesson will also delete all it\'s passed and future occurrences?');">
                                              <i class="ph ph-trash"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>



                           {{-- @if(! isset($nextOccurrence->zoomSession))
                              <!-- Add zoom button opens modal -->
                              <!-- <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#addZoomModal{{ $nextOccurrence?->id }}">
                                    Add Zoom Meeting 
                              </button> -->
                            @endif --}}

                            <!-- Add Lesson zoomlink Modal -->
                            <div class="modal fade" id="addZoomModal{{ $nextOccurrence?->id }}" tabindex="-1" aria-labelledby="createMeetingLabel" aria-hidden="true">
                              <div class="modal-dialog modal-lg">
                                <form action="{{ route('add.zoom', ['occurrence' => $nextOccurrence?->id]) }}" method="POST">
                                  @csrf
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title" id="createMeetingLabel">Add Zoom Meeting</h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <!-- lesson_occurrence_id -->
                                    <input type="hidden" name="lesson_occurrence_id" value="{{ $nextOccurrence?->id }}" >

                                    <div class="modal-body row g-3">
                                      <!-- Subject -->
                                      <div class="col-md-6">
                                        <label class="form-label">Topic</label>
                                        <input type="text" name="topic" class="form-control @error('topic') is-invalid @enderror" required>
                                        @error('topic')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                      </div>

                                      <div class="col-md-6">
                                        <label class="form-label">Zoom Meeting ID</label>
                                        <input type="text" name="zoom_meeting_id" class="form-control @error('zoom_meeting_id') is-invalid @enderror" required>
                                        @error('zoom_meeting_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                      </div>

                                      <div class="col-md-6">
                                        <label class="form-label">Start Url</label>
                                        <input type="text" name="start_url" class="form-control @error('start_url') is-invalid @enderror" required>
                                        <p class="muted-text">Start Meeting URL for Instructor</p>
                                        @error('start_url')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                      </div>

                                      <div class="col-md-6">
                                        <label class="form-label">Join Url</label>
                                        <input type="text" name="join_url" class="form-control @error('join_url') is-invalid @enderror" required>
                                        <p class="muted-text">Join Meeting URL for Student</p>
                                        @error('join_url')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                      </div>

                                    </div> <!-- modal-body -->

                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                      <button type="submit" class="btn btn-primary">Add Meeting</button>
                                    </div>
                                  </div>
                                </form>
                              </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No lessons found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $lessons->links() }}
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<!-- Recent Attendances Section -->
<div class="card mb-3">
    <div class="card-header">
        <h5>Recent Attendances</h5>
        <span class="text-muted">All recent attendance records from all instructors</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Instructor</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Report</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttendances ?? collect() as $attendance)
                        <tr>
                            <td>
                                <strong>{{ $attendance->occurrence->lesson->student->name }}</strong>
                            </td>
                            <td>{{ $attendance->occurrence->lesson->instructor->name }}</td>
                            <td>{{ $attendance->occurrence->lesson->subject }}</td>
                            <td>
                                <span class="badge 
                                    @if($attendance->status === 'present') bg-success 
                                    @elseif($attendance->status === 'absent') bg-danger
                                    @elseif($attendance->status === 'late') bg-warning
                                    @elseif($attendance->status === 'rescheduled') bg-info
                                    @else bg-secondary @endif">
                                    {{ Str::headline($attendance->status) }}
                                </span>
                            </td>
                            <td>{{ $attendance->created_at->format('d M Y h:i A') }}</td>
                            <td>
                                @if($attendance->raw)
                                    <button class="btn btn-sm btn-info view-admin-report-btn" data-report="{{ $attendance->raw }}" data-bs-toggle="modal" data-bs-target="#adminViewReportModal" title="View Report">
                                        <i class="feather icon-eye"></i> View
                                    </button>
                                @else
                                    <span class="text-muted text-sm">No report</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No attendance records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Report Modal for Admin -->
<div class="modal fade" id="adminViewReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attendance Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="adminReportContent" style="white-space: pre-wrap; word-wrap: break-word; line-height: 1.6;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle view report button click in admin section
    document.querySelectorAll('.view-admin-report-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const report = this.getAttribute('data-report');
            document.getElementById('adminReportContent').textContent = report;
        });
    });
});
</script>



@extends('layouts.app')

@section('title', 'Attendances')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title border-bottom pb-2 mb-2">
                        <h4 class="mb-0">Attendances <small class="text-muted">(500 per page)</small></h4>
                    </div>
                </div>
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"><i class="ph ph-house"></i></a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Attendances</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="card">
    <div class="card-body">
        <!-- Filter Section -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-4">
                <label class="form-label">Filter by Status</label>
                <select id="statusFilter" class="form-select">
                    <option value="">All Status</option>
                    <option value="present" @selected($status === 'present')>Present</option>
                    <option value="absent" @selected($status === 'absent')>Absent</option>
                    <option value="late" @selected($status === 'late')>Late</option>
                </select>
            </div>
            <div class="col-md-8 text-end">
                <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#bulkActionsPanel">
                    <i class="ph ph-gear"></i> Bulk Actions
                </button>
            </div>
        </div>

        <!-- Bulk Actions Panel -->
        <div class="collapse mb-4" id="bulkActionsPanel">
            <div class="card card-body bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted"><span id="selectedCount">0</span> attendance record(s) selected</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-sm btn-success" onclick="bulkUpdateStatus('present')">
                            <i class="ph ph-check"></i> Mark Present
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="bulkUpdateStatus('absent')">
                            <i class="ph ph-x"></i> Mark Absent
                        </button>
                        <button class="btn btn-sm btn-info" onclick="bulkUpdateStatus('late')">
                            <i class="ph ph-clock"></i> Mark Late
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendances Table -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="attendancesTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>Attendable</th>
                        <th>Type</th>
                        <th>Lesson</th>
                        <th>Subject</th>
                        <th>Instructor</th>
                        <th>Join Time</th>
                        <th>Leave Time</th>
                        <th>Duration (min)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input attendance-checkbox" value="{{ $attendance->id }}">
                            </td>
                            <td>
                                <span>{{ $attendance->attendable?->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ class_basename($attendance->attendable_type) }}
                                </span>
                            </td>
                            <td>
                                @if($attendance->occurrence)
                                    <a href="#" class="text-decoration-none">
                                        {{ $attendance->occurrence->id }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                {{ $attendance->occurrence?->lesson?->subject ?? '-' }}
                            </td>
                            <td>
                                {{ $attendance->occurrence?->lesson?->instructor?->name ?? '-' }}
                            </td>
                            <td>
                                @if($attendance->join_time)
                                    <small>{{ $attendance->join_time->format('d M Y h:i A') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->leave_time)
                                    <small>{{ $attendance->leave_time->format('d M Y h:i A') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->duration_minutes)
                                    <span class="badge bg-info">{{ $attendance->duration_minutes }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'absent' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                No attendance records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $attendances->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<script>
    // Filter status handler
    document.getElementById('statusFilter').addEventListener('change', function() {
        const status = this.value;
        const url = new URL(window.location);
        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        window.location = url.toString();
    });

    // Select all checkboxes
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.attendance-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });

    // Update selected count on checkbox change
    document.querySelectorAll('.attendance-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.attendance-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = checkedCount;
    }

    // Bulk update status
    function bulkUpdateStatus(status) {
        const selectedIds = Array.from(document.querySelectorAll('.attendance-checkbox:checked'))
            .map(checkbox => checkbox.value);

        if (selectedIds.length === 0) {
            alert('Please select at least one attendance record.');
            return;
        }

        if (!confirm(`Are you sure you want to mark ${selectedIds.length} record(s) as ${status}?`)) {
            return;
        }

        fetch('{{ route("admin.attendances.bulk-update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                attendance_ids: selectedIds,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Attendance records updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Failed to update records'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
</script>

@endsection

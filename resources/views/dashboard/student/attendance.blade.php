@extends('layouts.app')

@section('title', 'My Attendances')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
            <h4 class="mb-0">My Attendance</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('student.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">My Attendance</li>
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
        <table class="table">
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Duration</th>
                    <th>Join</th>
                    <th>Leave</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendance as $a)
                    <tr>
                        <td>{{ $a->occurrence->lesson->subject }}</td>
                        <td>
                            @if($a->status == 'present')
                            <span class="badge bg-success">{{ Str::headline($a->status) }}</span>
                            @elseif($a->status == 'absent')
                            <span class="badge bg-danger">{{ Str::headline($a->status) }}</span>
                            @elseif($a->status == 'late')
                            <span class="badge bg-warning">{{ Str::headline($a->status) }}</span>
                            @else
                            <span class="badge bg-info">{{ Str::headline($a->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $a->occurrence->scheduled_start->format('d M Y h:i A') ?? '-' }}</td>
                        <td>{{ $a->duration_minutes ?? '-' }}</td>
                        <td>{{ $a->join_time?->format('h:i A') ?? '-' }}</td>
                        <td>{{ $a->leave_time?->format('h:i A') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No attendance records yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $attendance->links() }}
        </div>
    </div>
</div>


@endsection
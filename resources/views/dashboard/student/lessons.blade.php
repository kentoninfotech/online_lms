@extends('layouts.app')

@section('title', 'My Lessons')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
            <h4 class="mb-0">My Lessons</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('student.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">My Lessons</li>
            </ul>
        </div>
        </div>
    </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->

<div class="card shadow-sm p-3">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Lesson</th>
                    <th>Instructor</th>
                    <th>Next Class</th>
                    <th>Status</th>
                    <th>Join</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lessons as $lesson)
                    <tr>
                        <td>{{ $lesson->subject }}</td>
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
                            @if($nextOccurrence->status === 'scheduled')
                                <span class="badge bg-success">{{ Str::headline($nextOccurrence->status) }}</span>
                            @else
                                <span class="badge bg-warning">{{ Str::headline($nextOccurrence->status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($nextOccurrence->zoomSession))
                                <a href="{{ $nextOccurrence->zoomSession->join_url }}" target="_blank" class="btn btn-sm btn-primary">
                                    Join
                                </a>
                            @else
                                <span class="text-muted">Zoom link not ready</span>
                            @endif
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


@endsection
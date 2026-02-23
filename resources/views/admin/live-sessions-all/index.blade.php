@extends('layouts.app')

@section('title', 'Live Sessions - Admin')

@section('content')

<div class="pc-container" style="margin-top: -2rem;">
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Live Sessions</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h4 class="m-0">Live Sessions Management</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="pc-container" style="margin-top: 0.5rem;">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>All Live Sessions</h5>
                    <span class="d-block text-muted" style="font-size: 0.875rem;"><i class="bi bi-info-circle"></i> Total Sessions: {{ $sessions->total() }}</span>
                </div>
                <div class="card-body">
                    @if($sessions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Course</th>
                                        <th>Facilitator</th>
                                        <th>Scheduled</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sessions as $session)
                                        <tr>
                                            <td>
                                                <strong>{{ Str::limit($session->title, 40) }}</strong>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.courses.show', $session->course) }}" class="badge badge-light-primary">
                                                    {{ $session->course->title }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($session->facilitator)
                                                    {{ $session->facilitator->name }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $session->scheduled_start->format('M d, Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-secondary">{{ ucfirst($session->session_type) }}</span>
                                            </td>
                                            <td>
                                                @if($session->status === 'scheduled')
                                                    <span class="badge badge-light-info">Scheduled</span>
                                                @elseif($session->status === 'live')
                                                    <span class="badge badge-light-warning">Live</span>
                                                @elseif($session->status === 'completed')
                                                    <span class="badge badge-light-success">Completed</span>
                                                @else
                                                    <span class="badge badge-light-danger">{{ ucfirst($session->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.live-sessions.show', [$session->course, $session]) }}" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                                <a href="{{ route('admin.live-sessions.edit', [$session->course, $session]) }}" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $sessions->links('pagination::bootstrap-5') }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No live sessions found. <a href="{{ route('admin.courses.index') }}">Create live sessions for your courses</a>.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

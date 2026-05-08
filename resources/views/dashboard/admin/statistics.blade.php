@extends('layouts.app')

@section('title', 'Admin Statistics')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Platform Statistics</h4>
        <a href="{{ route('admin.statistics.pdf') }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf"></i> Download PDF
        </a>
    </div>

    <!-- Key Metrics Cards Row 1 -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card virtual-secondary order-card">
                <div class="card-body">
                    <h6 class="text-white">Total Students</h6>
                    <h2 class="text-end text-white">
                        <i class="feather bi bi-people float-start"></i>
                        <span>{{ $totalStudents }}</span>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-grd-primary order-card">
                <div class="card-body">
                    <h6 class="text-white">Total Instructors</h6>
                    <h2 class="text-end text-white">
                        <i class="feather bi bi-person-check float-start"></i>
                        <span>{{ $totalInstructors }}</span>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-grd-warning order-card">
                <div class="card-body">
                    <h6 class="text-white">Total Parents</h6>
                    <h2 class="text-end text-white">
                        <i class="feather bi bi-people-fill float-start"></i>
                        <span>{{ $totalParents }}</span>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-grd-success order-card">
                <div class="card-body">
                    <h6 class="text-white">Total Lessons</h6>
                    <h2 class="text-end text-white">
                        <i class="feather bi bi-book float-start"></i>
                        <span>{{ $totalLessons }}</span>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards Row 2 -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Lesson Occurrences</h6>
                    <h3 class="mb-0">{{ $totalLessonOccurrences }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Attendance Rate</h6>
                    <h3 class="mb-0 text-success">{{ $attendanceRate }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Impact Percentage</h6>
                    <h3 class="mb-0 text-info">{{ $impactPercentage }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Avg Duration (min)</h6>
                    <h3 class="mb-0 text-warning">{{ $avgDuration }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Breakdown -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Attendance Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Attendances</span>
                            <strong>{{ $totalAttendances }}</strong>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-secondary" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-success">Present</span>
                            <strong class="text-success">{{ $presentAttendances }}</strong>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalAttendances > 0 ? ($presentAttendances / $totalAttendances) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-warning">Late</span>
                            <strong class="text-warning">{{ $lateAttendances }}</strong>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-warning" style="width: {{ $totalAttendances > 0 ? ($lateAttendances / $totalAttendances) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-danger">Absent</span>
                            <strong class="text-danger">{{ $absentAttendances }}</strong>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-danger" style="width: {{ $totalAttendances > 0 ? ($absentAttendances / $totalAttendances) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription and Payment Stats -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Subscription & Payment Stats</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <td>Active Subscriptions</td>
                                <td class="text-end"><strong class="text-success">{{ $activeSubscriptions }}</strong></td>
                            </tr>
                            <tr>
                                <td>Expired Subscriptions</td>
                                <td class="text-end"><strong class="text-danger">{{ $expiredSubscriptions }}</strong></td>
                            </tr>
                            <tr>
                                <td>Pending Payments</td>
                                <td class="text-end"><strong class="text-warning">{{ $pendingPayments }}</strong></td>
                            </tr>
                            <tr class="table-active">
                                <td><strong>Total Revenue</strong></td>
                                <td class="text-end"><strong class="text-info">₦{{ number_format($totalRevenue, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Lesson Performance -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Lesson Performance</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Lesson Completion Rate</span>
                                    <strong class="text-success">{{ $lessonCompletionRate }}%</strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $lessonCompletionRate }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Trend Chart Data -->
    @if($attendanceTrend->count() > 0)
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Attendance Trend (Last 6 Months)</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Total Attendances</th>
                                <th>Present</th>
                                <th>Attendance %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceTrend as $trend)
                            <tr>
                                <td>{{ \Carbon\Carbon::createFromDate($trend->year, $trend->month, 1)->format('M Y') }}</td>
                                <td>{{ $trend->count }}</td>
                                <td>{{ $trend->present_count }}</td>
                                <td>
                                    @php
                                        $monthRate = $trend->count > 0 ? round(($trend->present_count / $trend->count) * 100, 2) : 0;
                                    @endphp
                                    <span class="badge bg-{{ $monthRate >= 80 ? 'success' : ($monthRate >= 60 ? 'warning' : 'danger') }}">
                                        {{ $monthRate }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection

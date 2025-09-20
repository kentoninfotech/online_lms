@extends('layouts.app')

@section('title', 'Instructor Dashboard')

@section('content')
<!-- <div class="row"> -->
<div class="container">
    <h5>Welcome back {{ $instructor->name }}, ready for your next lesson?</h5>

    <div class="row">
        <!-- [col-8] start -->
        <div class="col-lg-8">

           <div class="row">
               <div class="col-md-6 col-xl-6">
                    <div class="card bg-grd-success order-card">
                    <div class="card-body">
                        <h6 class="text-white">Total Courses</h6>
                        <h2 class="text-end text-white"><i class="feather icon-tag float-start"></i>
                             <span>{{ $instructor->lessons->count() }}</span> 
                        </h2>
                        <p class="m-b-0">Lessons this month<span class="float-end">{{ $lessonsThisMonth }}</span></p>
                    </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-6">
                    <div class="card bg-grd-primary order-card">
                    <div class="card-body">
                        <h6 class="text-white">Students</h6>
                        <h2 class="text-end text-white"><i class="feather icon-shopping-cart float-start"></i><span>{{ $students->count() }}</span> </h2>
                        <p class="m-b-0">Active<span class="float-end"> {{ $students->count() }} </span></p>
                    </div>
                    </div>
                </div>
            </div> <!-- row end -->

           <!-- Today’s Schedule -->
            <div class="card mb-3">
                <div class="card-header">Today’s Classes</div>
                <div class="card-body">
                    @forelse($todayLessons as $class)
                        <p>
                            {{ $class->scheduled_start->format('h:i A') }} — {{ $class->lesson->student->name }}
                            @if($class->zoomSession)
                                <a href="{{ $class->zoomSession->start_url }}" target="_blank" class="btn btn-sm btn-primary">Start class</a>
                            @endif
                        </p>
                    @empty
                        <p>No classes scheduled today.</p>
                    @endforelse
                </div>
            </div>

            <!-- Reschedule Requests -->
            <div class="card mb-3">
                <div class="card-header">Pending Reschedules</div>
                <div class="card-body">
                    @forelse($reschedules as $req)
                        <p>
                            {{ $req->occurrence->lesson->student->name }} requested 
                            {{ $req->proposed_start->format('d M Y h:i A') }} <br>
                            <small>{{ $req->reason }}</small>
                            <form action="{{ route('reschedule.approve', $req) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                            </form>
                            <!-- Reject button opens modal -->
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">
                                Reject
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('reschedule.reject', $req) }}">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Reject Reschedule Request</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Please provide a reason:</p>
                                                <textarea name="decision_reason" class="form-control" rows="3" required></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Reject</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </p>
                    @empty
                        <p>No pending reschedule requests.</p>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Classes start -->
            <div class="col-sm-12">
                <div class="card table-card">
                <div class="card-header">
                    <h5>Upcoming Classes</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Student</th>
                                <th>Time</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Zoom</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcoming as $class)
                                <tr>
                                    <td> {{ $class->lesson->subject }}</td>
                                    <td> {{ $class->lesson->student->name }} </td>
                                    <td> {{ $class->scheduled_start?->format('d M Y h:i A') }} </td>
                                    <td>
                                        <span class="badge bg-success">{{ $class->duration_minutes }}</span>
                                    </td>
                                    <td>
                                        @if($class->status === 'scheduled')
                                            <span class="badge bg-primary">{{ Str::headline($class->status) }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ Str::headline($class->status) }}</span>
                                        @endif
                                    </td>
                                    <td> 
                                        @if($class->zoomSession)
                                            <a href="{{ $class->zoomSession->start_url }}" target="_blank" class="btn btn-sm btn-primary">Start Class</a>
                                        @else
                                            <span class="text-muted">Zoom link not ready</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No upcoming classes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
                <!-- </div> -->
            </div>
            <!-- Upcoming Classes end -->

            <!-- Attendance History -->
            <div class="col-sm-12">
                <!-- Attendance -->
                <div class="card mb-3">
                    <div class="card-header">Recent Attendance</div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr><th>Student</th><th>Status</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttendance as $a)
                                    <tr>
                                        <td>{{ $a->occurrence->lesson->student->name }}</td>
                                        <td>{{ ucfirst($a->status) }}</td>
                                        <td>{{ $a->created_at->format('d M Y h:i A') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- col-sm-12 end -->
        
        </div><!-- [col-8] end -->

        <!-- [col-4] start -->
        <div class="col-lg-4">
            <iframe src="https://calendar.google.com/calendar/embed?height=300&wkst=1&ctz=UTC&showPrint=0&showTabs=0&showCalendars=0&showTz=0" style="border-width:0" width="300" height="300" frameborder="0" scrolling="no"></iframe>
            
            <h3 class="mt-3">Next Event</h3>

            <!-- Next Class -->
            @if($nextClass)
               <div class="card nav-action-card bg-brand-color-1">
                    <div class="card-body" style="background-image: url('../assets/images/layout/nav-card-bg.svg')">
                        <h5 class="text-white">Next Class</h5>
                        <p class="text-white text-opacity-75">
                            <strong>Student:</strong> {{ $nextClass->lesson->student->name }}
                        </p>
                        <p class="text-white text-opacity-75">
                            <strong>Scheduled:</strong> {{ $nextClass->scheduled_start->format('d M Y h:i A') }}
                        </p>

                        <p id="countdown" class="lead text-white text-opacity-75"></p>

                        @if($nextClass->zoomSession)
                            <a href="{{ $nextClass->zoomSession->start_url }}" class="btn btn-light" target="_blank">Start Class</a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Notifications -->
            <div class="card shadow mb-4">
                <div class="card-header lead">Recent Notifications</div>
                <div class="card-body">
                    @forelse($notifications as $note)
                        <div class="alert alert-info mb-2">
                            {{ $note->data['message'] ?? $note->type }}
                            <small class="d-block text-muted">{{ $note->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <p>No recent notifications.</p>
                    @endforelse
                </div>
            </div>

        </div>
        <!-- [col-4] end -->
    </div> <!-- row end -->
   
</div>
@endsection



<script>
document.addEventListener("DOMContentLoaded", function () {
    // ==============================
    // Next Class count down
    // ==============================
    const targetDate = new Date("{{ $nextClass?->scheduled_start->toIso8601String() }}").getTime();
    const countdownEl = document.getElementById("countdown");

    function updateCountdown() {
        const now = new Date().getTime();
        const diff = targetDate - now;

        if (diff <= 0) {
            countdownEl.innerHTML = "Class is starting!";
            return;
        }

        const days = Math.floor(diff / (1000*60*60*24));
        const hours = Math.floor((diff % (1000*60*60*24)) / (1000*60*60));
        const mins = Math.floor((diff % (1000*60*60)) / (1000*60));
        const secs = Math.floor((diff % (1000 * 60)) / 1000);
        countdownEl.innerHTML = `Starts in: ${days}d ${hours}h ${mins}m ${secs}s`;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);


    // ==============================
    // Attendance Line Chart
    // ==============================
    // {{-- const labels = @json($attendanceStats->pluck('month')); --}}
    // {{-- const data   = @json($attendanceStats->pluck('percent'));  --}}

    // const ctxLine = document.getElementById('attendanceChart');
    // if (ctxLine) {
    //     new Chart(ctxLine, {
    //         type: 'line',
    //         data: {
    //             labels: labels,
    //             datasets: [{
    //                 label: 'Attendance %',
    //                 data: data,
    //                 borderColor: 'rgba(54, 162, 235, 1)',
    //                 backgroundColor: 'rgba(54, 162, 235, 0.2)',
    //                 fill: true,
    //                 tension: 0.3,
    //                 pointRadius: 5,
    //             }]
    //         },
    //         options: {
    //             responsive: true,
    //             scales: {
    //                 y: {
    //                     beginAtZero: true,
    //                     max: 100,
    //                     ticks: {
    //                         callback: value => value + "%"
    //                     }
    //                 }
    //             }
    //         }
    //     });
    // }

    // ==============================
    // Attendance Doughnut Chart
    // ==============================
    // {{-- const present = @json($presentCount); --}}
    // {{-- const late    = @json($lateCount);  --}}
    // {{-- const absent  = @json($absentCount); --}}

    // const totalAttendance = present + late + absent;

    // const ctxDoughnut = document.getElementById('attendanceDoughnut');
    // const noDataMsg   = document.getElementById('noAttendanceMessage');

    // if (totalAttendance === 0) {
    //     if (ctxDoughnut) ctxDoughnut.style.display = 'none';
    //     if (noDataMsg) noDataMsg.style.display = 'block';
    // } else if (ctxDoughnut) {
    //     new Chart(ctxDoughnut, {
    //         type: 'doughnut',
    //         data: {
    //             labels: ['Present', 'Late', 'Absent'],
    //             datasets: [{
    //                 data: [present, late, absent],
    //                 backgroundColor: [
    //                     '#1cc88a',   // Present
    //                     '#d6b708ff', // Late
    //                     '#e74a3b'    // Absent
    //                 ],
    //                 hoverBackgroundColor: [
    //                     '#17a673',
    //                     '#8f7b09ff',
    //                     '#be2617'
    //                 ],
    //                 borderWidth: 1
    //             }]
    //         },
    //         options: {
    //             responsive: true,
    //             plugins: {
    //                 legend: { position: 'bottom' },
    //                 tooltip: {
    //                     callbacks: {
    //                         label: (context) => {
    //                             let dataset = context.chart.data.datasets[0].data;
    //                             let total = dataset.reduce((a, b) => a + b, 0);
    //                             let value = context.raw;
    //                             let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
    //                             return `${context.label}: ${value} (${percentage}%)`;
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     });
    // }

});

</script>


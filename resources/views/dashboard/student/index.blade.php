@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<!-- <div class="row"> -->
<div class="container">
    <h5>Welcome back {{ $student->name }}, ready for your next lesson?</h5>

    <div class="row">
        <!-- [col-8] start -->
        <div class="col-lg-8">

           <div class="row">
               <div class="col-md-6 col-xl-6">
                    <div class="card bg-virtual order-card">
                    <div class="card-body">
                        <h6 class="text-white">Total Courses</h6>
                        <h2 class="text-end text-white"><i class="feather icon-book float-start"></i>
                             <span>{{ $student->lessons->count() }}</span> 
                        </h2>
                        <p class="m-b-0">Lessons this month<span class="float-end">{{ $lessonsThisMonth }}</span></p>
                    </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-6">
                <div class="card statistics-card-1">
                    <div class="card-body">
                        <img src="../assets/images/widget/img-status-5.svg" alt="img" class="img-fluid img-bg" />
                        <div class="d-flex align-items-center justify-content-between mb-3 drp-div">
                        <h6 class="mb-0">Monthly Attendance</h6>
                        </div>
                        <div class="d-flex align-items-center mt-2">
                        <h3 class="f-w-300 d-flex align-items-center m-b-0">{{ $monthPresentCount }}/{{ $monthTotalClasses }}</h3>
                        <span class="badge bg-light-primary ms-2">{{ $monthAttendancePercent }}%</span>
                        </div>
                        <p class="text-muted mb-2 text-sm mt-2">
                            @if($monthAttendancePercent >= 80)
                                 Great job! 🎉 Keep it up this month. 
                            @elseif($monthAttendancePercent >= 50)
                                 You made an improvement this Month 👍 
                            @else
                                 Let’s work on improving attendance 📌 
                            @endif
                        </p>
                        <div class="progress" style="height: 5px">
                        <div
                            class="progress-bar bg-brand-color-3"
                            role="progressbar"
                            style="width: {{ $monthAttendancePercent }}%"
                            aria-valuenow="{{ $monthAttendancePercent }}"
                            aria-valuemin="0"
                            aria-valuemax="100"
                        ></div>
                        </div>
                    </div>
                </div>
            </div>
           </div> <!-- row end -->
           
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Attendance %</div>
                        <div class="card-body">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Overall Attendance</div>
                        <div class="card-body">
                            <canvas id="attendanceDoughnut"></canvas>
                            <div id="noAttendanceMessage" class="text-center text-muted mt-3" style="display: none;">
                                No attendance records yet.
                            </div>
                        </div>
                    </div>   
                </div>
            </div>

            @if($subscription)
            <div class="card mt-3">
                <div class="card-body">
                    <div class="row">
                           <div class="col-6">
                                <div class="media align-items-center">
                                    <div class="avtar avtar-s bg-grd-primary flex-shrink-0">
                                        <i class="ph ph-money f-20 text-white"></i>
                                    </div>
                                    <div class="media-body ms-2">
                                        <p class="mb-0 text-muted">Subscription: {{ $subscription->plan->name }}</p>
                                        <h6 class="mb-0">
                                            <span class="badge 
                                                (@if($subscription->status === 'active') bg-success 
                                                @elseif($subscription->status === 'pending') bg-warning
                                                @else bg-danger @endif)">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="media align-items-center">
                                    <div class="avtar avtar-s bg-grd-success flex-shrink-0">
                                        <i class="ph ph-shopping-cart text-white f-20"></i>
                                    </div>
                                    <div class="media-body ms-2">
                                        <p class="mb-0 text-muted">Expires</p>
                                        <h6 class="mb-0">
                                            {{ $subscription->end_date?->format('d M Y') ?? 'N/A' }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            @endif

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
                                <th>Instructor</th>
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
                                    <td> {{ $class->lesson->instructor->name }} </td>
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
                                        <a href="{{ route('lesson.join', $class) }}" target="_blank" class="btn btn-sm btn-primary">Join</a>
                                        {{-- <!-- @if($class->zoomSession)
                                            <a href="{{ $class->zoomSession->join_url }}" target="_blank" class="btn btn-sm btn-primary">Join</a>
                                        @else
                                            <span class="text-muted">Zoom link not ready</span>
                                        @endif --> --}}
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
                <div class="card">
                    <div class="card-header">Recent Attendance</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Status</th>
                                    <!-- <th>Duration</th> -->
                                    <th>Joined</th>
                                    <!-- <th>Leave</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendance as $a)
                                    <tr>
                                        <td>{{ $a->occurrence->lesson->subject }}</td>
                                        <td>
                                            @if($a->status == 'present')
                                            <span class="badge bg-success">{{ Str::headline($a->status) }}</span> was early
                                            @elseif($a->status == 'absent')
                                            <span class="badge bg-danger">{{ Str::headline($a->status) }}</span>
                                            @elseif($a->status == 'late')
                                            <span class="badge bg-warning">{{ Str::headline($a->status) }}</span> was present
                                            @else
                                            <span class="badge bg-info">{{ Str::headline($a->status) }}</span>
                                            @endif
                                        </td>
                                        <!-- <td>{{ $a->duration_minutes ?? '-' }}</td> -->
                                        <td>{{ $a->join_time?->format('h:i A') ?? '-' }}</td>
                                        <!-- <td>{{ $a->leave_time?->format('h:i A') ?? '-' }}</td> -->
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">No attendance records yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- col-sm-12 end -->
        
        </div>
        <!-- [col-8] end -->
        <!-- [col-4] start -->
        <div class="col-lg-4">
            <iframe src="https://calendar.google.com/calendar/embed?height=300&wkst=1&ctz=UTC&showPrint=0&showTabs=0&showCalendars=0&showTz=0" style="border-width:0" width="300" height="300" frameborder="0" scrolling="no"></iframe>
            
            <h3 class="mt-3">Next Event</h3>

            <!-- Next Class -->
            @if($nextClass)
               <div class="card nav-action-card bg-brand-color-1">
                    <div class="card-body" style="background-image: url('../assets/images/layout/nav-card-bg.svg')">
                        <h5 class="text-white">Next Class</h5>
                        <h6 class="text-white">Lesson: {{ $nextClass->lesson->subject }}</h6>
                        <p class="text-white text-opacity-75">
                            <strong>Instructor:</strong> {{ $nextClass->lesson->instructor->name }}
                        </p>
                        <p class="text-white text-opacity-75">
                            <strong>Scheduled:</strong> {{ $nextClass->scheduled_start->format('d M Y h:i A') }}
                        </p>

                        <p id="countdown" class="lead text-white text-opacity-75"></p>

                        <a href="{{ route('lesson.join', $nextClass) }}" class="btn btn-light" target="_blank">Join Class</a>
                        {{-- <!-- @if($nextClass->zoomSession)
                            <a href="{{ $nextClass->zoomSession->join_url }}" class="btn btn-light" target="_blank">Join Class</a>
                        @endif --> --}}

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
    const labels = @json($attendanceStats->pluck('month'));
    const data   = @json($attendanceStats->pluck('percent'));

    const ctxLine = document.getElementById('attendanceChart');
    if (ctxLine) {
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Attendance %',
                    data: data,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: value => value + "%"
                        }
                    }
                }
            }
        });
    }

    // ==============================
    // Attendance Doughnut Chart
    // ==============================
    const present = @json($presentCount);
    const late    = @json($lateCount);
    const absent  = @json($absentCount);

    const totalAttendance = present + late + absent;

    const ctxDoughnut = document.getElementById('attendanceDoughnut');
    const noDataMsg   = document.getElementById('noAttendanceMessage');

    if (totalAttendance === 0) {
        if (ctxDoughnut) ctxDoughnut.style.display = 'none';
        if (noDataMsg) noDataMsg.style.display = 'block';
    } else if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Late', 'Absent'],
                datasets: [{
                    data: [present, late, absent],
                    backgroundColor: [
                        '#1cc88a',   // Present
                        '#d6b708ff', // Late
                        '#e74a3b'    // Absent
                    ],
                    hoverBackgroundColor: [
                        '#17a673',
                        '#8f7b09ff',
                        '#be2617'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                let dataset = context.chart.data.datasets[0].data;
                                let total = dataset.reduce((a, b) => a + b, 0);
                                let value = context.raw;
                                let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

});

</script>


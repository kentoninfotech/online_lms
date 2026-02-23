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
                        <p class="m-b-0">Lessons this month<span class="float-end">{{ $lessonsThisMonth ?? 'N/A'}}</span></p>
                    </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-6">
                <div class="card statistics-card-1">
                    <div class="card-body">
                        <img src="{{ asset('assets/images/widget/img-status-5.svg') }}" alt="img" class="img-fluid img-bg" />
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
                                                @elseif($subscription->status === 'grace') bg-info
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
                            <div class="col-12">
                                @if($subscription->remainingDays <= 10 && $subscription->status === 'active')
                                    <div class="alert alert-warning mt-3 mb-0">
                                        {{ $subscription->remainingDays }} days remaining until your subscription expires.
                                    </div>
                                @endif
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
                                    <td> <x-format-time :date="$class->scheduled_start" /> </td>
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
            
            <!-- Custom Calendar -->
            <x-full-calendar />
            <!-- End Custom Calendar -->

            <!-- Ongoing Class -->
            @if($ongoingClass)
            <div class="card mt-3 border-0 shadow-sm">
                <div class="card-header bg-sky-blue text-white">
                    <h5 class="mb-0">Current Ongoing Class</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">{{ $ongoingClass->lesson->subject }}</h6>
                    <p>
                        <strong>Instructor:</strong> {{ $ongoingClass->lesson->instructor->name }}<br>
                        <strong>Start:</strong> <x-format-time :date="$ongoingClass->scheduled_start" /><br>
                        <strong>End:</strong> <x-format-time :date="$ongoingClass->scheduled_start->copy()->addMinutes((int)$ongoingClass->duration_minutes)" format="d M Y h:i A" />
                    </p>

                    <p id="class-countdown" class="fw-bold text-danger"></p>

                    <a href="{{ route('lesson.join', $ongoingClass) }}" class="btn btn-primary" target="_blank">Join Now</a>
                    <!-- {{-- @if($ongoingClass->zoomSession)
                        <a href="{{ $ongoingClass->zoomSession->join_url }}" 
                        target="_blank" 
                        class="btn btn-primary">Join Now</a>
                    @else
                        <span class="text-muted">Zoom link not yet available</span>
                    @endif --}} -->
                </div>
            </div>
            @endif

            <h3 class="mt-3">Next Event</h3>

            <!-- Next Class -->
            @if($nextClass)
               <div class="card nav-action-card bg-virtual">
                    <div class="card-body" style="background-image: url('../assets/images/layout/nav-card-bg.svg')">
                        <h5 class="text-white">Next Class</h5>
                        <h6 class="text-white">Lesson: {{ $nextClass->lesson->subject }}</h6>
                        <p class="text-white text-opacity-75">
                            <strong>Instructor:</strong> {{ $nextClass->lesson->instructor->name }}
                        </p>
                        <p class="text-white text-opacity-75">
                            <strong>Scheduled:</strong> <x-format-time :date="$nextClass->scheduled_start" />
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
                        @php
                            // Safely extract data from the notification
                            $data = $note->data;
                            $title = $data['title'] ?? 'New Notification';
                            $isRead = $note->read_at;
                            
                            // Get the first message line for a snippet
                            $snippet = $data['message_lines'][0] ?? $data['message'] ?? 'Click for details...';
                            
                            // Determine alert style (using primary for unread, secondary/light for read)
                            $alertClass = $isRead ? 'alert-light text-muted' : 'alert-secondary';
                            
                            // Resolve URL if an action route is present
                            $actionUrl = null;
                            if (isset($data['action']['route']['name'])) {
                                $routeName = $data['action']['route']['name'];
                                $routeParams = $data['action']['route']['params'] ?? [];
                                // Safely try to resolve the route
                                try {
                                    $actionUrl = route($routeName, $routeParams);
                                } catch (\Exception $e) {
                                    $actionUrl = null; 
                                }
                            }
                        @endphp

                        <div class="alert {{ $alertClass }} mb-2 p-2" role="alert">
                            
                            {{-- 1. Title --}}
                            <strong class="{{ $isRead ? 'text-secondary' : 'text-dark' }}">
                                {{ $title }}
                            </strong>

                            {{-- 2. Message Snippet --}}
                            <p class="mb-0 small {{ $isRead ? 'text-secondary' : 'text-body' }}">
                                {{ Str::limit(strip_tags($snippet), 60, '...') }}
                            </p>
                            
                            {{-- 3. Action Link and Timestamp --}}
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                @if ($actionUrl)
                                    <a href="{{ $actionUrl }}" class="alert-link small text-decoration-underline">
                                        {{ $data['action']['text'] ?? 'View Details' }}
                                    </a>
                                @else
                                    {{-- Placeholder or empty link to align time --}}
                                    <span></span>
                                @endif
                                
                                <small class="text-muted text-end">{{ $note->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No recent notifications.</p>
                    @endforelse

                    {{-- Optional: Link to full notifications page --}}
                    @if(count($notifications) > 0)
                        <div class="text-center mt-3">
                            <a href="{{ route('notifications') }}" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                    @endif
                </div>
            </div> <!-- Notifications End -->

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

    @if($ongoingClass)
        // Ongoing Class countdown
        const endTime = new Date("{{ $ongoingClass?->scheduled_start->copy()->addMinutes((int)$ongoingClass->duration_minutes)->toIso8601String() }}").getTime();
        const timer = document.getElementById("class-countdown");

        const interval = setInterval(() => {
            const ongoingNow = new Date().getTime();
            const ongoingDiff = endTime - ongoingNow;

            if (ongoingDiff <= 0) {
                clearInterval(interval);
                timer.innerHTML = "Class ended";

                // Give a short delay, then refresh the dashboard
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
                return;
            }

            const ongoingMins = Math.floor(ongoingDiff / (1000 * 60));
            const ongoingSecs = Math.floor((ongoingDiff % (1000 * 60)) / 1000);
            timer.innerHTML = `Time remaining: ${ongoingMins}m ${ongoingSecs}s`;
        }, 1000);

    @endif // End if ongoing class countdown


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


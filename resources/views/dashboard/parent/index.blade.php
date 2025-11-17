@extends('layouts.app')

@section('title', 'Parent Dashboard')

@section('content')
<!-- <div class="row"> -->
<div class="container">
    <h5>👨‍👩‍👧 Welcome back, {{ $parent->name ?? auth()->user()->name }}</h5>

    <div class="row">
        <!-- [col-8] start -->
        <div class="col-lg-8">

           {{-- Child Selector --}}
            @if($children->isNotEmpty())
                <form method="GET" class="mb-3">
                    <label class="form-label fw-bold">Select Child</label>
                    <select name="child_id" class="form-select" onchange="this.form.submit()">
                        @foreach($children as $c)
                            <option value="{{ $c->id }}" @if($child && $c->id === $child->id) selected @endif>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @else
                <div class="alert alert-info shadow-sm">
                    <h6 class="mb-1">No children linked yet</h6>
                    <p class="mb-2">You haven’t linked any student accounts. Please add or link a child to view lessons and attendance.</p>
                    
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#inviteChildModal">
                        Add / Link Child
                    </button>
                </div>

                {{-- Stop rendering rest of dashboard --}}
                <!-- @return -->
            @endif


            {{-- If child is selected, show stats --}}
            @if($child)
                <div class="row">
                    <div class="col-md-6 col-xl-6 mb-3">
                        <div class="card bg-virtual text-white shadow-sm">
                            <div class="card-body">
                                <h6 class="text-white">Total Courses</h6>
                                <h2 class="text-white">{{ $child->lessons->count() }}</h2>
                                <p class="mb-0">Lessons this month: {{ $lessonsThisMonth }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-6 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6>Monthly Attendance</h6>
                                <div class="d-flex align-items-center mt-2">
                                    <h3>{{ $monthPresentCount }}/{{ $monthTotalClasses }}</h3>
                                    <span class="badge bg-primary ms-2">{{ $monthAttendancePercent }}%</span>
                                </div>
                                <p class="text-muted mt-2 mb-0">
                                    @if($monthAttendancePercent >= 80)
                                        Excellent attendance 🎉
                                    @elseif($monthAttendancePercent >= 50)
                                        Improving 👍
                                    @else
                                        Needs work 📌
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Charts --}}
                <div class="row mt-3">
                    <div class="col-md-8 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-header">Attendance % Over Time</div>
                            <div class="card-body">
                                @if($attendanceStats->isEmpty())
                                    <p class="text-muted">No attendance data yet.</p>
                                @else
                                    <canvas id="attendanceChart"></canvas>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-header">Overall Attendance</div>
                            <div class="card-body">
                                @if(($presentCount + $lateCount + $absentCount) === 0)
                                    <p class="text-muted">No records yet.</p>
                                @else
                                    <canvas id="attendanceDoughnut"></canvas>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Upcoming Lessons --}}
                <div class="card mt-3 shadow-sm">
                    <div class="card-header">Upcoming Lessons</div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Instructor</th>
                                    <th>Time</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcoming as $class)
                                    <tr>
                                        <td>{{ $class->lesson->subject }}</td>
                                        <td>{{ $class->lesson->instructor->name }}</td>
                                        <td>{{ $class->scheduled_start->format('d M Y h:i A') }}</td>
                                        <td><span class="badge bg-success">{{ $class->duration_minutes }}m</span></td>
                                        <td><span class="badge bg-primary">{{ ucfirst($class->status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted">No upcoming classes</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @endif
        
        </div>
        <!-- [col-8] end -->
        <!-- [col-4] start -->
        <div class="col-lg-4">
            
            <!-- Custom Calendar -->
            <x-full-calendar />
            <!-- End Custom Calendar -->
            
            <!-- <h3 class="mt-3">Next Event</h3> -->

            <!-- Subscription -->
            <div class="card mb-3">
                <div class="card-header">Subscription</div>
                <div class="card-body">
                    @if($subscription)
                        Plan: <strong>{{ $subscription->plan->name }}</strong> <br>
                        Status: <span class="badge bg-{{ $subscription->status == 'active' ? 'success':'warning' }}">
                            {{ ucfirst($subscription->status) }}
                        </span><br>
                        Expires: {{ $subscription->end_date?->format('d M Y') ?? 'N/A' }}
                    @else
                        <p>No active subscription</p>
                    @endif
                </div>
            </div>

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

{{-- Invite Child Modal --}}
<div class="modal fade" id="inviteChildModal" tabindex="-1" aria-labelledby="inviteChildLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('parent.link.child') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="inviteChildLabel">Invite / Link Child</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="child_email" class="form-label">Child’s Email</label>
            <input type="email" class="form-control" id="child_email" name="child_email" required>
          </div>
          <div class="mb-3">
            <label for="link_code" class="form-label">Link Code</label>
            <input type="text" class="form-control" id="link_code" name="link_code" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Link Child</button>
        </div>
      </form>
    </div>
  </div>
</div>


@endsection



{{-- Scripts --}}
@if($child && !$attendanceStats->isEmpty())
<script>
document.addEventListener("DOMContentLoaded", function () {
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
            options: { responsive: true, scales: { y: { beginAtZero: true, max: 100 } } }
        });
    }

    const present = @json($presentCount);
    const late    = @json($lateCount);
    const absent  = @json($absentCount);
    const ctxDoughnut = document.getElementById('attendanceDoughnut');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Late', 'Absent'],
                datasets: [{
                    data: [present, late, absent],
                    backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }
});
</script>
@endif

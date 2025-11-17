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
                    <div class="card bg-v-secondary order-card">
                    <div class="card-body">
                        <h6 class="text-white">Total Courses</h6>
                        <h2 class="text-end text-white"><i class="feather ph ph-book float-start"></i>
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
                        <h2 class="text-end text-white"><i class="bi bi-people-fill float-start"></i><span>{{ $students->count() }}</span> </h2>
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
                            {{ $class->lesson->subject }}

                            <a href="{{ route('lesson.join', $class) }}" class="btn btn-sm btn-primary" target="_blank">Start class</a>
                            {{-- <!--
                            @if($class->zoomSession)
                                <a href="{{ $class->zoomSession->start_url }}" target="_blank" class="btn btn-sm btn-primary">Start class</a>
                            @endif --> --}}
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
                                        <a href="{{ route('lesson.join', $class) }}" class="btn btn-sm btn-primary" target="_blank">Start Class</a>
                                        {{-- <!-- @if($class->zoomSession)
                                            <a href="{{ $class->zoomSession->start_url }}" target="_blank" class="btn btn-sm btn-primary">Start Class</a>
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
            
            <!-- Custom Calendar -->
            <x-full-calendar />
            <!-- End Custom Calendar -->
                        
            @if($ongoingClass)
            <div class="card mt-3 border-0 shadow-sm">
                <div class="card-header bg-sky-blue text-white">
                    <h5 class="mb-0">Current Ongoing Class</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">{{ $ongoingClass->lesson->subject }}</h6>
                    <p>
                        <strong>Student:</strong> {{ $ongoingClass->lesson->student->name }}<br>
                        <strong>Start:</strong> {{ $ongoingClass->scheduled_start->format('d M Y h:i A') }}<br>
                        <strong>End:</strong> {{ $ongoingClass->scheduled_start->copy()->addMinutes($ongoingClass->duration_minutes)->format('h:i A') }}
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
                        <p class="text-white text-opacity-75">
                            <strong>Student:</strong> {{ $nextClass->lesson->student->name }}
                        </p>
                        <p class="text-white text-opacity-75">
                            <strong>Scheduled:</strong> {{ $nextClass->scheduled_start->format('d M Y h:i A') }}
                        </p>

                        <p id="countdown" class="lead text-white text-opacity-75"></p>

                        <a href="{{ route('lesson.join', $nextClass) }}" class="btn btn-light" target="_blank">Join Class</a>
                        {{-- <!-- @if($nextClass->zoomSession)
                            <a href="{{ $nextClass->zoomSession->start_url }}" class="btn btn-light" target="_blank">Start Class</a>
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
                            $snippet = $data['message_lines'][1] ?? $data['message'] ?? 'Click for details...';
                            
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
        const endTime = new Date("{{ $ongoingClass?->scheduled_start->copy()->addMinutes($ongoingClass->duration_minutes)->toIso8601String() }}").getTime();
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

});

</script>


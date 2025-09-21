@extends('layouts.app')

@section('title', 'Parent Dashboard')

@section('content')
<!-- <div class="row"> -->
<div class="container">
    <h5>👨‍👩‍👧 Welcome back, {{ $parent->name }}</h5>

    <div class="row">
        <!-- [col-8] start -->
        <div class="col-lg-8">

           <!-- Child Selector -->
            <form method="GET" action="{{ route('parent.dashboard') }}" class="mb-3">
                <label for="child_id" class="form-label">Select Child:</label>
                <select name="child_id" id="child_id" class="form-select" onchange="this.form.submit()">
                    @foreach($children as $c)
                        <option value="{{ $c->id }}" @if($child && $c->id === $child->id) selected @endif>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            @if($child)
            <div class="row">
                    <!-- Charts -->
                    <div class="card mb-3">
                        <div class="card-header">Attendance % Trend</div>
                        <div class="card-body">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">Overall Attendance</div>
                        <div class="card-body">
                            <canvas id="attendanceDoughnut"></canvas>
                        </div>
                    </div>

                    <!-- Upcoming lessons -->
                    <div class="card">
                        <div class="card-header">Upcoming Lessons</div>
                        <div class="card-body">
                            <table class="table">
                                <thead><tr><th>Subject</th><th>Instructor</th><th>Time</th></tr></thead>
                                <tbody>
                                @forelse($upcoming as $lesson)
                                    <tr>
                                        <td>{{ $lesson->lesson->subject }}</td>
                                        <td>{{ $lesson->lesson->instructor->name }}</td>
                                        <td>{{ $lesson->scheduled_start->format('d M Y h:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3">No upcoming lessons</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                
                
            </div>
            @endif
        
        </div>
        <!-- [col-8] end -->
        <!-- [col-4] start -->
        <div class="col-lg-4">
            <iframe src="https://calendar.google.com/calendar/embed?height=300&wkst=1&ctz=UTC&showPrint=0&showTabs=0&showCalendars=0&showTz=0" style="border-width:0" width="300" height="300" frameborder="0" scrolling="no"></iframe>
            
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
                        Expires: {{ $subscription->ends_at?->format('d M Y') ?? 'N/A' }}
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
    const labels = @json($attendanceStats->pluck('month'));
    const data   = @json($attendanceStats->pluck('percent'));

    new Chart(document.getElementById('attendanceChart'), {
        type: 'line',
        data: { labels, datasets:[{label:"Attendance %",data}] },
    });

    new Chart(document.getElementById('attendanceDoughnut'), {
        type: 'doughnut',
        data: {
            labels: ['Present','Late','Absent'],
            datasets: [{
                data: [
                    {{ $breakdown['present'] }},
                    {{ $breakdown['late'] }},
                    {{ $breakdown['absent'] }}
                ]
            }]
        }
    });

});

</script>


@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<!-- <div class="row"> -->
<div class="container">
    <h5>Welcome back {{ Auth::user()->name }}</h5>

    <!-- Quick Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-book-fill me-2"></i>Manage Courses
                        </a>
                        <a href="{{ route('admin.facilitators.index') }}" class="btn btn-outline-success">
                            <i class="bi bi-laptop me-2"></i>Manage Tutors
                        </a>
                        <a href="{{ route('admin.students') }}" class="btn btn-outline-info">
                            <i class="bi bi-people-fill me-2"></i>Manage Students
                        </a>
                        <a href="{{ route('admin.instructors') }}" class="btn btn-outline-warning">
                            <i class="bi bi-person-badge me-2"></i>Manage Instructors
                        </a>
                        <a href="{{ route('admin.courses.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>New Course
                        </a>
                        <a href="{{ route('admin.facilitators.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>New Tutor
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- [col-8] start -->
        <div class="col-lg-8">

           <div class="row">
               <div class="col-md-6 col-xl-6">
                    <div class="card virtual-secondary order-card">
                    <div class="card-body">
                        <h6 class="text-white">Total Courses</h6>
                        <h2 class="text-end text-white"><i class="bi bi-book-fill float-start"></i>
                             <span>{{ $totalCourses }}</span> 
                        </h2>
                        <p class="m-b-0">Active Courses<span class="float-end">{{ $totalCourses }}</span></p>
                    </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-6">
                    <div class="card bg-grd-primary order-card">
                    <div class="card-body">
                        <h6 class="text-white">Total Tutors</h6>
                        <h2 class="text-end text-white"><i class="bi bi-laptop float-start"></i><span>{{ $totalTutors }}</span> </h2>
                        <p class="m-b-0">Online Tutors<span class="float-end"> {{ $totalTutors }} </span></p>
                    </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-6">
                    <div class="card virtual-secondary order-card">
                    <div class="card-body">
                        <h6 class="text-white">Total Student</h6>
                        <h2 class="text-end text-white"><i class="feather bi bi-people float-start"></i>
                             <span>{{ $totalStudents }}</span> 
                        </h2>
                        <p class="m-b-0">Active<span class="float-end">{{ $totalStudents }}</span></p>
                    </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-6">
                    <div class="card bg-grd-primary order-card">
                    <div class="card-body">
                        <h6 class="text-white">Instructor</h6>
                        <h2 class="text-end text-white"><i class="feather bi bi-people-fill float-start"></i><span>{{ $totalInstructors }}</span> </h2>
                        <p class="m-b-0">Active<span class="float-end"> {{ $totalInstructors }} </span></p>
                    </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-6">
                    <div class="card bg-grd-primary order-card">
                    <div class="card-body">
                        <h6 class="text-white">Active Subscriptions</h6>
                        <h2 class="text-end text-white"><i class="feather bi bi-credit-card float-start"></i><span>{{ $activeSubs }}</span> </h2>
                        <p class="m-b-0">Pending Payments<span class="float-end"> {{ $pendingPayments }} </span></p>
                    </div>
                    </div>
                </div>
            </div> <!-- row end -->

            <!-- Recent Courses -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Recent Courses</h5>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Category</th>
                                <th>Facilitator</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCourses as $course)
                                <tr>
                                    <td><strong>{{ $course->name }}</strong></td>
                                    <td>{{ $course->category->name ?? 'N/A' }}</td>
                                    <td>{{ $course->facilitator->name ?? 'N/A' }}</td>
                                    <td>₦{{ number_format($course->price ?? 0, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $course->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $course->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-info">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No courses yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Tutors -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Recent Online Tutors</h5>
                    <a href="{{ route('admin.facilitators.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tutor Name</th>
                                <th>Expertise</th>
                                <th>Qualifications</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTutors as $tutor)
                                <tr>
                                    <td><strong>{{ $tutor->name }}</strong></td>
                                    <td>{{ $tutor->expertise ?? 'N/A' }}</td>
                                    <td>{{ Str::limit($tutor->qualification ?? 'N/A', 30) }}</td>
                                    <td>
                                        <span class="badge {{ $tutor->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $tutor->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.facilitators.edit', $tutor) }}" class="btn btn-sm btn-info">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">No tutors yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Payments -->\n            <div class="card mb-4">
                <div class="card-header">Recent Payments</div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Parent</th>
                                <th>Student</th>
                                <th>Plan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $p)
                                <tr>
                                    <td>{{ $p->parent->name }}</td>
                                    <td>{{ $p->subscription->student->name }}</td>
                                    <td>{{ $p->subscription->plan->name }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($p->status == 'approved') bg-success 
                                            @elseif($p->status == 'pending') bg-warning 
                                            @else bg-danger @endif">
                                            {{ ucfirst($p->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4">No recent payments.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Reschedule Requests -->
            <div class="card mb-3">
                <div class="card-header">Pending Reschedules</div>
                <div class="card-body">
                    @forelse($pendingReschedules as $req)
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

           <!-- Upcoming Lessons -->
            <div class="card mb-4">
                <div class="card-header">Upcoming Lessons</div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Student</th>
                                <th>Instructor</th>
                                <th>Time</th>
                                <!-- <th>Zoom</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingLessons as $lesson)
                                <tr>
                                    <td>{{ $lesson->lesson->subject }}</td>
                                    <td>{{ $lesson->lesson->student->name }}</td>
                                    <td>{{ $lesson->lesson->instructor->name }}</td>
                                    <td>{{ $lesson->scheduled_start->format('d M Y h:i A') }}</td>
                                    <!-- <td>
                                        @if($lesson->zoomSession)
                                            <a href="{{ $lesson->zoomSession->join_url }}" target="_blank" class="btn btn-sm btn-primary">Join</a>
                                        @else
                                            <span class="text-muted">Not ready</span>
                                        @endif
                                    </td> -->
                                </tr>
                            @empty
                                <tr><td colspan="5">No upcoming lessons.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        
        </div><!-- [col-8] end -->

        <!-- [col-4] start -->
        <div class="col-lg-4">
            
            <!-- Custom Calendar -->
            <x-full-calendar />
            <!-- End Custom Calendar -->
            

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




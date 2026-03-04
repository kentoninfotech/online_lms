@extends('layouts.landing')

@section('title', $course->title)

@section('content')
<div style="padding-top: 20px;">
    <div class="container-fluid">
        <div style="margin-bottom: 30px;">
            <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Courses
            </a>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Featured Image -->
                @if($course->featured_image)
                    <img src="{{ asset($course->featured_image) }}" alt="{{ $course->title }}" class="img-fluid rounded-3 shadow-lg mb-4" style="max-height: 400px; object-fit: cover; width: 100%;">
                @else
                    <div class="bg-gradient-to-r" style="background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%); height: 400px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 30px;">
                        <span style="font-size: 5rem;">📚</span>
                    </div>
                @endif

                <!-- Course Header -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-primary">{{ $course->category->name }}</span>
                            <span class="h4 mb-0 text-primary fw-bold">{{ number_format($course->fee) }} {{ $course->currency }}</span>
                        </div>
                        <h1 class="h2 fw-bold mb-2">{{ $course->title }}</h1>
                        @if($course->subtitle)
                            <p class="lead text-muted mb-3">{{ $course->subtitle }}</p>
                        @endif
                        
                        <div class="row g-4 py-3 border-top border-bottom">
                            <div class="col-md-4">
                                <small class="text-muted d-block"><i class="bi bi-clock"></i> Duration</small>
                                <strong>{{ $course->course_hours ?? 'N/A' }} hours</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block"><i class="bi bi-people"></i> Students</small>
                                <strong>{{ $enrollmentCount }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block"><i class="bi bi-diagram-2"></i> Mode</small>
                                <strong>
                                    @if($course->is_online && $course->is_offline)
                                        Hybrid
                                    @elseif($course->is_online)
                                        Online
                                    @else
                                        Offline
                                    @endif
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($course->description)
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h2 class="h4 fw-bold mb-3">Course Overview</h2>
                            <div class="text-body">
                                {!! $course->description !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Dates & Venues -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h4 fw-bold mb-4">Available Dates & Locations</h2>
                        
                        @forelse($course->courseDates as $date)
                            <div class="mb-4 pb-4 border-bottom" style="last-child: border-bottom: none;">
                                <h5 class="fw-semibold mb-3">
                                    <i class="bi bi-calendar-event"></i> {{ $date->date_label ?? $date->start_date->format('M d - ') . $date->end_date->format('M d, Y') }}
                                </h5>
                                <div class="row g-3">
                                    @forelse($date->venues as $venue)
                                        <div class="col-md-6">
                                            <div class="border rounded p-3" style="border: 1px solid #dee2e6;">
                                                <p class="fw-semibold mb-2"><i class="bi bi-geo-alt"></i> {{ $venue->venue_name }}</p>
                                                @if($venue->address)
                                                    <p class="small text-muted mb-1">{{ $venue->address }}</p>
                                                @endif
                                                @if($venue->city || $venue->state)
                                                    <p class="small text-muted mb-2">{{ $venue->city }}{{ $venue->city && $venue->state ? ', ' : '' }}{{ $venue->state }}</p>
                                                @endif
                                                @if($venue->capacity)
                                                    <p class="small mt-2">
                                                        <strong>Capacity:</strong> {{ $venue->enrolled_count }}/{{ $venue->capacity }}
                                                        @if($venue->isAtCapacity())
                                                            <span class="badge bg-danger ms-2">FULL</span>
                                                        @else
                                                            <span class="badge bg-success ms-2">Available</span>
                                                        @endif
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="text-muted">No venues available for this date</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No scheduled dates available</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Facilitator(s) Card -->
                @if($course->facilitators && $course->facilitators->count() > 0)
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-3">
                                @if($course->facilitators->count() === 1)
                                    About the Facilitator
                                @else
                                    About the Facilitators
                                @endif
                            </h5>
                            
                            @foreach($course->facilitators as $facilitator)
                                <div class="text-center mb-4@if(!$loop->last) pb-4 border-bottom@endif">
                                    @if($facilitator->profile_image)
                                        <img src="{{ asset($facilitator->profile_image) }}" alt="{{ $facilitator->name }}" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle mx-auto mb-3 bg-primary d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                            <span style="font-size: 2rem;">👤</span>
                                        </div>
                                    @endif
                                    <p class="fw-semibold mb-1">{{ $facilitator->name }}</p>
                                    @if($facilitator->qualification)
                                        <p class="small text-muted mb-2">{{ $facilitator->qualification }}</p>
                                    @endif
                                    @if($facilitator->bio)
                                        <p class="small text-muted mb-3">{{ $facilitator->bio }}</p>
                                    @endif
                                    <a href="{{ route('facilitators.show', $facilitator) }}" class="btn btn-sm btn-outline-primary">
                                        View Profile →
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($course->facilitator)
                    <!-- Fallback to single facilitator if facilitators collection is empty -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold mb-3">About the Facilitator</h5>
                            @if($course->facilitator->profile_image)
                                <img src="{{ asset($course->facilitator->profile_image) }}" alt="{{ $course->facilitator->name }}" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="rounded-circle mx-auto mb-3 bg-primary d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                    <span style="font-size: 2rem;">👤</span>
                                </div>
                            @endif
                            <p class="fw-semibold mb-1">{{ $course->facilitator->name }}</p>
                            @if($course->facilitator->qualification)
                                <p class="small text-muted mb-2">{{ $course->facilitator->qualification }}</p>
                            @endif
                            @if($course->facilitator->bio)
                                <p class="small text-muted mb-3">{{ $course->facilitator->bio }}</p>
                            @endif
                            <a href="{{ route('facilitators.show', $course->facilitator) }}" class="btn btn-sm btn-outline-primary">
                                View Profile →
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Announcements Section (for Tutors/Admins) -->
                @if(Auth::check() && (Auth::user()->hasRole('admin') || $course->facilitator_id === Auth::id()))
                    <div class="card shadow-sm mb-4 border-primary">
                        <div class="card-body">
                            <h6 class="card-title fw-bold mb-3">
                                <i class="bi bi-megaphone text-primary me-2"></i>Course Announcements
                            </h6>
                            <p class="small text-muted mb-3">Send messages to all enrolled students</p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('course.announcement.create', $course) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle me-2"></i>Send Announcement
                                </a>
                                <a href="{{ route('course.announcement.history', $course) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-list me-2"></i>View History
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Enrollment CTA -->
                <div class="card shadow-sm" style="position: sticky; top: 100px;">
                    <div class="card-body">
                        @if($hasEnrolled)
                            @if($enrollmentStatus === 'active')
                                <div class="text-center">
                                    <p class="text-success fw-semibold mb-3"><i class="bi bi-check-circle"></i> You are enrolled</p>
                                    <a href="{{ route('courses.learn', $course) }}" class="btn btn-primary w-100">
                                        Go to Course
                                    </a>
                                </div>
                            @else
                                <div class="text-center">
                                    @if($pendingPayment)
                                        {{-- Show "Make Payment" button if payment is pending, rejected, or failed --}}
                                        @if($pendingPayment->status === 'pending' && $pendingPayment->approval_status === 'rejected')
                                            <p class="text-danger fw-semibold mb-3"><i class="bi bi-x-circle"></i> Payment Rejected</p>
                                            <p class="small text-muted mb-3">Your payment was rejected by the admin. Please try again.</p>
                                        @elseif($pendingPayment->status === 'failed')
                                            <p class="text-danger fw-semibold mb-3"><i class="bi bi-x-circle"></i> Payment Failed</p>
                                            <p class="small text-muted mb-3">Your payment could not be processed. Please try again.</p>
                                        @else
                                            <p class="text-warning fw-semibold mb-3"><i class="bi bi-credit-card"></i> Payment Pending</p>
                                            <p class="small text-muted mb-3">Your enrollment requires payment to proceed.</p>
                                        @endif
                                        <a href="{{ route('course.payment.show', $pendingPayment) }}" class="btn btn-warning w-100 mb-2">
                                            <i class="bi bi-credit-card me-2"></i>Make Payment
                                        </a>
                                        <p class="small text-muted">
                                            Amount Due: <strong>{{ number_format($pendingPayment->amount) }} {{ $pendingPayment->course->currency }}</strong>
                                        </p>
                                    @else
                                        {{-- No pending payment means it's either completed or awaiting admin approval --}}
                                        <p class="text-info fw-semibold mb-3"><i class="bi bi-hourglass-split"></i> Processing Payment</p>
                                        <p class="small text-muted">Your payment is being processed or awaiting admin approval. Please check back soon.</p>
                                        <button class="btn btn-secondary w-100" disabled>
                                            <i class="bi bi-clock-history me-2"></i>Awaiting Confirmation
                                        </button>
                                    @endif
                                </div>
                            @endif
                        @else
                            @auth
                                <a href="{{ route('courses.enroll', $course) }}" class="btn btn-success w-100 mb-2">
                                    <i class="bi bi-plus-circle me-2"></i>Enroll Now
                                </a>
                                <p class="text-center small text-muted">
                                    Price: <strong>{{ number_format($course->fee) }} {{ $course->currency }}</strong>
                                </p>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">
                                    Login to Enroll
                                </a>
                                <p class="text-center small">
                                    Don't have an account? <a href="{{ route('register') }}" class="text-primary fw-semibold">Sign up</a>
                                </p>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

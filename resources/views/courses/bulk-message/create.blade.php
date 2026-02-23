@extends('layouts.landing')

@section('title', 'Send Announcement')

@section('content')
<div style="padding: 40px 0;">
    <div class="container" style="max-width: 700px;">

        <div class="mb-4">
            <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Course
            </a>
        </div>

        <div class="card shadow-lg">
            <div class="card-body p-5">
                <h2 class="h3 fw-bold mb-2">Send Announcement</h2>
                <p class="text-muted mb-4">Send a message to all enrolled students</p>

                <!-- Course & Recipient Info -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Course</h6>
                        <h5 class="mb-3">{{ $course->title }}</h5>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted d-block">Total Recipients</small>
                                <h6>{{ $enrolleeCount }} Active Enrollees</h6>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Sender</small>
                                <h6>{{ Auth::user()->name }}</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Announcement Form -->
                <form action="{{ route('course.announcement.store', $course) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" 
                            value="{{ old('subject') }}" placeholder="Announcement subject..." required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" 
                            rows="6" placeholder="Write your message here..." required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-2">Minimum 10 characters</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Send Via <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="methods[]" value="email" id="emailCheck" checked>
                            <label class="form-check-label" for="emailCheck">
                                <i class="bi bi-envelope me-2"></i>Email
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="methods[]" value="sms" id="smsCheck">
                            <label class="form-check-label" for="smsCheck">
                                <i class="bi bi-chat-dots me-2"></i>SMS
                            </label>
                        </div>
                        @error('methods')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-2">Select at least one delivery method</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Schedule (Optional)</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror" 
                            value="{{ old('scheduled_at') }}">
                        @error('scheduled_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-2">Leave empty to send immediately</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send me-2"></i>Send Announcement
                        </button>
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>

                <div class="mt-4 p-3 bg-light rounded">
                    <small class="text-muted">
                        💡 <strong>Tips:</strong> 
                        <ul class="mb-0">
                            <li>Be clear and concise with your message</li>
                            <li>Email is more reliable for important announcements</li>
                            <li>All enrolled students will receive this announcement</li>
                        </ul>
                    </small>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

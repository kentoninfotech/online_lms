@extends('layouts.landing')

@section('title', 'Announcement History')

@section('content')
<div style="padding: 40px 0;">
    <div class="container">

        <div class="mb-4 d-flex justify-content-between align-items-center">
            <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Course
            </a>
            <a href="{{ route('course.announcement.create', $course) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-2"></i>New Announcement
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Announcement History - {{ $course->title }}</h5>
            </div>

            @if($messages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Subject</th>
                                <th>Sent By</th>
                                <th>Recipients</th>
                                <th>Status</th>
                                <th>Sent</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                                <tr>
                                    <td>
                                        <strong>{{ Str::limit($message->subject, 40) }}</strong>
                                    </td>
                                    <td>{{ $message->sender->name }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $message->total_recipients }}</span>
                                    </td>
                                    <td>
                                        @if($message->status === 'sent')
                                            <span class="badge bg-success">Sent</span>
                                        @elseif($message->status === 'scheduled')
                                            <span class="badge bg-warning">Scheduled</span>
                                        @else
                                            <span class="badge bg-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td>{{ $message->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        @if($message->status === 'scheduled')
                                            <form action="{{ route('course.announcement.send', $message) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success me-2">
                                                    <i class="bi bi-send"></i> Send Now
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('course.announcement.show', $message) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $messages->links() }}
                </div>
            @else
                <div class="card-body text-center py-5">
                    <i class="bi bi-chat-dots h2" style="opacity: 0.5;"></i>
                    <p class="text-muted mt-3">No announcements sent yet</p>
                    <a href="{{ route('course.announcement.create', $course) }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle me-2"></i>Send First Announcement
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

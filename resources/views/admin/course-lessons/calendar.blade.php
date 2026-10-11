@extends('layouts.app')

@section('title', 'Lessons Calendar')

@section('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    .fc {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }
    .fc .fc-button-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .fc .fc-button-primary:not(:disabled).fc-button-active {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .calendar-sidebar {
        background: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    .lesson-item {
        background: white;
        border-left: 4px solid #0d6efd;
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 0.25rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .lesson-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transform: translateX(4px);
    }
    .lesson-item.status-upcoming {
        border-left-color: #0dcaf0;
    }
    .lesson-item.status-ongoing {
        border-left-color: #ffc107;
    }
    .lesson-item.status-completed {
        border-left-color: #198754;
    }
    .lesson-time {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Lessons Calendar</h3>
                <p class="text-muted">Course: <strong>{{ $course->title }}</strong></p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.course-lessons.index', $course) }}" class="btn btn-secondary me-2">
                    <i class="bi bi-list me-2"></i>List View
                </a>
                <a href="{{ route('admin.course-lessons.create', $course) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add Lesson
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="calendar-sidebar">
                <h6 class="mb-3">
                    <i class="bi bi-clock-history me-2"></i>Upcoming Lessons
                </h6>
                <div id="upcomingLessons">
                    <div class="text-center text-muted">
                        <small>Loading...</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const courseId = {{ $course->id }};

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 'auto',
        events: function(info, successCallback, failureCallback) {
            fetch(`/api/courses/${courseId}/lessons/calendar`)
                .then(response => response.json())
                .then(data => {
                    const events = data.map(lesson => ({
                        id: lesson.id,
                        title: lesson.title,
                        start: lesson.start,
                        end: lesson.end,
                        extendedProps: {
                            description: lesson.description,
                            location: lesson.location,
                            instructor: lesson.instructor,
                            meetingLink: lesson.meetingLink,
                            status: lesson.status
                        }
                    }));
                    successCallback(events);
                    updateUpcomingLessons();
                })
                .catch(error => {
                    console.error('Error fetching lessons:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            const event = info.event;
            const props = event.extendedProps;
            
            alert(`
Topic: ${event.title}
Instructor: ${props.instructor}
Date & Time: ${new Date(event.start).toLocaleString()}
Location: ${props.location || 'Not specified'}
Status: ${props.status}
${props.meetingLink ? 'Meeting Link: ' + props.meetingLink : ''}
${props.description ? 'Description: ' + props.description : ''}
            `);
        },
        eventDisplay: 'block',
        eventColor: '#0d6efd'
    });

    calendar.render();

    function updateUpcomingLessons() {
        fetch(`/api/upcoming-lessons?courseId=${courseId}`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('upcomingLessons');
                
                if (data.length === 0) {
                    container.innerHTML = '<div class="text-center text-muted"><small>No upcoming lessons</small></div>';
                    return;
                }

                container.innerHTML = data.map(lesson => `
                    <div class="lesson-item status-${getStatusClass(lesson.startTime)}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>${lesson.topic}</strong>
                                <div class="text-muted" style="font-size: 0.85rem;">
                                    ${lesson.courseName}
                                </div>
                            </div>
                            <a href="/courses/${lesson.courseId}/lessons/${lesson.id}" 
                               class="btn btn-sm btn-link p-0" title="View">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                        <div class="lesson-time">
                            <i class="bi bi-calendar-event"></i> ${new Date(lesson.startTime).toLocaleDateString()}
                            <br>
                            <i class="bi bi-clock"></i> ${new Date(lesson.startTime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                        </div>
                        ${lesson.location ? `<div class="lesson-time"><i class="bi bi-geo-alt"></i> ${lesson.location}</div>` : ''}
                        ${lesson.meetingLink ? `<div class="lesson-time"><a href="${lesson.meetingLink}" target="_blank"><i class="bi bi-link-45deg"></i> Join Meeting</a></div>` : ''}
                        <div class="lesson-time">
                            <i class="bi bi-person"></i> ${lesson.instructor}
                        </div>
                    </div>
                `).join('');
            })
            .catch(error => console.error('Error fetching upcoming lessons:', error));
    }

    function getStatusClass(dateTime) {
        const date = new Date(dateTime);
        const now = new Date();
        if (date < now) return 'completed';
        if (date.getTime() - now.getTime() < 3600000) return 'ongoing'; // Less than 1 hour
        return 'upcoming';
    }

    // Initial load
    updateUpcomingLessons();
    
    // Refresh every 5 minutes
    setInterval(updateUpcomingLessons, 300000);
});
</script>
@endsection

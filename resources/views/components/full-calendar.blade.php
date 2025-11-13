
@push('styles')
    <!-- Include Bootstrap 5 CSS -->
    <!-- Include FullCalendar CSS (DayGrid is standard) -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.13/main.min.css' rel='stylesheet' />
    <style>
        .fc .fc-toolbar-title {
            font-size: 1rem;
            font-weight: 400;
        }
        .fc-button {
            background-color: #330952 !important;
        }
        #my-calendar {
            max-width: 900px;
            margin: 0 auto;
            padding: 10px;
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
    </style>
@endpush


{{-- The content section now only contains the calendar container and the modal. --}}
<!-- Container for the calendar -->
<div id="my-calendar" class="mb-3"></div>

<!-- Bootstrap Event Detail Modal -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventDetailModalLabel">Lesson Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Lesson:</strong> <span id="modal-lesson-title"></span></p>
                <p><strong>Instructor:</strong> <span id="modal-lesson-instructor"></span></p>
                <p><strong>Student:</strong> <span id="modal-lesson-student"></span></p>
                <p><strong>Status:</strong> <span id="modal-lesson-status" class="badge bg-primary"></span></p>
                <p><strong>Start:</strong> <span id="modal-lesson-start"></span></p>
                <p><strong>End:</strong> <span id="modal-lesson-end"></span></p>
            </div>
            <div class="modal-footer">
                <!-- Assuming you have a route named 'lesson.join' -->
                <a id="modal-lesson-link" href="#" class="btn btn-primary">Join Class</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End Bootstrap Modal -->


{{-- 
    2. SCRIPTS: Push JavaScript to the bottom of the <body> section of layouts.app 
    If your layouts.app does not have a @stack('scripts'), change this to @section('scripts') or adjust your layout.
--}}
@push('scripts')
    <!-- Include Bootstrap 5 JS -->
    <!-- Include FullCalendar Core, DayGrid, and Interaction Plugins -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.13/index.global.min.js'></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Helper function to format date/time
        function formatDateTime(isoString) {
            if (!isoString) return 'N/A';
            const date = new Date(isoString);
            return date.toLocaleString('en-US', {
                year: 'numeric', month: 'short', day: 'numeric',
                hour: '2-digit', minute: '2-digit', hour12: true
            });
        }

        var calendarEl = document.getElementById('my-calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            // --- General Configuration ---
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            height: '400px', //'auto',
            editable: false, 
            selectable: true,
            // Optional: time zone setup (Crucial for accurate display)
            timeZone: 'Africa/Lagos', // Ensure this matches your server's timezone settings or DB storage

            // --- Event Source Configuration ---
            events: {
                // FIXED: Using the confirmed route name: 'calendar.occurrences'
                url: '{{ route('calendar.occurrences') }}',
                method: 'GET',
                extraParams: function() {
                    return {};
                },
                failure: function() {
                    // Handle API error
                    console.error('There was an error while fetching lesson occurrences. The 500 error is likely in the PHP controller.');
                }
            },

            // --- Event Interaction ---
            eventClick: function(info) {
                info.jsEvent.preventDefault(); // Stop default navigation

                const titleEl      = document.getElementById('modal-lesson-title');
                const instructorEl = document.getElementById('modal-lesson-instructor');
                const studentEl    = document.getElementById('modal-lesson-student');
                const statusEl     = document.getElementById('modal-lesson-status');
                const startEl      = document.getElementById('modal-lesson-start');
                const endEl        = document.getElementById('modal-lesson-end');
                const linkEl       = document.getElementById('modal-lesson-link');
                
                // Set text content
                titleEl.textContent      = info.event.title;
                instructorEl.textContent = info.event.extendedProps.instructor;
                studentEl.textContent    = info.event.extendedProps.student;
                statusEl.textContent     = info.event.extendedProps.status.toUpperCase();
                startEl.textContent      = formatDateTime(info.event.startStr);
                endEl.textContent        = formatDateTime(info.event.endStr);

                // Update status badge color
                statusEl.className = 'badge';
                let statusColorClass = 'bg-primary';
                switch(info.event.extendedProps.status) {
                    case 'scheduled': statusColorClass = 'bg-primary'; break;
                    case 'ended': statusColorClass = 'bg-info'; break;
                    case 'pending': statusColorClass = 'bg-warning text-dark'; break;
                    case 'cancelled': statusColorClass = 'bg-danger'; break;
                }
                statusEl.classList.add(statusColorClass);
                
                // Use a global variable to store the user's role status
                const isParentUser = @json(Auth::user()->hasRole('parent'));
                // Update link
                linkEl.href = info.event.url || '#';
                if (typeof isParentUser !== 'undefined' && !isParentUser) {
                    // User is authenticated and NOT a parent, so show the link
                    linkEl.style.display = 'inline-block'; 
                } else {
                    // User is a parent, not authenticated
                    linkEl.style.display = 'none';
                }

                // Show the modal
                const detailModal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
                detailModal.show();
            },

            // --- Event Rendering ---
            eventDidMount: function(info) {
                // Optional: Customize event rendering based on status
                if (info.event.extendedProps.status === 'cancelled') {
                    info.el.style.textDecoration = 'line-through';
                    info.el.style.opacity = '0.6';
                }
            }
        });

        calendar.render();
    });
    </script>
@endpush

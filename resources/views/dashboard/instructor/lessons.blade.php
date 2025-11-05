@extends('layouts.app')

@section('title', 'My Lessons')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
                <!-- Button trigger modal -->
                <button class="btn btn-sm btn-primary float-end" data-bs-toggle="modal" data-bs-target="#createLessonModal">
                    <i class="ph ph-plus"></i> New Lesson
                </button>
            <h4 class="mb-0">My Lessons</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('instructor.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">My Lessons</li>
            </ul>
        </div>
        </div>
    </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->

<!-- Today’s Schedule -->
<div class="card mb-3">
    <div class="card-header">Today’s Classes</div>
    <div class="card-body">
        @forelse($todayLessons as $class)
            <p>
                {{ $class->scheduled_start->format('h:i A') }} — {{ $class->lesson->student->name }}
                — {{ $class->lesson->subject }}
                <a href="{{ route('lesson.join', $class) }}" target="_blank" class="btn btn-sm btn-primary">Start class</a>
                {{-- <!-- @if($class->zoomSession)
                    <a href="{{ $class->zoomSession->start_url }}" target="_blank" class="btn btn-sm btn-primary">Start class</a>
                @endif --> --}}
            </p>
        @empty
            <p>No classes scheduled today.</p>
        @endforelse
    </div>
</div>

<div class="card shadow-sm p-3">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Lesson</th>
                    <th>Student</th>
                    <th>Next Class</th>
                    <th>Status</th>
                    <th>Start</th>
                    <!-- <th>Action</th> -->
                </tr>
            </thead>
            <tbody>
                @forelse($lessons as $lesson)
                    <tr>
                        <td>{{ $lesson->subject }}</td>
                        <td>{{ $lesson->student->name ?? 'N/A' }}</td>
                        <td>
                            @php
                                $nextOccurrence = $lesson->occurrences()
                                  ->where('scheduled_start', '>=', now())
                                  ->orderBy('scheduled_start', 'asc')
                                  ->first();

                                if ($nextOccurrence == null) {
                                  // if no upcoming, get the latest past occurrence
                                  $nextOccurrence = $lesson->occurrences()
                                    ->orderBy('scheduled_start', 'desc')
                                    ->first();
                                }
                            @endphp

                            @if($nextOccurrence)
                                {{ $nextOccurrence->scheduled_start->format('d M Y h:i A') }}
                            @else
                                <span class="text-muted">No upcoming class</span>
                            @endif
                        </td>
                        <td>
                            @if($nextOccurrence?->status === 'scheduled')
                                <span class="badge bg-success">{{ Str::headline($nextOccurrence?->status)  ?? 'N/A' }}</span>
                            @else
                                <span class="badge bg-warning">{{ Str::headline($nextOccurrence?->status) ?? 'N/A' }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('lesson.join', $nextOccurrence) }}" target="_blank" class="btn btn-sm btn-primary">
                                Start Class
                            </a>
                            
                            {{-- @if(! isset($nextOccurrence->zoomSession))
                                <!-- <span class="text-muted">Zoom link not ready</span> -->
                            @endif --}}
                        </td>
                        <td>
                            {{-- @if(! isset($nextOccurrence->zoomSession))
                              <!-- Add zoom button opens modal -->
                              <!-- <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#addZoomModal{{ $nextOccurrence->id }}">
                                    Add Zoom Meeting 
                              </button> -->
                            @endif --}}

                            <!-- Add Zoom Modal -->
                            <div class="modal fade" id="addZoomModal{{ $nextOccurrence->id }}" tabindex="-1" aria-labelledby="createMeetingLabel{{ $nextOccurrence->id }}" aria-hidden="true">
                              <div class="modal-dialog modal-lg">
                                <form action="{{ route('add.zoom', $nextOccurrence) }}" method="POST">
                                  @csrf
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title" id="createMeetingLabel">Add Zoom Meeting</h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <!-- lesson_occurrence_id -->
                                    <input type="hidden" name="lesson_occurrence_id" value="{{ $nextOccurrence->id }}" >

                                    <div class="modal-body row g-3">
                                      <!-- Subject -->
                                      <div class="col-md-6">
                                        <label class="form-label">Topic</label>
                                        <input type="text" name="topic" class="form-control @error('topic') is-invalid @enderror" required>
                                        @error('topic')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                      </div>

                                      <div class="col-md-6">
                                        <label class="form-label">Zoom Meeting ID</label>
                                        <input type="text" name="zoom_meeting_id" class="form-control @error('zoom_meeting_id') is-invalid @enderror" required>
                                        @error('zoom_meeting_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                      </div>

                                      <div class="col-md-6">
                                        <label class="form-label">Start Url</label>
                                        <input type="text" name="start_url" class="form-control @error('start_url') is-invalid @enderror" required>
                                        <p class="muted-text">Start Meeting URL for Instructor</p>
                                        @error('start_url')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                      </div>

                                      <div class="col-md-6">
                                        <label class="form-label">Join Url</label>
                                        <input type="text" name="join_url" class="form-control @error('join_url') is-invalid @enderror" required>
                                        <p class="muted-text">Join Meeting URL for Student</p>
                                        @error('join_url')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                      </div>

                                    </div> <!-- modal-body -->

                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                      <button type="submit" class="btn btn-primary">Add Meeting</button>
                                    </div>
                                  </div>
                                </form>
                              </div>
                            </div>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No lessons found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $lessons->links() }}
        </div>
    </div>
</div>

<!-- Create Lesson Modal -->
<div class="modal fade" id="createLessonModal" tabindex="-1" aria-labelledby="createLessonLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('lesson.store') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createLessonLabel">Create Lesson</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <p><strong>Whoops! Something went wrong.</strong></p>
                  <ul>
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                  </ul>
            </div>
        @endif

        <div class="modal-body row g-3">
          <!-- Subject -->
          <div class="col-md-6">
            <label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" required>
            @error('subject')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
          </div>

          <!-- instructor id -->
          <input type="hidden" name="instructor_id" value="{{ auth()->user()->instructor->id }}" >

          <!-- Student -->
          <div class="col-md-6">
            <label class="form-label">Student</label>
            <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
              @foreach($students as $student)
                <option value="{{ $student->id }}">{{ $student->user->name }}</option>
              @endforeach
            </select>
            @error('student_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
          </div>

          <!-- Start time -->
          <div class="col-md-6">
            <label class="form-label">Start Time</label>
            <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror" required>
            @error('start_time')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
          </div>

          <!-- Duration -->
          <div class="col-md-6">
            <label class="form-label">Duration (minutes)</label>
            <input type="number" name="duration_minutes" class="form-control" min="15" value="60" required>
          </div>

          <div class="form-group row mt-3">

            <!-- Recurrence Type -->
            <div class="col-md-6">
              <label class="form-label">Recurrence</label>
              <select name="recurrence_type" id="recurrence_type" class="form-select">
                <option value="none">None (One-time)</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
              </select>
            </div>

            <!-- Recurrence Meta Controls -->
          <div class="form-group row mt-3">
              <!-- Occurrence Count or End Date -->
              <div class="col-md-4 recurrence-field recurrence-daily recurrence-weekly recurrence-monthly d-none">
                  <label class="form-label">Recurrence End</label>
                  <select id="recurrence_end_type" name="end_type" class="form-select">
                      {{-- Repopulate end type, default to count --}}
                      <option value="count" {{ old('end_type', 'count') == 'count' ? 'selected' : '' }}>After number of occurrences</option>
                      <option value="date" {{ old('end_type') == 'date' ? 'selected' : '' }}>Until end date</option>
                  </select>
              </div>

              <!-- Count -->
              <div class="col-md-4 recurrence-field recurrence-daily recurrence-weekly recurrence-monthly d-none" id="countField">
                  <label class="form-label">Number of Occurrences</label>
                  {{-- Repopulate count field, default to 2 --}}
                  <input type="number" name="count" class="form-control" min="1" value="{{ old('count', 2) }}">
              </div>

              <!-- End Date -->
              <div class="col-md-4 recurrence-field recurrence-daily recurrence-weekly recurrence-monthly d-none d-none" id="endDateField">
                  <label class="form-label">End Date</label>
                  {{-- Repopulate end date field --}}
                  <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
              </div>

              <!-- Interval / Repeat Every -->
              <div class="col-md-4 recurrence-field recurrence-daily recurrence-weekly recurrence-monthly d-none">
                <label class="form-label">Repeat Every (Interval)</label>
                <div class="input-group">
                  <input type="number" name="interval" class="form-control" min="1" value="{{ old('interval', 1) }}" required>
                  <span class="input-group-text" id="intervalLabelText">day(s)</span>
                </div>
              </div>
          </div>

          <!-- Weekly Days -->
          <div class="col-md-12">
              <div class="recurrence-field recurrence-weekly d-none">
                  <label class="form-label">Select Days</label><br>
                  @foreach(['mon'=>'Mon','tue'=>'Tue','wed'=>'Wed','thu'=>'Thu','fri'=>'Fri','sat'=>'Sat','sun'=>'Sun'] as $key=>$day)
                      <div class="form-check form-check-inline">
                          {{-- Repopulate weekly days checkbox array --}}
                          <input class="form-check-input" type="checkbox" name="days[]" value="{{ $key }}" {{ is_array(old('days')) && in_array($key, old('days')) ? 'checked' : '' }}>
                          <label class="form-check-label">{{ $day }}</label>
                      </div>
                  @endforeach
              </div>
          </div>

          <!-- Monthly Mode -->
          <div class="col-md-12">
            <div class="recurrence-field recurrence-monthly d-none">
              <label class="form-label">Monthly Mode</label><br>
              <div class="form-check form-check-inline">
                {{-- Repopulate monthly mode radio, default to 'day' --}}
                <input class="form-check-input" type="radio" name="mode" value="day" {{ old('mode', 'day') == 'day' ? 'checked' : '' }}>
                <label class="form-check-label">By Day (e.g., 5th of each month)</label>
              </div>
              <div class="form-check form-check-inline">
                {{-- Repopulate monthly mode radio --}}
                <input class="form-check-input" type="radio" name="mode" value="weekday" {{ old('mode') == 'weekday' ? 'checked' : '' }}>
                <label class="form-check-label">By Weekday (e.g., 2nd Monday of each month)</label>
              </div>
            </div>
          </div>

        </div> <!-- modal-body -->

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Lesson</button>
        </div>
      </div>
    </form>
  </div>
</div>


@endsection


<script>
document.addEventListener("DOMContentLoaded", () => {
    const typeSelect = document.getElementById('recurrence_type');
    const endTypeSelect = document.getElementById('recurrence_end_type');
    const countField = document.getElementById('countField');
    const endDateField = document.getElementById('endDateField');
    const intervalLabelText = document.getElementById('intervalLabelText');

    // Helper function to toggle recurrence fields
    function toggleRecurrenceFields() {
        const type = typeSelect.value;

        // Hide all recurrence fields first
        document.querySelectorAll('.recurrence-field').forEach(el => el.classList.add('d-none'));

        // Show relevant fields if recurrence type is not "none"
        if (type !== 'none') {
          document.querySelectorAll(`.recurrence-${type}`).forEach(el => el.classList.remove('d-none'));
          document.querySelectorAll(`.recurrence-${type}, .recurrence-daily`).forEach(el => el.classList.remove('d-none'));

          // Update interval label dynamically
          if (type === 'daily') intervalLabelText.textContent = 'day(s)';
          else if (type === 'weekly') intervalLabelText.textContent = 'week(s)';
          else if (type === 'monthly') intervalLabelText.textContent = 'month(s)';

          // Show End Type selector and default to "count"
          document.querySelector('#recurrence_end_type').closest('.recurrence-field').classList.remove('d-none');
          toggleEndType(); // trigger initial state
          
        }
    }

    // Helper function to toggle between count and end date
    function toggleEndType() {
      if (endTypeSelect.value === 'count') {
        countField.classList.remove('d-none');
        endDateField.classList.add('d-none');
      } else {
        countField.classList.add('d-none');
        endDateField.classList.remove('d-none');
      }
    }

    // Event listeners
    typeSelect.addEventListener('change', toggleRecurrenceFields);
    endTypeSelect.addEventListener('change', toggleEndType);

    // Initialize on page load
    toggleRecurrenceFields();
});
</script>

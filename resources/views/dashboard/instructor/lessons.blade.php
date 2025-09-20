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
                @if($class->zoomSession)
                    <a href="{{ $class->zoomSession->start_url }}" target="_blank" class="btn btn-sm btn-primary">Start class</a>
                @endif
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
                </tr>
            </thead>
            <tbody>
                @forelse($lessons as $lesson)
                    <tr>
                        <td>{{ $lesson->subject }}</td>
                        <td>{{ $lesson->student->name ?? 'N/A' }}</td>
                        <td>
                            @php
                                $nextOccurrence = $lesson->occurrences->first();
                            @endphp
                            @if($nextOccurrence)
                                {{ $nextOccurrence->scheduled_start->format('d M Y h:i A') }}
                            @else
                                <span class="text-muted">No upcoming class</span>
                            @endif
                        </td>
                        <td>
                            @if($nextOccurrence->status === 'scheduled')
                                <span class="badge bg-success">{{ Str::headline($nextOccurrence->status) }}</span>
                            @else
                                <span class="badge bg-warning">{{ Str::headline($nextOccurrence->status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($nextOccurrence->zoomSession))
                                <a href="{{ $nextOccurrence->zoomSession->start_url }}" target="_blank" class="btn btn-sm btn-primary">
                                    Start Class
                                </a>
                            @else
                                <span class="text-muted">Zoom link not ready</span>
                            @endif
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
          {{-- <input type="hidden" name="instructor_id" value="{{ auth()->user()->instructor->id }}" > --}}

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

            <!-- Recurrence Meta (hidden initially) -->
            <!-- Count field -->
            <div class="col-md-6">
                <div class="recurrence-field recurrence-daily recurrence-weekly recurrence-monthly d-none">
                  <label class="form-label">Number of Occurrences</label>
                  <input type="number" name="count" class="form-control" min="1" value="2">
                </div>
            </div>

          </div><!-- end group row -->

          <!-- Weekly days -->
          <div class="col-md-12">
              <div class="recurrence-field recurrence-weekly d-none">
                  <label class="form-label">Select Days</label><br>
                  @foreach(['mon'=>'Mon','tue'=>'Tue','wed'=>'Wed','thu'=>'Thu','fri'=>'Fri','sat'=>'Sat','sun'=>'Sun'] as $key=>$day)
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="checkbox" name="days[]" value="{{ $key }}">
                      <label class="form-check-label">{{ $day }}</label>
                    </div>
                  @endforeach
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

  typeSelect.addEventListener('change', function () {
    document.querySelectorAll('.recurrence-field').forEach(el => el.classList.add('d-none'));

    if (this.value === 'daily' || this.value === 'monthly') {
      document.querySelectorAll('.recurrence-daily, .recurrence-monthly').forEach(el => el.classList.remove('d-none'));
    }

    if (this.value === 'weekly') {
      document.querySelectorAll('.recurrence-weekly').forEach(el => el.classList.remove('d-none'));
      document.querySelectorAll('.recurrence-weekly, .recurrence-daily').forEach(el => el.classList.remove('d-none'));
    }
  });
});

</script>

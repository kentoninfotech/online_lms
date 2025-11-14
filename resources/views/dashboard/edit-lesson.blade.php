@extends('layouts.app')

@section('title', 'Edit Lesson')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
        <div class="card-body">
            <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title border-bottom pb-2 mb-2">
                    <a href="{{ route( auth()->user()->user_type .'.lessons') }}" 
                        class="btn btn-sm btn-primary float-end">
                        ← Back to Lessons
                    </a>
                     <h4 class="mb-0">Edit Lesson: {{ $lesson->subject }}</h4>
                </div>
            </div>
            <div class="col-md-12">
                <ul class="breadcrumb">
                <li class="breadcrumb-item"
                    ><a href="{{ route( auth()->user()->user_type .'.dashboard') }}"><i class="ph ph-house"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Edit Lesson</li>
                </ul>
            </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="card shadow-sm mt-3">
    <div class="card-body">
        <form action="{{ route('lesson.update', $lesson->id) }}" method="POST">
             @csrf
             @method('PUT') 

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

            <div class="form-group row">
                <!-- Subject -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" 
                        value="{{ old('subject', $lesson->subject) }}" required>
                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                @hasrole('admin')
                   <!-- Instructor -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Instructor</label>
                        <select name="instructor_id" class="form-select @error('instructor_id') is-invalid @enderror" required>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" 
                                {{ old('instructor_id', $lesson->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                {{ $instructor->user->name }}
                            </option>
                        @endforeach
                        </select>
                        @error('instructor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @else
                    <input type="hidden" name="instructor_id" value="{{ auth()->user()->instructor->id }}">
                @endhasrole

                <!-- Student -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" 
                            {{ old('student_id', $lesson->student_id) == $student->id ? 'selected' : '' }}>
                            {{ $student->user->name }}
                        </option>
                    @endforeach
                    </select>
                    @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <!-- Start time -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Time</label>
                    <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror" 
                        value="{{ old('start_time', \Carbon\Carbon::parse($lesson->start_time)->format('Y-m-d\TH:i')) }}" required>
                    @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Duration -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" class="form-control" min="15" 
                        value="{{ old('duration_minutes', $lesson->duration_minutes) }}" required>
                </div>
                <!-- Recurrence Type -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Recurrence</label>
                    <select name="recurrence_type" id="recurrence_type" class="form-select">
                        {{-- Repopulate recurrence type --}}
                        <option value="none" {{ old('recurrence_type', $lesson->recurrence_type ?? 'none') == 'none' ? 'selected' : '' }}>None (One-time)</option>
                        <option value="daily" {{ old('recurrence_type', $lesson->recurrence_type) == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ old('recurrence_type', $lesson->recurrence_type) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ old('recurrence_type', $lesson->recurrence_type) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>
            </div>

            <!-- Recurrence Meta Controls -->
            <div class="form-group row mt-3">
                @php
                    // Safely retrieve recurrence meta data
                    $meta = $lesson->recurrence_meta ?? [];
                @endphp

                <!-- Occurrence Count or End Date -->
                <div class="col-md-4 recurrence-field recurrence-daily recurrence-weekly recurrence-monthly d-none">
                    <label class="form-label">Recurrence End</label>
                    {{-- Access end_type from recurrence_meta --}}
                    <select id="recurrence_end_type" name="end_type" class="form-select">
                        <option value="count" {{ old('end_type', $meta['end_type'] ?? 'count') == 'count' ? 'selected' : '' }}>After number of occurrences</option>
                        <option value="date" {{ old('end_type', $meta['end_type'] ?? null) == 'date' ? 'selected' : '' }}>Until end date</option>
                    </select>
                </div>

                <!-- Count -->
                <div class="col-md-4 recurrence-field recurrence-daily recurrence-weekly recurrence-monthly d-none" id="countField">
                    <label class="form-label">Number of Occurrences</label>
                    {{-- Access count from recurrence_meta --}}
                    <input type="number" name="count" class="form-control" min="1" 
                        value="{{ old('count', $meta['count'] ?? 2) }}">
                </div>

                <!-- End Date -->
                <div class="col-md-4 recurrence-field recurrence-daily recurrence-weekly recurrence-monthly d-none d-none" id="endDateField">
                    <label class="form-label">End Date</label>
                    {{-- Access end_date from recurrence_meta, format date to 'YYYY-MM-DD' --}}
                    <input type="date" name="end_date" class="form-control" 
                        value="{{ old('end_date', ($meta['end_date'] ?? null) ? \Carbon\Carbon::parse($meta['end_date'])->format('Y-m-d') : '') }}">
                </div>

                <!-- Interval / Repeat Every -->
                <div class="col-md-4 recurrence-field recurrence-daily recurrence-weekly recurrence-monthly d-none">
                    <label class="form-label">Repeat Every (Interval)</label>
                    <div class="input-group">
                        {{-- Access interval from recurrence_meta --}}
                        <input type="number" name="interval" class="form-control" min="1" 
                            value="{{ old('interval', $meta['interval'] ?? 1) }}" required>
                        <span class="input-group-text" id="intervalLabelText">day(s)</span>
                    </div>
                </div>
            </div>

            <!-- Weekly Days -->
            <div class="col-md-12">
                <div class="recurrence-field recurrence-weekly d-none mb-3">
                    <label class="form-label">Select Days</label><br>
                    @php
                        // Combine old input with existing lesson data for repopulation. Access days_of_week from recurrence_meta
                        $selected_days = old('days', $meta['days'] ?? []);
                    @endphp
                    @foreach(['mon'=>'Mon','tue'=>'Tue','wed'=>'Wed','thu'=>'Thu','fri'=>'Fri','sat'=>'Sat','sun'=>'Sun'] as $key=>$day)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="days[]" value="{{ $key }}" 
                                {{ is_array($selected_days) && in_array($key, $selected_days) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $day }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Monthly Mode -->
            <div class="col-md-12">
                <div class="recurrence-field recurrence-monthly d-none mb-3">
                    <label class="form-label">Monthly Mode</label><br>
                    {{-- Access monthly_mode from recurrence_meta --}}
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mode" value="day" 
                            {{ old('mode', $meta['mode'] ?? 'day') == 'day' ? 'checked' : '' }}>
                        <label class="form-check-label">By Day (e.g., 5th of each month)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mode" value="weekday" 
                            {{ old('mode', $meta['mode'] ?? null) == 'weekday' ? 'checked' : '' }}>
                        <label class="form-check-label">By Weekday (e.g., 2nd Monday of each month)</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Lesson</button>
                
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
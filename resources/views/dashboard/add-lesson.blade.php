@extends('layouts.app')

@section('title', 'Add New Lesson')

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
                     <h4 class="mb-0">Create Lesson</h4>
                </div>
            </div>
            <div class="col-md-12">
                <ul class="breadcrumb">
                <li class="breadcrumb-item"
                    ><a href="{{ route( auth()->user()->user_type .'.dashboard') }}"><i class="ph ph-house"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">New Lesson</li>
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
        <form action="{{ route('lesson.store') }}" method="POST">
            @csrf

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

                    <select name="subject" class="form-select @error('subject') is-invalid @enderror" required>
                        <option value="">Select Subject</option>
                        <option value="Algebra 1" {{ old('subject') == 'Algebra 1' ? 'selected' : '' }}>Algebra 1</option>
                        <option value="Algebra 2" {{ old('subject') == 'Algebra 2' ? 'selected' : '' }}>Algebra 2</option>
                        <option value="Geometry" {{ old('subject') == 'Geometry' ? 'selected' : '' }}>Geometry</option>
                        <option value="Pre-Calculus" {{ old('subject') == 'Pre-Calculus' ? 'selected' : '' }}>Pre-Calculus</option>
                        <option value="Mathematics" {{ old('subject') == 'Mathematics' ? 'selected' : '' }}>Mathematics</option>
                        <option value="Science" {{ old('subject') == 'Science' ? 'selected' : '' }}>Science</option>
                        <option value="Physics" {{ old('subject') == 'Physics' ? 'selected' : '' }}>Physics</option>
                        <option value="Chemistry" {{ old('subject') == 'Chemistry' ? 'selected' : '' }}>Chemistry</option>
                        <option value="Biology" {{ old('subject') == 'Biology' ? 'selected' : '' }}>Biology</option>
                        <option value="Basic Science" {{ old('subject') == 'Basic Science' ? 'selected' : '' }}>Basic Science</option>
                        <option value="Basic Technology" {{ old('subject') == 'Basic Technology' ? 'selected' : '' }}>Basic Technology</option>
                        <option value="English" {{ old('subject') == 'English' ? 'selected' : '' }}>English</option>
                        <option value="French" {{ old('subject') == 'French' ? 'selected' : '' }}>French</option>                   
                        <option value="English Literature" {{ old('subject') == 'English Literature' ? 'selected' : '' }}>English Literature</option>
                        <option value="Reading" {{ old('subject') == 'Reading' ? 'selected' : '' }}>Reading</option>
                        <option value="Music" {{ old('subject') == 'Music' ? 'selected' : '' }}>Music</option>
                        <option value="Yoruba" {{ old('subject') == 'Yoruba' ? 'selected' : '' }}>Yoruba</option>
                        <option value="Igbo" {{ old('subject') == 'Igbo' ? 'selected' : '' }}>Igbo</option>
                        <option value="Spanish" {{ old('subject') == 'Spanish' ? 'selected' : '' }}>Spanish</option>
                        <option value="Economics" {{ old('subject') == 'Economics' ? 'selected' : '' }}>Economics</option>
                        <option value="Social studies" {{ old('subject') == 'Social studies' ? 'selected' : '' }}>Social studies</option>
                        <option value="Business studies" {{ old('subject') == 'Business studies' ? 'selected' : '' }}>Business studies</option>
                        <option value="Agricultural science" {{ old('subject') == 'Agricultural science' ? 'selected' : '' }}>Agricultural science</option>
                        <option value="Verbal reasoning" {{ old('subject') == 'Verbal reasoning' ? 'selected' : '' }}>Verbal reasoning</option>
                        <option value="Non-Verbal reasoning" {{ old('subject') == 'Non-Verbal reasoning' ? 'selected' : '' }}>Non-Verbal reasoning</option>
                        <option value="Quantitative reasoning" {{ old('subject') == 'Quantitative reasoning' ? 'selected' : '' }}>Quantitative reasoning</option>
                        <option value="Home Economics" {{ old('subject') == 'Home Economics' ? 'selected' : '' }}>Home Economics</option>
                        <option value="Civic Education" {{ old('subject') == 'Civic Education' ? 'selected' : '' }}>Civic Education</option>
                    </select>
                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                @hasrole('admin')
                   <!-- Instructor -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Instructor</label>
                        <select name="instructor_id" class="form-select @error('instructor_id') is-invalid @enderror" required>
                        @foreach($instructors as $instructor)
                            {{-- Repopulate instructor selection --}}
                            <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>{{ $instructor->user->name }}</option>
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
                        {{-- Repopulate student selection --}}
                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->user->name }}</option>
                    @endforeach
                    </select>
                    @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <!-- Start time -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Time (Africa/Lagos - UTC+1)</label>
                    {{-- All lessons are created in Africa/Lagos time for instructors in Nigeria --}}
                    <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                    <small class="text-muted">Enter class time in your local timezone (Africa/Lagos)</small>
                    @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Duration -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Duration (minutes)</label>
                    {{-- Repopulate duration field, default to 60 --}}
                    <input type="number" name="duration_minutes" class="form-control" min="15" value="{{ old('duration_minutes', 60) }}" required>
                </div>
                <!-- Recurrence Type -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Recurrence</label>
                    <select name="recurrence_type" id="recurrence_type" class="form-select">
                        {{-- Repopulate recurrence type, default to none --}}
                        <option value="none" {{ old('recurrence_type', 'none') == 'none' ? 'selected' : '' }}>None (One-time)</option>
                        <option value="daily" {{ old('recurrence_type') == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ old('recurrence_type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ old('recurrence_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>
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
                <div class="recurrence-field recurrence-weekly d-none mb-3">
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
                <div class="recurrence-field recurrence-monthly d-none mb-3">
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

            <button type="submit" class="btn btn-primary">Create Lesson</button>
                
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
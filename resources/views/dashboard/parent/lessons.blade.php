@extends('layouts.app')

@section('title', 'Upcoming Lessons (Children)')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
            <h4 class="mb-0">Upcoming Lessons</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('parent.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Upcoming Lessons</li>
            </ul>
        </div>
        </div>
    </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->

<div class="card shadow-sm p-3">
    <div class="card-body">
        <table class="table align-middle">
        <thead>
            <tr>
                <th>Child</th>
                <th>Subject</th>
                <th>Instructor</th>
                <th>Scheduled Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($occurrences as $occ)
                <tr>
                    <td>{{ $occ->lesson->student->user->name }}</td>
                    <td>{{ $occ->lesson->subject }}</td>
                    <td>{{ $occ->lesson->instructor->user->name }}</td>
                    <td>{{ $occ->scheduled_start->format('d M Y h:i A') }}</td>
                    <td>
                        <!-- Trigger Modal -->
                        <button class="btn btn-sm btn-outline-primary" 
                                data-bs-toggle="modal"
                                data-bs-target="#rescheduleModal"
                                data-occurrence-id="{{ $occ->id }}"
                                data-subject="{{ $occ->lesson->subject }}"
                                data-time="{{ $occ->scheduled_start->format('d M Y h:i A') }}">
                            Request Reschedule
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No upcoming lessons found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $occurrences->links() }}
    </div>
    </div>
</div>


<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="rescheduleForm">
        @csrf
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="modal-header">
          <h5 class="modal-title" id="rescheduleModalLabel">Request Reschedule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <p><strong>Lesson:</strong> <span id="lessonSubject"></span></p>
          <p><strong>Scheduled:</strong> <span id="lessonTime"></span></p>

          <div class="mb-3">
              <label for="proposed_start" class="form-label">Proposed New Time</label>
              <input type="datetime-local" name="proposed_start" id="proposed_start" class="form-control" required>
          </div>

          <div class="mb-3">
              <label for="reason" class="form-label">Reason</label>
              <textarea name="reason" id="reason" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit Request</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection


<script>
document.addEventListener("DOMContentLoaded", function () {
    var rescheduleModal = document.getElementById('rescheduleModal');
    rescheduleModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var occurrenceId = button.getAttribute('data-occurrence-id');
        var subject = button.getAttribute('data-subject');
        var time = button.getAttribute('data-time');

        // Fill in modal content
        document.getElementById('lessonSubject').textContent = subject;
        document.getElementById('lessonTime').textContent = time;

        // Update form action dynamically
        var form = document.getElementById('rescheduleForm');
        form.action = "/reschedules/" + occurrenceId +"/request"; // route('reschedule.store', occurrenceId)
    });
});
</script>

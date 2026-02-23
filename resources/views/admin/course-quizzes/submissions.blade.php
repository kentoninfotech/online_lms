@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $course) }}">{{ $course->title }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.course-quizzes.show', [$course, $quiz]) }}">{{ $quiz->title }}</a></li>
                    <li class="breadcrumb-item active">Student Results</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1">{{ $quiz->title }} - Student Results</h1>
            <p class="text-muted">Manage and review student quiz submissions</p>
        </div>
        <div class="btn-group" role="group">
            <input type="radio" class="btn-check" name="filterStatus" id="filterAll" value="all" checked>
            <label class="btn btn-outline-secondary" for="filterAll">All</label>

            <input type="radio" class="btn-check" name="filterStatus" id="filterPassed" value="passed">
            <label class="btn btn-outline-success" for="filterPassed">Passed</label>

            <input type="radio" class="btn-check" name="filterStatus" id="filterFailed" value="failed">
            <label class="btn btn-outline-danger" for="filterFailed">Failed</label>

            <input type="radio" class="btn-check" name="filterStatus" id="filterPending" value="pending">
            <label class="btn btn-outline-warning" for="filterPending">Pending Review</label>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Submissions</h6>
                    <h3>{{ $submissions->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Passed</h6>
                    <h3 class="text-success">{{ $submissions->where('is_passed', true)->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Failed</h6>
                    <h3 class="text-danger">{{ $submissions->where('is_passed', false)->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Pending Review</h6>
                    <h3 class="text-warning">{{ $submissions->whereNull('reviewed_at')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0" id="submissionsTable">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th>Student</th>
                        <th>Score</th>
                        <th>Correct</th>
                        <th>Attempt</th>
                        <th>Time Taken</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Review Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($submissions as $submission)
                    <tr class="align-middle submission-row" data-status="{{ $submission->is_passed ? 'passed' : 'failed' }}" data-reviewed="{{ $submission->reviewed_at ? 'yes' : 'no' }}">
                        <td>
                            <div>
                                <h6 class="mb-0">{{ $submission->courseEnrollee->user->name }}</h6>
                                <small class="text-muted">{{ $submission->courseEnrollee->user->email }}</small>
                            </div>
                        </td>
                        <td>
                            <h5 class="mb-0">
                                <span style="color: {{ $submission->is_passed ? '#28a745' : '#dc3545' }};">
                                    {{ $submission->score }}%
                                </span>
                            </h5>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $submission->correct_answers }}/{{ $submission->total_questions }}</span>
                        </td>
                        <td>
                            #{{ $submission->attempt_number }} of {{ $quiz->attempts_allowed }}
                        </td>
                        <td>
                            {{ $submission->time_taken_minutes }} min
                        </td>
                        <td>
                            <small>{{ $submission->created_at->format('M d, Y') }}</small>
                            <br>
                            <small class="text-muted">{{ $submission->created_at->format('g:i A') }}</small>
                        </td>
                        <td>
                            @if($submission->is_passed)
                            <span class="badge bg-success">PASSED ✓</span>
                            @else
                            <span class="badge bg-danger">FAILED ✗</span>
                            @endif
                        </td>
                        <td>
                            @if($submission->reviewed_at)
                            <span class="badge bg-info">
                                <i class="bi bi-check-circle"></i> Reviewed
                            </span>
                            <br>
                            <small class="text-muted">{{ $submission->reviewed_at->format('M d, Y') }}</small>
                            @else
                            <span class="badge bg-secondary">
                                <i class="bi bi-clock"></i> Pending
                            </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.quiz.view-submission', [$course, $quiz, $submission]) }}" class="btn btn-sm btn-primary">
                                Review
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5;"></i>
                            <p class="mt-2">No submissions yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.submission-row {
    transition: background-color 0.3s ease;
}
.submission-row:hover {
    background-color: rgba(0, 0, 0, 0.02);
}
</style>

<script>
document.querySelectorAll('input[name="filterStatus"]').forEach(radio => {
    radio.addEventListener('change', function() {
        filterSubmissions(this.value);
    });
});

function filterSubmissions(status) {
    const rows = document.querySelectorAll('.submission-row');
    let visibleCount = 0;

    rows.forEach(row => {
        if (status === 'all') {
            row.style.display = '';
            visibleCount++;
        } else if (status === 'passed') {
            row.style.display = row.dataset.status === 'passed' ? '' : 'none';
            if (row.dataset.status === 'passed') visibleCount++;
        } else if (status === 'failed') {
            row.style.display = row.dataset.status === 'failed' ? '' : 'none';
            if (row.dataset.status === 'failed') visibleCount++;
        } else if (status === 'pending') {
            row.style.display = row.dataset.reviewed === 'no' ? '' : 'none';
            if (row.dataset.reviewed === 'no') visibleCount++;
        }
    });

    if (visibleCount === 0) {
        let emptyMessage = 'No submissions match the selected filter.';
        if (!document.querySelector('.empty-message')) {
            const tbody = document.querySelector('#tableBody');
            const tr = document.createElement('tr');
            tr.className = 'empty-message';
            tr.innerHTML = `<td colspan="9" class="text-center py-4 text-muted">${emptyMessage}</td>`;
            tbody.appendChild(tr);
        }
    } else {
        const emptyMsg = document.querySelector('.empty-message');
        if (emptyMsg) emptyMsg.remove();
    }
}
</script>
@endsection

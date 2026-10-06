@extends('layouts.app')

@section('title', 'Site Visitors')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center mb-3">
        <div class="col">
            <h3 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Site Visitors</h3>
            <small class="text-muted">Monitor visitor traffic by location, country, and visit time.</small>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10 h-100">
                <div class="card-body">
                    <small class="text-primary fw-semibold">Total Visits</small>
                    <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10 h-100">
                <div class="card-body">
                    <small class="text-success fw-semibold">Today</small>
                    <h3 class="mb-0">{{ number_format($stats['today']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10 h-100">
                <div class="card-body">
                    <small class="text-warning fw-semibold">This Week</small>
                    <h3 class="mb-0">{{ number_format($stats['this_week']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info bg-opacity-10 h-100">
                <div class="card-body">
                    <small class="text-info fw-semibold">Countries</small>
                    <h3 class="mb-0">{{ number_format($stats['unique_countries']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.visitors.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="country" class="form-label">Country</label>
                    <select id="country" name="country" class="form-select">
                        <option value="">All Countries</option>
                        @foreach($availableCountries as $country)
                            <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>
                                {{ $country }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="from" class="form-label">From</label>
                    <input type="date" id="from" name="from" class="form-control" value="{{ request('from') }}">
                </div>
                <div class="col-md-3">
                    <label for="to" class="form-label">To</label>
                    <input type="date" id="to" name="to" class="form-control" value="{{ request('to') }}">
                </div>
                <div class="col-md-2 d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.visitors.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Top Countries</h6>
        </div>
        <div class="card-body">
            @if($topCountries->isEmpty())
                <p class="text-muted mb-0">No country data available yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th class="text-end">Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCountries as $country)
                                <tr>
                                    <td>{{ $country->country }}</td>
                                    <td class="text-end">{{ number_format($country->total_visits) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center gap-2">
            <h6 class="mb-0">Recent Visitor Activity</h6>
            <button type="submit" form="bulkDeleteVisitorsForm" id="bulkDeleteBtn" class="btn btn-sm btn-danger" disabled>
                Delete Selected
            </button>
        </div>
        <form id="bulkDeleteVisitorsForm" method="POST" action="{{ route('admin.visitors.bulk-delete') }}" onsubmit="return confirm('Delete selected visitor records? This action cannot be undone.');">
            @csrf
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAllVisitors" aria-label="Select all visitor records">
                        </th>
                        <th>Datetime</th>
                        <th>Country</th>
                        <th>Location</th>
                        <th>IP Address</th>
                        <th>Visitor</th>
                        <th>Visited URL</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitors as $visitor)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    class="visitor-checkbox"
                                    name="visitor_ids[]"
                                    value="{{ $visitor->id }}"
                                    aria-label="Select visitor {{ $visitor->id }}"
                                >
                            </td>
                            <td>{{ $visitor->visited_at?->format('M d, Y h:i A') ?? '-' }}</td>
                            <td>{{ $visitor->country ?? 'Unknown' }}</td>
                            <td>{{ $visitor->location ?? 'Unknown' }}</td>
                            <td><code>{{ $visitor->ip_address ?? '-' }}</code></td>
                            <td>{{ $visitor->user?->email ?? 'Guest' }}</td>
                            <td>
                                <a href="{{ $visitor->visited_url }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                    {{ \Illuminate\Support\Str::limit($visitor->visited_url, 80) }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No visitor records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </form>
        @if($visitors->hasPages())
            <div class="card-footer">
                {{ $visitors->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    (function () {
        const selectAllVisitors = document.getElementById('selectAllVisitors');
        const visitorCheckboxes = Array.from(document.querySelectorAll('.visitor-checkbox'));
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

        if (!selectAllVisitors || !bulkDeleteBtn || visitorCheckboxes.length === 0) {
            return;
        }

        const updateBulkDeleteState = function () {
            const selectedCount = visitorCheckboxes.filter(function (checkbox) {
                return checkbox.checked;
            }).length;

            bulkDeleteBtn.disabled = selectedCount === 0;
            bulkDeleteBtn.textContent = selectedCount > 0
                ? 'Delete Selected (' + selectedCount + ')'
                : 'Delete Selected';

            const allChecked = selectedCount === visitorCheckboxes.length;
            selectAllVisitors.checked = allChecked;
            selectAllVisitors.indeterminate = selectedCount > 0 && !allChecked;
        };

        selectAllVisitors.addEventListener('change', function () {
            visitorCheckboxes.forEach(function (checkbox) {
                checkbox.checked = selectAllVisitors.checked;
            });
            updateBulkDeleteState();
        });

        visitorCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateBulkDeleteState);
        });

        updateBulkDeleteState();
    })();
</script>
@endsection

@extends('layouts.app')

@section('title', 'Import Courses')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Import Courses</h3>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Courses
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Import Courses from CSV/Excel</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="bi bi-exclamation-circle me-2"></i>Import Failed
                            </h5>
                            <p class="mb-3">The following errors were encountered during the import:</p>
                            <div style="max-height: 400px; overflow-y: auto;">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li class="mb-2">
                                            <code class="bg-light px-2 py-1 rounded">{{ $error }}</code>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <hr class="my-3">
                            <small class="text-muted d-block">
                                <strong>💡 Tips:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Check that column headers match exactly (case-sensitive)</li>
                                    <li>For "With Dates & Venues" format, dates and venues are separated by line breaks</li>
                                    <li>Multiple venues on the same line should be comma-separated</li>
                                    <li>Ensure all required columns are present: CODE, TITLE, DATE, VENUE, FEE</li>
                                    <li>Course codes must be unique (not already in database)</li>
                                </ul>
                            </small>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.courses.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Course Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="" selected disabled>-- Select a category --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">All imported courses will be assigned to this category</small>
                            @error('category_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">CSV Format <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input format-radio" type="radio" name="csv_format" id="format_standard" value="standard" {{ old('csv_format', 'standard') === 'standard' ? 'checked' : '' }}>
                                <label class="form-check-label" for="format_standard">
                                    <strong>Standard Format</strong> 
                                    <span class="badge bg-secondary ms-2">Recommended</span>
                                </label>
                                <small class="d-block mt-1 ms-4 text-muted">
                                    For basic course data: code, title, subtitle, description, facilitator_id, fee, currency, course_hours, is_online, is_offline, is_featured, is_active, max_enrollees
                                </small>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input format-radio" type="radio" name="csv_format" id="format_dates_venues" value="dates_venues" {{ old('csv_format') === 'dates_venues' ? 'checked' : '' }}>
                                <label class="form-check-label" for="format_dates_venues">
                                    <strong>With Dates & Venues</strong>
                                    <span class="badge bg-info ms-2">Advanced</span>
                                </label>
                                <small class="d-block mt-1 ms-4 text-muted">
                                    For courses with multiple scheduling dates and venues: code, title, date, venue, fee
                                </small>
                            </div>
                            @error('csv_format')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="file" class="form-label">CSV or Excel File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".csv,.xlsx,.xls" required>
                            <small class="form-text text-muted">Upload a CSV or Excel (.xlsx, .xls) file with course data</small>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="format-info-standard" class="alert alert-info" style="display: {{ old('csv_format', 'standard') === 'standard' ? 'block' : 'none' }};">
                            <strong><i class="bi bi-info-circle me-2"></i>Standard Format Columns:</strong><br>
                            <small class="mt-2 d-block">Your file can contain any combination of these columns. Missing columns will default to null:</small>
                            <code style="font-size: 0.85rem;" class="mt-2 d-block">
                                code, title, subtitle, description, facilitator_id, fee, currency, course_hours, is_online, is_offline, is_featured, is_active, max_enrollees
                            </code>
                            <br>
                            <small style="margin-top: 0.5rem; display: block;">
                                <strong>Note:</strong> Column order doesn't matter. First row should contain headers. Code and title are required.
                            </small>
                        </div>

                        <div id="format-info-dates-venues" class="alert alert-info" style="display: {{ old('csv_format') === 'dates_venues' ? 'block' : 'none' }};">
                            <strong><i class="bi bi-info-circle me-2"></i>Dates & Venues Format Columns:</strong><br>
                            <small class="mt-2 d-block"><strong>Required columns:</strong></small>
                            <code style="font-size: 0.85rem;" class="mt-2 d-block">
                                code, title, date, venue, fee
                            </code>
                            <small style="margin-top: 0.5rem; display: block;">
                                <strong>DATE format:</strong> Each row in the date column can contain multiple dates separated by newlines (line breaks)
                            </small>
                            <small style="margin-top: 0.5rem; display: block;">
                                <strong>VENUE format:</strong> Each row in the venue column contains venues matching each date (comma-separated within a line, lines separated by newlines)
                            </small>
                            <small style="margin-top: 0.5rem; display: block;">
                                Example: If DATE has 4 lines, VENUE should also have 4 lines with venues for each date.
                            </small>
                        </div>

                        <div class="form-footer">
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-2"></i>Import Courses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Available Categories</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td><code>{{ $category->id }}</code></td>
                                    <td>{{ $category->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Sample File Format</h5>
                </div>
                <div class="card-body">
                    <!-- Standard Format Sample -->
                    <div id="sample-standard" style="display: {{ old('csv_format', 'standard') === 'standard' ? 'block' : 'none' }};">
                        <p><small><strong>Standard Format Example:</strong> Copy these headers to your CSV file</small></p>
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>code</th>
                                    <th>title</th>
                                    <th>fee</th>
                                    <th>currency</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><small>CRS001</small></td>
                                    <td><small>Web Development</small></td>
                                    <td><small>50000</small></td>
                                    <td><small>NGN</small></td>
                                </tr>
                                <tr>
                                    <td><small>CRS002</small></td>
                                    <td><small>Database Design</small></td>
                                    <td><small>45000</small></td>
                                    <td><small>NGN</small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Dates & Venues Format Sample -->
                    <div id="sample-dates-venues" style="display: {{ old('csv_format') === 'dates_venues' ? 'block' : 'none' }};">
                        <p><small><strong>Dates & Venues Format Example:</strong></small></p>
                        <div class="alert alert-light border p-2 mb-2" style="font-family: monospace; white-space: pre-wrap; font-size: 0.75rem; overflow-x: auto;">code,title,date,venue,fee
CRS001,Web Dev,"Jan 15, 2025
Feb 20, 2025
Mar 25, 2025","Lagos Tech Hub
Abuja Office
Port Harcourt Center",50000
CRS002,Database,"Feb 10, 2025
Mar 15, 2025","Lekki Campus
Victoria Island",45000</div>
                        <small class="text-muted d-block">
                            <strong>Note:</strong> Dates and venues must be separated by line breaks (Enter key), not commas. Multiple venues on one line are comma-separated.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formatRadios = document.querySelectorAll('.format-radio');
    const standardInfo = document.getElementById('format-info-standard');
    const datesVenuesInfo = document.getElementById('format-info-dates-venues');
    const standardSample = document.getElementById('sample-standard');
    const dateVenuesSample = document.getElementById('sample-dates-venues');

    function updateFormatDisplay() {
        const selected = document.querySelector('.format-radio:checked').value;
        if (selected === 'standard') {
            standardInfo.style.display = 'block';
            datesVenuesInfo.style.display = 'none';
            standardSample.style.display = 'block';
            dateVenuesSample.style.display = 'none';
        } else {
            standardInfo.style.display = 'none';
            datesVenuesInfo.style.display = 'block';
            standardSample.style.display = 'none';
            dateVenuesSample.style.display = 'block';
        }
    }

    formatRadios.forEach(radio => {
        radio.addEventListener('change', updateFormatDisplay);
    });

    // Initialize on page load
    updateFormatDisplay();
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'Manage Courses')

@section('content')

<style>
    .dataTables_wrapper {
        padding: 0;
    }

    .dataTables_filter {
        display: none;
    }

    .dataTables_length {
        display: none;
    }

    .column-search {
        background-color: #f8f9fa;
        padding: 1rem;
        border-bottom: 2px solid #dee2e6;
    }

    .column-search-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        font-size: 0.9rem;
        transition: border-color 0.2s ease;
    }

    .column-search-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .column-search-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #666;
        margin-bottom: 0.25rem;
        letter-spacing: 0.3px;
    }

    .dataTables_paginate {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .dataTables_info {
        padding-top: 1rem;
        padding-bottom: 1rem;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 8px;
    }

    .page-header h3 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 700;
    }

    .page-title {
        color: white;
    }

    .card {
        border: none;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border-radius: 12px;
    }

    .card-header {
        background-color: transparent;
        border-bottom: 2px solid #dee2e6;
        padding: 1.5rem;
    }

    .card-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
    }

    .table thead th {
        font-weight: 600;
        color: #333;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.3px;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .btn-group-sm .btn {
        padding: 0.35rem 0.5rem;
        font-size: 0.8rem;
    }
</style>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="container-fluid">
    <div class="page-header mb-4 shadow-sm">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">
                    <i class="bi bi-book me-2"></i>Manage Courses
                </h3>
            </div>
            <div class="col-auto">
                @php
                    $createRoute = auth()->user()->user_type === 'instructor'
                        ? route('tutor.courses.create')
                        : route('admin.courses.create');
                @endphp
                <a href="{{ $createRoute }}" class="btn btn-light text-white" style="background: rgba(255,255,255,0.2);">
                    <i class="bi bi-plus-circle me-2"></i>Create Course
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">All Courses</h5>
                            <p class="text-muted mb-0">Total: <strong>{{ $courses->total() }}</strong> courses</p>
                        </div>
                    </div>
                </div>

                <!-- Column Search Filters -->
                <div class="column-search">
                    <div class="row g-3">
                        <div class="col-lg-2">
                            <label class="column-search-label">Course Code</label>
                            <input type="text" id="column-code-search" class="column-search-input" placeholder="Search code...">
                        </div>
                        <div class="col-lg-2">
                            <label class="column-search-label">Title</label>
                            <input type="text" id="column-title-search" class="column-search-input" placeholder="Search title...">
                        </div>
                        <div class="col-lg-2">
                            <label class="column-search-label">Category</label>
                            <input type="text" id="column-category-search" class="column-search-input" placeholder="Search category...">
                        </div>
                        <div class="col-lg-2">
                            <label class="column-search-label">Facilitator</label>
                            <input type="text" id="column-facilitator-search" class="column-search-input" placeholder="Search facilitator...">
                        </div>
                        <div class="col-lg-2">
                            <label class="column-search-label">Fee</label>
                            <input type="text" id="column-fee-search" class="column-search-input" placeholder="Search fee...">
                        </div>
                        <div class="col-lg-2">
                            <label class="column-search-label">Status</label>
                            <input type="text" id="column-status-search" class="column-search-input" placeholder="Active/Inactive...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="courses-table" class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Facilitator</th>
                                <th>Content Count</th>
                                <th>Fee</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($courses as $course)
                                <tr>
                                    <td>
                                        <strong>{{ $course->code }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $showRoute = auth()->user()->user_type === 'instructor'
                                                ? route('tutor.courses.show', $course)
                                                : route('admin.courses.show', $course);
                                        @endphp
                                        <a href="{{ $showRoute }}" class="text-decoration-none">
                                            {{ Str::limit($course->title, 40) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $course->category?->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        {{ $course->facilitator?->name ?? 'Unassigned' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $course->contents()->count() }} contents</span>
                                    </td>
                                    <td>
                                        {{ $course->currency }} {{ number_format($course->fee, 2) }}
                                    </td>
                                    <td>
                                        @if($course->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $isInstructor = auth()->user()->user_type === 'instructor';
                                            $showRoute = $isInstructor ? route('tutor.courses.show', $course) : route('admin.courses.show', $course);
                                            $contentsRoute = $isInstructor ? route('tutor.course-contents.index', $course) : route('admin.course-contents.index', $course);
                                            $editRoute = $isInstructor ? route('tutor.courses.edit', $course) : route('admin.courses.edit', $course);
                                            $destroyRoute = $isInstructor ? route('tutor.courses.destroy', $course) : route('admin.courses.destroy', $course);
                                        @endphp
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ $showRoute }}" class="btn btn-outline-info" title="View" data-bs-toggle="tooltip">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ $contentsRoute }}" class="btn btn-outline-secondary" title="View Contents" data-bs-toggle="tooltip">
                                                <i class="bi bi-collection"></i>
                                            </a>
                                            <a href="{{ $editRoute }}" class="btn btn-outline-primary" title="Edit" data-bs-toggle="tooltip">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ $destroyRoute }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                        @php
                                            $createRoute = auth()->user()->user_type === 'instructor'
                                                ? route('tutor.courses.create')
                                                : route('admin.courses.create');
                                        @endphp
                                        <p class="mt-2">No courses found. <a href="{{ $createRoute }}">Create one now</a>.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#courses-table').DataTable({
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,
            paging: true,
            dom: '<"row"<"col-sm-12"i>><"row"<"col-sm-12"t>><"row"<"col-sm-12"p>>',
            language: {
                paginate: {
                    first: '«',
                    last: '»',
                    next: '›',
                    previous: '‹'
                },
                info: 'Showing _START_ to _END_ of _TOTAL_ courses',
                emptyTable: 'No courses found'
            }
        });

        // Column search - Course Code
        $('#column-code-search').on('keyup', function() {
            table.column(0).search(this.value).draw();
        });

        // Column search - Title
        $('#column-title-search').on('keyup', function() {
            table.column(1).search(this.value).draw();
        });

        // Column search - Category
        $('#column-category-search').on('keyup', function() {
            table.column(2).search(this.value).draw();
        });

        // Column search - Facilitator
        $('#column-facilitator-search').on('keyup', function() {
            table.column(3).search(this.value).draw();
        });

        // Column search - Fee
        $('#column-fee-search').on('keyup', function() {
            table.column(5).search(this.value).draw();
        });

        // Column search - Status
        $('#column-status-search').on('keyup', function() {
            table.column(6).search(this.value).draw();
        });
    });
</script>

@endsection

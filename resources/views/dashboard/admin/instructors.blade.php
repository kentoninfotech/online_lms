@extends('layouts.app')

@section('title', 'Instructors')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
                <a href="{{ route('admin.users.create', 'instructor') }}" class="btn btn-sm btn-primary float-end">
                    <i class="ph ph-plus"></i> Add Instructor
                </a>
                <h4 class="mb-0">Instructors</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('admin.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Instructors</li>
            </ul>
        </div>
        </div>
    </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->

<!-- Students -->
<div class="card">
    <div class="card-body">
        <table class="table table-striped table-bordered">
        <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Total Lessons</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($instructors as $instructor)
                <tr>
                    <td>{{ $instructor->name }}</td>
                    <td>{{ $instructor->email }}</td>
                    <td>{{ $instructor->lessons_count }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                                    <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                                </svg>
                            </button>
                            <ul class="dropdown-menu text-center">
                                <li>
                                    <a class="dropdown-item" href="{{ route('show.instructor', $instructor) }}"><i class="ph ph-user"></i> View</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.edit', ['user' => $instructor->user, 'role' => 'instructor']) }}"><i class="ph ph-user-circle"></i> Edit</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('show.instructor', $instructor) }}#lessons"><i class="ph ph-book"></i> View Lessons</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('show.instructor', $instructor) }}#attendance"><i class="ph ph-check-square"></i> View Attendance</a>
                                </li>
                                <li>
                                    <form class="d-inline" action="{{ route('admin.users.delete', $instructor->user }}" method="post">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this instructor, and all its records?');">
                                           <i class="ph ph-trash"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">No instructors found.</td></tr>
            @endforelse
        </tbody>
    </table>
    
    </div>
    <div class="mt-3">
        {{ $instructors->links() }}
    </div>
</div>
        
 


@endsection
@extends('layouts.app')

@section('title', 'Students')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
                <a href="{{ route('admin.users.create', 'student') }}" class="btn btn-sm btn-primary float-end">
                    <i class="ph ph-plus"></i> Add Student
                </a>
                <h4 class="mb-0">Students</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('admin.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Students</li>
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
        <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Subscription</th>
                <th>Plan</th>
                <th>Parents</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>
                        @if($student->subscription)
                            <span class="badge bg-{{ $student->subscription?->status === 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($student->subscription?->status) }}
                            </span>
                        @else
                            <span class="badge bg-secondary">None</span>
                        @endif
                    </td>
                    <td>{{ $student->subscription?->plan?->name ?? '-' }}</td>
                    <td>
                        @foreach($student->parents as $parent)
                          <a href="{{ route('show.parent', $parent) }}" class="btn btn-sm btn-outline-primary mb-1">
                              {{ $parent->name }}
                          </a>
                        @endforeach
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                                    <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                                </svg>
                            </button>
                            <ul class="dropdown-menu text-center">
                                <li>
                                    <a class="dropdown-item" href="{{ route('show.student', $student) }}"><i class="ph ph-user"></i> View Student</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.edit', ['user' => $student->user, 'role' => 'student']) }}"><i class="ph ph-user-circle"></i> Edit</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('show.student', $student) }}#lessons"><i class="ph ph-book"></i> View Lessons</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('show.student', $student) }}#attendance"><i class="ph ph-check-square"></i> View Attendance</a>
                                </li>
                                <li>
                                    <!-- <div class="dropdown-divider"></div> -->
                                    <form class="d-inline" action="{{ '#' }}" method="post">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this student?');">
                                          <i class="ph ph-trash"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    </div>
    <div class="mt-3">
        {{ $students->links() }}
    </div>
</div>
        
 


@endsection
@extends('layouts.app')

@section('title', 'My Students')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
            <h4 class="mb-0">My Students</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('instructor.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">My Students</li>
            </ul>
        </div>
        </div>
    </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->

<!-- Students -->
<div class="card mt-3">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Subscription</th>
                    <th>Plan</th>
                    <th>Total Classes</th>
                    <th>Attendance %</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($students as $student)
                <tr>
                    <td>
                        <img src="{{ $student['profile'] }}" width="30" class="rounded-circle me-2">
                        {{ $student['name'] }}
                    </td>
                    <td>{{ $student['email'] }}</td>
                    <td>
                        <span class="badge 
                            @if($student['subscription'] === 'active') bg-success 
                            @elseif($student['subscription'] === 'pending') bg-warning
                            @else bg-danger @endif">
                            {{ ucfirst($student['subscription']) }}
                        </span>
                    </td>
                    <td>{{ $student['plan'] }}</td>
                    <td>{{ $student['total_classes'] }}</td>
                    <td>{{ $student['attendance_percent'] }}%</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                                    <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                                </svg>
                            </button>
                            <ul class="dropdown-menu text-center">
                                <li>
                                    <a class="dropdown-item" href="{{ route('show.student', $student['id']) }}"><i class="ph ph-user"></i> View Student</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('show.student', $student['id']) }}#lessons"><i class="ph ph-book"></i> View Lessons</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('show.student', $student['id']) }}#attendance"><i class="ph ph-check-square"></i> View Attendance</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">No students found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{-- $students->links() --}}
    </div>
</div>
        
 


@endsection
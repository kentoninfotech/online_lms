@extends('layouts.app')

@section('title', 'Parents')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
                <a href="{{ route('admin.users.create', 'parent') }}" class="btn btn-sm btn-primary float-end">
                    <i class="ph ph-plus"></i> Add Parent
                </a>
                <h4 class="mb-0">Parents</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('admin.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">Parents</li>
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
                <th>Children</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($parents as $parent)
                <tr>
                    <td>{{ $parent->name }}</td>
                    <td>{{ $parent->email }}</td>
                    <td>
                        @foreach($parent->students as $student)
                        <a href="{{ route('show.student', $student) }}">
                            <span class="badge bg-primary">
                                {{ $student->name }}
                            </span>
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
                                    <a class="dropdown-item" href="{{ route('show.parent', $parent) }}"><i class="ph ph-user"></i> View Parent</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.edit', ['user' => $parent->user, 'role' => 'parent']) }}"><i class="ph ph-user-circle"></i>Edit</a>
                                </li>
                                <li>
                                    <form class="d-inline" action="{{ '#' }}" method="post">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this parent?');">
                                          <i class="ph ph-trash"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">No parents found.</td></tr>
            @endforelse
        </tbody>
    </table>
    
    </div>
    <div class="mt-3">
        {{ $parents->links() }}
    </div>
</div>
        
 


@endsection
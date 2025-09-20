@extends('layouts.app')

@section('title', 'My Lessons')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
            <h4 class="mb-0">Sample Page</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('instructor.dashboard') }}"><i class="ph ph-house"></i></a
            ></li>
            <li class="breadcrumb-item" aria-current="page">Sample Page</li>
            </ul>
        </div>
        </div>
    </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- [ sample-page ] start -->
    <div class="col-sm-12">
    <div class="card">
        <div class="card-header">
        <h5>Hello card</h5>
        </div>
        <div class="card-body"> </div>
    </div>
    </div>
    <!-- [ sample-page ] end -->
</div>
<!-- [ Main Content ] end -->

@endsection


<div class="dropdown">
    <button type="button" class="btn btn-secondary" data-toggle="dropdown">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16"><path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/></svg>
    </button>
    <div class="dropdown-menu text-center">

        <a class="dropdown-item" href="{{ route('show.personnel', $user->id) }}"><i class="fa fa-eye"></i> View</a>
        <a class="dropdown-item" href="{{ route('edit.personnel', $user->id) }}"><i class="fa fa-edit"></i> Edit</a>
        @can('delete user')
            <div class="dropdown-divider"></div>
            <form class="d-inline" action="{{ '#' }}" method="post">
                @csrf
                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this personnel?');">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/></svg> 
                Delete
                </button>
            </form>
        @endcan
    </div>
</div>

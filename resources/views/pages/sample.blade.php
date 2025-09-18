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
                ><a href="../dashboard/index.html"><i class="ph ph-house"></i></a
            ></li>
            <li class="breadcrumb-item"><a href="javascript: void(0)">Other</a></li>
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
@extends('layouts.app')

@section('title', 'Parent Settings')

@section('content')

<div class="page-header">
    <div class="page-block card mb-0">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title border-bottom pb-2 mb-2">
                        <h4 class="mb-0">Settings</h4>
                    </div>
                </div>
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}"><i class="ph ph-house"></i></a></li>
                        <li class="breadcrumb-item" aria-current="page">Settings</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Parent Preferences</h5>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Timezone</label>
                    <p class="form-control-plaintext">{{ $user->timezone ?? 'UTC' }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Occupation</label>
                    <p class="form-control-plaintext">{{ $user->parent->occupation ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="ph ph-info me-2"></i>
            To update your settings, please visit your <a href="{{ route('my.profile') }}">profile page</a>.
        </div>
    </div>
</div>

@endsection

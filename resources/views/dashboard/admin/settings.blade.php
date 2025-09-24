@extends('layouts.app')

@section('title', 'System Settings')

@section('content')

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block card mb-0">
    <div class="card-body">
        <div class="row align-items-center">
        <div class="col-md-12">
            <div class="page-header-title border-bottom pb-2 mb-2">
            <h4 class="mb-0">System Settings</h4>
            </div>
        </div>
        <div class="col-md-12">
            <ul class="breadcrumb">
            <li class="breadcrumb-item"
                ><a href="{{ route('admin.dashboard') }}"><i class="ph ph-house"></i></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">System Settings</li>
            </ul>
        </div>
        </div>
    </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->

 @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<!-- Students -->


<div class="card">
    <div class="card-header">
        <p class="text-muted mb-4">
            Manage business rules and system-wide configurations below. 
            Changes apply immediately after saving.
        </p>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            @method('PUT')

            <div class="card shadow-sm border-0">
                <div class="list-group list-group-flush">
                    @foreach($keys as $key)
                        <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                            <div class="me-3">
                                <div class="fw-semibold text-dark" style="letter-spacing:0.2px;">
                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                </div>
                                <div class="small text-muted">
                                    {{ $descriptions[$key] ?? '' }}
                                </div>
                            </div>

                            <div style="min-width:220px; max-width:420px;">
                                {{-- adapt input type by key (numbers for minutes/days/percent) --}}
                                @php
                                    $val = $settings[$key] ?? '';
                                    $numberKeys = [
                                        'reschedule_limit',
                                        'reschedule_guard_time_minutes',
                                        'attendance_grace_period_minutes',
                                        'billing_grace_period_days',
                                        'subscription_expiry_warning_days',
                                        'recurrence_horizon_days',
                                        'zoom_meeting_horizon_days',
                                        'attendance_min_percentage',
                                    ];
                                @endphp

                                @if(in_array($key, $numberKeys))
                                    <input type="number" 
                                        name="settings[{{ $key }}]" 
                                        value="{{ old('settings.' . $key, $val) }}" 
                                        class="form-control text-end" 
                                        @if($key === 'attendance_min_percentage') min="0" max="100" step="1" @endif>
                                @else
                                    <input type="text" 
                                        name="settings[{{ $key }}]" 
                                        value="{{ old('settings.' . $key, $val) }}" 
                                        class="form-control text-end">
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 text-start">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-2"></i> Save Changes
                </button>
            </div>
        </form>

    </div> 
</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    // optional: client-side niceties later (confirmations / toggles)
});
</script>
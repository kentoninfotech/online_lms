@extends('layouts.app')

@section('title', $parent->name ."'s". ' Profile')

@section('content')

<!-- [Parent Details] start -->
<div class="card shadow-sm border-0 rounded-3 overflow-hidden">
    <!-- Banner -->
    <div class="w-100" 
         style="background: linear-gradient(-180deg, #330952 0%, #f0c221 100%); height: 140px;">
    </div>

    <div class="card-body position-relative">
        <!-- Avatar & Basic Info -->
        <div class="d-flex align-items-center">
            <img src="{{ $parent->user->profile ? asset($parent->user->profile) : asset('storage/profiles/profile.png') ?? 'https://ui-avatars.com/api/?name='.$parent->name }}" 
                 alt="{{ $parent->name }}"
                 class="rounded-circle border border-3 border-white shadow"
                 style="width: 110px; height: 110px; margin-top:-80px;">

            <div class="ms-3">
                <h4 class="mb-0">{{ $parent->name }}</h4>
                <p class="text-muted mb-0">{{ $parent->email }}</p>
                <span class="badge bg-danger">Parent</span>
            </div>

            @role('admin')
               <div class="ms-auto">
                   <a href="{{ route('users.edit', ['user' => $parent->user, 'role' => 'parent']) }}" 
                       class="btn btn-sm btn-primary">Edit</a>
               </div>
            @endrole
        </div>

        <!-- Read-only Details -->
        <div class="row mt-4 g-3">
            <div class="col-md-6">
                <label class="form-label small text-muted">Full Name</label>
                <input type="text" class="form-control bg-light" value="{{ $parent->name }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted">Email</label>
                <input type="text" class="form-control bg-light" 
                       value="{{ $parent->email }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted">Phone</label>
                <input type="text" class="form-control bg-light" 
                       value="{{ $parent->number ?? 'N/A' }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted">Address</label>
                <input type="text" class="form-control bg-light" 
                       value="{{ $parent->address ?? 'N/A' }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label small text-muted">Occupation</label>
                <input type="text" class="form-control bg-light" 
                       value="{{ $parent->occupation ?? 'N/A' }}" readonly>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="mt-4 d-flex gap-4">
            <div class="text-center flex-fill p-3 bg-light rounded">
                <h5 class="mb-0">{{ $parent->students->count() ?? 0 }}</h5>
                <small class="text-muted">Children</small>
            </div>
            <div class="text-center flex-fill p-3 bg-light rounded">
                <h5 class="mb-0">{{ $paymentsCount ?? 0 }}</h5>
                <small class="text-muted">Payments</small>
            </div>
        </div>
    </div>
</div>
<!-- [Parent Details] end -->


<!-- Children -->
<div class="card">
    <div class="card-header">Children (Students)</div>
    <div class="card-body">
        @if($parent->students->isEmpty())
            <p class="text-muted">No children linked yet.</p>
        @else
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Lessons</th>
                        <th>Attendance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parent->students as $child)
                        <tr>
                            <td>{{ $child->name }}</td>
                            <td>{{ $child->user->email }}</td>
                            <td>
                                <a href="{{ route('show.student', $child->id) }}#lessons" class="btn btn-sm btn-outline-primary">View Lessons</a>
                            </td>
                            <td>
                                <a href="{{ route('show.student', $child->id) }}#attendance" class="btn btn-sm btn-outline-secondary">View Attendance</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@endsection




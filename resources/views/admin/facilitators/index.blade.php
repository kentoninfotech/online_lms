@extends('layouts.app')

@section('title', 'Manage Tutors')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Manage Tutors</h3>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.facilitators.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add Tutor
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Tutors</h5>
                    <p class="text-muted mb-0">Total: {{ $facilitators->total() }} tutors</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Courses</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($facilitators as $facilitator)
                                <tr>
                                    <td>
                                        <strong>{{ $facilitator->name }}</strong>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $facilitator->user->email }}" class="text-decoration-none">
                                            {{ $facilitator->user->email }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $facilitator->courses->count() }}</span>
                                    </td>
                                    <td>
                                        @if($facilitator->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.facilitators.edit', $facilitator) }}" class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.facilitators.destroy', $facilitator) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this tutor?');">
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
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                        <p class="mt-2">No tutors found. <a href="{{ route('admin.facilitators.create') }}">Add one now</a>.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($facilitators->hasPages())
                    <div class="card-footer">
                        {{ $facilitators->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

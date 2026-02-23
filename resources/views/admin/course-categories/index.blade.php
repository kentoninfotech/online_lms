@extends('layouts.app')

@section('title', 'Course Categories')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Course Categories</h3>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.course-categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add Category
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

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Categories</h5>
                    <p class="text-muted mb-0">Total: {{ $categories->total() }} categories</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Color</th>
                                <th>Icon</th>
                                <th>Status</th>
                                <th>Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <strong>{{ $category->name }}</strong>
                                        @if($category->description)
                                            <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge rounded-pill" style="background-color: {{ $category->color }}; width: 30px; height: 30px;"></span>
                                            <code class="ms-2">{{ $category->color }}</code>
                                        </div>
                                    </td>
                                    <td>
                                        @if($category->icon)
                                            <i class="{{ $category->icon }}"></i> {{ $category->icon }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $category->sort_order ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.course-categories.edit', $category) }}" class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.course-categories.destroy', $category) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
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
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                        <p class="mt-2">No categories found. <a href="{{ route('admin.course-categories.create') }}">Create one now</a>.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($categories->hasPages())
                    <div class="card-footer">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

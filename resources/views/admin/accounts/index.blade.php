@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark">Admin Accounts</h2>
            <p class="text-muted">Manage administrator accounts for your system</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.accounts.create') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-plus-circle me-2"></i>Create New Admin
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Validation Error!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Admin Accounts Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h5 class="mb-0 text-dark">Current Admin Accounts</h5>
        </div>
        <div class="card-body p-0">
            @if ($admins->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">ID</th>
                                <th style="width: 25%">Name</th>
                                <th style="width: 35%">Email</th>
                                <th style="width: 20%">Created Date</th>
                                <th style="width: 15%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($admins as $admin)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $admin->id }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $admin->name }}</strong>
                                        @if ($admin->id === auth()->id())
                                            <span class="badge bg-info ms-2">You</span>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $admin->email }}</code>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $admin->created_at->format('M d, Y @ h:i A') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.accounts.edit', $admin) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Edit Admin">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if ($admin->id !== auth()->id())
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal"
                                                    onclick="setDeleteAdmin({{ $admin->id }}, '{{ $admin->name }}')"
                                                    title="Delete Admin">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center p-3">
                    {{ $admins->links() }}
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.5;"></i>
                    <p class="mt-3">No admin accounts found</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Admin Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">
                    Are you sure you want to delete the admin account for <strong id="adminName"></strong>?
                </p>
                <p class="text-danger small mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This action cannot be undone. Type your password to confirm.
                </p>
            </div>
            <div class="modal-body pt-0">
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Your Password</label>
                        <input type="password" 
                               class="form-control" 
                               id="confirmPassword" 
                               name="password" 
                               required
                               placeholder="Enter your password">
                        <small class="text-muted">For security, please confirm with your admin password</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger" form="deleteForm">
                    <i class="bi bi-trash me-2"></i>Delete Admin
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function setDeleteAdmin(adminId, adminName) {
    document.getElementById('adminName').textContent = adminName;
    const route = "{{ route('admin.accounts.destroy', ':id') }}".replace(':id', adminId);
    document.getElementById('deleteForm').action = route;
    document.getElementById('confirmPassword').value = '';
}
</script>
@endsection

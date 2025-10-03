<div class="card shadow-sm">
    <div class="card-body">
        <p class="text-muted">
            Use the form below to change your password. Make sure to choose a strong password to keep your account secure.
        </p>
        <form method="POST" action="{{ route('profile.change.password', $user) }}">
            @csrf
            @method('PUT')

            <input type="hidden" name="_tab" value="password">

            @if(!auth()->user()->hasRole('admin') || auth()->id() == $user->id)
                <!-- Current password only if user is editing their own account -->
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                    @error('current_password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button class="btn btn-primary">Update Password</button>
        </form>
    </div>
</div>

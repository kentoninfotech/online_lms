<div class="card shadow-sm">
    <div class="card-body">
        <p class="text-muted">
            Upload a new profile picture. Accepted formats: JPG, PNG, GIF. Max size: 2MB.
        </p>
        <form method="POST" action="{{ route('profile.upload.picture', $user) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if($errors->has('profile'))
                <div class="alert alert-danger">
                    {{ $errors->first('profile') }}
                </div>
            @endif

            <input type="hidden" name="active_tab" value="picture">

            <div class="mb-3 text-center">
                <img id="profile-preview"
                     src="{{ $user->profile ? asset('storage/'.$user->profile) : asset('storage/profiles/profile.png') ?? 'https://ui-avatars.com/api/?name='.$user->name }}" 
                     alt="{{ $user->name }}"
                     class="rounded-circle shadow-sm mb-2"
                     style="width: 120px; height: 120px; object-fit: cover;">

                <input type="file" name="profile" class="form-control mt-2" accept="image/*">
            </div>

            <button class="btn btn-primary">Upload New Picture</button>
        </form>
    </div>
</div>

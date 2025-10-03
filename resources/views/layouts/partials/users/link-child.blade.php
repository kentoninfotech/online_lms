<form method="POST" action="{{ route('parent.link.child') }}">
    @csrf

    @if ($errors->any())
        <div class="alert alert-danger">
            <p><strong>Whoops! Something went wrong.</strong></p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <input type="hidden" name="_tab" value="link-child">

    <div class="mb-3">
        <label class="form-label">Child Email</label>
        <input type="email" name="child_email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Child Link Code</label>
        <input type="text" name="link_code" class="form-control" required>
    </div>
    <div class="save-btn-wrapper">
        <button type="submit" class="btn btn-primary w-100">🔗 Link Child</button>
    </div>
</form>
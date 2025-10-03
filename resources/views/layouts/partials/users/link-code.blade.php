<h5 class="mb-3">Your Current Link Code</h5>
<div class="p-3 bg-light rounded border d-inline-block mb-3">
    <strong>{{ $user->student->link_code ?? 'Not Generated' }}</strong>
</div>
<form method="POST" action="{{ route('generate.link.code') }}">
    @csrf
    <input type="hidden" name="active_tab" value="link-code">
    <button type="submit" class="btn btn-outline-primary">Generate New Link Code</button>
</form>
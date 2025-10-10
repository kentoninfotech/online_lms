@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h3>{{ $occurrence->lesson->subject }}</h3>
    <p>Class starts at: {{ $occurrence->scheduled_start->format('d M Y h:i A') }}</p>

    <div id="countdown" class="display-4 my-3"></div>

    <a href="{{ route('lesson.join', $occurrence) }}" 
       class="btn btn-primary btn-lg mt-3" id="joinBtn" disabled>Join Class</a>
</div>

<script>
    let remaining = {{ $remaining }};
    const joinBtn = document.getElementById('joinBtn');
    const countdown = document.getElementById('countdown');

    const timer = setInterval(() => {
        if (remaining <= 0) {
            countdown.innerText = "Class is starting!";
            joinBtn.disabled = false;
            clearInterval(timer);
        } else {
            let minutes = Math.floor(remaining / 60);
            let seconds = remaining % 60;
            countdown.innerText = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            remaining--;
        }
    }, 1000);
</script>
@endsection

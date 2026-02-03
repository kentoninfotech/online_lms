@extends('layouts.app')

@section('content')

<div class="bg-light py-4 border-bottom border-secondary border-2 shadow-sm">
    <div class="container-fluid">
        <div class="row ps-5">
            <div class="col-12">
                {{-- text-muted for gray secondary text --}}
                <p class="mb-1 text-muted">Date: <x-format-time :date="$occurrence->scheduled_start" /></p>
                <h2 class="mb-0 fw-bold">{{ $occurrence->lesson->subject }}</h2>
                <p class="text-muted">Instructor: {{ $occurrence->lesson->instructor->name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>
<div class="card p-5 mx-auto mt-3 mb-3 shadow-lg border-primary border-1 rounded-3" style="max-width: 500px;">
    <div class="card-body text-center p-0">
        <p class="lead fw-bold text-dark mb-2">
            Thank You for Joining the Waiting Room 
            <i class="bi bi-info-circle text-muted" style="font-size: 0.8rem;"></i>
        </p>
        <p class="text-secondary small mb-4">
            You will be able to join the class when the countdown ends.
        </p>

        <h1 class="fw-bolder text-dark mb-4" id="countdown" style="font-size: 3rem;">
            Starts in: --:--:--:--
        </h1>

        <a href="{{ route('lesson.join', $occurrence) }}" class="btn btn-primary btn-lg px-5 py-3 fw-bold" style="font-size: 1.25rem;" id="joinBtn" disabled>
            JOIN CLASS
        </a>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const targetDate = new Date("{{ $occurrence?->scheduled_start->toIso8601String() }}").getTime();
    const countdownEl = document.getElementById("countdown");

    function updateCountdown() {
        const now = new Date().getTime();
        const diff = targetDate - now;

        if (diff <= 0) {
            countdownEl.innerHTML = "Class is starting!";
            return;
        }

        const days = Math.floor(diff / (1000*60*60*24));
        const hours = Math.floor((diff % (1000*60*60*24)) / (1000*60*60));
        const mins = Math.floor((diff % (1000*60*60)) / (1000*60));
        const secs = Math.floor((diff % (1000 * 60)) / 1000);
        countdownEl.innerHTML = `${days}d ${hours}h ${mins}m ${secs}s`;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
});
</script>
@endsection

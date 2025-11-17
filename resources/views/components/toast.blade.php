<style>
    /*
    Adjust the time to match the data-bs-delay attribute on the toast element (e.g., 5s for 5000ms)
*/
@keyframes toast-progress-fill {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}

.toast .progress-bar {
    animation: toast-progress-fill 5s linear forwards; /* 5s matches the data-bs-delay="5000" */
}
</style>

@php
    // Define the type and message variables based on session
    $type = session('success') ? 'success' : (session('error') ? 'danger' : null);
    $message = session('success') ?? session('error'); 
    $title = $type == 'success' ? 'Success!' : ($type == 'danger' ? 'Warning!' : '');
@endphp

@if($type && $message)
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="liveToast" 
         class="toast align-items-center text-bg-{{ $type == 'danger' ? 'danger' : ($type == 'success' ? 'success' : $type) }} border-0" 
         role="alert" 
         aria-live="assertive" 
         aria-atomic="true" 
         data-bs-delay="5000">
        
        <div class="toast-header">
            <i class="me-2 
                @if ($type == 'success') bi bi-check-circle-fill text-success
                @elseif ($type == 'danger') bi bi-exclamation-triangle-fill text-danger
                @endif
            "></i>
            <strong class="me-auto">{{ $title }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>

        <div class="toast-body">
            {{ $message }}
        </div>

        <div class="progress" style="height: 5px; border-radius: 0 0 .25rem .25rem;">
            <div id="toast-progress" 
                 class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $type == 'danger' ? 'danger' : ($type == 'success' ? 'success' : $type) }}" 
                 role="progressbar" 
                 aria-valuenow="100" 
                 aria-valuemin="0" 
                 aria-valuemax="100" 
                 style="width: 100%">
            </div>
        </div>
    </div>
</div>
@endif



<script>
document.addEventListener('DOMContentLoaded', function () {
    var toastEl = document.getElementById('liveToast');
    if (toastEl) {
        // Initialize the toast
        var toast = new bootstrap.Toast(toastEl);
        
        // Get the progress bar element
        var progressBar = document.getElementById('toast-progress');

        // Listen for when the toast is shown
        toastEl.addEventListener('show.bs.toast', function () {
            // Remove the animation style if it exists
            progressBar.style.animation = 'none';
            
            // Set the correct duration for the animation based on data-bs-delay
            var delay = toastEl.getAttribute('data-bs-delay') / 1000; // Convert ms to s

            // Apply the animation style
            progressBar.style.animation = `toast-progress-fill ${delay}s linear forwards`;
        });

        // Show the toast
        toast.show();
    }
});
</script>
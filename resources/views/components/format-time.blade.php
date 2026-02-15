@props(['date', 'format' => 'd M Y h:i A'])

@if($date)
    {{-- 
        IMPORTANT: Lessons are stored in Africa/Lagos (UTC+1) in the database
        This component converts FROM Africa/Lagos TO the user's local timezone for display
    --}}
    {{ $date
        ->copy()
        ->setTimezone('Africa/Lagos')  // Ensure we start from Africa/Lagos (as stored in DB)
        ->setTimezone(function_exists('getUserTimezone') ? getUserTimezone() : config('app.timezone'))  // Convert to user's timezone
        ->format($format) 
    }}
@else
    Not Available
@endif

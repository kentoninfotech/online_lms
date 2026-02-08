@props(['date', 'format' => 'd M Y h:i A'])

@if($date)
    {{ $date->setTimezone(auth()->user()?->timezone ?? config('app.timezone'))->format($format) }}
@else
    Not Available
@endif

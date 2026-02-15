@props(['date', 'format' => 'd M Y h:i A'])

@if($date)
    {{ $date->copy()->setTimezone(function_exists('getUserTimezone') ? getUserTimezone() : config('app.timezone'))->format($format) }}
@else
    Not Available
@endif

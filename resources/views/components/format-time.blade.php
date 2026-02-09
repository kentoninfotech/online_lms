@props(['date', 'format' => 'd M Y h:i A'])

@if($date)
    {{ $date->setTimezone(getUserTimezone())->format($format) }}
@else
    Not Available
@endif

<h3>Attendance for {{ $student->name }}</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Lesson</th>
            <th>Status</th>
            <th>Join</th>
            <th>Leave</th>
            <th>Duration</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendances as $a)
            <tr>
                <td>{{ $a->occurrence->lesson->subject ?? '-' }}</td>
                <td>{{ ucfirst($a->status) }}</td>
                <td>{{ $a->join_time?->setTimezone(function_exists('getUserTimezone') ? getUserTimezone() : config('app.timezone'))->format('h:i A') ?? '-' }}</td>
                <td>{{ $a->leave_time?->setTimezone(function_exists('getUserTimezone') ? getUserTimezone() : config('app.timezone'))->format('h:i A') ?? '-' }}</td>
                <td>{{ $a->duration_minutes ?? '-' }} mins</td>
            </tr>
        @endforeach
    </tbody>
</table>

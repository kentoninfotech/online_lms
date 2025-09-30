<h3>Lessons for {{ $student->name }}</h3>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>Subject</th>
            <th>Instructor</th>
            <th>Start Time</th>
            <th>Duration</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lessons as $lesson)
            @foreach($lesson->occurrences as $occ)
                <tr>
                    <td>{{ $lesson->subject }}</td>
                    <td>{{ $lesson->instructor->name ?? '-' }}</td>
                    <td>{{ $occ->scheduled_start->format('d M Y h:i A') }}</td>
                    <td>{{ $occ->duration_minutes }} mins</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

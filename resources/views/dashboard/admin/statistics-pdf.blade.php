<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Statistics Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section h2 {
            font-size: 16px;
            background-color: #f0f0f0;
            padding: 10px;
            margin: 0 0 15px 0;
            border-left: 4px solid #007bff;
        }
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .metric-item {
            display: table-cell;
            width: 25%;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .metric-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .metric-value {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
        }
        .metric-value.success {
            color: #28a745;
        }
        .metric-value.danger {
            color: #dc3545;
        }
        .metric-value.warning {
            color: #ffc107;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background-color: #f0f0f0;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
        }
        table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .row {
            display: table;
            width: 100%;
        }
        .col {
            display: table-cell;
            width: 50%;
            padding-right: 10px;
            vertical-align: top;
        }
        .col:last-child {
            padding-right: 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .progress-bar {
            display: inline-block;
            background-color: #007bff;
            height: 20px;
            border-radius: 3px;
            text-align: center;
            color: white;
            font-size: 11px;
            line-height: 20px;
        }
        .percentage-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .percentage-badge.success {
            background-color: #28a745;
        }
        .percentage-badge.warning {
            background-color: #ffc107;
        }
        .percentage-badge.danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>The Virtual Academy - Admin Statistics Report</h1>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <!-- Key Metrics Section -->
    <div class="section">
        <h2>Platform Overview</h2>
        <div class="metrics-grid">
            <div class="metric-item">
                <div class="metric-label">Total Students</div>
                <div class="metric-value">{{ $totalStudents }}</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">Total Instructors</div>
                <div class="metric-value">{{ $totalInstructors }}</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">Total Parents</div>
                <div class="metric-value">{{ $totalParents }}</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">Total Lessons</div>
                <div class="metric-value">{{ $totalLessons }}</div>
            </div>
        </div>
        <div class="metrics-grid">
            <div class="metric-item">
                <div class="metric-label">Lesson Occurrences</div>
                <div class="metric-value">{{ $totalLessonOccurrences }}</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">Attendance Rate</div>
                <div class="metric-value success">{{ $attendanceRate }}%</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">Impact Percentage</div>
                <div class="metric-value">{{ $impactPercentage }}%</div>
            </div>
            <div class="metric-item">
                <div class="metric-label">Avg Duration (min)</div>
                <div class="metric-value">{{ $avgDuration }}</div>
            </div>
        </div>
    </div>

    <!-- Attendance Breakdown Section -->
    <div class="section">
        <h2>Attendance Statistics</h2>
        <table>
            <tr>
                <th>Status</th>
                <th>Count</th>
                <th>Percentage</th>
            </tr>
            <tr>
                <td><strong>Total Attendances</strong></td>
                <td>{{ $totalAttendances }}</td>
                <td>100%</td>
            </tr>
            <tr>
                <td style="color: #28a745;"><strong>Present</strong></td>
                <td>{{ $presentAttendances }}</td>
                <td>{{ $totalAttendances > 0 ? round(($presentAttendances / $totalAttendances) * 100, 2) : 0 }}%</td>
            </tr>
            <tr>
                <td style="color: #ffc107;"><strong>Late</strong></td>
                <td>{{ $lateAttendances }}</td>
                <td>{{ $totalAttendances > 0 ? round(($lateAttendances / $totalAttendances) * 100, 2) : 0 }}%</td>
            </tr>
            <tr>
                <td style="color: #dc3545;"><strong>Absent</strong></td>
                <td>{{ $absentAttendances }}</td>
                <td>{{ $totalAttendances > 0 ? round(($absentAttendances / $totalAttendances) * 100, 2) : 0 }}%</td>
            </tr>
        </table>
    </div>

    <!-- Subscription & Payment Section -->
    <div class="section">
        <h2>Subscription & Payment Statistics</h2>
        <table>
            <tr>
                <th>Metric</th>
                <th style="text-align: right;">Value</th>
            </tr>
            <tr>
                <td>Active Subscriptions</td>
                <td style="text-align: right; color: #28a745;"><strong>{{ $activeSubscriptions }}</strong></td>
            </tr>
            <tr>
                <td>Expired Subscriptions</td>
                <td style="text-align: right; color: #dc3545;"><strong>{{ $expiredSubscriptions }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Lesson Performance Section -->
    <div class="section">
        <h2>Lesson Performance</h2>
        <table>
            <tr>
                <th>Metric</th>
                <th style="text-align: right;">Percentage</th>
            </tr>
            <tr>
                <td>Lesson Completion Rate</td>
                <td style="text-align: right;">
                    <span class="percentage-badge success">{{ $lessonCompletionRate }}%</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>This is a confidential report generated for administration purposes only.</p>
        <p>Report generated using Virtual Academy Platform</p>
    </div>
</body>
</html>

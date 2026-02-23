<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }
        .footer {
            background: #f0f0f0;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 0 0 8px 8px;
        }
        .course-info {
            background: white;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
        }
        .sender-info {
            color: #666;
            font-size: 14px;
            margin-top: 20px;
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0;">📢 Course Announcement</h2>
        </div>

        <div class="content">
            <p>Hello {{ $recipient->user->name }},</p>

            <p>You have received a new announcement for your enrolled course:</p>

            <div class="course-info">
                <strong style="color: #667eea;">{{ $message->course->title }}</strong>
                <p style="margin: 10px 0 0 0; font-size: 14px; color: #666;">
                    <strong>Announcement:</strong> {{ $message->subject }}
                </p>
            </div>

            <div style="background: white; padding: 20px; border: 1px solid #e0e0e0; border-radius: 4px; margin: 20px 0;">
                {!! nl2br(e($message->message)) !!}
            </div>

            <a href="{{ route('courses.show', $message->course) }}" class="btn">
                View Course
            </a>

            <div class="sender-info">
                <p style="margin: 0;">
                    <strong>From:</strong> {{ $message->sender->name }}<br>
                    <strong>Date:</strong> {{ $message->created_at->format('M d, Y H:i A') }}
                </p>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated message from {{ config('app.name') }}. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>

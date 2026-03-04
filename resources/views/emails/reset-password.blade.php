<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        /* Header with gradient background */
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: #fff;
        }

        .email-header .logo {
            max-width: 140px;
            height: auto;
            margin-bottom: 20px;
        }

        .email-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .email-header p {
            font-size: 14px;
            opacity: 0.95;
            font-weight: 300;
        }

        /* Main content area */
        .email-body {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .message-text {
            font-size: 15px;
            color: #555;
            line-height: 1.8;
            margin-bottom: 24px;
        }

        .message-text p {
            margin-bottom: 12px;
        }

        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 13px;
            color: #856404;
        }

        /* CTA Button */
        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 14px 40px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: transform 0.2s;
        }

        .cta-button:hover {
            transform: translateY(-2px);
        }

        /* Link section */
        .link-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }

        .link-section p {
            margin-bottom: 10px;
            font-size: 13px;
            color: #666;
        }

        .link-section code {
            display: block;
            background: #fff;
            padding: 12px;
            border-radius: 4px;
            font-size: 12px;
            word-break: break-all;
            color: #667eea;
            font-family: 'Courier New', monospace;
            margin-top: 8px;
        }

        /* Divider */
        .divider {
            border-top: 1px solid #e0e0e0;
            margin: 30px 0;
        }

        /* Footer */
        .email-footer {
            padding: 30px;
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
            text-align: center;
        }

        .footer-text {
            font-size: 12px;
            color: #999;
            line-height: 1.6;
        }

        .footer-links {
            margin-top: 15px;
        }

        .footer-links a {
            color: #667eea;
            text-decoration: none;
            font-size: 12px;
            margin: 0 10px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                border-radius: 0;
            }

            .email-header {
                padding: 30px 20px;
            }

            .email-body {
                padding: 25px 20px;
            }

            .email-header h1 {
                font-size: 24px;
            }

            .cta-button {
                padding: 12px 30px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            @if ($logoPath)
                <img src="{{ $logoPath }}" alt="{{ $siteName }} Logo" class="logo">
            @endif
            <h1>Password Reset</h1>
            <p>Securely reset your account password</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <p class="greeting">Hi {{ $user->name }},</p>

            <p class="message-text">
                We received a request to reset the password for your {{ $siteName }} account. 
                Click the button below to set a new password.
            </p>

            <div class="button-container">
                <a href="{{ $resetUrl }}" class="cta-button">Reset Your Password</a>
            </div>

            <p class="message-text" style="text-align: center; color: #999; font-size: 13px;">
                Or copy and paste this link in your browser:
            </p>

            <div class="link-section">
                <p>Reset Link</p>
                <code>{{ $resetUrl }}</code>
            </div>

            <div class="warning-box">
                <strong>⏰ Link Expiration:</strong> This password reset link will expire in 1 hour. If you don't reset your password within this time, you'll need to request a new reset link.
            </div>

            <div class="warning-box" style="background: #e7f3ff; border-left-color: #0066cc; color: #004085;">
                <strong>🔒 Security Tip:</strong> If you didn't request this password reset, please ignore this email or contact our support team immediately.
            </div>

            <p class="message-text">
                If you have any questions or need assistance, please don't hesitate to reach out to our support team.
            </p>
        </div>

        <!-- Divider -->
        <div style="padding: 0 30px;">
            <div class="divider"></div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p class="footer-text">
                This is an automated message. Please do not reply directly to this email.
            </p>
            <p class="footer-text">
                © {{ date('Y') }} {{ $siteName }}. All rights reserved.
            </p>
            <div class="footer-links">
                <a href="{{ url('/') }}">Website</a>
            </div>
        </div>
    </div>
</body>
</html>

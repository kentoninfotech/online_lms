<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
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

        /* Steps section */
        .steps-section {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 6px;
            margin: 25px 0;
        }

        .steps-section h3 {
            font-size: 14px;
            font-weight: 700;
            color: #667eea;
            text-transform: uppercase;
            margin-bottom: 15px;
            letter-spacing: 0.5px;
        }

        .steps-section ol {
            margin: 0;
            padding-left: 20px;
        }

        .steps-section li {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            line-height: 1.6;
        }

        /* CTA Button */
        .cta-container {
            text-align: center;
            margin: 35px 0;
        }

        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 15px 45px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            cursor: pointer;
        }

        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        /* Alternative link */
        .alternative-link {
            text-align: center;
            margin: 20px 0;
            font-size: 13px;
            color: #999;
        }

        .alternative-link p {
            margin-bottom: 10px;
        }

        .alternative-link a {
            color: #667eea;
            text-decoration: none;
            word-break: break-all;
            font-size: 12px;
        }

        .alternative-link a:hover {
            text-decoration: underline;
        }

        /* Expiration notice */
        .expiration-notice {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            font-size: 13px;
            color: #856404;
        }

        .expiration-notice strong {
            color: #856404;
            display: block;
            margin-bottom: 5px;
        }

        /* Footer */
        .email-footer {
            background: #f8f9fa;
            padding: 30px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #999;
            font-size: 12px;
            line-height: 1.6;
        }

        .email-footer p {
            margin-bottom: 8px;
        }

        .email-footer a {
            color: #667eea;
            text-decoration: none;
        }

        .email-footer a:hover {
            text-decoration: underline;
        }

        .social-links {
            margin-top: 15px;
        }

        .social-links a {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            background: #e9ecef;
            border-radius: 50%;
            margin: 0 5px;
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }

        .social-links a:hover {
            background: #667eea;
            color: #fff;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                border-radius: 0;
            }

            .email-header {
                padding: 30px 20px;
            }

            .email-header h1 {
                font-size: 24px;
            }

            .email-body {
                padding: 30px 20px;
            }

            .message-text {
                font-size: 14px;
            }

            .verify-button {
                padding: 12px 35px;
                font-size: 14px;
                width: 100%;
            }

            .email-footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="email-header">
            @if(!empty($logoPath))
                @if(str_starts_with($logoPath, 'http'))
                    <img src="{{ $logoPath }}" alt="Logo" class="logo" style="max-width: 140px; height: auto;">
                @else
                    <img src="{{ $message->embed(public_path($logoPath)) }}" alt="Logo" class="logo">
                @endif
            @elseif(file_exists(public_path('assets/images/logo.png')))
                <img src="{{ $message->embed(public_path('assets/images/logo.png')) }}" alt="Logo" class="logo">
            @elseif(file_exists(public_path('assets/images/logo.svg')))
                <img src="{{ $message->embed(public_path('assets/images/logo.svg')) }}" alt="Logo" class="logo">
            @else
                <div style="font-size: 48px; margin-bottom: 15px;">🎓</div>
            @endif
            <h1>Verify Your Email</h1>
            <p>Complete your registration in one click</p>
        </div>

        <!-- Body Content -->
        <div class="email-body">
            <div class="greeting">
                Hello {{ $user->name }}! 👋
            </div>

            <div class="message-text">
                <p>Thank you for registering with <strong>{{ $siteName ?? config('app.name') }}</strong>!</p>
                <p>We're excited to have you on board. To get started, please verify your email address by clicking the button below.</p>
            </div>

            <!-- Steps Section -->
            <div class="steps-section">
                <h3>How to verify your email</h3>
                <ol>
                    <li><strong>Click the button below</strong> to verify your email address</li>
                    <li><strong>You'll be redirected</strong> to your dashboard</li>
                    <li><strong>Start learning</strong> right away!</li>
                </ol>
            </div>

            <!-- CTA Button -->
            <div class="cta-container">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    ✓ Verify Email Address
                </a>
            </div>

            <!-- Alternative Link -->
            <div class="alternative-link">
                <p><small>Or copy and paste this link into your browser:</small></p>
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </div>

            <!-- Expiration Notice -->
            <div class="expiration-notice">
                <strong>⏱ Link expires in 24 hours</strong>
                This verification link will expire on {{ $expiresAt->format('F j, Y') }} at {{ $expiresAt->format('g:i A') }}.
                If the link expires, you can request a new one from your account dashboard.
            </div>

            <!-- Additional Message -->
            <div class="message-text" style="margin-top: 25px; background: #f0f7ff; padding: 15px; border-radius: 6px; border-left: 3px solid #667eea;">
                <p><strong>Trouble verifying?</strong></p>
                <p>If you didn't create this account or have any questions, please contact our support team. We're here to help!</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p><strong>{{ $siteName ?? config('app.name') }}</strong></p>
            <p>Learning Made Simple & Accessible</p>

            <div class="social-links">
                <a href="https://facebook.com" title="Facebook">f</a>
                <a href="https://twitter.com" title="Twitter">𝕏</a>
                <a href="https://instagram.com" title="Instagram">📷</a>
                <a href="https://linkedin.com" title="LinkedIn">in</a>
            </div>

            <p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e9ecef;">
                © {{ date('Y') }} {{ $siteName ?? config('app.name') }}. All rights reserved.
            </p>

            <p style="font-size: 11px; color: #bbb; margin-top: 10px;">
                <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a> | <a href="#">Contact Us</a>
            </p>
        </div>
    </div>
</body>
</html>

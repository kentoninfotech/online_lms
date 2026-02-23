<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Course Completion Certificate</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .certificate-container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 60px;
            position: relative;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            border: 15px solid #d4af37;
            page-break-inside: avoid;
        }

        .certificate-container::before,
        .certificate-container::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 80px;
            border: 3px solid #d4af37;
            opacity: 0.5;
        }

        .certificate-container::before {
            top: 20px;
            left: 20px;
            border-right: none;
            border-bottom: none;
        }

        .certificate-container::after {
            bottom: 20px;
            right: 20px;
            border-left: none;
            border-top: none;
        }

        .certificate-header {
            text-align: center;
            margin-bottom: 40px;
            color: #d4af37;
        }

        .certificate-logo {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .certificate-title {
            font-size: 48px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .certificate-subtitle {
            font-size: 18px;
            color: #f0f0f0;
            margin-bottom: 40px;
        }

        .certificate-content {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .certificate-text {
            font-size: 16px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .certificate-recipient {
            font-size: 28px;
            font-weight: bold;
            color: #d4af37;
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .certificate-course {
            font-size: 20px;
            color: #f0f0f0;
            margin: 20px 0;
            font-style: italic;
        }

        .certificate-details {
            margin-top: 50px;
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            padding-top: 40px;
            border-top: 2px solid #d4af37;
        }

        .detail-section {
            text-align: center;
            color: #f0f0f0;
            font-size: 12px;
        }

        .detail-line {
            border-top: 2px solid #d4af37;
            width: 150px;
            margin: 20px auto 10px;
            color: #d4af37;
        }

        .detail-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #d4af37;
            margin-top: 10px;
        }

        .certificate-number {
            text-align: right;
            color: #999;
            font-size: 10px;
            margin-top: 20px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }

            .certificate-container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
            }
        }

        .seal {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            border: 3px solid #d4af37;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
        }

        .date-issued {
            color: #d4af37;
            font-size: 14px;
            margin-top: 10px;
        }

        .honors-text {
            font-size: 14px;
            font-style: italic;
            color: #f0f0f0;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Decorative Seal -->
        <div class="seal">
            ★
        </div>

        <div class="certificate-header">
            <div class="certificate-logo">
                <strong>{{ config('app.name', 'LearnHub') }}</strong>
            </div>
            <div class="certificate-title">Certificate</div>
            <div class="certificate-subtitle">of Course Completion</div>
        </div>

        <div class="certificate-content">
            <div class="certificate-text">
                This is to certify that
            </div>

            <div class="certificate-recipient">
                {{ strtoupper($enrollment->user->name) }}
            </div>

            <div class="certificate-text">
                has successfully completed the course
            </div>

            <div class="certificate-course">
                {{ $course->title }}
            </div>

            <div class="honors-text">
                Demonstrating proficiency in all required course materials, assessments, and learning objectives
            </div>

            <div class="certificate-text">
                <strong>Certificate Details:</strong><br>
                Certificate Number: {{ $certificate->certificate_number }}<br>
                Issued: {{ $certificate->issued_at->format('F d, Y') }}<br>
                Valid Until: {{ optional($certificate->expires_at)->format('F d, Y') ?? 'Lifetime' }}
            </div>
        </div>

        <div class="certificate-details">
            <div class="detail-section">
                <div class="detail-line"></div>
                <div class="detail-label">Authorized Signature</div>
            </div>

            <div class="detail-section">
                <div class="seal" style="width: 80px; height: 80px; font-size: 48px; margin: 0 auto 20px;">✓</div>
                <div class="detail-label">Official Seal</div>
            </div>

            <div class="detail-section">
                <div class="detail-line"></div>
                <div class="detail-label">Director of Education</div>
            </div>
        </div>

        <div class="date-issued">
            Date Issued: {{ $certificate->issued_at->format('F d, Y') }}
        </div>

        <div class="certificate-number">
            Certificate ID: {{ $certificate->certificate_number }}
        </div>
    </div>
</body>
</html>

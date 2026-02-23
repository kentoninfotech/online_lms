<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion</title>
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
            max-width: 1000px;
            height: 700px;
            margin: 20px auto;
            background: linear-gradient(135deg, #ffffff 0%, #f0f4f8 100%);
            border: 8px solid #2c3e50;
            position: relative;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 60px 80px;
            page-break-after: always;
        }

        /* Corner decorations */
        .certificate-container::before,
        .certificate-container::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 80px;
            border: 3px solid #2c3e50;
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

        .header-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 14px;
            color: #7f8c8d;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .certificate-title {
            font-size: 48px;
            color: #2c3e50;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .certificate-subtitle {
            font-size: 18px;
            color: #34495e;
            font-style: italic;
            margin-top: 10px;
        }

        .content-section {
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 30px 0;
        }

        .presented-to {
            font-size: 18px;
            color: #34495e;
            margin-bottom: 15px;
        }

        .student-name {
            font-size: 42px;
            color: #c0392b;
            font-weight: bold;
            text-decoration: underline;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .course-section {
            margin: 40px 0;
        }

        .course-text {
            font-size: 16px;
            color: #34495e;
            margin-bottom: 10px;
        }

        .course-name {
            font-size: 28px;
            color: #2c3e50;
            font-weight: bold;
            margin: 15px 0;
        }

        .achievement-text {
            font-size: 14px;
            color: #7f8c8d;
            font-style: italic;
            margin-top: 30px;
            line-height: 1.6;
        }

        .footer-section {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #bdc3c7;
        }

        .signature-block {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 2px solid #2c3e50;
            height: 80px;
            margin-bottom: 10px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            color: #2c3e50;
            font-weight: bold;
            padding-bottom: 5px;
        }

        .signature-label {
            font-size: 12px;
            color: #34495e;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .date-section {
            text-align: center;
        }

        .date-label {
            font-size: 12px;
            color: #34495e;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .date-value {
            font-size: 14px;
            color: #2c3e50;
            font-weight: bold;
        }

        .certificate-number {
            font-size: 10px;
            color: #95a5a6;
            position: absolute;
            bottom: 10px;
            right: 20px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .certificate-container {
                margin: 0;
                box-shadow: none;
            }
            .no-print {
                display: none;
            }
        }

        .print-button {
            background-color: #3498db;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 20px;
        }

        .print-button:hover {
            background-color: #2980b9;
        }

        .certificate-actions {
            text-align: center;
            margin-top: 20px;
            no-print: true;
        }

        .action-link {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 20px;
            background-color: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .action-link:hover {
            background-color: #229954;
        }

        .action-link.secondary {
            background-color: #95a5a6;
        }

        .action-link.secondary:hover {
            background-color: #7f8c8d;
        }
    </style>
</head>
<body>
    <!-- Certificate --
    <div class="certificate-container">
        <!-- Top left corner -->
        <!-- Top right corner -->

        <div class="header-section">
            <div class="logo">{{ config('app.name', 'Learning Platform') }}</div>
            <div class="certificate-title">Certificate</div>
            <div class="certificate-subtitle">of Completion</div>
        </div>

        <div class="content-section">
            <div class="presented-to">This is proudly presented to</div>

            <div class="student-name">
                {{ $courseEnrollee->user->name }}
            </div>

            <div class="course-section">
                <div class="course-text">For successfully completing the course</div>
                <div class="course-name">{{ $course->title }}</div>
                <div class="achievement-text">
                    In recognition of demonstrated mastery and completion of all course requirements,
                    including assignments and assessments.
                </div>
            </div>
        </div>

        <div class="footer-section">
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-label">Instructor Signature</div>
            </div>

            <div class="date-section">
                <div class="date-label">Date of Completion</div>
                <div class="date-value">{{ $courseEnrollee->updated_at->format('F d, Y') }}</div>
            </div>

            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-label">Institution Director</div>
            </div>
        </div>

        <div class="certificate-number">
            Certificate #{{ $courseEnrollee->id }}-{{ $course->id }}
        </div>
    </div>

    <!-- Action buttons -->
    <div class="certificate-actions no-print">
        <button class="print-button" onclick="window.print()">
            <i class="bi bi-printer"></i> Print Certificate
        </button>
        <button class="print-button" onclick="downloadPDF()">
            <i class="bi bi-file-pdf"></i> Download PDF
        </button>
    </div>

    <script>
        function downloadPDF() {
            // Using html2pdf library (would need to be included in the page)
            const element = document.querySelector('.certificate-container');
            const opt = {
                margin: 10,
                filename: 'Certificate-{{ preg_replace("/[^A-Za-z0-9 ]/", "", $courseEnrollee->user->name) }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { orientation: 'landscape', unit: 'mm', format: 'a4' }
            };
            // html2pdf().set(opt).save(element);
            alert('PDF download feature requires html2pdf library integration');
        }
    </script>
</body>
</html>

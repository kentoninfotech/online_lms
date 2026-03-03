@extends('layouts.app')

@section('title', 'Email Configuration - Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h2 mb-0"><i class="fa fa-cogs"></i> Email Configuration</h1>
                    <p class="text-muted mt-1">View your current email setup and driver settings</p>
                </div>
                <a href="{{ route('admin.email-testing.index') }}" class="btn btn-outline-primary">
                    <i class="fa fa-arrow-left"></i> Back to Testing
                </a>
            </div>
        </div>
    </div>

    <!-- Current Configuration -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa fa-envelope-open"></i> Active Configuration</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <td class="fw-semibold w-25">Mail Driver:</td>
                            <td>
                                <span class="badge bg-info" style="font-size: 0.95rem;">{{ $config['driver'] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">From Email:</td>
                            <td><code>{{ $config['from_address'] }}</code></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">From Name:</td>
                            <td><code>{{ $config['from_name'] }}</code></td>
                        </tr>
                        @if($config['driver'] === 'smtp')
                        <tr>
                            <td class="fw-semibold">SMTP Host:</td>
                            <td><code>{{ $config['host'] }}</code></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">SMTP Port:</td>
                            <td><code>{{ $config['port'] }}</code></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        @if($config['driver'] === 'smtp')
                            <i class="fa fa-check-circle text-success"></i> SMTP Configured
                        @elseif($config['driver'] === 'mailgun')
                            <i class="fa fa-check-circle text-success"></i> Mailgun Configured
                        @elseif($config['driver'] === 'sendmail')
                            <i class="fa fa-check-circle text-success"></i> Sendmail Configured
                        @else
                            <i class="fa fa-info-circle text-warning"></i> {{ ucfirst($config['driver']) }} Configured
                        @endif
                    </h6>
                    <p class="text-muted small mb-0">
                        Your emails are being sent through <strong>{{ $config['driver'] }}</strong> driver.
                        All system emails (registration, notifications, etc.) will use this configuration.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Environment Variables -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fa fa-code"></i> Environment Variables (.env)</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Expected environment variables for your mail driver (check your <code>.env</code> file):
                    </p>

                    @if($config['driver'] === 'smtp')
                    <div class="alert alert-info small">
                        <code>
                            MAIL_DRIVER=smtp<br>
                            MAIL_HOST={{ $config['host'] ?? 'smtp.gmail.com' }}<br>
                            MAIL_PORT={{ $config['port'] ?? 587 }}<br>
                            MAIL_USERNAME=your-email@gmail.com<br>
                            MAIL_PASSWORD=your-app-password<br>
                            MAIL_ENCRYPTION=tls<br>
                            MAIL_FROM_ADDRESS={{ $config['from_address'] }}<br>
                            MAIL_FROM_NAME="{{ $config['from_name'] }}"
                        </code>
                    </div>
                    @elseif($config['driver'] === 'mailgun')
                    <div class="alert alert-info small">
                        <code>
                            MAIL_DRIVER=mailgun<br>
                            MAILGUN_DOMAIN=your-domain.mailgun.org<br>
                            MAILGUN_SECRET=your-mailgun-api-key<br>
                            MAIL_FROM_ADDRESS={{ $config['from_address'] }}<br>
                            MAIL_FROM_NAME="{{ $config['from_name'] }}"
                        </code>
                    </div>
                    @else
                    <div class="alert alert-info small">
                        <code>
                            MAIL_DRIVER={{ $config['driver'] }}<br>
                            MAIL_FROM_ADDRESS={{ $config['from_address'] }}<br>
                            MAIL_FROM_NAME="{{ $config['from_name'] }}"
                        </code>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Setup Guides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fa fa-book"></i> Setup Guides</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">
                                <i class="fa fa-envelope text-primary"></i> Gmail SMTP Setup
                            </h6>
                            <ol class="small">
                                <li>Enable Two-Factor Authentication in Google Account</li>
                                <li>Go to <a href="https://myaccount.google.com/apppasswords" target="_blank">App Passwords</a></li>
                                <li>Select "Mail" and "Windows Computer"</li>
                                <li>Copy the generated 16-character password</li>
                                <li>Use in <code>.env</code> as <code>MAIL_PASSWORD</code></li>
                                <li>Set <code>MAIL_HOST=smtp.gmail.com</code></li>
                                <li>Set <code>MAIL_PORT=587</code></li>
                                <li>Set <code>MAIL_ENCRYPTION=tls</code></li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">
                                <i class="fa fa-paper-plane text-success"></i> Mailgun SMTP Setup
                            </h6>
                            <ol class="small">
                                <li>Create account at <a href="https://mailgun.com" target="_blank">Mailgun.com</a></li>
                                <li>Verify your domain</li>
                                <li>Get your SMTP credentials from Sending → Domain Settings</li>
                                <li>You'll get Host, Port (587), Username, and Password</li>
                                <li>Configure in <code>.env</code>:</li>
                            </ol>
                            <code class="small">
                                MAIL_HOST=smtp.mailgun.org<br>
                                MAIL_PORT=587<br>
                                MAIL_USERNAME=postmaster@yourdomain.com<br>
                                MAIL_PASSWORD=your-password
                            </code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testing Commands -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fa fa-terminal"></i> Command Line Testing</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Run these commands in your terminal to test email functionality:
                    </p>

                    <div class="alert alert-light border">
                        <p class="small mb-2"><strong>Test verification email:</strong></p>
                        <code>php artisan email:test-verification your-email@example.com</code>
                    </div>

                    <div class="alert alert-light border">
                        <p class="small mb-2"><strong>Clear email queue (if stuck):</strong></p>
                        <code>php artisan queue:clear</code>
                    </div>

                    <div class="alert alert-light border">
                        <p class="small mb-2"><strong>Start queue worker to process emails:</strong></p>
                        <code>php artisan queue:work</code>
                    </div>

                    <div class="alert alert-light border">
                        <p class="small mb-2"><strong>Monitor failed jobs:</strong></p>
                        <code>php artisan queue:failed</code>
                    </div>

                    <div class="alert alert-light border">
                        <p class="small mb-2"><strong>Retry failed email jobs:</strong></p>
                        <code>php artisan queue:retry all</code>
                    </div>

                    <div class="alert alert-warning small mt-3 mb-0">
                        <i class="fa fa-info-circle"></i>
                        <strong>Note:</strong> Make sure queue worker is running if using async email processing!
                        Run <code>php artisan queue:work</code> in a separate terminal or use supervisor.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

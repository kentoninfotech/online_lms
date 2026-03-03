@extends('layouts.app')

@section('title', 'Email Testing - Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h2 mb-0"><i class="fa fa-envelope"></i> Email Testing</h1>
                    <p class="text-muted mt-1">Test email notifications and verify configuration</p>
                </div>
                <a href="{{ route('admin.email-testing.config') }}" class="btn btn-outline-primary">
                    <i class="fa fa-cogs"></i> View Configuration
                </a>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Verification Email -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa fa-check"></i> Registration Verification Email</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">
                        Test the email sent to new users during registration. This email includes:
                    </p>
                    <ul class="small text-muted mb-4">
                        <li><i class="fa fa-check-circle text-success"></i> Your site logo</li>
                        <li><i class="fa fa-check-circle text-success"></i> Verification link (24-hour expiry)</li>
                        <li><i class="fa fa-check-circle text-success"></i> Professional layout</li>
                        <li><i class="fa fa-check-circle text-success"></i> Clear call-to-action button</li>
                    </ul>

                    <form action="{{ route('admin.email-testing.send-verification') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                value="{{ auth()->user()->email }}" placeholder="test@example.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                value="{{ auth()->user()->name }}" placeholder="John Doe" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-paper-plane"></i> Send Test Email
                        </button>
                    </form>

                    <div class="alert alert-info small mt-3 mb-0">
                        <i class="fa fa-info-circle"></i>
                        <strong>Tip:</strong> Use your own email to receive the test email immediately.
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Configuration -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fa fa-cogs"></i> Email Configuration</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Current email driver and settings:
                    </p>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tr>
                                <td class="fw-semibold">Mail Driver:</td>
                                <td>
                                    <span class="badge bg-info">{{ config('mail.default') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">From Address:</td>
                                <td><code>{{ config('mail.from.address') }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">From Name:</td>
                                <td><code>{{ config('mail.from.name') }}</code></td>
                            </tr>
                            @if(config('mail.default') === 'smtp')
                            <tr>
                                <td class="fw-semibold">Host:</td>
                                <td><code>{{ config('mail.mailers.smtp.host') }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Port:</td>
                                <td><code>{{ config('mail.mailers.smtp.port') }}</code></td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-semibold">Queue Status:</td>
                                <td>
                                    @if(config('queue.default') === 'sync')
                                        <span class="badge bg-warning">Synchronous (no queue)</span>
                                    @else
                                        <span class="badge bg-success">{{ config('queue.default') }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="alert alert-info small mb-0">
                        <i class="fa fa-info-circle"></i>
                        <strong>Note:</strong> Emails are configured with the settings in your <code>.env</code> file.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testing Checklist -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm bg-light">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fa fa-list-check"></i> Email Testing Checklist</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Verification Email Checklist:</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check1">
                                <label class="form-check-label" for="check1">
                                    Email received in inbox
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check2">
                                <label class="form-check-label" for="check2">
                                    Site logo displays correctly
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check3">
                                <label class="form-check-label" for="check3">
                                    "Verify Email Address" button visible
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check4">
                                <label class="form-check-label" for="check4">
                                    Professional design and layout
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Functional Testing:</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check5">
                                <label class="form-check-label" for="check5">
                                    Email link works (24-hour expiry shown)
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check6">
                                <label class="form-check-label" for="check6">
                                    Clicking link verifies the account
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check7">
                                <label class="form-check-label" for="check7">
                                    User can login after verification
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check8">
                                <label class="form-check-label" for="check8">
                                    Email appears on multiple email clients
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Troubleshooting Guide -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fa fa-wrench"></i> Troubleshooting Guide</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="troubleshootingAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#troubleshoot1">
                                    <strong>Email not received?</strong>
                                </button>
                            </h2>
                            <div id="troubleshoot1" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                                <div class="accordion-body">
                                    <ul class="mb-0">
                                        <li>Check spam/junk folder first</li>
                                        <li>Verify email configuration in <code>.env</code> file</li>
                                        <li>Check SMTP credentials (host, port, username, password)</li>
                                        <li>Ensure "mail.from.address" is configured</li>
                                        <li>Check server/hosting logs for email errors</li>
                                        <li>For Gmail: Enable "Less secure app access" or use App Passwords</li>
                                        <li>Run: <code>php artisan queue:work</code> if using queue</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#troubleshoot2">
                                    <strong>Logo not displaying in email?</strong>
                                </button>
                            </h2>
                            <div id="troubleshoot2" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                                <div class="accordion-body">
                                    <ul class="mb-0">
                                        <li>Check logo files exist: <code>/public/assets/images/logo.png</code> or <code>.svg</code></li>
                                        <li>Set logo in Homepage Settings → Site Builder → Branding</li>
                                        <li>Ensure file permissions allow reading (644 for files)</li>
                                        <li>Try different logo format (PNG, SVG, etc.)</li>
                                        <li>Logo should be under 100KB for best email compatibility</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#troubleshoot3">
                                    <strong>Email layout broken or styling issues?</strong>
                                </button>
                            </h2>
                            <div id="troubleshoot3" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                                <div class="accordion-body">
                                    <ul class="mb-0">
                                        <li>Check email client compatibility (Gmail, Outlook, Apple Mail)</li>
                                        <li>Email CSS is inline - check <code>/resources/views/emails/verify-email.blade.php</code></li>
                                        <li>Some email clients strip certain CSS properties</li>
                                        <li>Use email testing tools like: Litmus, Email on Acid</li>
                                        <li>Test in multiple email clients before going live</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#troubleshoot4">
                                    <strong>Queue or async issues?</strong>
                                </button>
                            </h2>
                            <div id="troubleshoot4" class="accordion-collapse collapse" data-bs-parent="#troubleshootingAccordion">
                                <div class="accordion-body">
                                    <ul class="mb-0">
                                        <li>If using database queue: <code>php artisan queue:table && php artisan migrate</code></li>
                                        <li>Start queue worker: <code>php artisan queue:work</code></li>
                                        <li>For production, use: <code>supervisord</code> or similar process manager</li>
                                        <li>Check queue failed jobs: <code>php artisan queue:failed</code></li>
                                        <li>Retry failed jobs: <code>php artisan queue:retry</code></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-responsive {
        border-radius: 6px;
    }

    .accordion-button:not(.collapsed) {
        background-color: #e7f3ff;
        color: #0c5aa0;
    }
</style>
@endsection

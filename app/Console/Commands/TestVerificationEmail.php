<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestVerificationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-verification {email?}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Send a test email verification email to verify setup is working correctly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('Enter email address to test');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address provided.');
            return 1;
        }

        $this->info("Preparing to send test verification email to: $email");

        try {
            // Try to find existing user or create a test one
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $this->ask('Enter name for this user', 'Test User'),
                    'user_type' => 'student',
                    'password' => \Illuminate\Support\Facades\Hash::make('TestPassword123!'),
                ]
            );

            $this->info("User created/retrieved: {$user->name} ({$user->email})");

            // Send verification notification
            $user->sendEmailVerificationNotification();

            $this->info('✅ Verification email sent successfully!');
            $this->info('Check your email inbox for the verification message.');
            $this->warn("\nℹ️  The email should contain:");
            $this->warn('  • Your site logo');
            $this->warn('  • A verification button');
            $this->warn('  • Your site name');
            $this->warn('  • Link expiration info (24 hours)');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send verification email: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}

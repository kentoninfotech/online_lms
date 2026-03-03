<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmailTestController extends Controller
{
    /**
     * Show email testing dashboard
     */
    public function index()
    {
        return view('admin.email-testing.index');
    }

    /**
     * Send test verification email
     */
    public function sendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
        ]);

        try {
            // Create or get test user
            $user = User::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name,
                    'user_type' => 'student',
                    'password' => Hash::make('TestPassword123!'),
                ]
            );

            // Reset email_verified_at to test verification flow
            $user->update(['email_verified_at' => null]);

            // Send verification notification
            $user->sendEmailVerificationNotification();

            return redirect()->back()
                ->with('success', 'Verification email sent successfully to ' . $request->email . '. Check your inbox!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Send test contact response email
     */
    public function sendContactResponseEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            \Illuminate\Support\Facades\Mail::send('emails.contact-response', [], function ($mail) use ($request) {
                $mail->to($request->email)
                    ->subject('We Received Your Message - ' . config('app.name'));
            });

            return redirect()->back()
                ->with('success', 'Contact response email sent to ' . $request->email);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * View email configuration
     */
    public function viewConfig()
    {
        $config = [
            'driver' => config('mail.default'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'host' => config('mail.' . config('mail.default') . '.host') ?? 'N/A',
            'port' => config('mail.' . config('mail.default') . '.port') ?? 'N/A',
        ];

        return view('admin.email-testing.config', compact('config'));
    }
}

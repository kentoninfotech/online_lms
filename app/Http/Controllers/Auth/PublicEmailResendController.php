<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Notifications\VerifyEmailNotification;

class PublicEmailResendController extends Controller
{
    /**
     * Resend verification email for public/unauthenticated users
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        \Log::info('Email resend request received', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
        ]);

        // Validate the email
        try {
            $validated = $request->validate([
                'email' => ['required', 'email', 'exists:users,email'],
            ]);
        } catch (ValidationException $e) {
            \Log::warning('Email validation failed', [
                'email' => $request->input('email'),
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        // Find the user
        $user = User::where('email', $validated['email'])->first();
        
        if (!$user) {
            \Log::warning('User not found for email resend', [
                'email' => $validated['email'],
            ]);
            return back()
                ->withInput()
                ->with('error', 'User not found. Please check your email address.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            \Log::info('User already verified', ['user_id' => $user->id, 'email' => $user->email]);
            return back()->with('info', 'Your email is already verified. Please log in to your account.');
        }

        // Send verification notification
        try {
            \Log::info('Sending verification email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'timestamp' => now(),
            ]);

            // Directly send the notification
            $user->notify(new VerifyEmailNotification());
            
            \Log::info('Verification email sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            
            // Redirect to verification notice with email
            return redirect()->route('verification.notice')
                ->with('email', $user->email)
                ->with('resent', true)
                ->with('warning', 'A fresh verification link has been sent to your email address.');
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to send verification email: ' . $e->getMessage() . '. Please try again later.');
        }
    }
}

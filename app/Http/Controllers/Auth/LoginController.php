<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        // $this->middleware('auth')->only('logout');
    }

    /**
     * The user has been authenticated.
     * Check email verification status and update timezone.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            auth()->logout();
            return redirect()->route('verification.notice')
                ->with('email', $user->email)
                ->withInput(['email' => $user->email])
                ->with('warning', 'Please verify your email address before accessing your dashboard.');
        }

        // Get timezone from multiple sources (priority order):
        // 1. Request header (from AJAX/XHR)
        // 2. Form input (hidden field added by JavaScript)
        // 3. Session
        $timezone = $request->header('X-User-Timezone') 
                    ?? $request->input('timezone')
                    ?? session('user_timezone');
        
        // Validate and update timezone if valid
        if ($timezone && $this->isValidTimezone($timezone) && $timezone !== ($user->timezone ?? config('app.timezone'))) {
            $user->update(['timezone' => $timezone]);
            
            \Log::info('User timezone saved on login', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'timezone' => $timezone,
                'source' => $request->header('X-User-Timezone') ? 'header' : 'form',
            ]);
        }
        
        // Store timezone in session for immediate use
        session(['user_timezone' => $user->timezone ?? config('app.timezone')]);
    }

    /**
     * Validate if timezone is valid
     */
    private function isValidTimezone($timezone): bool
    {
        try {
            new \DateTimeZone($timezone);
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    protected function redirectTo()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('instructor')) {
            return route('instructor.dashboard');
        }

        if ($user->hasRole('student')) {
            return route('student.dashboard');
        }

        if ($user->hasRole('parent')) {
            return route('parent.dashboard');
        }

        return '/'; // fallback
    }

}

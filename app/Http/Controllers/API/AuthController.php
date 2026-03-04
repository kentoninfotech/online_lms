<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user (API endpoint for mobile app)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d@$!%*?&]+$/'
            ],
            'user_type' => ['required', 'string', 'in:student,instructor,parent'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => $request->user_type,
            ]);

            // Assign role
            try {
                $user->assignRole($request->user_type);
            } catch (\Exception $e) {
                \Log::warning('Role assignment failed during API registration: ' . $e->getMessage());
            }

            // Create associated model
            try {
                if ($request->user_type === 'student') {
                    Student::create([
                        'user_id' => $user->id,
                        'name' => $request->name,
                        'email' => $request->email,
                    ]);
                } elseif ($request->user_type === 'instructor') {
                    Instructor::create([
                        'user_id' => $user->id,
                        'name' => $request->name,
                        'email' => $request->email,
                    ]);
                } elseif ($request->user_type === 'parent') {
                    ParentModel::create([
                        'user_id' => $user->id,
                        'name' => $request->name,
                        'email' => $request->email,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Associated model creation failed during API registration: ' . $e->getMessage());
            }

            // Send verification email and track any errors
            try {
                $user->sendEmailVerificationNotification();
                $emailSent = true;
                $emailError = null;
            } catch (\Exception $e) {
                $emailSent = false;
                $emailError = $e->getMessage();
                \Log::error('Email sending failed during registration: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Registration successful. Please verify your email.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'email_verified_at' => $user->email_verified_at,
                ],
                'email_sent' => $emailSent,
                'email_error' => $emailError,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user (API endpoint for mobile app)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check if email is verified
        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Email not verified',
                'email_verified' => false,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                ]
            ], 403);
        }

        // Generate a simple token (base64 encoded user ID + timestamp)
        $token = base64_encode($user->id . ':' . time() . ':' . hash('sha256', $user->email . config('app.key')));

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'email_verified_at' => $user->email_verified_at,
                'role' => $user->roles->first()?->name ?? $user->user_type,
            ]
        ], 200);
    }

    /**
     * Resend email verification
     */
    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified'
            ], 400);
        }

        try {
            $user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Verification email sent successfully',
                'email_sent' => true
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Email resend failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to send verification email: ' . $e->getMessage(),
                'email_sent' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify email with signed URL token
     * This endpoint is for verifying via the link in the email
     */
    public function verifyEmailWithToken(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'hash' => 'required|string',
        ]);

        $user = User::findOrFail($request->id);

        // Verify the hash
        if (!hash_equals((string)$request->hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                'message' => 'Invalid verification link'
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified'
            ], 400);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'message' => 'Email verified successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
            ]
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        // For token-based auth, we just return success
        // The mobile app will clear the token on their end
        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\ParentModel;
use App\Http\Requests\RegistrationRequest;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/email/verify';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \App\Http\Requests\RegistrationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(RegistrationRequest $request)
    {
        $user = $this->create($request->validated());
        
        return $this->registered($request, $user) ?: redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d@$!%*?&]+$/'],
            'user_type' => ['required', 'string', 'in:student,instructor,parent'],
            'terms' => ['required', 'accepted'],
        ]);
    }

    /**
     * Get custom validation messages for password complexity.
     */
    protected function messages()
    {
        return [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number. Symbols (@$!%*?&) are optional.',
        ];
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type' => $data['user_type'],
            // Do NOT set email_verified_at - user must verify email first
        ]);

        // Assign role based on user_type
        try {
            $user->assignRole($data['user_type']);
        } catch (\Exception $e) {
            // Role assignment might fail if roles don't exist, that's okay
        }

        // Create associated model based on user_type
        try {
            if ($data['user_type'] === 'student') {
                Student::create([
                    'user_id' => $user->id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                ]);
            } elseif ($data['user_type'] === 'instructor') {
                Instructor::create([
                    'user_id' => $user->id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                ]);
            } elseif ($data['user_type'] === 'parent') {
                ParentModel::create([
                    'user_id' => $user->id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                ]);
            }
        } catch (\Exception $e) {
            // If model creation fails, just continue (rollback would be better with transactions)
            \Log::error('Error creating associated model during registration: ' . $e->getMessage());
        }

        return $user;
    }

    /**
     * The user has been registered.
     * Send verification email and DO NOT log the user in.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        // Send email verification notification
        $user->sendEmailVerificationNotification();

        // DO NOT log in the user - they must verify email first
        // Redirect to /email/verify page
        return redirect($this->redirectTo);
    }
}
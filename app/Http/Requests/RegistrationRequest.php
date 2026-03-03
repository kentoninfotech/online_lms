<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d@$!%*?&]+$/'],
            'user_type' => ['required', 'string', 'in:student,instructor,parent'],
            'terms' => ['required', 'accepted'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'password.required' => 'Password is required.',
            'password.string' => 'Password must be a string.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Passwords do not match.',
            'password.regex' => $this->getPasswordErrorMessage($this->get('password')),
        ];
    }

    /**
     * Get detailed password validation error message.
     *
     * @param  string  $password
     * @return string
     */
    protected function getPasswordErrorMessage($password = ''): string
    {
        if (empty($password)) {
            return 'Password is required.';
        }

        $violations = [];

        if (!preg_match('/[A-Z]/', $password)) {
            $violations[] = 'at least one uppercase letter (A-Z)';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $violations[] = 'at least one lowercase letter (a-z)';
        }

        if (!preg_match('/\d/', $password)) {
            $violations[] = 'at least one number (0-9)';
        }

        if (strlen($password) < 8) {
            $violations[] = 'minimum 8 characters (currently ' . strlen($password) . ')';
        }

        if (!empty($violations)) {
            return 'Password is missing: ' . implode(', ', $violations) . '.';
        }

        return 'Password does not meet the security requirements.';
    }
}

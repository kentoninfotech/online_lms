<?php

namespace App\Http\Requests\Abstracts;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin'); // only admin can create
    }

    /**
     * Common validation rules for user creation and update.
     */
    public function commonRules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:8|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d@$!%*?&]+$/',
            'number'   => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255'
        ];
    }

    /**
     * Custom error messages for password validation.
     */
    public function messages(): array
    {
        return [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number. Symbols (@$!%*?&) are optional.',
        ];
    }
}

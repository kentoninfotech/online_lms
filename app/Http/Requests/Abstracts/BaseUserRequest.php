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
            'password' => 'nullable|string|min:6',
            'number'   => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255'
        ];
    }
}

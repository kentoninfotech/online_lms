<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Let policies/guards handle actual authorization
        return true;
    }

    public function rules(): array
    {
        $role = $this->route('role'); // comes from route {role}
        $user   = $this->route('user');   // user

        // Common rules
        $rules = [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ];

        if ($role === 'student') {
            $rules += [
                'address'        => 'nullable|string|max:255',
                'number'         => 'nullable|string|max:20',
            ];
        }

        if ($role === 'parent') {
            $rules += [
                'address'        => 'nullable|string|max:255',
                'number'         => 'nullable|string|max:20',
                // 'occupation'     => 'nullable|string|max:255',
            ];
        }

        if ($role === 'instructor') {
            $rules += [
                'address'        => 'nullable|string|max:255',
                'number'         => 'nullable|string|max:20',
                // 'specialization' => 'nullable|string|max:255',
                // 'bio'            => 'nullable|string|max:1000',
            ];
        }

        // Admin profile could be just name/email

        return $rules;
    }
}

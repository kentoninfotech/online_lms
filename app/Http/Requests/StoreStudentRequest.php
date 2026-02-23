<?php

namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Abstracts\BaseUserRequest;

class StoreStudentRequest extends BaseUserRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge($this->commonRules(), [
            'parent_id' => 'nullable|exists:users,id',
            'dob'       => 'nullable|date',
            'gender'    => 'nullable|in:male,female',
        ]);
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'price'              => 'required|numeric|min:1',
            'duration_type'      => 'required|in:none,daily,weekly,monthly',
            'duration_count'     => 'nullable|integer|min:1',
            'reschedule_limit'   => 'nullable|integer',
            'payment_grace_days' => 'nullable|integer',
            'features'           => 'nullable|string|max:500',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
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
            'subject'          => 'required|string|max:255',
            'student_id'       => 'required|exists:students,id',
            'instructor_id'    => 'required|exists:instructors,id',
            'start_time'       => 'required|date',
            'duration_minutes' => 'required|integer|min:15',
            'recurrence_type'  => 'required|in:none,daily,weekly,monthly',
            'recurrence_meta'  => 'nullable|array',

            // Common recurrence meta
            'interval'         => 'required_if:recurrence_type,daily,weekly,monthly|integer|min:1',
            'end_type'         => 'required_if:recurrence_type,daily,weekly,monthly|in:count,date',

            // Count-based endings
            'count'            => 'required_if:end_type,count|integer|min:1',

            // Date-based endings
            'end_date'         => [
                'nullable',
                'required_if:end_type,date',
                'date',
                'after_or_equal:start_time',
            ],

            // Weekly-specific options
            'days'             => 'required_if:recurrence_type,weekly|array',
            'days.*'           => 'in:mon,tue,wed,thu,fri,sat,sun',

            // Monthly-specific options
            'monthly_mode'     => 'nullable|in:day,weekday',
        ];

    }
}

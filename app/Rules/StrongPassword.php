<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/', $value);
    }

    /**
     * Get the validation error message with specific violations.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return string
     */
    public function message()
    {
        // This will be called when validation fails
        // The detailed errors are shown on the frontend
        return 'Password must contain at least one uppercase letter, one lowercase letter, and one number (minimum 8 characters).';
    }

    /**
     * Get detailed violation messages for the password.
     *
     * @param  string  $password
     * @return array
     */
    public static function violations($password): array
    {
        $violations = [];

        if (!preg_match('/[A-Z]/', $password)) {
            $violations[] = 'Missing uppercase letter (A-Z)';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $violations[] = 'Missing lowercase letter (a-z)';
        }

        if (!preg_match('/\d/', $password)) {
            $violations[] = 'Missing number (0-9)';
        }

        if (strlen($password) < 8) {
            $violations[] = 'Must be at least 8 characters (currently ' . strlen($password) . ')';
        }

        return $violations;
    }
}

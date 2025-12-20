<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class CheckPassword implements ValidationRule
{
    /**
     * Create a new rule instance.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Hash::check($value, auth('api')->user()->password)) {
            $fail(__('messages.old_password_is_not_correct'));
        }
    }
}

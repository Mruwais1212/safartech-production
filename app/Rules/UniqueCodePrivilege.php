<?php

namespace App\Rules;

use App\Models\Panel\Privilege;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueCodePrivilege implements ValidationRule
{
    /**
     * Create a new rule instance.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $privilege_id = request()->sub_privilege['id'][explode('.', $attribute)[1]];

        $controller_name = request()->sub_privilege['controller'][explode('.', $attribute)[1]];

        $method_name = request()->sub_privilege['method'][explode('.', $attribute)[1]];

        $privilege_key = explode('.', $attribute)[1] + 1;

        $check_code = $privilege_id ? ! Privilege::where('id', '!=', $privilege_id)->where('code', $value)->exists() : ! Privilege::where('code', $value)->exists();

        $check_method_name = $privilege_id ? ! Privilege::where('id', '!=', $privilege_id)->where('controller', $controller_name)->where('method', $method_name)->exists() : ! Privilege::where('controller', $controller_name)->where('method', $method_name)->exists();

        if (! ($check_code && $check_method_name)) {
            $fail('dashboard.privilege_used_before', ['attribute' => __('dashboard.privilege_name_ar'), 'key' => $privilege_key]);
        }
    }
}

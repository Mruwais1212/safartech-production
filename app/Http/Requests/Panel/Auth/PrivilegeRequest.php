<?php

namespace App\Http\Requests\Panel\Auth;

use App\Rules\UniqueCodePrivilege;
use Illuminate\Foundation\Http\FormRequest;

class PrivilegeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $request_method = request()->getMethod() == 'POST';

        $id = request()->isMethod('POST') ?: array_reverse(explode('/', request()->getRequestUri()))[0];

        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:255'],
            'controller' => ['required', 'string', 'max:255'],
            'method' => ['nullable'],
            'type' => ['nullable', 'integer'],
            'icon' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
            'sort' => ['nullable', 'integer'],
            'status' => ['nullable', 'integer'],

            'code' => ['required', 'string', 'max:255', $request_method ? 'unique:privileges' :
                'unique:privileges,code,'.$id],

            'sub_privilege' => ['nullable', 'array'],
            'sub_privilege.*.name_ar' => ['required', 'string', 'max:255'],
            'sub_privilege.*.name_en' => ['required', 'string', 'max:255'],
            'sub_privilege.*.url' => ['nullable', 'string', 'max:255'],
            'sub_privilege.*.controller' => ['required', 'string', 'max:255'],
            'sub_privilege.*.method' => ['required', 'string', 'max:255'],
            'sub_privilege.*.type' => ['nullable', 'integer'],
            'sub_privilege.*.icon' => ['nullable', 'string', 'max:255'],
            'sub_privilege.*.parent_id' => ['nullable', 'integer'],
            'sub_privilege.*.sort' => ['nullable', 'integer'],
            'sub_privilege.*.status' => ['nullable', 'integer'],
            'sub_privilege.*.code' => ['required', 'string', 'max:255', new UniqueCodePrivilege],
        ];
    }

    public function attributes()
    {
        return [
            'name_ar' => __('dashboard.privilege_name_ar'),
            'name_en' => __('dashboard.privilege_name_en'),
            'url' => __('dashboard.privilege_url'),
            'controller' => __('dashboard.privilege_controller'),
            'method' => __('dashboard.privilege_method'),
            'type' => __('dashboard.privilege_type'),
            'icon' => __('dashboard.privilege_icon'),
            'parent_id' => __('dashboard.privilege_parent_id'),
            'sort' => __('dashboard.privilege_sort'),
            'status' => __('dashboard.privilege_status'),
            'sub_privilege' => __('dashboard.sub_privilege'),
            'code' => __('dashboard.privilege_code'),
            'sub_privilege.*.code' => __('dashboard.sub_privilege_code'),
        ];
    }

    public function prepareForValidation()
    {
        $sub_privilege = [];
        if (request()->sub_privilege) {
            for ($i = 0; $i < count(request()->sub_privilege['name_ar']); $i++) {
                foreach (request()->sub_privilege as $key => $value) {
                    if (request()->sub_privilege[$key][$i] != null && request()->sub_privilege[$key][$i] != '') {
                        $sub_privilege[$i][$key] = request()->sub_privilege[$key][$i];
                    }
                }
                if (count($sub_privilege[$i]) < count(request()->sub_privilege) - 1) {
                    unset($sub_privilege[$i]);
                }
            }
        }

        $this->merge(
            [
                'sub_privilege' => $sub_privilege,
                'type' => $this->get('type', 2),
            ]
        );
    }
}

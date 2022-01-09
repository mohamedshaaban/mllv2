<?php

namespace Backpack\PermissionManager\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreCrudRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return ['password'=>'Password Length must be at least 6 characters and must contain numbers and capital letter'];
    }
        public function rules()

    {

        return [
            'name'    => 'required|regex:/^[a-zA-Z0-9]+$/u|unique:'.config('permission.table_names.users', 'users').',name',
            'password' => 'required|string|min:6|regex:/[a-z]/|regex:/[0-9]/|confirmed',
            'roles_show' => 'required',
            'commission' =>'required_if:roles_show,4|numeric|nullable|min:0',

        ];
    }
}

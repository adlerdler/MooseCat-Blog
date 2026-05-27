<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
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
        $roleId = $this->route('role')?->id;
        
        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $roleId,
            'value' => 'nullable|string|max:255|unique:roles,value,' . $roleId,
            'guard_name' => 'required|string|in:web,api',
            'label' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ];
    }
}

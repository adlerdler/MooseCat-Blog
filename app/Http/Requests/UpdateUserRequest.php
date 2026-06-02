<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')?->id;
        
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'password' => ['nullable', 'string', 'min:8', 'max:15', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+\-=\[\]{};:\'",.<>\/\\|~`])/'],
            'status' => 'nullable|string|in:active,inactive',
            'level_id' => 'nullable|exists:user_levels,id',
            'points' => 'nullable|integer|min:0',
            'role_id' => 'nullable|exists:roles,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            // author_profiles 字段（nullable，基础用户编辑不强制填）
            'display_name' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|string|max:2048',
            'role_title' => 'nullable|string|max:255',
            'role_label' => 'nullable|string|max:255',
            'status_label' => 'nullable|string|max:255',
            'status_text' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'social_links' => 'nullable|array',
            'skills' => 'nullable|array',
            'manifestos' => 'nullable|array',
            'expertise' => 'nullable|array',
            'slug' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->sometimes('password', ['required', 'string', 'min:8', 'max:15', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+\-=\[\]{};:\'",.<>\/\\|~`])/'], function ($input) {
            return isset($input->password) && $input->password !== '';
        });
    }

    public function messages(): array
    {
        return [
            'password.regex' => '密码必须包含大写字母、小写字母、数字和特殊符号',
        ];
    }
}

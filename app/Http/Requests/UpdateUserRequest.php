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
            'password' => 'nullable|string|min:8',
            'avatar' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'github' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:active,inactive',
            'level_id' => 'nullable|exists:user_levels,id',
            'role_id' => 'nullable|exists:roles,id',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'social_links' => 'nullable|array',
            'skills' => 'nullable|array',
            'manifestos' => 'nullable|array',
            'expertise' => 'nullable|array',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->sometimes('password', 'required|string|min:8', function ($input) {
            return isset($input->password) && $input->password !== '';
        });
    }
}

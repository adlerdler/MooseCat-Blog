<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'      => ['required', 'exists:users,id'],
            'slug'         => ['required', 'string', 'unique:author_profiles,slug'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'bio'          => ['nullable', 'string'],
            'role_label'   => ['nullable', 'string', 'max:100'],
            'role_title'   => ['nullable', 'string', 'max:100'],
            'company'      => ['nullable', 'string', 'max:255'],
            'social_links' => ['nullable', 'array'],
            'skills'       => ['nullable', 'array'],
            'manifestos'   => ['nullable', 'array'],
        ];
    }
}

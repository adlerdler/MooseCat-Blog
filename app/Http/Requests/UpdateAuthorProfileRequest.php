<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAuthorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profileId = $this->route('profile')?->id ?? $this->route('profile');

        return [
            'slug'         => ['required', 'string', Rule::unique('author_profiles', 'slug')->ignore($profileId)],
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

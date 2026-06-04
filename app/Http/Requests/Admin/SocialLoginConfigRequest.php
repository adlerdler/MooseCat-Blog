<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SocialLoginConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:50'],
            'client_id'     => ['nullable', 'string', 'max:255'],
            'client_secret' => ['nullable', 'string'],
            'redirect_uri'  => ['nullable', 'string', 'max:255'],
            'enabled'       => ['boolean'],
            'extra_config'  => ['nullable', 'array'],
        ];
    }
}

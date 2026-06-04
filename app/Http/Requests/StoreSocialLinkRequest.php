<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocialLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform'   => ['required', 'string', 'max:255'],
            'url'        => ['required', 'url', 'max:500'],
            'icon'       => ['nullable', 'string', 'max:255'],
            'label'      => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active'  => ['boolean'],
        ];
    }
}

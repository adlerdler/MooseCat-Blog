<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSeoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page_key'    => ['required', 'string', 'unique:page_seo,page_key'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:500'],
            'keywords'    => ['nullable', 'string', 'max:500'],
            'og_image'    => ['nullable', 'url'],
        ];
    }
}

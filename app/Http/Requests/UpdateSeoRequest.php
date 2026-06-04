<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSeoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords'    => ['nullable', 'string'],
            'google_analytics' => ['nullable', 'string'],
            'baidu_analytics'  => ['nullable', 'string'],
            'canonical_url'    => ['nullable', 'url'],
            'og_image'         => ['nullable', 'url'],
            'og_type'          => ['nullable', 'string', 'max:50'],
            'twitter_card'     => ['nullable', 'string', 'max:50'],
            'sitemap'          => ['boolean'],
            'robots'           => ['boolean'],
            'llm_txt'          => ['boolean'],
        ];
    }
}

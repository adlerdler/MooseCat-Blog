<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdvertisementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'image_url' => ['sometimes', 'required', 'url', 'max:500'],
            'link_url' => ['sometimes', 'required', 'url', 'max:500'],
            'position_id' => ['nullable', 'integer', 'exists:ad_positions,id'],
            'is_active' => ['nullable', 'boolean'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '广告标题不能为空',
            'image_url.required' => '图片URL不能为空',
            'image_url.url' => '图片URL格式不正确',
            'link_url.required' => '链接URL不能为空',
            'link_url.url' => '链接URL格式不正确',
            'end_date.after_or_equal' => '结束时间不能早于开始时间',
        ];
    }
}
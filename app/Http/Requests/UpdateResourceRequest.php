<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'format' => ['nullable', 'string', 'max:50'],
            'file_size' => ['nullable', 'string', 'max:50'],
            'image' => ['nullable', 'string', 'max:500'],
            'direct_link' => ['nullable', 'string', 'max:500'],
            'drives' => ['nullable', 'array'],
            'drives.*.name' => ['required_with:drives', 'string'],
            'drives.*.url' => ['required_with:drives', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '资源标题不能为空',
            'description.required' => '资源描述不能为空',
            'category_id.exists' => '选择的分类不存在',
        ];
    }
}
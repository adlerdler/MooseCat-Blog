<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreResourceRequest - 创建资源表单验证
 * 
 * 验证创建资源时的输入数据。
 * Validates input data when creating resources.
 */
class StoreResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:resources,slug'],
            'description' => ['nullable', 'string', 'max:500'],
            'file_url' => ['required', 'string', 'max:500'],
            'file_size' => ['nullable', 'integer', 'min:0'],
            'download_count' => ['nullable', 'integer', 'min:0'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '资源标题不能为空',
            'slug.unique' => '该slug已被占用',
            'file_url.required' => '文件URL不能为空',
            'category_id.exists' => '选择的分类不存在',
        ];
    }
}
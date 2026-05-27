<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreProjectRequest - 创建项目表单验证
 * 
 * 验证创建项目时的输入数据。
 * Validates input data when creating projects.
 */
class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:projects,slug'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:500'],
            'project_url' => ['nullable', 'string', 'max:500'],
            'repo_url' => ['nullable', 'string', 'max:500'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'status' => ['nullable', 'in:draft,published'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '项目名称不能为空',
            'slug.unique' => '该slug已被占用',
            'category_id.exists' => '选择的分类不存在',
            'tags.*.exists' => '选择的标签不存在',
        ];
    }
}
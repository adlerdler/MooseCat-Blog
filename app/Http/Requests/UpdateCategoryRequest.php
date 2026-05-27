<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateCategoryRequest - 更新分类表单验证
 * 
 * 验证更新分类时的输入数据。
 * Validates input data when updating categories.
 */
class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id ?? $this->route('category');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'description' => ['nullable', 'string', 'max:500'],
            'parent_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->whereNot('id', $categoryId)],
            'order' => ['nullable', 'integer', 'min:0'],
            'cover_image' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '分类名称不能为空',
            'name.max' => '分类名称不能超过100个字符',
            'slug.unique' => '该slug已被占用',
            'parent_id.exists' => '选择的父分类不存在',
        ];
    }
}
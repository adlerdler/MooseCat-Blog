<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'parent_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->whereNot('id', $categoryId)],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '分类名称不能为空',
            'slug.unique' => '该slug已被占用',
            'parent_id.exists' => '选择的父分类不存在',
        ];
    }
}
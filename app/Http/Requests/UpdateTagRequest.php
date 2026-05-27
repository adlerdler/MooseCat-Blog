<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateTagRequest - 更新标签表单验证
 * 
 * 验证更新标签时的输入数据。
 * Validates input data when updating tags.
 */
class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tagId = $this->route('tag')?->id ?? $this->route('tag');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:50'],
            'slug' => ['nullable', 'string', 'max:50', Rule::unique('tags', 'slug')->ignore($tagId)],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '标签名称不能为空',
            'name.max' => '标签名称不能超过50个字符',
            'slug.unique' => '该slug已被占用',
        ];
    }
}
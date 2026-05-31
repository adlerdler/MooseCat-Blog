<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreSubscriberRequest - 创建订阅者表单验证
 * 
 * 验证创建订阅者时的输入数据。
 * Validates input data when creating subscribers.
 */
class StoreSubscriberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255', 'unique:subscribers,email'],
            'name' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => '邮箱地址不能为空',
            'email.email' => '请输入有效的邮箱地址',
            'email.unique' => '该邮箱已存在',
        ];
    }
}
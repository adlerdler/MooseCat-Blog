<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * SubscribeRequest - 订阅表单验证
 * 
 * 验证订阅邮箱时的输入数据。
 * Validates input data when subscribing with email.
 */
class SubscribeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255', 'unique:subscribers,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => '邮箱地址不能为空',
            'email.email' => '请输入有效的邮箱地址',
            'email.unique' => '该邮箱已订阅',
        ];
    }
}
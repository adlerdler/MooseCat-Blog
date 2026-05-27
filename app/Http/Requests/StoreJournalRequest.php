<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreJournalRequest - 创建日志表单验证
 * 
 * 验证创建日志时的输入数据。
 * Validates input data when creating journals.
 */
class StoreJournalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'mood' => ['nullable', 'integer', 'min:1', 'max:5'],
            'weather' => ['nullable', 'string', 'max:50'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'is_public' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => '日志内容不能为空',
            'mood.min' => '心情指数最小为1',
            'mood.max' => '心情指数最大为5',
        ];
    }
}
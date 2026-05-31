<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_approved' => ['nullable', 'boolean'],
            'body' => ['sometimes', 'required', 'string', 'min:1', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => '评论内容不能为空',
            'body.min' => '评论内容至少1个字符',
            'body.max' => '评论内容不能超过2000个字符',
        ];
    }
}
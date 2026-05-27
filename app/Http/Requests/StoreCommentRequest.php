<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreCommentRequest - 创建评论表单验证
 * 
 * 验证创建评论时的输入数据。
 * Validates input data when creating comments.
 */
class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:1', 'max:2000'],
            'commentable_type' => ['required', 'string', 'in:App\Models\Post,App\Models\Video,App\Models\Project'],
            'commentable_id' => ['required', 'integer'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => '评论内容不能为空',
            'body.min' => '评论内容至少1个字符',
            'body.max' => '评论内容不能超过2000个字符',
            'commentable_type.in' => '评论对象类型无效',
            'commentable_id.required' => '评论对象ID不能为空',
            'parent_id.exists' => '父评论不存在',
        ];
    }
}
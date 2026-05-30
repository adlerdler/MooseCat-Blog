<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdatePostRequest - 更新文章表单验证
 * 
 * 验证更新文章时的输入数据，允许部分字段更新。
 * Validates input data when updating posts, allows partial field updates.
 */
class UpdatePostRequest extends FormRequest
{
    /**
     * 判断用户是否有权限提交此请求
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 获取验证规则
     * Get the validation rules that apply to the request
     */
    public function rules(): array
    {
        $postId = $this->route('post')?->id ?? $this->route('post');

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('posts', 'slug')->ignore($postId)],
            'content' => ['sometimes', 'required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
            'cover_image' => ['nullable', 'string', 'max:500'],
            'status' => ['nullable', 'in:draft,published,scheduled'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['nullable', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * 获取自定义验证消息
     * Get custom messages for validator errors
     */
    public function messages(): array
    {
        return [
            'title.required' => '文章标题不能为空',
            'title.max' => '文章标题不能超过255个字符',
            'content.required' => '文章内容不能为空',
            'slug.unique' => '该slug已被占用',
            'category_id.exists' => '选择的分类不存在',
            'tags.*.exists' => '选择的标签不存在',
        ];
    }
}
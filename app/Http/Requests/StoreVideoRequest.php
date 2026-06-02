<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreVideoRequest - 创建视频表单验证
 * 
 * 验证创建视频时的输入数据。
 * Validates input data when creating videos.
 */
class StoreVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:videos,slug'],
            'description' => ['nullable', 'string'],
            'video_id' => ['nullable', 'string', 'max:255'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'platform' => ['nullable', 'string', 'in:youtube,bilibili,local'],
            'cover_image' => ['nullable', 'string', 'max:500'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
            'status' => ['nullable', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '视频标题不能为空',
            'video_url.required' => '视频URL不能为空',
            'slug.unique' => '该slug已被占用',
            'category_id.exists' => '选择的分类不存在',
            'tags.*.exists' => '选择的标签不存在',
        ];
    }
}
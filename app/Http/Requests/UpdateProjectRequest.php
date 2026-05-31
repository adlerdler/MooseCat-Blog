<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateProjectRequest - 更新项目表单验证
 * 
 * 验证更新项目时的输入数据。
 * Validates input data when updating projects.
 */
class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'long_description' => ['nullable', 'string'],
            'client' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'year' => ['sometimes', 'integer', 'min:2000', 'max:2100'],
            'image' => ['nullable', 'string', 'max:500'],
            'url' => ['nullable', 'string', 'max:500'],
            'github_url' => ['nullable', 'string', 'max:500'],
            'technologies' => ['nullable', 'array'],
            'technologies.*' => ['string', 'max:100'],
            'status' => ['sometimes', 'required', 'in:planning,in-progress,completed'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '项目标题不能为空',
            'description.required' => '项目描述不能为空',
            'year.min' => '年份不能小于2000',
            'year.max' => '年份不能大于2100',
            'status.required' => '项目状态不能为空',
            'status.in' => '状态必须是 planning、in-progress 或 completed',
        ];
    }
}
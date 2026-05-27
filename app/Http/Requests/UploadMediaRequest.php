<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:jpeg,jpg,png,gif,webp,mp4,pdf|max:51200',
            'name' => 'nullable|string|max:255',
            'collection' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => '请选择要上传的文件',
            'file.file' => '上传的必须是文件',
            'file.mimes' => '文件格式必须是 jpeg, jpg, png, gif, webp, mp4, pdf',
            'file.max' => '文件大小不能超过 50MB',
        ];
    }
}

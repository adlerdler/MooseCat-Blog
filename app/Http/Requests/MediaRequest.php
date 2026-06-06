<?php

namespace App\Http\Requests;

use App\Services\SettingService;
use Illuminate\Foundation\Http\FormRequest;

class MediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $settingService = app(SettingService::class);
        $maxSize = $settingService->get('max_upload_size', 10);
        $fileTypesArray = $settingService->get('file_types', [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf',
            'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'txt', 'csv', 'zip', 'rar', 'mp4', 'mp3'
        ]);
        $fileTypes = is_array($fileTypesArray) ? implode(',', $fileTypesArray) : $fileTypesArray;
        $maxKb = (int)$maxSize * 1024;

        return [
            'file' => "required|file|mimes:{$fileTypes}|max:{$maxKb}",
            'name' => 'nullable|string|max:255',
            'collection' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        $settingService = app(SettingService::class);
        $maxSize = $settingService->get('max_upload_size', 10);
        $fileTypesArray = $settingService->get('file_types', [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf',
            'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'txt', 'csv', 'zip', 'rar', 'mp4', 'mp3'
        ]);
        $fileTypes = is_array($fileTypesArray) ? implode(', ', $fileTypesArray) : $fileTypesArray;

        return [
            'file.required' => '请选择要上传的文件',
            'file.file' => '上传的必须是文件',
            'file.mimes' => "文件格式必须是: {$fileTypes}",
            'file.max' => "文件大小不能超过 {$maxSize}MB",
        ];
    }
}

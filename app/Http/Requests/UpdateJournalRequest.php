<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJournalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['sometimes', 'required', 'string'],
            'mood' => ['nullable', 'string', 'max:50'],
            'weather' => ['nullable', 'string', 'max:50'],
            'date' => ['nullable', 'date'],
            'is_public' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => '日志内容不能为空',
        ];
    }
}
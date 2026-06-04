<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'unique:email_templates,name'],
            'subject'     => ['required', 'string'],
            'content'     => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ];
    }
}

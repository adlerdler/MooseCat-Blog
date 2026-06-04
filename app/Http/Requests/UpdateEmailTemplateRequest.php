<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['sometimes', 'required', 'string', 'unique:email_templates,name,' . $this->route('id')],
            'subject'     => ['sometimes', 'required', 'string'],
            'content'     => ['sometimes', 'required', 'string'],
            'description' => ['nullable', 'string'],
        ];
    }
}

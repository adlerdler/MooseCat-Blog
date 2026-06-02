<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateI18nRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'sometimes|string|max:100',
            'native_name' => 'nullable|string|max:100',
            'flag'        => 'nullable|string|max:20',
            'file_path'   => 'nullable|string|max:200',
            'direction'   => 'nullable|string|max:3',
            'is_default'  => 'boolean',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer',
        ];
    }
}

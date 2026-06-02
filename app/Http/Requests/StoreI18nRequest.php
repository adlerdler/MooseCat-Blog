<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreI18nRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'        => 'required|string|max:10|unique:languages,code',
            'name'        => 'required|string|max:100',
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

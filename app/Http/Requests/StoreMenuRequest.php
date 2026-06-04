<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'           => ['required', 'string', 'in:front,admin'],
            'label_key'      => ['required', 'string', 'max:255'],
            'path'           => ['nullable', 'string', 'max:500'],
            'icon_name'      => ['nullable', 'string', 'max:255'],
            'component_name' => ['nullable', 'string', 'max:255'],
            'parent_id'      => ['nullable', 'integer', 'exists:menus,id'],
            'sort_order'     => ['nullable', 'integer'],
            'is_active'      => ['boolean'],
        ];
    }
}

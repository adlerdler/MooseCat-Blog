<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'level' => 'required|integer|unique:user_levels,level',
            'min_points' => 'required|integer|min:0',
            'max_points' => 'nullable|integer|gte:min_points',
            'discount' => 'nullable|integer|min:0|max:100',
            'color' => 'required|string|max:50',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'benefits' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }
}

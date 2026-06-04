<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMailConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'host'        => ['required', 'string'],
            'port'        => ['required', 'integer'],
            'username'    => ['required', 'string'],
            'encryption'  => ['nullable', 'string'],
            'fromAddress' => ['required', 'email'],
            'fromName'    => ['required', 'string'],
            'password'    => ['nullable', 'string'],
            'driver'      => ['nullable', 'string'],
        ];
    }
}

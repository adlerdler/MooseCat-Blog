<?php

namespace App\Http\Requests\Auth;

use App\Services\CaptchaService;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
            'captcha'           => ['required', 'string'],
            'verification_code' => ['required', 'string', 'size:6'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $captchaService = app(CaptchaService::class);
            $result = $captchaService->check($this->captcha);
            if (! $result['valid']) {
                $validator->errors()->add('captcha', $result['message'] ?: __('auth.invalid_captcha'));
            }
        });
    }

    public function messages(): array
    {
        return [
            'verification_code.required'    => __('auth.invalid_code'),
            'verification_code.size'        => __('auth.invalid_code'),
        ];
    }
}

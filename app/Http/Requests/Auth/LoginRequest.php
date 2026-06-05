<?php

namespace App\Http\Requests\Auth;

use App\Services\CaptchaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    protected CaptchaService $captchaService;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'captcha'  => ['required', 'string', 'max:4'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->captchaService = app(CaptchaService::class);
            if (! $this->captchaService->check($this->captcha)) {
                throw ValidationException::withMessages([
                    'captcha' => [__('auth.invalid_captcha')],
                ]);
            }
        });
    }
}

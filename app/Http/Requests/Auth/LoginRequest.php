<?php

namespace App\Http\Requests\Auth;

use App\Services\CaptchaService;
use Illuminate\Foundation\Http\FormRequest;

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
                $validator->errors()->add('captcha', __('auth.invalid_captcha'));
            }
        });
    }

    /**
     * 仅返回可用于 Auth::attempt 的字段（captcha 不得参与用户表查询）
     */
    public function credentials(): array
    {
        return $this->safe()->only(['email', 'password']);
    }
}

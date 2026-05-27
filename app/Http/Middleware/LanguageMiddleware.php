<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageMiddleware
{
    /**
     * 支持的语言列表
     *
     * @var array
     */
    protected $supportedLocales = ['zh_CN', 'en', 'ja'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $this->getLocale($request);
        
        if (in_array($locale, $this->supportedLocales)) {
            App::setLocale($locale);
            $this->setLocaleCookie($request, $locale);
        } else {
            App::setLocale(config('app.fallback_locale'));
        }

        return $next($request);
    }

    /**
     * 获取当前语言设置
     *
     * @param Request $request
     * @return string
     */
    protected function getLocale(Request $request): string
    {
        // 1. 优先从请求参数获取
        if ($request->has('lang')) {
            Session::put('locale', $request->get('lang'));
            return $request->get('lang');
        }

        // 2. 从 Session 获取
        if (Session::has('locale')) {
            return Session::get('locale');
        }

        // 3. 从 Cookie 获取
        if ($request->hasCookie('locale')) {
            return $request->cookie('locale');
        }

        // 4. 从浏览器语言偏好获取
        $browserLang = $request->getPreferredLanguage($this->supportedLocales);
        if ($browserLang) {
            return $browserLang;
        }

        // 5. 使用默认语言
        return config('app.locale');
    }

    /**
     * 设置语言 Cookie
     *
     * @param Request $request
     * @param string $locale
     * @return void
     */
    protected function setLocaleCookie(Request $request, string $locale): void
    {
        if (!$request->hasCookie('locale') || $request->cookie('locale') !== $locale) {
            $response = \Illuminate\Support\Facades\Response::make();
            $response->withCookie(cookie()->forever('locale', $locale));
        }
    }
}
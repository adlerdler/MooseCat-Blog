<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SocialLoginConfigRequest;
use App\Services\SocialLoginService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SocialLoginController extends Controller
{
    protected SocialLoginService $socialLoginService;

    public function __construct(SocialLoginService $socialLoginService)
    {
        $this->socialLoginService = $socialLoginService;
    }

    /**
     * 后台管理页面：列出所有 Provider 配置
     */
    public function index(): Response
    {
        return Inertia::render('admin/SocialLogin', [
            'configs' => $this->socialLoginService->getAllConfigs(),
        ]);
    }

    /**
     * 更新单个 Provider 配置
     */
    public function update(SocialLoginConfigRequest $request, string $provider): RedirectResponse
    {
        $this->socialLoginService->updateConfig($provider, $request->validated());

        return back()->with('success', __('admin.social_login_updated'));
    }

    /**
     * 测试 OAuth 配置是否完整
     */
    public function test(string $provider): RedirectResponse
    {
        $config = $this->socialLoginService->getConfig($provider);
        if (!$config || !$config->client_id || !$config->client_secret) {
            return back()->with('error', __('admin.social_login_incomplete'));
        }

        return back()->with('success', __('admin.social_login_configured'));
    }
}

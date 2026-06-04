<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Jobs\SendEmailJob;
use App\Models\AuthorProfile;
use App\Models\SocialAccount;
use App\Models\User;
use App\Services\CaptchaService;
use App\Services\MailService;
use App\Services\PointsService;
use App\Services\SocialLoginService;
use App\Services\UserLevelService;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;

class FrontendAuthController extends Controller
{
    protected SocialLoginService $socialLoginService;
    protected MailService $mailService;
    protected PointsService $pointsService;
    protected UserLevelService $userLevelService;
    protected CaptchaService $captchaService;

    public function __construct(
        SocialLoginService $socialLoginService,
        MailService $mailService,
        PointsService $pointsService,
        UserLevelService $userLevelService,
        CaptchaService $captchaService,
    ) {
        $this->socialLoginService = $socialLoginService;
        $this->mailService = $mailService;
        $this->pointsService = $pointsService;
        $this->userLevelService = $userLevelService;
        $this->captchaService = $captchaService;
    }

    /**
     * 为用户创建 authorProfile（如果尚不存在）
     */
    protected function ensureAuthorProfile(User $user, ?string $avatar = null): AuthorProfile
    {
        $profile = $user->authorProfile;

        if (!$profile) {
            $profile = $user->authorProfile()->create([
                'slug'         => $this->generateProfileSlug($user),
                'display_name' => $user->name,
                'avatar'       => $avatar,
                'is_active'    => true,
            ]);
        } elseif ($avatar && !$profile->avatar) {
            $profile->update(['avatar' => $avatar]);
        }

        return $profile;
    }

    /**
     * 生成唯一 slug（匿名格式 Ar_xxxxxxxxxxxx，与后台 AuthorProfileService 一致）
     */
    protected function generateProfileSlug(User $user): string
    {
        do {
            $slug = 'Ar_' . Str::random(12);
        } while (AuthorProfile::where('slug', $slug)->exists());

        return $slug;
    }

    /**
     * 登录页面（Inertia）
     */
    public function showLogin(): Response
    {
        return Inertia::render('front/Auth', [
            'mode'      => 'login',
            'providers' => $this->socialLoginService->getEnabledProviders(),
        ]);
    }

    /**
     * 处理邮箱密码登录
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->withInput($request->except('password'));
    }

    /**
     * 注册页面（Inertia）
     */
    public function showRegister(): Response
    {
        return Inertia::render('front/Auth', [
            'mode'      => 'register',
            'providers' => $this->socialLoginService->getEnabledProviders(),
        ]);
    }

    /**
     * 获取/刷新验证码图片（JSON API）
     */
    public function captcha(): JsonResponse
    {
        return response()->json([
            'captcha' => $this->captchaService->create(),
        ]);
    }

    /**
     * 发送邮箱验证码
     */
    public function sendVerificationCode(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $email = strtolower(trim($request->email));

            // 60秒发送频率限制
            $cooldownKey = "email_code_cooldown:{$email}";
            if (Cache::has($cooldownKey)) {
                $remaining = Cache::ttl($cooldownKey);
                return response()->json([
                    'message' => __('auth.code_send_too_frequent', ['s' => $remaining]),
                    'errors'  => ['email' => [__('auth.code_send_too_frequent', ['s' => $remaining])]],
                ], 422);
            }

            // 生成6位验证码，5分钟有效
            $code = (string) random_int(100000, 999999);
            Cache::put("email_code:{$email}", $code, now()->addMinutes(5));
            Cache::put($cooldownKey, true, now()->addSeconds(60));

            // 发送邮件（afterResponse 异步，避免阻塞验证码接口响应）
            $brandName = $this->getBrandName();
            dispatch(new SendEmailJob(
                $email,
                "Your Verification Code | {$brandName}",
                view('emails.verification-code', [
                    'brandName' => $brandName,
                    'code'      => $code,
                    'timestamp' => now()->format('Y-m-d H:i'),
                ])->render()
            ))->afterResponse();

        return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('sendVerificationCode failed', [
                'email'   => $request->email ?? 'unknown',
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 处理邮箱密码注册
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        // 校验图片验证码
        if (! $this->captchaService->check($request->captcha)) {
            throw ValidationException::withMessages([
                'captcha' => [__('auth.invalid_captcha')],
            ]);
        }

        // 校验邮箱验证码
        $email = strtolower(trim($request->email));
        $storedCode = Cache::get("email_code:{$email}");

        if (! $storedCode || $storedCode !== $request->verification_code) {
            throw ValidationException::withMessages([
                'verification_code' => [__('auth.invalid_code')],
            ]);
        }

        // 验证通过后清除验证码
        Cache::forget("email_code:{$email}");

        // 创建用户，邮箱已验证
        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'email_verified_at' => now(),
            'status'            => 'active',
        ]);

        // 分配 Guest 角色（零权限，无法访问后台）
        $user->assignRole('Guest');

        // 分配默认新手等级（避免后续 awardPoints 时触发虚假的"null→新手"升级通知）
        $defaultLevel = $this->userLevelService->getDefaultLevel();
        if ($defaultLevel) {
            $user->update(['level_id' => $defaultLevel->id]);
        }

        // 创建 authorProfile（avatar 为空 → accessor 自动生成 identicon）
        $this->ensureAuthorProfile($user);

        // 注册积分奖励 +40
        $this->pointsService->awardPoints(
            user: $user,
            points: 40,
            type: 'register',
            description: '注册奖励',
        );

        // 发送欢迎邮件
        $this->sendWelcomeEmail($user, null);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    /**
     * 构建 Socialite Provider（本地开发时可配置代理访问 Google 等被墙服务，
     * 生产环境部署在美国服务器则无需设置代理）
     */
    protected function buildSocialProvider(string $providerClass, array $config): AbstractProvider
    {
        $provider = Socialite::buildProvider($providerClass, $config);

        $provider->setHttpClient(new GuzzleClient([
            'timeout'         => 15,
            'connect_timeout' => 10,
            'verify'          => false,
        ]));

        return $provider;
    }

    /**
     * OAuth 重定向到第三方平台
     */
    public function redirect(string $provider): RedirectResponse
    {
        $config = $this->socialLoginService->getConfig($provider);

        if (! $config) {
            Log::warning("[OAuth] Provider [{$provider}] not found or disabled.");
            abort(404, "Provider [{$provider}] not found or disabled.");
        }

        $providerMap = [
            'google' => \Laravel\Socialite\Two\GoogleProvider::class,
            'github' => \Laravel\Socialite\Two\GithubProvider::class,
            'apple'  => \Laravel\Socialite\Two\AppleProvider::class,
        ];

        $providerClass = $providerMap[$provider] ?? null;
        if (! $providerClass) {
            Log::warning("[OAuth] Unsupported provider [{$provider}].");
            abort(400, "Unsupported provider [{$provider}].");
        }

        $redirectUri = $this->socialLoginService->getRedirectUri($provider);
        Log::info("[OAuth] Redirecting to {$provider}", [
            'redirect_uri' => $redirectUri,
            'client_id'    => substr($config->client_id, 0, 20) . '...',
        ]);

        return $this->buildSocialProvider($providerClass, [
            'client_id'     => $config->client_id,
            'client_secret' => $config->client_secret,
            'redirect'      => $redirectUri,
        ])->redirect();
    }

    /**
     * OAuth 回调处理
     */
    public function callback(string $provider): RedirectResponse
    {
        Log::info("[OAuth] Callback received for {$provider}", [
            'query' => request()->query(),
        ]);

        $config = $this->socialLoginService->getConfig($provider);

        if (!$config) {
            Log::warning("[OAuth] Callback: config not found for {$provider}");
            abort(404, "Provider [{$provider}] not found or disabled.");
        }

        // 构建 Provider 映射（后续可扩展）
        $providerMap = [
            'google' => \Laravel\Socialite\Two\GoogleProvider::class,
            'github' => \Laravel\Socialite\Two\GithubProvider::class,
            'apple'  => \Laravel\Socialite\Two\AppleProvider::class,
        ];

        $providerClass = $providerMap[$provider] ?? null;
        if (! $providerClass) {
            Log::warning("[OAuth] Unsupported provider in callback [{$provider}].");
            abort(400, "Unsupported provider [{$provider}].");
        }

        try {
            $redirectUri = $this->socialLoginService->getRedirectUri($provider);
            Log::info("[OAuth] Building provider for callback", [
                'provider'      => $provider,
                'redirect_uri'  => $redirectUri,
                'client_id'     => substr($config->client_id, 0, 20) . '...',
            ]);

            $socialUser = $this->buildSocialProvider($providerClass, [
                'client_id'     => $config->client_id,
                'client_secret' => $config->client_secret,
                'redirect'      => $redirectUri,
            ])->user();

            Log::info("[OAuth] User retrieved from {$provider}", [
                'id'    => $socialUser->getId(),
                'email' => $socialUser->getEmail(),
                'name'  => $socialUser->getName(),
            ]);
        } catch (\Exception $e) {
            Log::error("[OAuth] Failed to get user from {$provider}", [
                'message'   => $e->getMessage(),
                'class'     => get_class($e),
                'code'      => $e->getCode(),
                'trace'     => $e->getTraceAsString(),
            ]);
            return redirect('/login')->withErrors([
                'email' => __('auth.social_failed', ['provider' => ucfirst($provider)]),
            ]);
        }

        // 1. 查 social_accounts 是否已绑定
        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            Auth::login($socialAccount->user);
            request()->session()->regenerate();
            return redirect()->intended('/');
        }

        // 2. 查 users 表是否有该邮箱
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // 已有账号 → 绑定 social_accounts
            $user->socialAccounts()->create([
                'provider'      => $provider,
                'provider_id'   => $socialUser->getId(),
                'provider_data' => [
                    'name'   => $socialUser->getName(),
                    'avatar' => $socialUser->getAvatar(),
                    'email'  => $socialUser->getEmail(),
                ],
            ]);
        } else {
            // 3. 全新用户 → 创建 user + social_accounts + authorProfile + Guest 角色
            $user = User::create([
                'name'              => $socialUser->getName() ?? $socialUser->getEmail(),
                'email'             => $socialUser->getEmail(),
                'password'          => Hash::make(Str::random(32)),
                'email_verified_at' => now(), // OAuth 三方已验证邮箱，无需二次验证
                'status'            => 'active',
            ]);

            $user->assignRole('Guest');

            // 分配默认新手等级
            $defaultLevel = $this->userLevelService->getDefaultLevel();
            if ($defaultLevel) {
                $user->update(['level_id' => $defaultLevel->id]);
            }

            // 创建 authorProfile（OAuth 头像由第三方提供）
            $this->ensureAuthorProfile($user, $socialUser->getAvatar());

            $user->socialAccounts()->create([
                'provider'      => $provider,
                'provider_id'   => $socialUser->getId(),
                'provider_data' => [
                    'name'   => $socialUser->getName(),
                    'avatar' => $socialUser->getAvatar(),
                    'email'  => $socialUser->getEmail(),
                ],
            ]);

            // 注册积分奖励 +40（OAuth 新用户）
            $this->pointsService->awardPoints(
                user: $user,
                points: 40,
                type: 'register',
                description: '注册奖励（通过 ' . ucfirst($provider) . ' 登录）',
            );

            // 发送欢迎邮件（标注注册来源）
            $this->sendWelcomeEmail($user, $provider);
        }

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->intended('/');
    }

    /**
     * 发送密码重置链接（支持用户名或邮箱，通过数据库 SMTP 配置发送）
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'credential' => 'required|string',
        ]);

        $credential = $request->credential;

        // 智能识别：包含 @ 视为邮箱，否则视为用户名
        if (str_contains($credential, '@')) {
            // 输入的是邮箱 → 直接查 email
            $user = User::where('email', $credential)->first();
        } else {
            // 输入的是用户名 → 按 name 查
            $user = User::where('name', $credential)->first();
        }

        if (!$user) {
            return back()->withInput()->with('user_not_found', true);
        }

        $email = $user->email;

        $token = Password::createToken($user);
        $resetUrl = url(route('front.password.reset', [
            'token' => $token,
            'email' => $email,
        ], false));

        $brandName = $this->getBrandName();
        $subject = "Reset Password | {$brandName}";

        // 通过 Blade 视图渲染 HTML，对齐项目其他邮件风格
        $htmlBody = view('emails.password-reset', [
            'brandName'     => $brandName,
            'userName'      => $user->name ?? $user->email,
            'resetUrl'      => $resetUrl,
            'expireMinutes' => config('auth.passwords.users.expire', 60),
            'timestamp'     => now()->format('Y-m-d H:i'),
        ])->render();

        // 响应后异步发送
        dispatch(new SendEmailJob($email, $subject, $htmlBody))->afterResponse();

        return back()->with('status', __('auth.reset_link_sent'));
    }

    /**
     * 密码重置表单（从邮件链接进入）
     */
    public function showResetForm(string $token): Response
    {
        return Inertia::render('front/ResetPassword', [
            'token' => $token,
            'email' => request('email', ''),
        ]);
    }

    /**
     * 处理密码重置
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // 重置成功，跳转到登录页并显示成功消息
            return redirect()->route('front.login')->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    /**
     * 登出
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * 个人中心（需登录）
     */
    public function profile(string $slug): Response
    {
        $profile = AuthorProfile::where('slug', $slug)->where('is_active', true)->first();

        if (! $profile) {
            abort(404);
        }

        $user = $profile->user->loadCount('interactions')->load('authorProfile');

        $likedItems = $user->interactions()
            ->where('type', 'like')
            ->with('interactable')
            ->latest()
            ->paginate(20);

        return Inertia::render('front/UserProfile', [
            'user'       => $user,
            'likedItems' => $likedItems,
        ]);
    }

    /**
     * 获取站点名称（优先数据库 Settings 表，其次 .env APP_NAME）
     */
    protected function getBrandName(): string
    {
        $settingName = \App\Models\Setting::value('name');
        return $settingName ?: config('app.name', 'ARCHYX');
    }

    /**
     * 发送注册欢迎邮件
     */
    protected function sendWelcomeEmail(User $user, ?string $provider = null): void
    {
        $brandName = $this->getBrandName();
        $subject = "Welcome to {$brandName}";

        $htmlBody = view('emails.welcome', [
            'brandName' => $brandName,
            'userName'  => $user->name,
            'siteUrl'   => url('/'),
            'provider'  => $provider,
            'timestamp' => now()->format('Y-m-d H:i'),
        ])->render();

        dispatch(new SendEmailJob($user->email, $subject, $htmlBody))->afterResponse();
    }
}

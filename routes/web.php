<?php

use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SocialLinksController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Api\V1\SubscribeController;
use App\Http\Controllers\Admin\I18nController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\EmailTemplatesController;
use App\Http\Controllers\Admin\JournalsController;
use App\Http\Controllers\Admin\FrontMenuController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\MailConfigController;
use App\Http\Controllers\Admin\LogsController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\RestoreController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\SubscribersController;
use App\Http\Controllers\Admin\UserLevelsController;
use App\Http\Controllers\Admin\CommentsController;
use App\Http\Controllers\Admin\AdvertisementsController;
use App\Http\Controllers\Admin\AuthorProfileController;
use App\Http\Controllers\Admin\SocialLoginController;
use App\Http\Controllers\Auth\FrontendAuthController;
use App\Http\Controllers\Web\FrontendController;
use App\Http\Controllers\Web\PostController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\CommentController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\LikeController;
use App\Http\Controllers\Web\SitemapController;
use App\Http\Controllers\Web\RobotsController;
use App\Http\Controllers\Web\LlmTxtController;
use App\Http\Controllers\Web\FeedController;
use App\Models\AuthorProfile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

// 测试路由（不涉及数据库）
Route::get('/test', function () {
    return Inertia::render('front/Test', [
        'message' => 'Inertia.js is working!'
    ]);
});

// SEO 基础设施路由（始终可用，不受维护模式影响）
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/robots.txt', [RobotsController::class, 'index']);
Route::get('/llm.txt', [LlmTxtController::class, 'index']);
Route::get('/feed', [FeedController::class, 'index'])->name('feed');
Route::get('/rss.xml', [FeedController::class, 'index']);

// 前台公共路由（受维护模式控制）
Route::middleware(['maintenance'])->group(function () {
    // 前台页面路由（Inertia.js）
    Route::get('/', [FrontendController::class, 'home'])->name('home');
    Route::get('/blog', [FrontendController::class, 'blog'])->name('blog');
    Route::get('/projects', [FrontendController::class, 'projects'])->name('projects');
    Route::get('/projects/{project:slug}', [FrontendController::class, 'projectDetail'])->name('projects.detail');
    Route::get('/resources', [FrontendController::class, 'resources'])->name('resources');
    Route::post('/resources/{resource}/download-track', [FrontendController::class, 'trackDownload'])->name('resources.download-track');
    // 自定义视频路由绑定：slug 优先，纯数字 ID 兜底
    Route::bind('video', function ($value) {
        $video = \App\Models\Video::where('slug', $value)->first();
        if ($video) return $video;
        if (is_numeric($value)) {
            $video = \App\Models\Video::find($value);
            if ($video) return $video;
        }
        abort(404);
    });
    Route::get('/videos', [FrontendController::class, 'videos'])->name('videos');
    Route::get('/videos/{video}', [FrontendController::class, 'videoDetail'])->name('videos.detail');
    Route::get('/blog/{post:slug}', [FrontendController::class, 'postDetail'])->name('posts.detail');
    Route::get('/author', function () {
        $profile = AuthorProfile::where('is_active', true)->first();
        if ($profile) {
            return redirect('/author/' . $profile->slug);
        }
        abort(404);
    });
    Route::get('/author/{slug}', [FrontendController::class, 'author'])->name('author');

    // 文章相关路由
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

    // 订阅（网站公开，无需认证）
    Route::post('/subscribe', [SubscribeController::class, 'subscribe'])->name('subscribe');

    // 点赞相关路由
    Route::post('/likes/toggle', [LikeController::class, 'toggle'])->name('likes.toggle');

    // 评论相关路由
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

    // 分类相关路由
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

    // 标签相关路由
    Route::get('/tags/{tag:slug}', [CategoryController::class, 'tag'])->name('tags.show');

    // 全局搜索 API
    Route::get('/api/search', [SearchController::class, 'search'])->name('api.search');
});

// ============================================================
// 前台认证路由（Inertia.js 渲染）
// ============================================================

// 未登录用户（guest）— 登录/注册/OAuth
Route::middleware(['maintenance', 'guest', 'registration'])->group(function () {
    Route::get('/login', [FrontendAuthController::class, 'showLogin'])->name('front.login');
    Route::post('/login', [FrontendAuthController::class, 'login'])->name('front.login.handle');
    Route::get('/register', [FrontendAuthController::class, 'showRegister'])->name('front.register');
    Route::post('/register', [FrontendAuthController::class, 'register'])->name('front.register.handle');
    Route::get('/auth/{provider}/redirect', [FrontendAuthController::class, 'redirect'])->name('front.social.redirect');
    Route::get('/auth/{provider}/callback', [FrontendAuthController::class, 'callback'])->name('front.social.callback');
    Route::post('/forgot-password', [FrontendAuthController::class, 'sendResetLink'])->name('front.password.email');
    Route::get('/reset-password/{token}', [FrontendAuthController::class, 'showResetForm'])->name('front.password.reset');
    Route::post('/reset-password', [FrontendAuthController::class, 'reset'])->name('front.password.update');
});

// 已登录用户（auth）— 个人中心/登出
Route::middleware(['maintenance', 'auth', 'registration'])->group(function () {
    Route::post('/logout', [FrontendAuthController::class, 'logout'])->name('front.logout');
    Route::get('/profile/{slug}', [FrontendAuthController::class, 'profile'])->name('front.profile');
});

// 验证码 & 邮箱验证码 API（公开，维护模式下也需可用）
Route::middleware(['maintenance'])->group(function () {
    Route::get('/api/captcha', [FrontendAuthController::class, 'captcha'])->name('api.captcha');
    Route::post('/api/send-verification-code', [FrontendAuthController::class, 'sendVerificationCode'])
        ->name('api.send-verification-code')
        ->middleware('throttle:3,1');
});

// 媒体文件服务路由（UUID 访问，无需认证）
Route::get('/media/{uuid}.{ext}', function ($uuid, $ext) {
    $media = SpatieMedia::findByUuid($uuid);
    if (!$media) abort(404);

    $disk = Storage::disk($media->disk);
    $path = $media->getPathRelativeToRoot();

    if (!$disk->exists($path)) abort(404);

    return response()->file($disk->path($path), [
        'Content-Type' => $media->mime_type ?? 'application/octet-stream',
    ]);
});

// 管理后台登录路由
Route::get('/admin/login', [DashboardController::class, 'login'])->name('login')->middleware('guest');
Route::post('/admin/login', [DashboardController::class, 'handleLogin'])->name('login.handle')->middleware('guest');

// 管理后台路由（需要认证）
Route::middleware(['auth'])->prefix('admin')->group(function () {
    // 登出
    Route::post('/logout', [DashboardController::class, 'logout'])->name('logout');
    // 首页
    Route::get('/', fn () => redirect('/admin/index'))->name('admin');
    Route::get('/index', [DashboardController::class, 'index']);
    
    // 无权限页面（通过正常 Inertia 流程渲染）
    Route::get('/forbidden', function () {
        return Inertia::render('Forbidden', [
            'title' => '访问被拒绝',
            'message' => '抱歉，您没有权限访问此页面。如需获取访问权限，请联系管理员。',
        ]);
    })->name('admin.forbidden');
    // 常规选项
    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/themes', [SettingsController::class, 'storeTheme'])->name('admin.settings.themes.store');
    Route::put('/settings/themes/{id}', [SettingsController::class, 'updateTheme'])->name('admin.settings.themes.update');
    Route::delete('/settings/themes/{id}', [SettingsController::class, 'deleteTheme'])->name('admin.settings.themes.destroy');
    Route::get('/social-links', [SocialLinksController::class, 'index'])->name('admin.social-links');
    Route::post('/social-links', [SocialLinksController::class, 'store'])->name('admin.social-links.store');
    Route::put('/social-links', [SocialLinksController::class, 'update'])->name('admin.social-links.update');
    Route::put('/social-links/{id}', [SocialLinksController::class, 'update'])->name('admin.social-links.update-item');
    Route::delete('/social-links/{id}', [SocialLinksController::class, 'destroy'])->name('admin.social-links.destroy');
    Route::get('/seo', [SeoController::class, 'index'])->name('admin.seo');
    Route::put('/seo', [SeoController::class, 'update'])->name('admin.seo.update');
    Route::post('/seo/page-seo', [SeoController::class, 'storePageSeo'])->name('admin.seo.page-seo.store');
    Route::put('/seo/page-seo/{pageSeo}', [SeoController::class, 'updatePageSeo'])->name('admin.seo.page-seo.update');
    Route::delete('/seo/page-seo/{pageSeo}', [SeoController::class, 'destroyPageSeo'])->name('admin.seo.page-seo.destroy');
    Route::get('/i18n', [I18nController::class, 'index'])->name('admin.i18n');
    Route::put('/i18n/translations', [I18nController::class, 'saveTranslations'])->name('admin.i18n.translations.save');
    Route::post('/i18n/languages', [I18nController::class, 'addLanguage'])->name('admin.i18n.languages.store');
    Route::post('/i18n/upload-locale', [I18nController::class, 'uploadLocale'])->name('admin.i18n.upload-locale');
    Route::put('/i18n/languages/{code}', [I18nController::class, 'updateLanguage'])->name('admin.i18n.languages.update');
    Route::delete('/i18n/languages/{code}', [I18nController::class, 'deleteLanguage'])->name('admin.i18n.languages.destroy');
    Route::get('/media', [MediaController::class, 'index'])->name('admin.media');
    Route::post('/media', [MediaController::class, 'store'])->name('admin.media.store');
    Route::delete('/media/{media:uuid}', [MediaController::class, 'destroy'])->name('admin.media.destroy');
    Route::get('/email-templates', [EmailTemplatesController::class, 'index'])->name('admin.email-templates');
    Route::post('/email-templates', [EmailTemplatesController::class, 'store'])->name('admin.email-templates.store');
    Route::get('/email-templates/{id}/edit', [EmailTemplatesController::class, 'edit'])->name('admin.email-templates.edit');
    Route::put('/email-templates/{id}', [EmailTemplatesController::class, 'update'])->name('admin.email-templates.update');
    // 内容管理
    Route::resource('posts', AdminPostController::class)->names('admin.posts');
    Route::resource('videos', AdminVideoController::class)->names('admin.videos');
    Route::resource('projects', AdminProjectController::class)->names('admin.projects');
    Route::resource('resources', AdminResourceController::class)->names('admin.resources');
    Route::resource('categories', AdminCategoryController::class)->names('admin.categories');
    Route::resource('tags', AdminTagController::class)->names('admin.tags');
    Route::resource('journals', JournalsController::class)->names([
        'index' => 'admin.journals.index',
        'store' => 'admin.journals.store',
        'update' => 'admin.journals.update',
        'destroy' => 'admin.journals.destroy',
    ]);
    
    // 用户管理
    Route::get('/users', [UsersController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [UsersController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UsersController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{slug}', [UsersController::class, 'show'])->name('admin.users.show');
    Route::get('/users/{slug}/edit', [UsersController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [UsersController::class, 'update'])->name('admin.users.update');
    Route::patch('/users/{user}/status', [UsersController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('admin.users.destroy');
    Route::resource('subscribers', SubscribersController::class)->names([
        'index' => 'admin.subscribers.index',
        'store' => 'admin.subscribers.store',
        'update' => 'admin.subscribers.update',
        'destroy' => 'admin.subscribers.destroy',
    ]);
    Route::resource('user-levels', UserLevelsController::class)->names([
        'index' => 'admin.user-levels.index',
        'store' => 'admin.user-levels.store',
        'update' => 'admin.user-levels.update',
        'destroy' => 'admin.user-levels.destroy',
    ]);
    
    // 系统管理
    Route::resource('front-menu', FrontMenuController::class)->names([
        'index' => 'admin.front-menu.index',
        'store' => 'admin.front-menu.store',
        'update' => 'admin.front-menu.update',
        'destroy' => 'admin.front-menu.destroy',
    ]);
    Route::post('front-menu/batch-update', [FrontMenuController::class, 'batchUpdate'])->name('admin.front-menu.batch-update');
    Route::resource('roles', RolesController::class)->names([
        'index' => 'admin.roles.index',
        'store' => 'admin.roles.store',
        'update' => 'admin.roles.update',
        'destroy' => 'admin.roles.destroy',
    ]);
    // 社交登录管理
    Route::get('/social-login', [SocialLoginController::class, 'index'])->name('admin.social-login');
    Route::put('/social-login/{provider}', [SocialLoginController::class, 'update'])->name('admin.social-login.update');
    Route::post('/social-login/{provider}/test', [SocialLoginController::class, 'test'])->name('admin.social-login.test');
    Route::get('/notifications', [NotificationsController::class, 'index'])->name('admin.notifications');
    Route::post('/notifications', [NotificationsController::class, 'store'])->name('admin.notifications.store');
    Route::patch('/notifications/{id}/mark-as-read', [NotificationsController::class, 'markAsRead'])->name('admin.notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [NotificationsController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-as-read');
    Route::delete('/notifications/{id}', [NotificationsController::class, 'destroy'])->name('admin.notifications.destroy');
    Route::delete('/notifications', [NotificationsController::class, 'clear'])->name('admin.notifications.clear');
    Route::get('/mail-config', [MailConfigController::class, 'index'])->name('admin.mail-config');
    Route::put('/mail-config', [MailConfigController::class, 'update'])->name('admin.mail-config.update');
    Route::post('/mail-config/test', [MailConfigController::class, 'test'])->name('admin.mail-config.test');
    Route::get('/logs', [LogsController::class, 'index'])->name('admin.logs');
    Route::delete('/logs/{id}', [LogsController::class, 'destroy'])->name('admin.logs.destroy');
    Route::delete('/logs', [LogsController::class, 'clear'])->name('admin.logs.clear');
    Route::get('/backup', [BackupController::class, 'index'])->name('admin.backup');
    Route::post('/backup', [BackupController::class, 'create'])->name('admin.backup.create');
    Route::get('/backup/{id}/download', [BackupController::class, 'download'])->name('admin.backup.download');
    Route::delete('/backup/{id}', [BackupController::class, 'destroy'])->name('admin.backup.destroy');
    Route::get('/restore', [RestoreController::class, 'index'])->name('admin.restore');
    Route::post('/restore/{id}', [RestoreController::class, 'restore'])->name('admin.restore.execute');
    Route::get('/restore/{id}/preview', [RestoreController::class, 'preview'])->name('admin.restore.preview');
    Route::get('/about', [DashboardController::class, 'about'])->name('admin.about');
    
    // 其他管理
    Route::resource('comments', CommentsController::class)->only(['index', 'update', 'destroy']);
    Route::post('comments/{comment}/reply', [CommentsController::class, 'reply'])->name('admin.comments.reply');
    Route::resource('advertisements', AdvertisementsController::class);
    Route::resource('author-profiles', AuthorProfileController::class)->names([
        'index' => 'admin.author-profiles.index',
        'store' => 'admin.author-profiles.store',
        'update' => 'admin.author-profiles.update',
        'destroy' => 'admin.author-profiles.destroy',
    ]);

    // 头像上传
    Route::post('/profile/avatar/{slug}', [AuthorProfileController::class, 'uploadAvatar'])->name('admin.profile.avatar');

    // 个人资料页面（catch-all，放在所有具名路由之后）
    Route::get('/{slug}', [AuthorProfileController::class, 'profile'])->name('admin.profile');
    Route::put('/{slug}', [AuthorProfileController::class, 'updateProfile'])->name('admin.profile.update');
});
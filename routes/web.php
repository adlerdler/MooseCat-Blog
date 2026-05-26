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
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Web\PostController;
use App\Http\Controllers\Web\CommentController;
use App\Http\Controllers\Web\CategoryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// 测试路由（不涉及数据库）
Route::get('/test', function () {
    return Inertia::render('front/Test', [
        'message' => 'Inertia.js is working!'
    ]);
});

// 前台页面路由（Inertia.js）
Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/blog', [FrontendController::class, 'blog'])->name('blog');
Route::get('/projects', [FrontendController::class, 'projects'])->name('projects');
Route::get('/projects/{id}', [FrontendController::class, 'projectDetail'])->name('projects.detail');
Route::get('/resources', [FrontendController::class, 'resources'])->name('resources');
Route::get('/videos', [FrontendController::class, 'videos'])->name('videos');
Route::get('/videos/{id}', [FrontendController::class, 'videoDetail'])->name('videos.detail');
Route::get('/blog/{id}', [FrontendController::class, 'postDetail'])->name('posts.detail');
Route::get('/author', [FrontendController::class, 'author'])->name('author');

// 文章相关路由
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// 评论相关路由
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

// 分类相关路由
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// 标签相关路由
Route::get('/tags/{tag:slug}', [CategoryController::class, 'tag'])->name('tags.show');

// 管理后台登录路由
Route::get('/admin/login', [DashboardController::class, 'login'])->name('login');
Route::post('/admin/login', [DashboardController::class, 'handleLogin'])->name('login.handle');

// 管理后台路由（临时无认证）
Route::prefix('admin')->group(function () {
    // 首页
    Route::get('/', [DashboardController::class, 'index'])->name('admin');
    Route::get('/index', [DashboardController::class, 'index']);
    
    // 常规选项
    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
    Route::get('/social-links', [SocialLinksController::class, 'index'])->name('admin.social-links');
    Route::put('/social-links', [SocialLinksController::class, 'update'])->name('admin.social-links.update');
    Route::get('/seo', [SeoController::class, 'index'])->name('admin.seo');
    Route::put('/seo', [SeoController::class, 'update'])->name('admin.seo.update');
    Route::get('/i18n', [I18nController::class, 'index'])->name('admin.i18n');
    Route::put('/i18n', [I18nController::class, 'update'])->name('admin.i18n.update');
    Route::get('/media', [MediaController::class, 'index'])->name('admin.media');
    Route::post('/media', [MediaController::class, 'store'])->name('admin.media.store');
    Route::delete('/media/{id}', [MediaController::class, 'destroy'])->name('admin.media.destroy');
    Route::get('/email-templates', [EmailTemplatesController::class, 'index'])->name('admin.email-templates');
    Route::get('/email-templates/{id}/edit', [EmailTemplatesController::class, 'edit'])->name('admin.email-templates.edit');
    Route::put('/email-templates/{id}', [EmailTemplatesController::class, 'update'])->name('admin.email-templates.update');
    
    // 内容管理
    Route::resource('posts', AdminPostController::class);
    Route::resource('videos', AdminVideoController::class);
    Route::resource('projects', AdminProjectController::class);
    Route::resource('resources', AdminResourceController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('tags', AdminTagController::class);
    Route::resource('journals', JournalsController::class);
    
    // 用户管理
    Route::resource('users', UsersController::class);
    Route::resource('subscribers', SubscribersController::class)->only(['index', 'store', 'destroy']);
    Route::resource('user-levels', UserLevelsController::class);
    
    // 系统管理
    Route::resource('front-menu', FrontMenuController::class)->names([
        'index' => 'admin.front-menu.index',
        'store' => 'admin.front-menu.store',
        'update' => 'admin.front-menu.update',
        'destroy' => 'admin.front-menu.destroy',
    ]);
    Route::resource('roles', RolesController::class);
    Route::get('/notifications', [NotificationsController::class, 'index'])->name('admin.notifications');
    Route::patch('/notifications/{id}/mark-as-read', [NotificationsController::class, 'markAsRead'])->name('admin.notifications.mark-as-read');
    Route::delete('/notifications', [NotificationsController::class, 'clear'])->name('admin.notifications.clear');
    Route::get('/mail-config', [MailConfigController::class, 'index'])->name('admin.mail-config');
    Route::put('/mail-config', [MailConfigController::class, 'update'])->name('admin.mail-config.update');
    Route::post('/mail-config/test', [MailConfigController::class, 'test'])->name('admin.mail-config.test');
    Route::get('/logs', [LogsController::class, 'index'])->name('admin.logs');
    Route::delete('/logs', [LogsController::class, 'clear'])->name('admin.logs.clear');
    Route::get('/backup', [BackupController::class, 'index'])->name('admin.backup');
    Route::post('/backup', [BackupController::class, 'create'])->name('admin.backup.create');
    Route::get('/backup/{id}/download', [BackupController::class, 'download'])->name('admin.backup.download');
    Route::delete('/backup/{id}', [BackupController::class, 'destroy'])->name('admin.backup.destroy');
    Route::get('/restore', [RestoreController::class, 'index'])->name('admin.restore');
    Route::post('/restore/{id}', [RestoreController::class, 'restore'])->name('admin.restore.execute');
    Route::get('/about', [DashboardController::class, 'about'])->name('admin.about');
    
    // 其他管理
    Route::resource('comments', CommentsController::class)->only(['index', 'update', 'destroy']);
    Route::resource('advertisements', AdvertisementsController::class);
});
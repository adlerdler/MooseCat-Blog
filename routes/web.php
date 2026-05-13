<?php

use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\Web\PostController;
use App\Http\Controllers\Web\CommentController;
use App\Http\Controllers\Web\CategoryController;
use Illuminate\Support\Facades\Route;

// 前台页面路由（Blade + Vue 混合）
Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/blog', [FrontendController::class, 'blog'])->name('blog');
Route::get('/projects', [FrontendController::class, 'projects'])->name('projects');
Route::get('/resources', [FrontendController::class, 'resources'])->name('resources');
Route::get('/videos', [FrontendController::class, 'videos'])->name('videos');

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
Route::get('/admin/login', [FrontendController::class, 'adminLogin'])->name('login');

// 管理后台路由（需要认证）
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // 首页
    Route::get('/', [FrontendController::class, 'admin'])->name('admin');
    
    // 文章管理
    Route::resource('posts', AdminPostController::class);
    
    // 视频管理
    Route::resource('videos', AdminVideoController::class);
    
    // 项目管理
    Route::resource('projects', AdminProjectController::class);
    
    // 资源管理
    Route::resource('resources', AdminResourceController::class);
    
    // 分类管理
    Route::resource('categories', AdminCategoryController::class);
    
    // 标签管理
    Route::resource('tags', AdminTagController::class);
});

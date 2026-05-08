<?php

use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Web\PostController;
use App\Http\Controllers\Web\CommentController;
use App\Http\Controllers\Web\VideoController;
use App\Http\Controllers\Web\ProjectController;
use App\Http\Controllers\Web\ResourceController;
use App\Http\Controllers\Web\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// 文章相关路由
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// 评论相关路由
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

// 视频相关路由
Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
Route::get('/videos/{video}', [VideoController::class, 'show'])->name('videos.show');

// 项目相关路由
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

// 资源下载相关路由
Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
Route::get('/resources/{resource}', [ResourceController::class, 'show'])->name('resources.show');

// 分类相关路由
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// 标签相关路由
Route::get('/tags/{tag:slug}', [CategoryController::class, 'tag'])->name('tags.show');

// 管理后台路由（需要认证）
Route::prefix('admin')->middleware(['auth'])->group(function () {
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

<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ResourceController;
use App\Http\Controllers\Api\V1\RolesController;
use App\Http\Controllers\Api\V1\SocialLinkController;
use App\Http\Controllers\Api\V1\SubscribeController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VideoController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // 订阅管理
    Route::post('/subscribe', [SubscribeController::class, 'subscribe']);
    Route::post('/unsubscribe', [SubscribeController::class, 'unsubscribe']);
    
    // 作者信息
    Route::get('/authors', [AuthorController::class, 'index']);
    Route::get('/authors/{slug}', [AuthorController::class, 'show']);
    
    // 社交链接
    Route::get('/social-links', [SocialLinkController::class, 'index']);
    
    // 内容接口
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{post:slug}', [PostController::class, 'show']);

    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);

    Route::get('/videos', [VideoController::class, 'index']);
    Route::get('/videos/{video}', [VideoController::class, 'show']);

    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);

    Route::get('/resources', [ResourceController::class, 'index']);
    Route::get('/resources/{resource}', [ResourceController::class, 'show']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show']);

    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/{tag:slug}', [TagController::class, 'show']);

    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/roles', [RolesController::class, 'index']);
    Route::get('/roles/{role}', [RolesController::class, 'show']);
    Route::get('/permissions', [RolesController::class, 'permissions']);
    Route::put('/roles/{role}/permissions', [RolesController::class, 'syncPermissions']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

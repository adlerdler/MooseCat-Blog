<?php

use App\Http\Controllers\Web\PostController;
use App\Http\Controllers\Web\CommentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 文章相关路由
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// 评论相关路由
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

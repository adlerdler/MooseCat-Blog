<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Models\Subscriber;
use App\Models\User;
use App\Notifications\NewSubscriberNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscribeController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:subscribers,email',
                'name' => 'nullable|string|max:255',
            ]);

            // 未提供名称时，使用邮箱 @ 前面的部分作为默认用户名
            $name = $validated['name'] ?? null;
            if (!$name) {
                $name = explode('@', $validated['email'])[0] ?? '';
            }

            $subscriber = Subscriber::create([
                'email' => $validated['email'],
                'name' => $name,
                'source' => $request->is('api/*') ? 'api' : 'website',
                'subscribed_at' => now(),
            ]);

            Log::info('SubscribeController: subscriber created', [
                'id'    => $subscriber->id,
                'email' => $subscriber->email,
                'name'  => $subscriber->name,
            ]);

            // 管理员通知（使用队列异步执行，不阻塞响应）
            try {
                dispatch(function () use ($subscriber) {
                    $admin = User::role('admin')->first();
                    if (!$admin) {
                        return;
                    }

                    // 数据库通知
                    $admin->notify(new NewSubscriberNotification($subscriber));

                    // 邮件通知
                    if ($admin->email) {
                        $subject = "新订阅者 | {$subscriber->email}";
                        $htmlBody = view('emails.notification', [
                            'title'      => '新订阅者通知',
                            'message'    => "新订阅者 <strong>{$subscriber->email}</strong> ({$subscriber->name}) 已加入你的邮件列表。",
                            'detail'     => '订阅时间：' . ($subscriber->created_at ? $subscriber->created_at->format('Y-m-d H:i') : now()->format('Y-m-d H:i')),
                            'actionUrl'  => url('/admin/subscribers'),
                            'actionText' => '查看订阅者列表',
                            'brandName'  => config('app.name', 'Archyx'),
                            'timestamp'  => now()->format('Y-m-d H:i'),
                        ])->render();

                        dispatch(new SendEmailJob($admin->email, $subject, $htmlBody))->afterResponse();
                    }
                })->afterResponse();
            } catch (\Throwable $e) {
                // dispatch() 本身失败不影响订阅成功
                Log::warning('SubscribeController: admin notification dispatch failed', [
                    'error' => $e->getMessage(),
                    'email' => $subscriber->email,
                ]);
            }

            return response()->json([
                'message' => '订阅成功',
                'data'    => $subscriber,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // 验证失败（如邮箱重复）→ 交给 Laravel 默认处理
            throw $e;

        } catch (\Throwable $e) {
            // 捕获所有其他异常，记录日志并返回友好提示
            Log::error('SubscribeController: subscribe failed', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->input(),
            ]);

            return response()->json([
                'message' => '订阅失败，请稍后重试',
            ], 500);
        }
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:subscribers,email',
        ]);

        $subscriber = Subscriber::where('email', $validated['email'])->first();
        $subscriber->update(['is_active' => false]);

        return response()->json([
            'message' => '已取消订阅',
        ]);
    }
}

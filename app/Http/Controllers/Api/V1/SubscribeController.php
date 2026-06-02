<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Notifications\NewSubscriberNotification;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubscribeController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:subscribers,email',
            'name' => 'nullable|string|max:255',
        ]);

        $subscriber = Subscriber::create([
            'email' => $validated['email'],
            'name' => $validated['name'] ?? null,
            'source' => 'api',
            'subscribed_at' => now(),
        ]);

        $admin = User::role('admin')->first();
        if ($admin) {
            // 站内 database 通知
            $admin->notify(new NewSubscriberNotification($subscriber));

            // 邮件通知（通过 MailService 直连 SMTP）
            if ($admin->email) {
                $subject = "新订阅者 | {$subscriber->email}";
                $htmlBody = view('emails.notification', [
                    'title'      => '新订阅者通知',
                    'message'    => "新订阅者 <strong>{$subscriber->email}</strong> 已加入你的邮件列表。",
                    'detail'     => '订阅时间：' . $subscriber->created_at->format('Y-m-d H:i'),
                    'actionUrl'  => url('/admin/subscribers'),
                    'actionText' => '查看订阅者列表',
                    'brandName'  => config('app.name', 'Archyx'),
                    'timestamp'  => now()->format('Y-m-d H:i'),
                ])->render();

                app(MailService::class)->send($admin->email, $subject, $htmlBody);
            }
        }

        return response()->json([
            'message' => '订阅成功',
            'data' => $subscriber,
        ], 201);
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

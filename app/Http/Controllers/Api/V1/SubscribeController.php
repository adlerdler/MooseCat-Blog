<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Notifications\NewSubscriberNotification;
use App\Models\User;
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
            $admin->notify(new NewSubscriberNotification($subscriber));
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

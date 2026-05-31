<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscribersController extends Controller
{
    public function index(): Response
    {
        $subscribers = Subscriber::orderBy('created_at', 'desc')
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'email' => $s->email,
                'name' => $s->name,
                'source' => $s->source,
                'is_active' => $s->is_active,
                'subscribed_at' => $s->subscribed_at?->format('Y-m-d'),
                'created_at' => $s->created_at?->format('Y-m-d'),
            ]);

        return Inertia::render('admin/Subscribers', [
            'subscribers' => $subscribers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:subscribers,email',
            'name' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Subscriber::create($validated);

        return back()->with('success', '订阅者已添加');
    }

    public function update(Request $request, Subscriber $subscriber): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'sometimes|required|email|unique:subscribers,email,' . $subscriber->id,
            'name' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $subscriber->update($validated);

        return back()->with('success', '订阅者已更新');
    }

    public function destroy(Subscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return back()->with('success', '订阅者已删除');
    }
}
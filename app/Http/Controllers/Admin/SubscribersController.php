<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Services\SubscriberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscribersController extends Controller
{
    public function __construct(
        protected SubscriberService $subscriberService,
    ) {
        $this->middleware('permission:manage_subscribers');
    }

    public function index(): Response
    {
        return Inertia::render('admin/Subscribers', [
            'subscribers' => $this->subscriberService->getAll(),
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

        $this->subscriberService->create($validated);

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

        $this->subscriberService->update($subscriber, $validated);

        return back()->with('success', '订阅者已更新');
    }

    public function destroy(Subscriber $subscriber): RedirectResponse
    {
        $this->subscriberService->delete($subscriber);

        return back()->with('success', '订阅者已删除');
    }
}

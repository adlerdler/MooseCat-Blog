<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscribersController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $subscribers = $this->mockDataService->getSubscribers();
        
        return Inertia::render('admin/Subscribers', [
            'subscribers' => $subscribers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:subscribers',
        ]);

        return back()->with('success', '订阅成功');
    }

    public function destroy(string $id)
    {
        return back()->with('success', '订阅已取消');
    }
}
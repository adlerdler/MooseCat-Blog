<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Subscribers Controller
 * 
 * Handles subscriber management operations.
 * Provides functionality for managing email subscribers.
 */
class SubscribersController extends Controller
{
    protected $mockDataService;

    /**
     * Constructor
     * 
     * @param MockDataService $mockDataService
     */
    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    /**
     * Display the subscriber list
     * 
     * @return Response
     */
    public function index(): Response
    {
        $subscribers = $this->mockDataService->getSubscribers();
        
        return Inertia::render('admin/Subscribers', [
            'subscribers' => $subscribers,
        ]);
    }

    /**
     * Store a newly created subscriber
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:subscribers',
        ]);

        return back()->with('success', '订阅成功');
    }

    /**
     * Remove the specified subscriber
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        return back()->with('success', '订阅已取消');
    }
}
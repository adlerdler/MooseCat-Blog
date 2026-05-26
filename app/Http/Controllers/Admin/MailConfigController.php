<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Mail Config Controller
 * 
 * Handles mail configuration management.
 * Provides functionality for viewing, updating, and testing email settings.
 */
class MailConfigController extends Controller
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
     * Display the mail configuration page
     * 
     * @return Response
     */
    public function index(): Response
    {
        $mailConfig = $this->mockDataService->getMailConfig();
        
        return Inertia::render('admin/MailConfig', [
            'mailConfig' => $mailConfig,
        ]);
    }

    /**
     * Update mail configuration
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'driver' => 'required|string',
            'host' => 'required|string',
            'port' => 'required|integer',
            'username' => 'required|string',
            'password' => 'required|string',
            'encryption' => 'nullable|string',
        ]);

        return back()->with('success', '邮件配置已更新');
    }

    /**
     * Test mail configuration
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function test()
    {
        return back()->with('success', '测试邮件已发送');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MailConfigController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $mailConfig = $this->mockDataService->getMailConfig();
        
        return Inertia::render('admin/MailConfig', [
            'mailConfig' => $mailConfig,
        ]);
    }

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

    public function test()
    {
        return back()->with('success', '测试邮件已发送');
    }
}
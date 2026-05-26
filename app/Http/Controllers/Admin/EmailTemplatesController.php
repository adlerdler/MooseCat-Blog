<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailTemplatesController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $templates = $this->mockDataService->getEmailTemplates();
        
        return Inertia::render('admin/EmailTemplates', [
            'templates' => $templates,
        ]);
    }

    public function edit(string $id): Response
    {
        $templates = $this->mockDataService->getEmailTemplates();
        $template = collect($templates)->firstWhere('id', $id);
        
        return Inertia::render('admin/EmailTemplates', [
            'template' => $template,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'subject' => 'required|string',
            'content' => 'required|string',
        ]);

        return back()->with('success', '邮件模板已更新');
    }
}
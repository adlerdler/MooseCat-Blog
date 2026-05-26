<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Email Templates Controller
 * 
 * Handles email template management.
 * Provides functionality for viewing and updating email templates.
 */
class EmailTemplatesController extends Controller
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
     * Display the email template list
     * 
     * @return Response
     */
    public function index(): Response
    {
        $templates = $this->mockDataService->getEmailTemplates();
        
        return Inertia::render('admin/EmailTemplates', [
            'templates' => $templates,
        ]);
    }

    /**
     * Display the edit email template form
     * 
     * @param string $id
     * @return Response
     */
    public function edit(string $id): Response
    {
        $templates = $this->mockDataService->getEmailTemplates();
        $template = collect($templates)->firstWhere('id', $id);
        
        return Inertia::render('admin/EmailTemplates', [
            'template' => $template,
        ]);
    }

    /**
     * Update the specified email template
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'subject' => 'required|string',
            'content' => 'required|string',
        ]);

        return back()->with('success', '邮件模板已更新');
    }
}
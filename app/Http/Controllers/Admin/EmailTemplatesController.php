<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmailTemplateRequest;
use App\Http\Requests\UpdateEmailTemplateRequest;
use App\Models\EmailTemplate;
use App\Services\EmailTemplateService;
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
    public function __construct(
        protected EmailTemplateService $emailTemplateService,
    ) {
        $this->middleware('permission:manage_email_templates');
    }

    /**
     * Display all email templates.
     *
     * Auto-seeds default templates if the table is empty.
     */
    public function index(): Response
    {
        $templates = $this->emailTemplateService->getAll();

        return Inertia::render('admin/EmailTemplates', [
            'templates' => $templates->toArray(),
        ]);
    }

    /**
     * Show the edit form for a specific template.
     */
    public function edit(string $id): Response
    {
        $templates = $this->emailTemplateService->getAll();
        $template  = $this->emailTemplateService->getById($id);

        return Inertia::render('admin/EmailTemplates', [
            'templates' => $templates->toArray(),
            'template'  => $template->toArray(),
        ]);
    }

    /**
     * Update a specific email template.
     */
    public function update(UpdateEmailTemplateRequest $request, string $id): \Illuminate\Http\RedirectResponse
    {
        $template = $this->emailTemplateService->getById($id);
        $this->emailTemplateService->update($template, $request->validated());

        return back()->with('success', '邮件模板已更新');
    }

    /**
     * Create a new email template.
     */
    public function store(StoreEmailTemplateRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->emailTemplateService->create($request->validated());

        return back()->with('success', '邮件模板已创建');
    }
}

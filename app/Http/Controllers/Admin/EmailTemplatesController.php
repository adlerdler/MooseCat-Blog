<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function __construct()
    {
        $this->middleware('permission:manage_email_templates');
    }
    /**
     * Display all email templates.
     * 
     * Auto-seeds default templates if the table is empty.
     */
    public function index(): Response
    {
        if (EmailTemplate::count() === 0) {
            $this->seedDefaults();
        }

        $templates = EmailTemplate::all();

        return Inertia::render('admin/EmailTemplates', [
            'templates' => $templates->toArray(),
        ]);
    }

    /**
     * Show the edit form for a specific template.
     * 
     * Renders the same page with the selected template for editing.
     */
    public function edit(string $id): Response
    {
        if (EmailTemplate::count() === 0) {
            $this->seedDefaults();
        }

        $templates = EmailTemplate::all();
        $template  = EmailTemplate::findOrFail($id);

        return Inertia::render('admin/EmailTemplates', [
            'templates' => $templates->toArray(),
            'template'  => $template->toArray(),
        ]);
    }

    /**
     * Update a specific email template.
     */
    public function update(Request $request, string $id): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string',
            'content' => 'required|string',
        ]);

        $template = EmailTemplate::findOrFail($id);
        $template->update($validated);

        return back()->with('success', '邮件模板已更新');
    }

    /**
     * Create a new email template.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|unique:email_templates,name',
            'subject'     => 'required|string',
            'content'     => 'required|string',
            'description' => 'nullable|string',
        ]);

        EmailTemplate::create([
            'name'        => $validated['name'],
            'subject'     => $validated['subject'],
            'content'     => $validated['content'],
            'description' => $validated['description'] ?? '',
            'is_active'   => true,
        ]);

        return back()->with('success', '邮件模板已创建');
    }

    /**
     * Seed default email templates into the database.
     */
    private function seedDefaults(): void
    {
        $defaults = [
            [
                'name'        => 'welcome_email',
                'subject'     => 'Welcome to Archyx!',
                'content'     => '<h1>Welcome, {{user_name}}!</h1><p>We are glad to have you here.</p>',
                'description' => '新用户注册后发送的欢迎邮件',
                'variables'   => ['user_name', 'site_name'],
                'is_active'   => true,
            ],
            [
                'name'        => 'password_reset',
                'subject'     => 'Reset Your Password',
                'content'     => '<h1>Reset Password</h1><p>Click the link below to reset your password: <a href="{{reset_link}}">Reset Now</a></p>',
                'description' => '用户请求密码重置时发送的邮件',
                'variables'   => ['user_name', 'reset_link', 'site_name'],
                'is_active'   => true,
            ],
            [
                'name'        => 'comment_reply_notification',
                'subject'     => 'Someone replied to your comment',
                'content'     => '<h1>New Reply</h1><p>{{replier_name}} replied to your comment: "{{comment_content}}"</p>',
                'description' => '用户收到评论回复时发送的通知邮件',
                'variables'   => ['user_name', 'replier_name', 'comment_content', 'post_url'],
                'is_active'   => true,
            ],
        ];

        foreach ($defaults as $data) {
            EmailTemplate::create($data);
        }
    }
}

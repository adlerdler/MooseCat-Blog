<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Collection;

/**
 * EmailTemplateService - 邮件模板服务类
 *
 * 提供邮件模板的 CRUD 和默认数据填充功能。
 * Provides email template CRUD and default seeding.
 */
class EmailTemplateService
{
    public function __construct() {}

    /**
     * 默认模板定义
     * Default template definitions.
     */
    private const DEFAULT_TEMPLATES = [
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

    /**
     * 获取所有邮件模板（空表时自动填充默认模板）
     * Get all email templates, auto-seed defaults if table is empty.
     */
    public function getAll(): Collection
    {
        if (EmailTemplate::count() === 0) {
            $this->seedDefaults();
        }

        return EmailTemplate::all();
    }

    /**
     * 按 ID 获取模板
     * Get template by ID.
     */
    public function getById(string $id): EmailTemplate
    {
        return EmailTemplate::findOrFail($id);
    }

    /**
     * 创建邮件模板
     * Create a new email template.
     */
    public function create(array $data): EmailTemplate
    {
        return EmailTemplate::create([
            'name'        => $data['name'],
            'subject'     => $data['subject'],
            'content'     => $data['content'],
            'description' => $data['description'] ?? '',
            'is_active'   => true,
        ]);
    }

    /**
     * 更新邮件模板
     * Update an email template.
     */
    public function update(EmailTemplate $template, array $data): EmailTemplate
    {
        $template->update($data);
        return $template;
    }

    /**
     * 删除邮件模板
     * Delete an email template.
     */
    public function delete(EmailTemplate $template): void
    {
        $template->delete();
    }

    /**
     * 填充默认邮件模板到数据库
     * Seed default email templates into the database.
     */
    public function seedDefaults(): void
    {
        foreach (self::DEFAULT_TEMPLATES as $data) {
            EmailTemplate::create($data);
        }
    }
}

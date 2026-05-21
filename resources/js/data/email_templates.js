/**
 * email_templates.js - 邮件模板数据（数据库格式模拟）
 * 
 * 功能说明：
 * - 模拟数据库邮件模板表结构
 * - 存储系统邮件模板配置
 * - 支持模板变量和预览功能
 * 
 * 字段说明：
 * - id: 模板唯一标识
 * - name: 模板名称
 * - subject: 邮件主题
 * - body: 邮件内容（HTML格式）
 * - description: 模板描述
 * - variables: 可用变量列表（JSON数组）
 * - is_active: 是否启用
 * - created_at: 创建时间
 * - updated_at: 更新时间
 */

export const emailTemplates = [
  {
    id: 1,
    name: 'welcome_email',
    subject: 'Welcome to Archyx!',
    body: '<h1>Welcome, {{user_name}}!</h1><p>We are glad to have you here.</p>',
    description: '新用户注册后发送的欢迎邮件',
    variables: ['user_name', 'site_name'],
    is_active: true,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
  {
    id: 2,
    name: 'password_reset',
    subject: 'Reset Your Password',
    body: '<h1>Reset Password</h1><p>Click the link below to reset your password: <a href="{{reset_link}}">Reset Now</a></p>',
    description: '用户请求密码重置时发送的邮件',
    variables: ['user_name', 'reset_link', 'site_name'],
    is_active: true,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  },
  {
    id: 3,
    name: 'comment_reply_notification',
    subject: 'Someone replied to your comment',
    body: '<h1>New Reply</h1><p>{{replier_name}} replied to your comment: "{{comment_content}}"</p>',
    description: '用户收到评论回复时发送的通知邮件',
    variables: ['user_name', 'replier_name', 'comment_content', 'post_url'],
    is_active: true,
    created_at: '2024-01-01T00:00:00Z',
    updated_at: '2024-01-01T00:00:00Z'
  }
];

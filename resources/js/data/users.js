/**
 * users.js - 用户数据定义（以数据库为准）
 * 
 * ⚠️ 字段变更备注（2026-05-24）：
 * - email_notifications → notifications（精简字段名）
 * - role_id 字段已删除（改用 Spatie RBAC 的 model_has_roles 中间表管理角色）
 * 
 * 字段说明：
 * - id: 用户唯一标识
 * - name: 用户姓名
 * - email: 用户邮箱
 * - password: 加密密码
 * - avatar: 头像
 * - status: 状态（active/inactive/suspended）
 * - points: 积分
 * - notifications: 邮件通知开关（原 email_notifications）
 * - comment_approval_alert: 评论审核提醒
 * - new_user_alert: 新用户注册提醒
 * - weekly_report: 周报开关
 * - digest_email: 摘要邮件开关
 * - digest_frequency: 摘要邮件频率（daily/weekly/monthly）
 * - last_login_at: 最后登录时间
 * - created_at: 创建时间
 * - updated_at: 更新时间
 * 
 * ⚠️ 角色管理说明：
 * - 已删除 role_id 字段，改用 Spatie/laravel-permission 管理角色
 * - 分配角色：$user->assignRole('admin')
 * - 检查角色：$user->hasRole('admin')
 * - 获取角色：$user->getRoleNames()
 * - 本文件中的 role_id 仅用于前端展示参考，实际数据存储在 model_has_roles 表中
 */

export const adminUsers = [
  {
    id: 1,
    name: 'Admin User',
    email: 'admin@archyx.com',
    password: 'admin123',
    avatar: null,
    // role_id 已删除，此处仅用于前端展示参考
    role_id: 1,
    status: 'active',
    points: 1000,
    email_notifications: true,
    comment_approval_alert: true,
    new_user_alert: true,
    weekly_report: true,
    digest_email: true,
    digest_frequency: 'weekly',
    joined: '2024-01-15',
    last_login_at: '2024-01-15T10:00:00',
    created_at: '2024-01-15T10:00:00',
    updated_at: '2024-01-15T10:00:00',
  },
  {
    id: 9,
    name: 'ADLER DECHT',
    email: 'adler@archyx.com',
    password: 'adler123',
    avatar: '/images/avatars/adler.jpg',
    role_id: 3,
    status: 'active',
    points: 850,
    email_notifications: true,
    comment_approval_alert: true,
    new_user_alert: true,
    weekly_report: true,
    digest_email: true,
    digest_frequency: 'weekly',
    joined: '2024-01-01',
    last_login_at: '2024-01-01T00:00:00',
    created_at: '2024-01-01T00:00:00',
    updated_at: '2024-01-01T00:00:00',
  },
  {
    id: 2,
    name: 'Content Editor',
    email: 'editor@archyx.com',
    password: 'editor123',
    avatar: null,
    role_id: 2,
    status: 'active',
    points: 750,
    email_notifications: true,
    comment_approval_alert: true,
    new_user_alert: false,
    weekly_report: false,
    digest_email: true,
    digest_frequency: 'weekly',
    joined: '2024-03-20',
    last_login_at: '2024-03-20T08:30:00',
    created_at: '2024-03-20T08:30:00',
    updated_at: '2024-03-20T08:30:00',
  },
  {
    id: 3,
    name: 'Author Writer',
    email: 'author@archyx.com',
    password: 'author123',
    avatar: null,
    role_id: 3,
    status: 'active',
    points: 620,
    email_notifications: true,
    comment_approval_alert: false,
    new_user_alert: false,
    weekly_report: false,
    digest_email: true,
    digest_frequency: 'monthly',
    joined: '2024-05-10',
    last_login_at: '2024-05-10T14:20:00',
    created_at: '2024-05-10T14:20:00',
    updated_at: '2024-05-10T14:20:00',
  },
  {
    id: 4,
    name: 'Guest User',
    email: 'guest@example.com',
    password: 'guest123',
    avatar: null,
    role_id: 7,
    status: 'inactive',
    points: 0,
    email_notifications: false,
    comment_approval_alert: false,
    new_user_alert: false,
    weekly_report: false,
    digest_email: false,
    digest_frequency: 'monthly',
    joined: '2024-06-01',
    last_login_at: null,
    created_at: '2024-06-01T09:15:00',
    updated_at: '2024-06-01T09:15:00',
  },
  {
    id: 5,
    name: 'Contributor',
    email: 'contributor@archyx.com',
    password: 'contributor123',
    avatar: null,
    role_id: 3,
    status: 'active',
    points: 480,
    email_notifications: true,
    comment_approval_alert: false,
    new_user_alert: false,
    weekly_report: false,
    digest_email: true,
    digest_frequency: 'weekly',
    joined: '2024-07-15',
    last_login_at: '2024-07-15T16:45:00',
    created_at: '2024-07-15T16:45:00',
    updated_at: '2024-07-15T16:45:00',
  },
  {
    id: 6,
    name: 'Moderator',
    email: 'moderator@archyx.com',
    password: 'moderator123',
    avatar: null,
    role_id: 4,
    status: 'active',
    points: 550,
    email_notifications: true,
    comment_approval_alert: true,
    new_user_alert: true,
    weekly_report: true,
    digest_email: true,
    digest_frequency: 'daily',
    joined: '2024-08-20',
    last_login_at: '2024-08-20T11:30:00',
    created_at: '2024-08-20T11:30:00',
    updated_at: '2024-08-20T11:30:00',
  },
  {
    id: 7,
    name: 'Subscriber',
    email: 'subscriber@example.com',
    password: 'subscriber123',
    avatar: null,
    role_id: 5,
    status: 'inactive',
    points: 100,
    email_notifications: false,
    comment_approval_alert: false,
    new_user_alert: false,
    weekly_report: false,
    digest_email: false,
    digest_frequency: 'monthly',
    joined: '2024-09-01',
    last_login_at: null,
    created_at: '2024-09-01T13:20:00',
    updated_at: '2024-09-01T13:20:00',
  },
  {
    id: 8,
    name: 'API User',
    email: 'api@archyx.com',
    password: 'api123',
    avatar: null,
    role_id: 6,
    status: 'active',
    points: 300,
    email_notifications: false,
    comment_approval_alert: false,
    new_user_alert: false,
    weekly_report: false,
    digest_email: false,
    digest_frequency: 'monthly',
    joined: '2024-10-15',
    last_login_at: '2024-10-15T10:00:00',
    created_at: '2024-10-15T10:00:00',
    updated_at: '2024-10-15T10:00:00',
  },
];

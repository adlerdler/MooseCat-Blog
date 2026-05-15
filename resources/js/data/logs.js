/**
 * logs.js - 系统操作日志数据
 * 
 * 注意：日志动作标签已迁移至国际化文件中
 * 直接使用动作标识作为国际化 key（如 t('user_login')）
 */

export const adminLogs = [
  {
    id: 1,
    action: 'user_login',
    user: 'admin',
    ip: '192.168.1.100',
    userAgent: 'Chrome/120.0.0.0',
    details: '管理员登录系统',
    createdAt: '2026-05-15T10:30:00'
  },
  {
    id: 2,
    action: 'post_create',
    user: 'admin',
    ip: '192.168.1.100',
    userAgent: 'Chrome/120.0.0.0',
    details: '创建新文章：Laravel 框架最佳实践',
    createdAt: '2026-05-15T11:15:00'
  },
  {
    id: 3,
    action: 'user_update',
    user: 'admin',
    ip: '192.168.1.101',
    userAgent: 'Firefox/121.0',
    details: '更新用户资料：user001',
    createdAt: '2026-05-15T12:00:00'
  },
  {
    id: 4,
    action: 'role_change',
    user: 'admin',
    ip: '192.168.1.100',
    userAgent: 'Chrome/120.0.0.0',
    details: '修改用户角色：user002 从 editor 改为 admin',
    createdAt: '2026-05-15T14:20:00'
  },
  {
    id: 5,
    action: 'comment_delete',
    user: 'admin',
    ip: '192.168.1.102',
    userAgent: 'Safari/17.1',
    details: '删除评论 #123',
    createdAt: '2026-05-15T15:45:00'
  },
  {
    id: 6,
    action: 'settings_update',
    user: 'admin',
    ip: '192.168.1.100',
    userAgent: 'Chrome/120.0.0.0',
    details: '更新系统配置',
    createdAt: '2026-05-15T16:30:00'
  },
  {
    id: 7,
    action: 'user_login',
    user: 'user001',
    ip: '10.0.0.50',
    userAgent: 'Edge/120.0.0.0',
    details: '用户登录系统',
    createdAt: '2026-05-14T09:00:00'
  },
  {
    id: 8,
    action: 'post_edit',
    user: 'admin',
    ip: '192.168.1.100',
    userAgent: 'Chrome/120.0.0.0',
    details: '编辑文章：Vue 3 组件开发',
    createdAt: '2026-05-14T10:30:00'
  },
  {
    id: 9,
    action: 'category_create',
    user: 'admin',
    ip: '192.168.1.100',
    userAgent: 'Chrome/120.0.0.0',
    details: '创建新分类：前端开发',
    createdAt: '2026-05-14T11:45:00'
  },
  {
    id: 10,
    action: 'media_upload',
    user: 'admin',
    ip: '192.168.1.100',
    userAgent: 'Chrome/120.0.0.0',
    details: '上传图片：logo.png',
    createdAt: '2026-05-14T14:00:00'
  }
];
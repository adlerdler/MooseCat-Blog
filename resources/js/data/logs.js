/**
 * logs.js - 系统操作日志数据
 * 
 * 注意：日志动作标签已迁移至国际化文件中
 * 直接使用动作标识作为国际化 key（如 t('user_login')）
 */

/**
 * 字段说明：
 * - id: 日志唯一标识
 * - action: 动作标识
 * - module: 模块
 * - user: 用户名
 * - ip: IP地址
 * - user_agent: 用户代理
 * - details: 详情
 * - target_id: 目标ID
 * - changes: 变更内容
 * - created_at: 创建时间
 */

export const adminLogs = [
  {
    id: 1,
    action: 'user_login',
    module: 'auth',
    user: 'admin',
    ip: '192.168.1.100',
    user_agent: 'Chrome/120.0.0.0',
    details: '管理员登录系统',
    created_at: '2026-05-15T10:30:00'
  },
  {
    id: 2,
    action: 'post_create',
    module: 'posts',
    user: 'admin',
    ip: '192.168.1.100',
    user_agent: 'Chrome/120.0.0.0',
    details: '创建新文章：Laravel 框架最佳实践',
    target_id: '45',
    created_at: '2026-05-15T11:15:00'
  },
  {
    id: 3,
    action: 'post_update',
    module: 'posts',
    user: 'admin',
    ip: '192.168.1.100',
    user_agent: 'Chrome/120.0.0.0',
    details: '修改了文章《架构设计》的标题和分类',
    target_id: '12',
    changes: {
      before: { title: '架构设计', category: 'Design' },
      after: { title: '现代软件架构设计', category: 'Architecture' }
    },
    created_at: '2026-05-15T11:45:00'
  },
  {
    id: 4,
    action: 'user_update',
    module: 'users',
    user: 'admin',
    ip: '192.168.1.101',
    user_agent: 'Firefox/121.0',
    details: '更新用户资料：user001',
    target_id: '5',
    changes: {
      before: { nickname: 'User1', status: 'inactive' },
      after: { nickname: 'Adlerian_Dev', status: 'active' }
    },
    created_at: '2026-05-15T12:00:00'
  },
  {
    id: 5,
    action: 'role_change',
    module: 'roles',
    user: 'admin',
    ip: '192.168.1.100',
    user_agent: 'Chrome/120.0.0.0',
    details: '修改用户角色：user002 从 editor 改为 admin',
    target_id: '6',
    created_at: '2026-05-15T14:20:00'
  },
  {
    id: 6,
    action: 'comment_delete',
    module: 'comments',
    user: 'admin',
    ip: '192.168.1.102',
    user_agent: 'Safari/17.1',
    details: '删除了评论 #123 (内容含违规信息)',
    target_id: '123',
    created_at: '2026-05-15T15:45:00'
  },
  {
    id: 7,
    action: 'settings_update',
    module: 'settings',
    user: 'admin',
    ip: '192.168.1.100',
    user_agent: 'Chrome/120.0.0.0',
    details: '更新了网站 SEO 全局配置',
    changes: {
      before: { meta_title: 'Old Blog' },
      after: { meta_title: 'Archyx Digital Blog' }
    },
    created_at: '2026-05-15T16:30:00'
  },
  {
    id: 8,
    action: 'ad_update',
    module: 'advertisements',
    user: 'admin',
    ip: '192.168.1.100',
    user_agent: 'Chrome/120.0.0.0',
    details: '启用了侧边栏广告：Spring Sale',
    target_id: '2',
    changes: {
      before: { is_active: false },
      after: { is_active: true }
    },
    created_at: '2026-05-15T17:10:00'
  },
  {
    id: 9,
    action: 'media_delete',
    module: 'media',
    user: 'admin',
    ip: '192.168.1.100',
    user_agent: 'Chrome/120.0.0.0',
    details: '删除了媒体文件：temp_banner.jpg',
    target_id: '234',
    created_at: '2026-05-14T14:00:00'
  }
];

/**
 * permissions.js - 权限数据定义
 *
 * 基于 Spatie/laravel-permission 方案 + 扩展字段。
 *
 * Spatie 标准字段：
 * - name: 权限名称 (唯一标识)
 * - guard_name: 守卫名称 (默认 'web')
 *
 * 扩展字段：
 * - program_id: 关联项目/程序 ID
 * - description: 权限描述
 */

export const permissions = [
  { id: 1, name: 'manage_posts', label: 'Posts', description: '文章管理权限', guard_name: 'web', program_id: 'manage_posts' },
  { id: 2, name: 'manage_videos', label: 'Videos', description: '视频管理权限', guard_name: 'web', program_id: 'manage_videos' },
  { id: 3, name: 'manage_projects', label: 'Projects', description: '项目管理权限', guard_name: 'web', program_id: 'manage_projects' },
  { id: 4, name: 'manage_resources', label: 'Resources', description: '资源管理权限', guard_name: 'web', program_id: 'manage_resources' },
  { id: 5, name: 'manage_comments', label: 'Comments', description: '评论管理权限', guard_name: 'web', program_id: 'manage_comments' },
  { id: 6, name: 'manage_users', label: 'Users', description: '用户管理权限', guard_name: 'web', program_id: 'manage_users' },
  { id: 7, name: 'manage_categories', label: 'Categories', description: '分类管理权限', guard_name: 'web', program_id: 'manage_categories' },
  { id: 8, name: 'manage_tags', label: 'Tags', description: '标签管理权限', guard_name: 'web', program_id: 'manage_tags' },
  { id: 9, name: 'manage_roles', label: 'Roles', description: '角色管理权限', guard_name: 'web', program_id: 'manage_roles' },
  { id: 10, name: 'manage_settings', label: 'Settings', description: '系统设置权限', guard_name: 'web', program_id: 'manage_settings' },
  { id: 11, name: 'api_access', label: 'API Access', description: 'API访问权限', guard_name: 'api', program_id: 'api_access' },
  { id: 12, name: 'view_analytics', label: 'Analytics', description: '数据分析权限', guard_name: 'web', program_id: 'view_analytics' },
  { id: 13, name: 'manage_media', label: 'Media', description: '媒体库管理权限', guard_name: 'web', program_id: 'manage_media' },
  { id: 14, name: 'manage_subscribers', label: 'Subscribers', description: '订阅者管理权限', guard_name: 'web', program_id: 'manage_subscribers' },
  { id: 15, name: 'manage_journals', label: 'Journals', description: '日志管理权限', guard_name: 'web', program_id: 'manage_journals' },
  { id: 16, name: 'manage_ads', label: 'Advertisements', description: '广告管理权限', guard_name: 'web', program_id: 'manage_ads' }
];

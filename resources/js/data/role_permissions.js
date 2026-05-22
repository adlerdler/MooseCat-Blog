/**
 * role_permissions.js - 角色权限关联表
 *
 * 基于 Spatie/laravel-permission 方案的多对多关联表。
 * Spatie 使用 pivot 表 `role_has_permissions` 来管理角色与权限的关系。
 *
 * 字段说明：
 * - permission_id: 权限 ID (对应 permissions.js 中的 id)
 * - role_id: 角色 ID (对应 roles.js 中的 id)
 */

export const rolePermissions = [
  // admin (id: 1) - 系统管理员 - 拥有所有权限
  { role_id: 1, permission_id: 1 },   // manage_posts
  { role_id: 1, permission_id: 2 },   // manage_videos
  { role_id: 1, permission_id: 3 },   // manage_projects
  { role_id: 1, permission_id: 4 },   // manage_resources
  { role_id: 1, permission_id: 5 },   // manage_comments
  { role_id: 1, permission_id: 6 },   // manage_users
  { role_id: 1, permission_id: 7 },   // manage_categories
  { role_id: 1, permission_id: 8 },   // manage_tags
  { role_id: 1, permission_id: 9 },   // manage_roles
  { role_id: 1, permission_id: 10 },  // manage_settings
  { role_id: 1, permission_id: 11 },  // api_access
  { role_id: 1, permission_id: 12 },  // view_analytics
  { role_id: 1, permission_id: 13 },  // manage_media
  { role_id: 1, permission_id: 14 },  // manage_subscribers
  { role_id: 1, permission_id: 15 },  // manage_journals
  { role_id: 1, permission_id: 16 },  // manage_ads

  // editor (id: 2) - 内容编辑
  { role_id: 2, permission_id: 1 },   // manage_posts
  { role_id: 2, permission_id: 5 },   // manage_comments
  { role_id: 2, permission_id: 7 },   // manage_categories
  { role_id: 2, permission_id: 8 },   // manage_tags
  { role_id: 2, permission_id: 15 },  // manage_journals

  // author (id: 3) - 文章作者
  { role_id: 3, permission_id: 1 },   // manage_posts

  // moderator (id: 4) - 社区版主
  { role_id: 4, permission_id: 5 },   // manage_comments
  { role_id: 4, permission_id: 6 },   // manage_users
  { role_id: 4, permission_id: 14 },  // manage_subscribers

  // subscriber (id: 5) - 订阅用户
  // 无特殊权限

  // api (id: 6) - API用户
  { role_id: 6, permission_id: 11 },  // api_access

  // guest (id: 7) - 访客用户
  // 无权限
];

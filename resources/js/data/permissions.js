/**
 * permissions.js - 权限数据定义
 * 
 * 统一管理系统中的权限定义，避免硬编码重复。
 */

export const permissions = [
  { id: 1, name: 'posts', label: 'Posts', description: '文章管理权限', programId: 'manage_posts' },
  { id: 2, name: 'videos', label: 'Videos', description: '视频管理权限', programId: 'manage_videos' },
  { id: 3, name: 'projects', label: 'Projects', description: '项目管理权限', programId: 'manage_projects' },
  { id: 4, name: 'resources', label: 'Resources', description: '资源管理权限', programId: 'manage_resources' },
  { id: 5, name: 'comments', label: 'Comments', description: '评论管理权限', programId: 'manage_comments' },
  { id: 6, name: 'users', label: 'Users', description: '用户管理权限', programId: 'manage_users' },
  { id: 7, name: 'categories', label: 'Categories', description: '分类管理权限', programId: 'manage_categories' },
  { id: 8, name: 'tags', label: 'Tags', description: '标签管理权限', programId: 'manage_tags' },
  { id: 9, name: 'roles', label: 'Roles', description: '角色管理权限', programId: 'manage_roles' },
  { id: 10, name: 'settings', label: 'Settings', description: '系统设置权限', programId: 'manage_settings' },
  { id: 11, name: 'api_access', label: 'API Access', description: 'API访问权限', programId: 'api_access' },
  { id: 12, name: 'analytics', label: 'Analytics', description: '数据分析权限', programId: 'view_analytics' }
];
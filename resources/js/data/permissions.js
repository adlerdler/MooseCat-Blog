/**
 * permissions.js - 权限数据定义
 *
 * 统一管理系统中的权限定义，避免硬编码重复。
 */

export const permissions = [
  { id: 'posts', name: 'Posts', description: '文章管理权限', programId: 'manage_posts' },
  { id: 'videos', name: 'Videos', description: '视频管理权限', programId: 'manage_videos' },
  { id: 'projects', name: 'Projects', description: '项目管理权限', programId: 'manage_projects' },
  { id: 'resources', name: 'Resources', description: '资源管理权限', programId: 'manage_resources' },
  { id: 'comments', name: 'Comments', description: '评论管理权限', programId: 'manage_comments' },
  { id: 'users', name: 'Users', description: '用户管理权限', programId: 'manage_users' },
  { id: 'categories', name: 'Categories', description: '分类管理权限', programId: 'manage_categories' },
  { id: 'tags', name: 'Tags', description: '标签管理权限', programId: 'manage_tags' },
  { id: 'roles', name: 'Roles', description: '角色管理权限', programId: 'manage_roles' },
  { id: 'settings', name: 'Settings', description: '系统设置权限', programId: 'manage_settings' },
  { id: 'api_access', name: 'API Access', description: 'API访问权限', programId: 'api_access' },
  { id: 'analytics', name: 'Analytics', description: '数据分析权限', programId: 'view_analytics' }
];

export const getPermissionById = (permissionId) => {
  return permissions.find(p => p.id === permissionId);
};

export const getPermissionByProgramId = (programId) => {
  return permissions.find(p => p.programId === programId);
};

export const getPermissionName = (permissionId) => {
  const permission = getPermissionById(permissionId);
  return permission ? permission.name : permissionId;
};

export const getPermissionDescription = (permissionId) => {
  const permission = getPermissionById(permissionId);
  return permission ? permission.description : '';
};

export const availablePermissions = permissions.map(p => p.name);

export const getAllPermissionIds = () => {
  return permissions.map(p => p.id);
};
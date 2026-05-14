/**
 * roles.js - 角色数据定义
 * 
 * 统一管理系统中的角色定义，避免硬编码重复。
 */

export const roles = [
  { id: 'admin', value: 'admin', label: 'ADMIN', color: 'red', description: '系统管理员' },
  { id: 'editor', value: 'editor', label: 'EDITOR', color: 'blue', description: '内容编辑' },
  { id: 'author', value: 'author', label: 'AUTHOR', color: 'green', description: '文章作者' },
  { id: 'moderator', value: 'moderator', label: 'MODERATOR', color: 'purple', description: '社区版主' },
  { id: 'subscriber', value: 'subscriber', label: 'SUBSCRIBER', color: 'gray', description: '订阅用户' },
  { id: 'api', value: 'api', label: 'API', color: 'yellow', description: 'API 用户' },
  { id: 'guest', value: 'guest', label: 'GUEST', color: 'cyan', description: '访客用户' }
];

export const getRoleById = (roleId) => {
  return roles.find(r => r.id === roleId);
};

export const getRoleLabel = (roleId) => {
  const role = getRoleById(roleId);
  return role ? role.label : 'Unknown';
};

export const getRoleColor = (roleId) => {
  const role = getRoleById(roleId);
  return role ? role.color : 'gray';
};

export const getRoleDescription = (roleId) => {
  const role = getRoleById(roleId);
  return role ? role.description : '未知角色';
};

export const getRoleStyle = (roleId) => {
  const colorMap = {
    admin: 'bg-red-600 text-white border-red-500',
    editor: 'bg-blue-600 text-white border-blue-500',
    author: 'bg-green-600 text-white border-green-500',
    moderator: 'bg-purple-600 text-white border-purple-500',
    subscriber: 'bg-gray-600 text-white border-gray-500',
    api: 'bg-yellow-500 text-gray-900 border-yellow-400',
    guest: 'bg-cyan-600 text-white border-cyan-500'
  };
  return colorMap[roleId] || 'bg-gray-600 text-white border-gray-500';
};

export const adminRoles = [
  { id: 'admin', name: 'Admin', description: '系统管理员 - 拥有所有权限', permissions: ['users', 'posts', 'comments', 'settings', 'roles', 'categories', 'videos', 'projects', 'resources', 'tags', 'api_access', 'analytics'] },
  { id: 'editor', name: 'Editor', description: '内容编辑 - 可以管理文章和评论', permissions: ['posts', 'comments', 'categories', 'tags'] },
  { id: 'author', name: 'Author', description: '文章作者 - 可以创建和编辑自己的文章', permissions: ['posts'] },
  { id: 'moderator', name: 'Moderator', description: '社区版主 - 可以管理评论和用户', permissions: ['comments', 'users'] },
  { id: 'subscriber', name: 'Subscriber', description: '订阅用户 - 可以阅读和评论', permissions: [] },
  { id: 'api', name: 'API', description: 'API用户 - 用于程序访问', permissions: ['api_access'] }
];
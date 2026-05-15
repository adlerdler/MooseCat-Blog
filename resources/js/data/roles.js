/**
 * roles.js - 角色数据定义
 * 
 * 统一管理系统中的角色定义，避免硬编码重复。
 * 角色权限通过 role_permissions.js 关联表管理。
 */

export const roles = [
  { 
    id: 1, 
    value: 'admin', 
    label: 'ADMIN', 
    name: 'Admin',
    color: 'red', 
    description: '系统管理员 - 拥有所有权限'
  },
  { 
    id: 2, 
    value: 'editor', 
    label: 'EDITOR', 
    name: 'Editor',
    color: 'blue', 
    description: '内容编辑 - 可以管理文章和评论'
  },
  { 
    id: 3, 
    value: 'author', 
    label: 'AUTHOR', 
    name: 'Author',
    color: 'green', 
    description: '文章作者 - 可以创建和编辑自己的文章'
  },
  { 
    id: 4, 
    value: 'moderator', 
    label: 'MODERATOR', 
    name: 'Moderator',
    color: 'purple', 
    description: '社区版主 - 可以管理评论和用户'
  },
  { 
    id: 5, 
    value: 'subscriber', 
    label: 'SUBSCRIBER', 
    name: 'Subscriber',
    color: 'gray', 
    description: '订阅用户 - 可以阅读和评论'
  },
  { 
    id: 6, 
    value: 'api', 
    label: 'API', 
    name: 'API',
    color: 'yellow', 
    description: 'API用户 - 用于程序访问'
  },
  { 
    id: 7, 
    value: 'guest', 
    label: 'GUEST', 
    name: 'Guest',
    color: 'cyan', 
    description: '访客用户'
  }
];

export const getRoleById = (roleId) => {
  return roles.find(r => r.id === roleId);
};

export const getRoleByValue = (value) => {
  return roles.find(r => r.value === value);
};

/**
 * 获取角色信息（支持数字ID和字符串value）
 * @param {number|string} roleIdentifier - 角色ID（数字）或角色value（字符串）
 * @returns {Object|undefined} 角色对象
 */
const getRole = (roleIdentifier) => {
  if (typeof roleIdentifier === 'number') {
    return roles.find(r => r.id === roleIdentifier);
  } else {
    return roles.find(r => r.value === roleIdentifier);
  }
};

export const getRoleLabel = (roleIdentifier) => {
  const role = getRole(roleIdentifier);
  return role ? role.label : 'Unknown';
};

export const getRoleColor = (roleIdentifier) => {
  const role = getRole(roleIdentifier);
  return role ? role.color : 'gray';
};

export const getRoleDescription = (roleIdentifier) => {
  const role = getRole(roleIdentifier);
  return role ? role.description : '未知角色';
};

export const getRoleStyle = (roleIdentifier) => {
  const colorMap = {
    admin: 'bg-red-600 text-white border-red-500',
    editor: 'bg-blue-600 text-white border-blue-500',
    author: 'bg-green-600 text-white border-green-500',
    moderator: 'bg-purple-600 text-white border-purple-500',
    subscriber: 'bg-gray-600 text-white border-gray-500',
    api: 'bg-yellow-500 text-gray-900 border-yellow-400',
    guest: 'bg-cyan-600 text-white border-cyan-500'
  };
  
  const role = getRole(roleIdentifier);
  const value = role ? role.value : 'guest';
  return colorMap[value] || 'bg-gray-600 text-white border-gray-500';
};

// 向后兼容：adminRoles 别名（保留现有引用）
export const adminRoles = roles;
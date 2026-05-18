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

// 角色相关工具函数已迁移到 useRolePermissions composable

// 向后兼容：adminRoles 别名（保留现有引用）
export const adminRoles = roles;
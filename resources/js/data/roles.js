/**
 * roles.js - 角色数据定义
 *
 * 基于 Spatie/laravel-permission 方案 + 扩展字段。
 *
 * 扩展字段：
 * - color: 角色颜色 (用于 UI 显示)
 * - label: 角色标签 (用于 i18n)
 *
 * Spatie 标准字段：
 * - guard_name: 守卫名称 (默认 'web')
 */

export const roles = [
  {
    id: 1,
    name: 'admin',
    label: 'ADMIN',
    guard_name: 'web',
    color: 'red',
    description: '系统管理员 - 拥有所有权限'
  },
  {
    id: 2,
    name: 'editor',
    label: 'EDITOR',
    guard_name: 'web',
    color: 'blue',
    description: '内容编辑 - 可以管理文章和评论'
  },
  {
    id: 3,
    name: 'author',
    label: 'AUTHOR',
    guard_name: 'web',
    color: 'green',
    description: '文章作者 - 可以创建和编辑自己的文章'
  },
  {
    id: 4,
    name: 'moderator',
    label: 'MODERATOR',
    guard_name: 'web',
    color: 'purple',
    description: '社区版主 - 可以管理评论和用户'
  },
  {
    id: 5,
    name: 'subscriber',
    label: 'SUBSCRIBER',
    guard_name: 'web',
    color: 'gray',
    description: '订阅用户 - 可以阅读和评论'
  },
  {
    id: 6,
    name: 'api',
    label: 'API',
    guard_name: 'api',
    color: 'yellow',
    description: 'API用户 - 用于程序访问'
  },
  {
    id: 7,
    name: 'guest',
    label: 'GUEST',
    guard_name: 'web',
    color: 'cyan',
    description: '访客用户'
  }
];

export const adminRoles = roles;

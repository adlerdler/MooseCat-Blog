/**
 * role_permissions.js - 角色权限关联表
 * 
 * 通过关联字段链接角色与权限，模拟数据库中的多对多关系。
 * roleId: 角色ID
 * permissionId: 权限ID（对应 permissions.js 中的 id）
 */

export const rolePermissions = [
  // admin (id: 1) - 系统管理员
  { roleId: 1, permissionId: 1 },   // posts
  { roleId: 1, permissionId: 2 },   // videos
  { roleId: 1, permissionId: 3 },   // projects
  { roleId: 1, permissionId: 4 },   // resources
  { roleId: 1, permissionId: 5 },   // comments
  { roleId: 1, permissionId: 6 },   // users
  { roleId: 1, permissionId: 7 },   // categories
  { roleId: 1, permissionId: 8 },   // tags
  { roleId: 1, permissionId: 9 },   // roles
  { roleId: 1, permissionId: 10 },  // settings
  { roleId: 1, permissionId: 11 },  // api_access
  { roleId: 1, permissionId: 12 },  // analytics

  // editor (id: 2) - 内容编辑
  { roleId: 2, permissionId: 1 },   // posts
  { roleId: 2, permissionId: 5 },   // comments
  { roleId: 2, permissionId: 7 },   // categories
  { roleId: 2, permissionId: 8 },   // tags

  // author (id: 3) - 文章作者
  { roleId: 3, permissionId: 1 },   // posts

  // moderator (id: 4) - 社区版主
  { roleId: 4, permissionId: 5 },   // comments
  { roleId: 4, permissionId: 6 },   // users

  // subscriber (id: 5) - 订阅用户
  // 无权限

  // api (id: 6) - API用户
  { roleId: 6, permissionId: 11 },  // api_access

  // guest (id: 7) - 访客用户
  // 无权限
];

/**
 * 获取角色的所有权限ID
 * @param {number} roleId - 角色ID
 * @returns {number[]} 权限ID数组
 */
export const getPermissionIdsByRoleId = (roleId) => {
  return rolePermissions
    .filter(rp => rp.roleId === roleId)
    .map(rp => rp.permissionId);
};

/**
 * 获取具有指定权限的角色ID列表
 * @param {number} permissionId - 权限ID
 * @returns {number[]} 角色ID数组
 */
export const getRoleIdsByPermissionId = (permissionId) => {
  return rolePermissions
    .filter(rp => rp.permissionId === permissionId)
    .map(rp => rp.roleId);
};

/**
 * 检查角色是否具有指定权限
 * @param {number} roleId - 角色ID
 * @param {number} permissionId - 权限ID
 * @returns {boolean}
 */
export const hasPermission = (roleId, permissionId) => {
  return rolePermissions.some(rp => rp.roleId === roleId && rp.permissionId === permissionId);
};